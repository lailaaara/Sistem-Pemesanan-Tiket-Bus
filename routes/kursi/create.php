<?php
$id_admin = $body['id_admin'] ?? $_POST['id_admin'] ?? '';
$id_bus = $body['id_bus'] ?? $_POST['id_bus'] ?? '';
$no_kursi = trim($body['no_kursi'] ?? $_POST['no_kursi'] ?? '');
if (!$id_admin || !$id_bus || !$no_kursi) {
    http_response_code(400);
    response("error", "id_admin, id_bus, dan no_kursi wajib diisi");
    return;
}
$cekAdmin = pg_query_params(
    $conn,
    "SELECT role FROM users WHERE id_user = $1",
    [$id_admin]
);
$admin = pg_fetch_assoc($cekAdmin);
if (!$admin || $admin['role'] !== 'admin') {
    http_response_code(403);
    response("error", "Akses ditolak, hanya admin");
    return;
}
$cekBus = pg_query_params(
    $conn,
    "SELECT bus_id FROM bus WHERE bus_id = $1",
    [$id_bus]
);
if (!$cekBus || pg_num_rows($cekBus) === 0) {
    http_response_code(404);
    response("error", "Bus tidak ditemukan");
    return;
}
$cekDuplikat = pg_query_params($conn, "
    SELECT 1 FROM kursi WHERE id_bus = $1 AND no_kursi = $2
", [$id_bus, $no_kursi]);
if (pg_num_rows($cekDuplikat) > 0) {
    http_response_code(409);
    response("error", "Kursi $no_kursi sudah ada di bus ini");
    return;
}
$result = pg_query_params($conn, "
    INSERT INTO kursi (no_kursi, id_bus)
    VALUES ($1, $2)
    RETURNING id_kursi, no_kursi, id_bus
", [$no_kursi, $id_bus]);
if (!$result) {
    http_response_code(500);
    response("error", "Gagal menambahkan kursi");
    return;
}
$data = pg_fetch_assoc($result);
http_response_code(201);
response("success", "Kursi berhasil ditambahkan", $data);
