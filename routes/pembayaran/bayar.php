<?php
$pemesanan_id = is_numeric($param1) ? (int)$param1 : null;
if (!$pemesanan_id) {
    http_response_code(400);
    response("error", "ID pemesanan tidak valid");
    return;
}
$user_id        = $body['user_id']        ?? $_POST['user_id']        ?? '';
$metode_bayar   = $body['metode_bayar']   ?? $_POST['metode_bayar']   ?? 'transfer';
if (!$user_id) {
    http_response_code(400);
    response("error", "user_id wajib diisi");
    return;
}
$cek = pg_query_params($conn, "
    SELECT pemesanan_id, user_id, status_pembayaran, total_harga
    FROM pemesanan_pembayaran
    WHERE pemesanan_id = $1
", [$pemesanan_id]);
if (!$cek || pg_num_rows($cek) === 0) {
    http_response_code(404);
    response("error", "Pemesanan tidak ditemukan");
    return;
}
$pemesanan = pg_fetch_assoc($cek);
if ($pemesanan['user_id'] != $user_id) {
    http_response_code(403);
    response("error", "Akses ditolak");
    return;
}
if ($pemesanan['status_pembayaran'] === 'lunas') {
    http_response_code(400);
    response("error", "Pemesanan ini sudah dibayar");
    return;
}
$update = pg_query_params($conn, "
    UPDATE pemesanan_pembayaran
    SET status_pembayaran = 'lunas',
        metode_pembayaran = $2,
        tanggal_bayar = NOW()
    WHERE pemesanan_id = $1
    RETURNING pemesanan_id, status_pembayaran, total_harga
", [$pemesanan_id, $metode_bayar]);
if (!$update) {
    http_response_code(500);
    response("error", "Gagal memproses pembayaran");
    return;
}
$data = pg_fetch_assoc($update);
response("success", "Pembayaran berhasil", [
    "pemesanan_id"      => $data['pemesanan_id'],
    "status_pembayaran" => $data['status_pembayaran'],
    "total_harga"       => $data['total_harga'],
]);