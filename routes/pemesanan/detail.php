<?php
$pemesanan_id = is_numeric($param1) ? (int) $param1 : null;
if (!$pemesanan_id) {
    http_response_code(400);
    response("error", "ID pemesanan tidak valid");
    return;
}
$resPesan = pg_query_params($conn, "
SELECT
    p.pemesanan_id,
    p.kode_booking,
    p.tanggal_pemesanan,
    p.jumlah_kursi,
    p.total_harga,
    p.status_pembayaran,
    p.metode_pembayaran,
    p.tanggal_bayar,
    u.id_user,
    u.nama AS nama_user,
    u.email,
    j.id_jadwal,
    j.tanggal_berangkat,
    j.jam_berangkat,
    j.harga AS harga_per_kursi,
    b.bus_id,
    b.nama_bus,
    r.rute_id,
    r.kota_asal,
    r.kota_tujuan
FROM pemesanan_pembayaran p
JOIN users  u ON p.user_id   = u.id_user
JOIN jadwal j ON p.jadwal_id = j.id_jadwal
JOIN bus    b ON j.bus_id    = b.bus_id
JOIN rute   r ON j.rute_id   = r.rute_id
WHERE p.pemesanan_id = $1
", [$pemesanan_id]);
if (!$resPesan || pg_num_rows($resPesan) === 0) {
    http_response_code(404);
    response("error", "Pemesanan tidak ditemukan");
    return;
}
$p = pg_fetch_assoc($resPesan);
$resTiket = pg_query_params($conn, "
SELECT t.tiket_id, t.kode_tiket, t.status_tiket, k.no_kursi
FROM tiket t
JOIN kursi k ON t.id_kursi = k.id_kursi
WHERE t.pemesanan_id = $1
ORDER BY k.no_kursi ASC
", [$pemesanan_id]);
$tiket = [];
while ($row = pg_fetch_assoc($resTiket)) {
    $tiket[] = [
        "tiket_id" => $row['tiket_id'],
        "kode_tiket" => $row['kode_tiket'],
        "status_tiket" => $row['status_tiket'],
        "no_kursi" => $row['no_kursi'],
    ];
}
response("success", "Detail pemesanan", [
    "pemesanan_id" => $p['pemesanan_id'],
    "kode_booking" => $p['kode_booking'],
    "tanggal_pemesanan" => $p['tanggal_pemesanan'],
    "jumlah_kursi" => $p['jumlah_kursi'],
    "harga_per_kursi" => $p['harga_per_kursi'],
    "total_harga" => $p['total_harga'],
    "status_pembayaran" => $p['status_pembayaran'],
    "metode_pembayaran" => $p['metode_pembayaran'],
    "tanggal_bayar" => $p['tanggal_bayar'],
    "user" => [
        "id_user" => $p['id_user'],
        "nama" => $p['nama_user'],
        "email" => $p['email'],
    ],
    "jadwal" => [
        "id_jadwal" => $p['id_jadwal'],
        "bus_id" => $p['bus_id'],
        "bus" => $p['nama_bus'],
        "rute_id" => $p['rute_id'],
        "asal" => $p['kota_asal'],
        "tujuan" => $p['kota_tujuan'],
        "tanggal" => $p['tanggal_berangkat'],
        "jam" => $p['jam_berangkat'],
    ],
    "tiket" => $tiket,
]);
