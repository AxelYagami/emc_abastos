<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

$portal = App\Models\Portal::find(1);
$dominios = $portal->dominios ?? [];
$dominios[] = 'localhost';
$dominios[] = '127.0.0.1';
$portal->dominios = array_unique($dominios);
$portal->save();

echo "Dominios actualizados: " . json_encode($portal->dominios) . PHP_EOL;
