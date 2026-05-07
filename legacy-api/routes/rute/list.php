<?php
$result = pg_query($conn, "
    SELECT rute_id, kota_asal, kota_tujuan, jarak_km
    FROM rute
    ORDER BY kota_asal ASC, kota_tujuan ASC
");
if (!$result) {
    http_response_code(500);
    response("error", "Gagal mengambil data rute");
    return;
}
$data = [];
while ($row = pg_fetch_assoc($result)) {
    $data[] = [
        "rute_id" => $row['rute_id'],
        "kota_asal" => $row['kota_asal'],
        "kota_tujuan" => $row['kota_tujuan'],
        "jarak_km" => $row['jarak_km'],
    ];
}
response("success", "Data rute", $data);
