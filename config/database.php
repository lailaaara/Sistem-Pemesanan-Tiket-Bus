<?php
header("Content-Type: application/json");

// Load .env
$envFile = __DIR__ . "/../utils/.env";
if (file_exists($envFile)) {
    $lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        if (strpos(trim($line), '#') === 0) continue;
        list($name, $value) = explode('=', $line, 2);
        $_ENV[trim($name)] = trim(trim($value), '"\'');
    }
}

$db_uri = $_ENV['DATABASE_URI'] ?? "host=ep-still-pond-ao33nn92-pooler.c-2.ap-southeast-1.aws.neon.tech port=5432 dbname=neondb user=neondb_owner password=npg_6EoItH1vPfCS sslmode=require";

$conn = pg_connect($db_uri);

if (!$conn) {
    echo json_encode([
        "status" => "error",
        "message" => "Koneksi PostgreSQL gagal"
    ]);
    exit;
}
?>