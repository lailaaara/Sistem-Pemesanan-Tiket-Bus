<?php
$bus_id = is_numeric($param1) ? (int)$param1 : null;
if (!$bus_id) {
    http_response_code(400);
    response("error", "ID bus tidak valid");
    return;
}
$cekBus = pg_query_params($conn,
    "SELECT bus_id, nama_bus, kapasitas FROM bus WHERE bus_id = $1", [$bus_id]);
if (!$cekBus || pg_num_rows($cekBus) === 0) {
    http_response_code(404);
    response("error", "Bus tidak ditemukan");
    return;
}
$bus = pg_fetch_assoc($cekBus);
$result = pg_query_params($conn, "
    SELECT id_kursi, no_kursi
    FROM kursi
    WHERE id_bus = $1
    ORDER BY no_kursi ASC
", [$bus_id]);
$kursi = [];
while ($row = pg_fetch_assoc($result)) {
    $kursi[] = [
        "id_kursi" => $row['id_kursi'],
        "no_kursi" => $row['no_kursi'],
    ];
}
response("success", "Kursi bus " . $bus['nama_bus'], [
    "bus_id"   => $bus['bus_id'],
    "nama_bus" => $bus['nama_bus'],
    "kapasitas"=> $bus['kapasitas'],
    "total_kursi" => count($kursi),
    "kursi"    => $kursi,
]);
