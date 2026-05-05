<?php
$id_jadwal = is_numeric($param1) ? (int) $param1 : null;
if (!$id_jadwal) {
    http_response_code(400);
    response("error", "ID jadwal tidak valid");
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
$cekJadwal = pg_query_params(
    $conn,
    "SELECT id_jadwal FROM jadwal WHERE id_jadwal = $1",
    [$id_jadwal]
);
if (!$cekJadwal || pg_num_rows($cekJadwal) === 0) {
    http_response_code(404);
    response("error", "Jadwal tidak ditemukan");
    return;
}
$cekPesan = pg_query_params($conn, "
    SELECT 1 FROM pemesanan_pembayaran
    WHERE jadwal_id = $1
      AND status_pembayaran NOT IN ('dibatalkan')
", [$id_jadwal]);
if (pg_num_rows($cekPesan) > 0) {
    http_response_code(409);
    response("error", "Jadwal tidak bisa dihapus karena sudah ada pemesanan aktif");
    return;
}
$cekSemuaPesan = pg_query_params(
    $conn,
    "SELECT 1 FROM pemesanan_pembayaran WHERE jadwal_id = $1",
    [$id_jadwal]
);
if (pg_num_rows($cekSemuaPesan) > 0) {
    pg_query_params(
        $conn,
        "UPDATE jadwal SET status_jadwal = 'nonaktif' WHERE id_jadwal = $1",
        [$id_jadwal]
    );
    response("success", "Jadwal dinonaktifkan (ada riwayat pemesanan)");
} else {
    pg_query_params(
        $conn,
        "DELETE FROM jadwal WHERE id_jadwal = $1",
        [$id_jadwal]
    );
    response("success", "Jadwal berhasil dihapus");
}
