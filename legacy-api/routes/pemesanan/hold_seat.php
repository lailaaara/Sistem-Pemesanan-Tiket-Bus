<?php
$user_id   = $body['user_id']   ?? $_POST['user_id']   ?? '';
$id_kursi  = $body['id_kursi']  ?? $_POST['id_kursi']  ?? '';
$id_jadwal = $body['id_jadwal'] ?? $_POST['id_jadwal'] ?? '';
if (!$user_id || !$id_kursi || !$id_jadwal) {
    http_response_code(400);
    response("error", "user_id, id_kursi, dan id_jadwal wajib diisi");
    return;
}
pg_query($conn, "DELETE FROM seat_hold WHERE expired_at < NOW()");
$cekHold = pg_query_params($conn, "
    SELECT user_id FROM seat_hold
    WHERE id_kursi = $1 AND id_jadwal = $2 AND expired_at > NOW()
", [$id_kursi, $id_jadwal]);
if (pg_num_rows($cekHold) > 0) {
    $holder = pg_fetch_assoc($cekHold);
    if ($holder['user_id'] != $user_id) {
        http_response_code(409);
        response("error", "Kursi sedang dikunci oleh pengguna lain");
        return;
    }
}
$cekTiket = pg_query_params($conn, "
    SELECT 1 FROM tiket t
    JOIN pemesanan_pembayaran p ON t.pemesanan_id = p.pemesanan_id
    WHERE t.id_kursi = $1
      AND p.jadwal_id = $2
      AND p.status_pembayaran != 'dibatalkan'
", [$id_kursi, $id_jadwal]);
if (pg_num_rows($cekTiket) > 0) {
    http_response_code(409);
    response("error", "Kursi sudah dipesan");
    return;
}
pg_query_params($conn, "
    DELETE FROM seat_hold WHERE id_kursi = $1 AND id_jadwal = $2 AND user_id = $3
", [$id_kursi, $id_jadwal, $user_id]);
$result = pg_query_params($conn, "
    INSERT INTO seat_hold (id_kursi, id_jadwal, user_id, expired_at)
    VALUES ($1, $2, $3, NOW() + INTERVAL '10 minutes')
    RETURNING hold_id, expired_at
", [$id_kursi, $id_jadwal, $user_id]);
if (!$result) {
    http_response_code(500);
    response("error", "Gagal mengunci kursi");
    return;
}
$row = pg_fetch_assoc($result);
http_response_code(201);
response("success", "Kursi berhasil dikunci selama 10 menit", [
    "hold_id"    => $row['hold_id'],
    "id_kursi"   => $id_kursi,
    "id_jadwal"  => $id_jadwal,
    "expired_at" => $row['expired_at'],
]);
