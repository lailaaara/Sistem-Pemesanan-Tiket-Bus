<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$tables = ['bus', 'rute', 'jadwal', 'pemesanan_pembayaran', 'tiket', 'users'];
$schema = [];
foreach($tables as $table) {
    $columns = Illuminate\Support\Facades\Schema::getColumns($table);
    $schema[$table] = array_map(function($c) { return $c['name'] . ' (' . $c['type_name'] . ')'; }, $columns);
}
echo json_encode($schema, JSON_PRETTY_PRINT);
