<?php
$id_kursi = is_numeric($param1) ? (int) $param1 : null;
if (!$id_kursi) {
    http_response_code(400);
    response("error", "ID kursi tidak valid");
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
$cekKursi = pg_query_params(
    $conn,
    "SELECT id_kursi FROM kursi WHERE id_kursi = $1",
    [$id_kursi]
);
if (!$cekKursi || pg_num_rows($cekKursi) === 0) {
    http_response_code(404);
    response("error", "Kursi tidak ditemukan");
    return;
}
$cekTiket = pg_query_params(
    $conn,
    "SELECT 1 FROM tiket WHERE id_kursi = $1",
    [$id_kursi]
);
if (pg_num_rows($cekTiket) > 0) {
    http_response_code(409);
    response("error", "Kursi tidak bisa dihapus karena sudah digunakan di tiket");
    return;
}
$result = pg_query_params(
    $conn,
    "DELETE FROM kursi WHERE id_kursi = $1",
    [$id_kursi]
);
if (!$result) {
    http_response_code(500);
    response("error", "Gagal menghapus kursi");
    return;
}
response("success", "Kursi berhasil dihapus");
