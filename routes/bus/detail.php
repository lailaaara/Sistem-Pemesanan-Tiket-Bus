<?php
$bus_id = is_numeric($param1) ? (int)$param1 : null;
if (!$bus_id) {
    http_response_code(400);
    response("error", "ID bus tidak valid");
    return;
}
$result = pg_query_params($conn, "
    SELECT bus_id, no_polisi, nama_bus, kapasitas, status_bus
    FROM bus
    WHERE bus_id = $1
", [$bus_id]);
if (!$result || pg_num_rows($result) === 0) {
    http_response_code(404);
    response("error", "Bus tidak ditemukan");
    return;
}
$row = pg_fetch_assoc($result);
response("success", "Detail bus", [
    "bus_id"    => $row['bus_id'],
    "no_polisi" => $row['no_polisi'],
    "nama_bus"  => $row['nama_bus'],
    "kapasitas" => $row['kapasitas'],
    "status_bus"=> $row['status_bus'],
]);
