<?php
$bus_id = $_GET['bus_id'] ?? '';
if (!$bus_id) {
    response("error", "bus_id wajib diisi");
    return;
}
$result = pg_query_params($conn, "
SELECT id_kursi, no_kursi 
FROM kursi 
WHERE id_bus = $1
", [$bus_id]);
$data = [];
while ($row = pg_fetch_assoc($result)) {
    $data[] = $row;
}
response("success", "Data kursi", $data);