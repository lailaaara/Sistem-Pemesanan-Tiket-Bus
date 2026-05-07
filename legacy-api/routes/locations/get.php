<?php
$result = pg_query($conn, "
    SELECT DISTINCT kota_asal AS kota FROM rute
    UNION
    SELECT DISTINCT kota_tujuan AS kota FROM rute
    ORDER BY kota ASC
");
if (!$result) {
    http_response_code(500);
    response("error", "Gagal mengambil data lokasi");
    return;
}
$kota = [];
while ($row = pg_fetch_assoc($result)) {
    $kota[] = $row['kota'];
}
response("success", count($kota) . " lokasi ditemukan", $kota);
