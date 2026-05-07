<?php
$id_admin = $body['id_admin'] ?? $_POST['id_admin'] ?? '';
$bus_id = $body['bus_id'] ?? $_POST['bus_id'] ?? '';
$rute_id = $body['rute_id'] ?? $_POST['rute_id'] ?? '';
$tanggal = $body['tanggal_berangkat'] ?? $_POST['tanggal_berangkat'] ?? '';
$jam = $body['jam_berangkat'] ?? $_POST['jam_berangkat'] ?? '';
$harga = $body['harga'] ?? $_POST['harga'] ?? '';
if (!$id_admin || !$bus_id || !$rute_id || !$tanggal || !$jam || !$harga) {
    response("error", "Data tidak lengkap");
    return;
}
$cekAdmin = pg_query_params(
    $conn,
    "SELECT role FROM users WHERE id_user = $1",
    [$id_admin]
);
$admin = pg_fetch_assoc($cekAdmin);
if (!$admin || $admin['role'] !== 'admin') {
    response("error", "Akses ditolak, hanya admin");
    return;
}
$qBus = pg_query_params(
    $conn,
    "SELECT kapasitas FROM bus WHERE bus_id = $1",
    [$bus_id]
);
$bus = pg_fetch_assoc($qBus);
if (!$bus) {
    response("error", "Bus tidak ditemukan");
    return;
}
$kapasitas = $bus['kapasitas'];
$result = pg_query_params($conn, "
INSERT INTO jadwal (
    bus_id, rute_id, tanggal_berangkat, jam_berangkat,
    harga, kursi_tersedia, status_jadwal, id_admin
)
VALUES ($1,$2,$3,$4,$5,$6,'aktif',$7)
RETURNING id_jadwal
", [$bus_id, $rute_id, $tanggal, $jam, $harga, $kapasitas, $id_admin]);
if ($result) {
    $data = pg_fetch_assoc($result);
    response("success", "Jadwal berhasil ditambahkan", [
        "id_jadwal" => $data['id_jadwal']
    ]);
} else {
    response("error", "Gagal menambahkan jadwal");
}