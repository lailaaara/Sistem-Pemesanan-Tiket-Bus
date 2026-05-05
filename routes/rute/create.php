<?php
$id_admin = $body['id_admin'] ?? $_POST['id_admin'] ?? '';
$kota_asal = trim($body['kota_asal'] ?? $_POST['kota_asal'] ?? '');
$kota_tujuan = trim($body['kota_tujuan'] ?? $_POST['kota_tujuan'] ?? '');
$jarak_km = $body['jarak_km'] ?? $_POST['jarak_km'] ?? '';
if (!$id_admin || !$kota_asal || !$kota_tujuan || !$jarak_km) {
    http_response_code(400);
    response("error", "id_admin, kota_asal, kota_tujuan, dan jarak_km wajib diisi");
    return;
}
if (strtolower($kota_asal) === strtolower($kota_tujuan)) {
    http_response_code(400);
    response("error", "Kota asal dan tujuan tidak boleh sama");
    return;
}
if (!is_numeric($jarak_km) || (float) $jarak_km <= 0) {
    http_response_code(400);
    response("error", "jarak_km harus berupa angka positif");
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
$cekDuplikat = pg_query_params($conn, "
    SELECT 1 FROM rute
    WHERE LOWER(kota_asal) = LOWER($1)
      AND LOWER(kota_tujuan) = LOWER($2)
", [$kota_asal, $kota_tujuan]);
if (pg_num_rows($cekDuplikat) > 0) {
    http_response_code(409);
    response("error", "Rute $kota_asal → $kota_tujuan sudah ada");
    return;
}
$result = pg_query_params($conn, "
    INSERT INTO rute (kota_asal, kota_tujuan, jarak_km, id_admin)
    VALUES ($1, $2, $3, $4)
    RETURNING rute_id, kota_asal, kota_tujuan, jarak_km
", [$kota_asal, $kota_tujuan, (float) $jarak_km, $id_admin]);
if (!$result) {
    http_response_code(500);
    response("error", "Gagal menambahkan rute");
    return;
}
$data = pg_fetch_assoc($result);
http_response_code(201);
response("success", "Rute berhasil ditambahkan", $data);
