<?php
$user_id = $_GET['user_id'] ?? '';
if (!$user_id) {
    response("error", "user_id wajib diisi");
    return;
}
$result = pg_query_params($conn, "
SELECT 
    t.tiket_id,
    t.kode_tiket,
    t.status_tiket,
    k.no_kursi,
    p.kode_booking,
    j.tanggal_berangkat,
    j.jam_berangkat,
    b.nama_bus,
    r.kota_asal,
    r.kota_tujuan
FROM tiket t
JOIN kursi k ON t.id_kursi = k.id_kursi
JOIN pemesanan_pembayaran p ON t.pemesanan_id = p.pemesanan_id
JOIN jadwal j ON p.jadwal_id = j.id_jadwal
JOIN bus b ON j.bus_id = b.bus_id
JOIN rute r ON j.rute_id = r.rute_id
WHERE p.user_id = $1
ORDER BY t.tiket_id DESC
", [$user_id]);
if (!$result) {
    response("error", "Gagal mengambil data tiket");
    return;
}
$data = [];
while ($row = pg_fetch_assoc($result)) {
    $data[] = [
        "tiket_id" => $row['tiket_id'],
        "kode_tiket" => $row['kode_tiket'],
        "status" => $row['status_tiket'],
        "kursi" => $row['no_kursi'],
        "kode_booking" => $row['kode_booking'],
        "bus" => $row['nama_bus'],
        "asal" => $row['kota_asal'],
        "tujuan" => $row['kota_tujuan'],
        "tanggal" => $row['tanggal_berangkat'],
        "jam" => $row['jam_berangkat']
    ];
}
response("success", "Data tiket", $data);