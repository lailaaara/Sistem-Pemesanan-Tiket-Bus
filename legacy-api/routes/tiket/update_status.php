<?php
$tiket_id = is_numeric($param1) ? (int) $param1 : null;
if (!$tiket_id) {
    http_response_code(400);
    response("error", "ID tiket tidak valid");
    return;
}
$id_admin = $body['id_admin'] ?? $_POST['id_admin'] ?? '';
$status_baru = $body['status_tiket'] ?? $_POST['status_tiket'] ?? '';
if (!$id_admin || !$status_baru) {
    http_response_code(400);
    response("error", "id_admin dan status_tiket wajib diisi");
    return;
}
$statusValid = ['aktif', 'digunakan', 'dibatalkan'];
if (!in_array($status_baru, $statusValid)) {
    http_response_code(400);
    response("error", "Status tidak valid. Pilihan: " . implode(', ', $statusValid));
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
$cekTiket = pg_query_params(
    $conn,
    "SELECT tiket_id, status_tiket FROM tiket WHERE tiket_id = $1",
    [$tiket_id]
);
if (!$cekTiket || pg_num_rows($cekTiket) === 0) {
    http_response_code(404);
    response("error", "Tiket tidak ditemukan");
    return;
}
$result = pg_query_params($conn, "
    UPDATE tiket SET status_tiket = $1
    WHERE tiket_id = $2
    RETURNING tiket_id, kode_tiket, status_tiket
", [$status_baru, $tiket_id]);
if (!$result) {
    http_response_code(500);
    response("error", "Gagal mengupdate status tiket");
    return;
}
response("success", "Status tiket berhasil diupdate", pg_fetch_assoc($result));
