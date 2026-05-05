<?php
header("Content-Type: application/json");
$conn = pg_connect("
    host=localhost 
    port=5432 
    dbname=tiket_bus 
    user=postgres 
    password=password
");
if (!$conn) {
    echo json_encode([
        "status" => "error",
        "message" => "Koneksi PostgreSQL gagal"
    ]);
    exit;
}
?>