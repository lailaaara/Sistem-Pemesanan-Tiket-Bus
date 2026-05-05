<?php
$rute_id = is_numeric($param1) ? (int) $param1 : null;
if (!$rute_id) {
    http_response_code(400);
    response("error", "ID rute tidak valid");
    return;
}
$id_admin = $body['id_admin'] ?? $_POST['id_admin'] ?? $_GET['id_admin'] ?? '';
if (!$id_admin) {
    http_response_code(400);
    response("error", "id_admin wajib diisi");
    return;
}
$cekAdmin = pg_query_params(
    $conn,
    "SELECT role FROM users WHERE id_user = $1",
    [$id_admin]
);
$admin = pg_fetch_assoc($cekAdmin);
if (!$admin || $admin['role'] !== 'admin') {
    http_response_code(403);
    response("error", "Akses ditolak, hanya admin");
    return;
}
$cekRute = pg_query_params(
    $conn,
    "SELECT rute_id FROM rute WHERE rute_id = $1",
    [$rute_id]
);
if (!$cekRute || pg_num_rows($cekRute) === 0) {
    http_response_code(404);
    response("error", "Rute tidak ditemukan");
    return;
}
$cekJadwal = pg_query_params(
    $conn,
    "SELECT 1 FROM jadwal WHERE rute_id = $1",
    [$rute_id]
);
if (pg_num_rows($cekJadwal) > 0) {
    http_response_code(409);
    response("error", "Rute tidak bisa dihapus karena masih digunakan di jadwal");
    return;
}
$result = pg_query_params(
    $conn,
    "DELETE FROM rute WHERE rute_id = $1",
    [$rute_id]
);
if (!$result) {
    http_response_code(500);
    response("error", "Gagal menghapus rute");
    return;
}
response("success", "Rute berhasil dihapus");
