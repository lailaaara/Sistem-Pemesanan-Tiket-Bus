<?php
$id_jadwal = is_numeric($param1) ? (int) $param1 : null;
if (!$id_jadwal) {
    http_response_code(400);
    response("error", "ID jadwal tidak valid");
    return;
}
$cekJadwal = pg_query_params($conn, "
    SELECT id_jadwal, bus_id, kursi_tersedia
    FROM jadwal
    WHERE id_jadwal = $1
", [$id_jadwal]);
if (!$cekJadwal || pg_num_rows($cekJadwal) === 0) {
    http_response_code(404);
    response("error", "Jadwal tidak ditemukan");
    return;
}
$jadwal = pg_fetch_assoc($cekJadwal);
$semuaKursi = pg_query_params($conn, "
    SELECT id_kursi, no_kursi
    FROM kursi
    WHERE id_bus = $1
    ORDER BY no_kursi ASC
", [$jadwal['bus_id']]);
$kursiDipesan = pg_query_params($conn, "
    SELECT t.id_kursi
    FROM tiket t
    JOIN pemesanan_pembayaran p ON t.pemesanan_id = p.pemesanan_id
    WHERE p.jadwal_id = $1
      AND p.status_pembayaran != 'dibatalkan'
", [$id_jadwal]);
$idDipesan = [];
while ($row = pg_fetch_assoc($kursiDipesan)) {
    $idDipesan[] = $row['id_kursi'];
}
$data = [];
while ($row = pg_fetch_assoc($semuaKursi)) {
    $data[] = [
        "id_kursi" => $row['id_kursi'],
        "no_kursi" => $row['no_kursi'],
        "status" => in_array($row['id_kursi'], $idDipesan) ? "terisi" : "tersedia",
    ];
}
response("success", "Kursi jadwal", [
    "id_jadwal" => $id_jadwal,
    "kursi_tersedia" => (int) $jadwal['kursi_tersedia'],
    "kursi" => $data,
]);
