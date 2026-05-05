<?php
$id_jadwal = is_numeric($param1) ? (int) $param1 : null;
if (!$id_jadwal) {
    http_response_code(400);
    response("error", "ID jadwal tidak valid");
    return;
}
$id_admin = $body['id_admin'] ?? $_POST['id_admin'] ?? '';
$bus_id = $body['bus_id'] ?? $_POST['bus_id'] ?? '';
$rute_id = $body['rute_id'] ?? $_POST['rute_id'] ?? '';
$tanggal = $body['tanggal_berangkat'] ?? $_POST['tanggal_berangkat'] ?? '';
$jam = $body['jam_berangkat'] ?? $_POST['jam_berangkat'] ?? '';
$harga = $body['harga'] ?? $_POST['harga'] ?? '';
$status = $body['status_jadwal'] ?? $_POST['status_jadwal'] ?? '';
if (!$id_admin) {
    http_response_code(400);
    response("error", "id_admin wajib diisi");
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
$cekJadwal = pg_query_params($conn, "
    SELECT id_jadwal, bus_id, rute_id, tanggal_berangkat,
           jam_berangkat, harga, status_jadwal
    FROM jadwal WHERE id_jadwal = $1
", [$id_jadwal]);
if (!$cekJadwal || pg_num_rows($cekJadwal) === 0) {
    http_response_code(404);
    response("error", "Jadwal tidak ditemukan");
    return;
}
$existing = pg_fetch_assoc($cekJadwal);
$bus_id = $bus_id ?: $existing['bus_id'];
$rute_id = $rute_id ?: $existing['rute_id'];
$tanggal = $tanggal ?: $existing['tanggal_berangkat'];
$jam = $jam ?: $existing['jam_berangkat'];
$harga = $harga ?: $existing['harga'];
$status = $status ?: $existing['status_jadwal'];
$result = pg_query_params($conn, "
    UPDATE jadwal
    SET bus_id = $1, rute_id = $2, tanggal_berangkat = $3,
        jam_berangkat = $4, harga = $5, status_jadwal = $6
    WHERE id_jadwal = $7
    RETURNING id_jadwal, bus_id, rute_id, tanggal_berangkat,
              jam_berangkat, harga, kursi_tersedia, status_jadwal
", [$bus_id, $rute_id, $tanggal, $jam, $harga, $status, $id_jadwal]);
if (!$result) {
    http_response_code(500);
    response("error", "Gagal mengupdate jadwal");
    return;
}
$data = pg_fetch_assoc($result);
response("success", "Jadwal berhasil diupdate", [
    "id_jadwal" => $data['id_jadwal'],
    "bus_id" => $data['bus_id'],
    "rute_id" => $data['rute_id'],
    "tanggal" => $data['tanggal_berangkat'],
    "jam" => $data['jam_berangkat'],
    "harga" => $data['harga'],
    "kursi_tersedia" => $data['kursi_tersedia'],
    "status" => $data['status_jadwal'],
]);
