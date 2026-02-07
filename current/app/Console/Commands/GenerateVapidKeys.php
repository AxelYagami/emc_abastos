<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class GenerateVapidKeys extends Command
{
    protected $signature = 'push:vapid';
    protected $description = 'Genera VAPID keys para Web Push notifications';

    public function handle(): int
    {
        // Intentar via libreria primero
        if (class_exists(\Minishlink\WebPush\VAPID::class)) {
            try {
                $keys = \Minishlink\WebPush\VAPID::createVapidKeys();
                $this->outputKeys($keys['publicKey'], $keys['privateKey']);
                return self::SUCCESS;
            } catch (\Throwable $e) {
                $this->warn("Libreria fallo ({$e->getMessage()}), usando fallback PHP nativo...");
            }
        }

        // Fallback: generar con OpenSSL nativo de PHP
        // Buscar openssl.cnf en ubicaciones comunes de Windows
        $configPaths = [
            getenv('OPENSSL_CONF'),
            'C:/Program Files/Common Files/SSL/openssl.cnf',
            'C:/xampp/apache/conf/openssl.cnf',
            'C:/laragon/etc/ssl/openssl.cnf',
            'C:/php/extras/ssl/openssl.cnf',
            dirname(PHP_BINARY) . '/extras/ssl/openssl.cnf',
            dirname(PHP_BINARY) . '/../extras/ssl/openssl.cnf',
        ];

        $config = [];
        foreach ($configPaths as $path) {
            if ($path && file_exists($path)) {
                $config['config'] = $path;
                break;
            }
        }

        $config['curve_name'] = 'prime256v1';
        $config['private_key_type'] = OPENSSL_KEYTYPE_EC;

        $key = openssl_pkey_new($config);

        if (!$key) {
            // Ultimo intento: sin config file
            $key = openssl_pkey_new([
                'curve_name' => 'prime256v1',
                'private_key_type' => OPENSSL_KEYTYPE_EC,
            ]);
        }

        if (!$key) {
            $this->error('No se pudo generar la key EC. Error OpenSSL:');
            while ($msg = openssl_error_string()) {
                $this->line("  - {$msg}");
            }
            $this->newLine();
            $this->info('Alternativa: genera las keys online en https://vapidkeys.com');
            $this->info('O en Node.js: npx web-push generate-vapid-keys');
            return self::FAILURE;
        }

        $details = openssl_pkey_get_details($key);
        $x = $details['ec']['x'];
        $y = $details['ec']['y'];
        $d = $details['ec']['d'];

        // Codificar en base64url sin padding
        $publicKey = rtrim(strtr(base64_encode(chr(4) . $x . $y), '+/', '-_'), '=');
        $privateKey = rtrim(strtr(base64_encode($d), '+/', '-_'), '=');

        $this->outputKeys($publicKey, $privateKey);
        return self::SUCCESS;
    }

    private function outputKeys(string $public, string $private): void
    {
        $this->info('VAPID keys generadas:');
        $this->newLine();
        $this->line("VAPID_PUBLIC_KEY={$public}");
        $this->line("VAPID_PRIVATE_KEY={$private}");
        $this->newLine();
        $this->info('Copia estas lineas a tu archivo .env');
    }
}
