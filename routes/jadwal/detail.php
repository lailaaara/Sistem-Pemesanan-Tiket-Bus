<?php
$id_jadwal = is_numeric($param1) ? (int)$param1 : null;
if (!$id_jadwal) {
    http_response_code(400);
    response("error", "ID jadwal tidak valid");
    return;
}
$result = pg_query_params($conn, "
SELECT
    j.id_jadwal,
    j.tanggal_berangkat,
    j.jam_berangkat,
    j.harga,
    j.kursi_tersedia,
    j.status_jadwal,
    j.id_admin,
    b.bus_id,
    b.no_polisi,
    b.nama_bus,
    b.kapasitas,
    r.rute_id,
    r.kota_asal,
    r.kota_tujuan,
    r.jarak_km
FROM jadwal j
JOIN bus  b ON j.bus_id  = b.bus_id
JOIN rute r ON j.rute_id = r.rute_id
WHERE j.id_jadwal = $1
", [$id_jadwal]);
if (!$result || pg_num_rows($result) === 0) {
    http_response_code(404);
    response("error", "Jadwal tidak ditemukan");
    return;
}
$row = pg_fetch_assoc($result);
response("success", "Detail jadwal", [
    "id_jadwal"      => $row['id_jadwal'],
    "tanggal"        => $row['tanggal_berangkat'],
    "jam"            => $row['jam_berangkat'],
    "harga"          => $row['harga'],
    "kursi_tersedia" => $row['kursi_tersedia'],
    "status"         => $row['status_jadwal'],
    "id_admin"       => $row['id_admin'],
    "bus" => [
        "bus_id"    => $row['bus_id'],
        "no_polisi" => $row['no_polisi'],
        "nama_bus"  => $row['nama_bus'],
        "kapasitas" => $row['kapasitas'],
    ],
    "rute" => [
        "rute_id"     => $row['rute_id'],
        "kota_asal"   => $row['kota_asal'],
        "kota_tujuan" => $row['kota_tujuan'],
        "jarak_km"    => $row['jarak_km'],
    ],
]);
