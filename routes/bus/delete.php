<?php
$bus_id = is_numeric($param1) ? (int)$param1 : null;
if (!$bus_id) {
    http_response_code(400);
    response("error", "ID bus tidak valid");
    return;
}
$id_admin = $body['id_admin'] ?? $_POST['id_admin'] ?? $_GET['id_admin'] ?? '';
if (!$id_admin) {
    http_response_code(400);
    response("error", "id_admin wajib diisi");
    return;
}
$cekAdmin = pg_query_params($conn,
    "SELECT role FROM users WHERE id_user = $1", [$id_admin]);
$admin = pg_fetch_assoc($cekAdmin);
if (!$admin || $admin['role'] !== 'admin') {
    http_response_code(403);
    response("error", "Akses ditolak, hanya admin");
    return;
}
$cekBus = pg_query_params($conn,
    "SELECT bus_id FROM bus WHERE bus_id = $1", [$bus_id]);
if (!$cekBus || pg_num_rows($cekBus) === 0) {
    http_response_code(404);
    response("error", "Bus tidak ditemukan");
    return;
}
$cekJadwal = pg_query_params($conn, "
    SELECT 1 FROM jadwal
    WHERE bus_id = $1 AND status_jadwal = 'aktif'
", [$bus_id]);
if (pg_num_rows($cekJadwal) > 0) {
    http_response_code(409);
    response("error", "Bus tidak bisa dihapus karena masih digunakan di jadwal aktif");
    return;
}
pg_query_params($conn, "DELETE FROM kursi WHERE id_bus = $1", [$bus_id]);
$result = pg_query_params($conn, "DELETE FROM bus WHERE bus_id = $1", [$bus_id]);
if (!$result) {
    http_response_code(500);
    response("error", "Gagal menghapus bus");
    return;
}
response("success", "Bus berhasil dihapus");
