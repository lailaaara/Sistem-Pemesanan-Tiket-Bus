<?php
$rute_id = is_numeric($param1) ? (int) $param1 : null;
if (!$rute_id) {
    http_response_code(400);
    response("error", "ID rute tidak valid");
    return;
}
$id_admin = $body['id_admin'] ?? $_POST['id_admin'] ?? '';
$kota_asal = trim($body['kota_asal'] ?? $_POST['kota_asal'] ?? '');
$kota_tujuan = trim($body['kota_tujuan'] ?? $_POST['kota_tujuan'] ?? '');
$jarak_km = $body['jarak_km'] ?? $_POST['jarak_km'] ?? '';
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
$cekRute = pg_query_params(
    $conn,
    "SELECT rute_id, kota_asal, kota_tujuan, jarak_km FROM rute WHERE rute_id = $1",
    [$rute_id]
);
if (!$cekRute || pg_num_rows($cekRute) === 0) {
    http_response_code(404);
    response("error", "Rute tidak ditemukan");
    return;
}
$existing = pg_fetch_assoc($cekRute);
$kota_asal = $kota_asal ?: $existing['kota_asal'];
$kota_tujuan = $kota_tujuan ?: $existing['kota_tujuan'];
$jarak_km = $jarak_km ?: $existing['jarak_km'];
if (strtolower($kota_asal) === strtolower($kota_tujuan)) {
    http_response_code(400);
    response("error", "Kota asal dan tujuan tidak boleh sama");
    return;
}
$cekDuplikat = pg_query_params($conn, "
    SELECT 1 FROM rute
    WHERE LOWER(kota_asal) = LOWER($1)
      AND LOWER(kota_tujuan) = LOWER($2)
      AND rute_id != $3
", [$kota_asal, $kota_tujuan, $rute_id]);
if (pg_num_rows($cekDuplikat) > 0) {
    http_response_code(409);
    response("error", "Rute $kota_asal → $kota_tujuan sudah ada");
    return;
}
$result = pg_query_params($conn, "
    UPDATE rute
    SET kota_asal = $1, kota_tujuan = $2, jarak_km = $3
    WHERE rute_id = $4
    RETURNING rute_id, kota_asal, kota_tujuan, jarak_km
", [$kota_asal, $kota_tujuan, (float) $jarak_km, $rute_id]);
if (!$result) {
    http_response_code(500);
    response("error", "Gagal mengupdate rute");
    return;
}
response("success", "Rute berhasil diupdate", pg_fetch_assoc($result));
