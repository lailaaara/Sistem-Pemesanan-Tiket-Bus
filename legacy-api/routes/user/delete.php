<?php
$id_user = is_numeric($param1) ? (int) $param1 : null;
if (!$id_user) {
    http_response_code(400);
    response("error", "ID user tidak valid");
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
if ($id_admin == $id_user) {
    http_response_code(400);
    response("error", "Tidak bisa menghapus akun sendiri");
    return;
}
$cekUser = pg_query_params(
    $conn,
    "SELECT id_user, nama FROM users WHERE id_user = $1",
    [$id_user]
);
if (!$cekUser || pg_num_rows($cekUser) === 0) {
    http_response_code(404);
    response("error", "User tidak ditemukan");
    return;
}
$cekPesan = pg_query_params($conn, "
    SELECT 1 FROM pemesanan_pembayaran
    WHERE user_id = $1
      AND status_pembayaran NOT IN ('dibatalkan')
", [$id_user]);
if (pg_num_rows($cekPesan) > 0) {
    http_response_code(409);
    response("error", "User tidak bisa dihapus karena masih punya pemesanan aktif");
    return;
}
$result = pg_query_params(
    $conn,
    "DELETE FROM users WHERE id_user = $1",
    [$id_user]
);
if (!$result) {
    http_response_code(500);
    response("error", "Gagal menghapus user");
    return;
}
response("success", "User berhasil dihapus");
