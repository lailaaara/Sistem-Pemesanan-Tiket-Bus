<?php
$user_id  = $body['user_id']  ?? $_POST['user_id']  ?? '';
$jadwal_id= $body['jadwal_id']?? $_POST['jadwal_id']?? '';
$kursi    = $body['kursi']    ?? $_POST['kursi']    ?? []; 
if (!$user_id || !$jadwal_id || empty($kursi)) {
    response("error", "Data tidak lengkap");
    return;
}
if (!is_array($kursi)) {
    $kursi = explode(',', $kursi);
}
pg_query($conn, "BEGIN");

$query = "
SELECT t.id_kursi 
FROM tiket t
JOIN pemesanan_pembayaran p ON t.pemesanan_id = p.pemesanan_id
WHERE p.jadwal_id = $1 
AND t.id_kursi = ANY($2)
FOR UPDATE
";
$check = pg_query_params($conn, $query, [$jadwal_id, '{' . implode(',', $kursi) . '}']);
if (pg_num_rows($check) > 0) {
    pg_query($conn, "ROLLBACK");
    response("error", "Salah satu kursi sudah dipesan");
    return;
}

$q = pg_query_params($conn, "
SELECT harga, kursi_tersedia 
FROM jadwal 
WHERE id_jadwal = $1
FOR UPDATE
", [$jadwal_id]);
$jadwal = pg_fetch_assoc($q);

if (!$jadwal) {
    pg_query($conn, "ROLLBACK");
    response("error", "Jadwal tidak ditemukan");
    return;
}

if ($jadwal['kursi_tersedia'] < count($kursi)) {
    pg_query($conn, "ROLLBACK");
    response("error", "Kursi tidak mencukupi");
    return;
}

$harga = $jadwal['harga'];
$total = $harga * count($kursi);
$kode = 'BOOK' . strtoupper(substr(uniqid(), -6)) . rand(10, 99);

$res = pg_query_params($conn, "
INSERT INTO pemesanan_pembayaran
(user_id, jadwal_id, kode_booking, jumlah_kursi, total_harga, status_pembayaran)
VALUES ($1,$2,$3,$4,$5,'pending')
RETURNING pemesanan_id
", [$user_id, $jadwal_id, $kode, count($kursi), $total]);

if (!$res) {
    pg_query($conn, "ROLLBACK");
    response("error", "Gagal membuat pesanan");
    return;
}

$pemesanan = pg_fetch_assoc($res);
$pemesanan_id = $pemesanan['pemesanan_id'];

foreach ($kursi as $k) {
    $res_tiket = pg_query_params($conn, "
    INSERT INTO tiket (pemesanan_id, id_kursi, kode_tiket, status_tiket)
    VALUES ($1, $2, $3, 'aktif')
    ", [$pemesanan_id, $k, 'TKT' . strtoupper(substr(uniqid(), -6)) . rand(10,99)]);
    
    if (!$res_tiket) {
        pg_query($conn, "ROLLBACK");
        response("error", "Gagal membuat tiket");
        return;
    }
}

$res_update = pg_query_params($conn, "
UPDATE jadwal
SET kursi_tersedia = kursi_tersedia - $1
WHERE id_jadwal = $2
", [count($kursi), $jadwal_id]);

if (!$res_update) {
    pg_query($conn, "ROLLBACK");
    response("error", "Gagal memperbarui jadwal");
    return;
}

pg_query($conn, "COMMIT");

response("success", "Booking berhasil", [
    "kode_booking" => $kode,
    "total_harga" => $total
]);