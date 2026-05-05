<?php
$statusFilter = "WHERE j.status_jadwal = 'aktif'";
if (isset($_GET['status']) && $_GET['status'] === 'semua') {
    $statusFilter = "";
}
$result = pg_query($conn, "
SELECT 
    j.id_jadwal,
    j.tanggal_berangkat,
    j.jam_berangkat,
    j.harga,
    j.kursi_tersedia,
    j.status_jadwal,
    b.bus_id,
    b.nama_bus,
    r.rute_id,
    r.kota_asal,
    r.kota_tujuan
FROM jadwal j
JOIN bus b ON j.bus_id = b.bus_id
JOIN rute r ON j.rute_id = r.rute_id
$statusFilter
ORDER BY j.tanggal_berangkat ASC, j.jam_berangkat ASC
");
if (!$result) {
    response("error", "Gagal mengambil data jadwal");
    return;
}
$data = [];
while ($row = pg_fetch_assoc($result)) {
    $data[] = [
        "id_jadwal"       => $row['id_jadwal'],
        "bus_id"          => $row['bus_id'],
        "bus"             => $row['nama_bus'],
        "rute_id"         => $row['rute_id'],
        "asal"            => $row['kota_asal'],
        "tujuan"          => $row['kota_tujuan'],
        "tanggal"         => $row['tanggal_berangkat'],
        "jam"             => $row['jam_berangkat'],
        "harga"           => $row['harga'],
        "kursi_tersedia"  => $row['kursi_tersedia'],
        "status"          => $row['status_jadwal'],
    ];
}
response("success", "Data jadwal", $data);