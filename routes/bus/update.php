<?php
$bus_id = is_numeric($param1) ? (int)$param1 : null;
if (!$bus_id) {
    http_response_code(400);
    response("error", "ID bus tidak valid");
    return;
}
$id_admin  = $body['id_admin']   ?? $_POST['id_admin']   ?? '';
$no_polisi = trim($body['no_polisi']  ?? $_POST['no_polisi']  ?? '');
$nama_bus  = trim($body['nama_bus']   ?? $_POST['nama_bus']   ?? '');
$kapasitas = $body['kapasitas']  ?? $_POST['kapasitas']  ?? '';
$status_bus= $body['status_bus'] ?? $_POST['status_bus'] ?? '';
if (!$id_admin) {
    http_response_code(400);
    response("error", "id_admin wajib diisi");
    return;
}
$cekAdmin = pg_query_params($conn,
    "SELECT role FROM users WHERE id_user = $1", [$id_admin]);
$admin = pg_fetch_assoc($cekAdmin);
if (!$admin || $admin['role'] !== 'admin') {
    http_response_code(403);
    response("error", "Akses ditolak, hanya admin");
    return;
}
$cekBus = pg_query_params($conn,
    "SELECT bus_id, no_polisi, nama_bus, kapasitas, status_bus FROM bus WHERE bus_id = $1",
    [$bus_id]);
if (!$cekBus || pg_num_rows($cekBus) === 0) {
    http_response_code(404);
    response("error", "Bus tidak ditemukan");
    return;
}
$existing = pg_fetch_assoc($cekBus);
$no_polisi  = $no_polisi  ?: $existing['no_polisi'];
$nama_bus   = $nama_bus   ?: $existing['nama_bus'];
$kapasitas  = $kapasitas  ?: $existing['kapasitas'];
$status_bus = $status_bus ?: $existing['status_bus'];
if ($no_polisi !== $existing['no_polisi']) {
    $cekPolisi = pg_query_params($conn,
        "SELECT 1 FROM bus WHERE no_polisi = $1 AND bus_id != $2",
        [$no_polisi, $bus_id]);
    if (pg_num_rows($cekPolisi) > 0) {
        http_response_code(409);
        response("error", "No. polisi $no_polisi sudah dipakai bus lain");
        return;
    }
}
$result = pg_query_params($conn, "
    UPDATE bus
    SET no_polisi = $1, nama_bus = $2, kapasitas = $3, status_bus = $4
    WHERE bus_id = $5
    RETURNING bus_id, no_polisi, nama_bus, kapasitas, status_bus
", [$no_polisi, $nama_bus, (int)$kapasitas, $status_bus, $bus_id]);
if (!$result) {
    http_response_code(500);
    response("error", "Gagal mengupdate bus");
    return;
}
response("success", "Bus berhasil diupdate", pg_fetch_assoc($result));
