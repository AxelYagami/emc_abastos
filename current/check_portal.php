<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

$portal = \App\Models\Portal::find(1);
echo "Portal: " . $portal->nombre . PHP_EOL;
echo "Slug: " . $portal->slug . PHP_EOL;
echo "Dominios: " . json_encode($portal->dominios) . PHP_EOL;
echo "Activo: " . ($portal->activo ? 'Sí' : 'No') . PHP_EOL;
