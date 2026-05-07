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

$host = $_ENV['DB_HOST'] ?? 'ep-still-pond-ao33nn92-pooler.c-2.ap-southeast-1.aws.neon.tech';
$port = $_ENV['DB_PORT'] ?? '5432';
$dbname = $_ENV['DB_NAME'] ?? 'neondb';
$user = $_ENV['DB_USER'] ?? 'neondb_owner';
$password = $_ENV['DB_PASSWORD'] ?? 'npg_6EoItH1vPfCS';
$endpoint_id = $_ENV['DB_ENDPOINT_ID'] ?? 'ep-still-pond-ao33nn92';

$db_uri = "host=$host port=$port dbname=$dbname user=$user password=$password sslmode=require options='endpoint=$endpoint_id'";

$conn = pg_connect($db_uri);

if (!$conn) {
    echo json_encode([
        "status" => "error",
        "message" => "Koneksi PostgreSQL gagal"
    ]);
    exit;
}
?>