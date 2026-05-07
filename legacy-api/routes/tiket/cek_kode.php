<?php
$kode_tiket = $param2 ?? '';
if (!$kode_tiket) {
    http_response_code(400);
    response("error", "Kode tiket wajib diisi");
    return;
}
$result = pg_query_params($conn, "
SELECT
    t.tiket_id,
    t.kode_tiket,
    t.status_tiket,
    k.no_kursi,
    p.kode_booking,
    p.status_pembayaran,
    u.nama AS nama_user,
    j.tanggal_berangkat,
    j.jam_berangkat,
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
WHERE t.kode_tiket = $1
", [$kode_tiket]);
if (!$result || pg_num_rows($result) === 0) {
    http_response_code(404);
    response("error", "Tiket dengan kode '$kode_tiket' tidak ditemukan");
    return;
}
$row = pg_fetch_assoc($result);
$valid = ($row['status_tiket'] === 'aktif' && $row['status_pembayaran'] === 'lunas');
response("success", "Hasil scan tiket", [
    "valid"           => $valid,
    "tiket_id"        => $row['tiket_id'],
    "kode_tiket"      => $row['kode_tiket'],
    "status_tiket"    => $row['status_tiket'],
    "no_kursi"        => $row['no_kursi'],
    "nama_penumpang"  => $row['nama_user'],
    "kode_booking"    => $row['kode_booking'],
    "status_pembayaran"=> $row['status_pembayaran'],
    "bus"             => $row['nama_bus'],
    "asal"            => $row['kota_asal'],
    "tujuan"          => $row['kota_tujuan'],
    "tanggal"         => $row['tanggal_berangkat'],
    "jam"             => $row['jam_berangkat'],
]);
