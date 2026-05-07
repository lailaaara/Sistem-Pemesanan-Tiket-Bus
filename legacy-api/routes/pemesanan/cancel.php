<?php
$pemesanan_id = is_numeric($param1) ? (int) $param1 : null;
if (!$pemesanan_id) {
    http_response_code(400);
    response("error", "ID pemesanan tidak valid");
    return;
}
$user_id = $body['user_id'] ?? $_POST['user_id'] ?? '';
if (!$user_id) {
    http_response_code(400);
    response("error", "user_id wajib diisi");
    return;
}
$cekPesan = pg_query_params($conn, "
    SELECT pemesanan_id, user_id, jadwal_id, jumlah_kursi, status_pembayaran
    FROM pemesanan_pembayaran
    WHERE pemesanan_id = $1
", [$pemesanan_id]);
if (!$cekPesan || pg_num_rows($cekPesan) === 0) {
    http_response_code(404);
    response("error", "Pemesanan tidak ditemukan");
    return;
}
$pesan = pg_fetch_assoc($cekPesan);
$cekUser = pg_query_params(
    $conn,
    "SELECT role FROM users WHERE id_user = $1",
    [$user_id]
);
$userRow = pg_fetch_assoc($cekUser);
if ($pesan['user_id'] != $user_id && (!$userRow || $userRow['role'] !== 'admin')) {
    http_response_code(403);
    response("error", "Akses ditolak");
    return;
}
if ($pesan['status_pembayaran'] === 'dibatalkan') {
    http_response_code(400);
    response("error", "Pemesanan sudah dibatalkan sebelumnya");
    return;
}
pg_query_params($conn, "
    UPDATE pemesanan_pembayaran
    SET status_pembayaran = 'dibatalkan'
    WHERE pemesanan_id = $1
", [$pemesanan_id]);
pg_query_params($conn, "
    UPDATE tiket SET status_tiket = 'dibatalkan'
    WHERE pemesanan_id = $1
", [$pemesanan_id]);
pg_query_params($conn, "
    UPDATE jadwal
    SET kursi_tersedia = kursi_tersedia + $1
    WHERE id_jadwal = $2
", [$pesan['jumlah_kursi'], $pesan['jadwal_id']]);
response("success", "Pemesanan berhasil dibatalkan");
