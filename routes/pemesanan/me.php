<?php
$user_id = requireAuth();
$result = pg_query_params($conn, "
SELECT
    p.pemesanan_id,
    p.kode_booking,
    p.tanggal_pemesanan,
    p.jumlah_kursi,
    p.total_harga,
    p.status_pembayaran,
    p.metode_pembayaran,
    p.tanggal_bayar,
    j.id_jadwal,
    j.tanggal_berangkat,
    j.jam_berangkat,
    b.nama_bus,
    r.kota_asal,
    r.kota_tujuan
FROM pemesanan_pembayaran p
JOIN jadwal j ON p.jadwal_id = j.id_jadwal
JOIN bus    b ON j.bus_id    = b.bus_id
JOIN rute   r ON j.rute_id   = r.rute_id
WHERE p.user_id = $1
ORDER BY p.pemesanan_id DESC
", [$user_id]);
if (!$result) {
    http_response_code(500);
    response("error", "Gagal mengambil riwayat pemesanan");
    return;
}
$data = [];
while ($row = pg_fetch_assoc($result)) {
    $data[] = [
        "pemesanan_id" => $row['pemesanan_id'],
        "kode_booking" => $row['kode_booking'],
        "tanggal_pemesanan" => $row['tanggal_pemesanan'],
        "jumlah_kursi" => $row['jumlah_kursi'],
        "total_harga" => $row['total_harga'],
        "status_pembayaran" => $row['status_pembayaran'],
        "metode_pembayaran" => $row['metode_pembayaran'],
        "tanggal_bayar" => $row['tanggal_bayar'],
        "jadwal" => [
            "id_jadwal" => $row['id_jadwal'],
            "bus" => $row['nama_bus'],
            "asal" => $row['kota_asal'],
            "tujuan" => $row['kota_tujuan'],
            "tanggal" => $row['tanggal_berangkat'],
            "jam" => $row['jam_berangkat'],
        ],
    ];
}
response("success", "Riwayat pemesanan saya", $data);
