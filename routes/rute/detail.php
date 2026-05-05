<?php
$rute_id = is_numeric($param1) ? (int) $param1 : null;
if (!$rute_id) {
    http_response_code(400);
    response("error", "ID rute tidak valid");
    return;
}
$result = pg_query_params($conn, "
    SELECT rute_id, kota_asal, kota_tujuan, jarak_km, id_admin
    FROM rute
    WHERE rute_id = $1
", [$rute_id]);
if (!$result || pg_num_rows($result) === 0) {
    http_response_code(404);
    response("error", "Rute tidak ditemukan");
    return;
}
$row = pg_fetch_assoc($result);
response("success", "Detail rute", [
    "rute_id" => $row['rute_id'],
    "kota_asal" => $row['kota_asal'],
    "kota_tujuan" => $row['kota_tujuan'],
    "jarak_km" => $row['jarak_km'],
    "id_admin" => $row['id_admin'],
]);
