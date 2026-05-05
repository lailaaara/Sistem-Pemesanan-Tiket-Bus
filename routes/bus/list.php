<?php
$result = pg_query($conn, "
    SELECT bus_id, no_polisi, nama_bus, kapasitas, status_bus
    FROM bus
    ORDER BY nama_bus ASC
");
if (!$result) {
    http_response_code(500);
    response("error", "Gagal mengambil data bus");
    return;
}
$data = [];
while ($row = pg_fetch_assoc($result)) {
    $data[] = [
        "bus_id"    => $row['bus_id'],
        "no_polisi" => $row['no_polisi'],
        "nama_bus"  => $row['nama_bus'],
        "kapasitas" => $row['kapasitas'],
        "status_bus"=> $row['status_bus'],
    ];
}
response("success", "Data bus", $data);
