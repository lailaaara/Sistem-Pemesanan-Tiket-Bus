<?php
$tiket_id = is_numeric($param1) ? (int) $param1 : null;
if (!$tiket_id) {
    http_response_code(400);
    response("error", "ID tiket tidak valid");
    return;
}
$result = pg_query_params($conn, "
SELECT
    t.tiket_id,
    t.kode_tiket,
    t.status_tiket,
    k.no_kursi,
    k.id_bus,
    p.pemesanan_id,
    p.kode_booking,
    p.total_harga,
    p.status_pembayaran,
    p.tanggal_pemesanan,
    u.id_user,
    u.nama AS nama_user,
    u.email,
    j.id_jadwal,
    j.tanggal_berangkat,
    j.jam_berangkat,
    j.harga AS harga_per_kursi,
    b.nama_bus,
    r.kota_asal,
    r.kota_tujuan
FROM tiket t
JOIN kursi               k ON t.id_kursi     = k.id_kursi
JOIN pemesanan_pembayaran p ON t.pemesanan_id = p.pemesanan_id
JOIN users               u ON p.user_id       = u.id_user
JOIN jadwal              j ON p.jadwal_id     = j.id_jadwal
JOIN bus                 b ON j.bus_id        = b.bus_id
JOIN rute                r ON j.rute_id       = r.rute_id
WHERE t.tiket_id = $1
", [$tiket_id]);
if (!$result || pg_num_rows($result) === 0) {
    http_response_code(404);
    response("error", "Tiket tidak ditemukan");
    return;
}
$row = pg_fetch_assoc($result);
response("success", "Detail tiket", [
    "tiket_id" => $row['tiket_id'],
    "kode_tiket" => $row['kode_tiket'],
    "status_tiket" => $row['status_tiket'],
    "no_kursi" => $row['no_kursi'],
    "user" => [
        "id_user" => $row['id_user'],
        "nama" => $row['nama_user'],
        "email" => $row['email'],
    ],
    "pemesanan" => [
        "pemesanan_id" => $row['pemesanan_id'],
        "kode_booking" => $row['kode_booking'],
        "tanggal_pemesanan" => $row['tanggal_pemesanan'],
        "total_harga" => $row['total_harga'],
        "status_pembayaran" => $row['status_pembayaran'],
    ],
    "jadwal" => [
        "id_jadwal" => $row['id_jadwal'],
        "bus" => $row['nama_bus'],
        "asal" => $row['kota_asal'],
        "tujuan" => $row['kota_tujuan'],
        "tanggal" => $row['tanggal_berangkat'],
        "jam" => $row['jam_berangkat'],
        "harga_per_kursi" => $row['harga_per_kursi'],
    ],
]);
