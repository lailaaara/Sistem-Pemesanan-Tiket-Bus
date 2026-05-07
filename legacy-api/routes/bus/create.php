<?php
$id_admin  = $body['id_admin']   ?? $_POST['id_admin']   ?? '';
$no_polisi = trim($body['no_polisi']  ?? $_POST['no_polisi']  ?? '');
$nama_bus  = trim($body['nama_bus']   ?? $_POST['nama_bus']   ?? '');
$kapasitas = $body['kapasitas']  ?? $_POST['kapasitas']  ?? '';
$status_bus= $body['status_bus'] ?? $_POST['status_bus'] ?? 'aktif';
if (!$id_admin || !$no_polisi || !$nama_bus || !$kapasitas) {
    http_response_code(400);
    response("error", "id_admin, no_polisi, nama_bus, dan kapasitas wajib diisi");
    return;
}
$cekAdmin = pg_query_params($conn,
    "SELECT role FROM users WHERE id_user = $1",
    [$id_admin]
);
$admin = pg_fetch_assoc($cekAdmin);
if (!$admin || $admin['role'] !== 'admin') {
    http_response_code(403);
    response("error", "Akses ditolak, hanya admin");
    return;
}
if (!is_numeric($kapasitas) || (int)$kapasitas <= 0) {
    http_response_code(400);
    response("error", "Kapasitas harus berupa angka positif");
    return;
}
$cekPolisi = pg_query_params($conn,
    "SELECT 1 FROM bus WHERE no_polisi = $1",
    [$no_polisi]
);
if (pg_num_rows($cekPolisi) > 0) {
    http_response_code(409);
    response("error", "No. polisi $no_polisi sudah terdaftar");
    return;
}
$result = pg_query_params($conn, "
    INSERT INTO bus (no_polisi, nama_bus, kapasitas, status_bus)
    VALUES ($1, $2, $3, $4)
    RETURNING bus_id, no_polisi, nama_bus, kapasitas, status_bus
", [$no_polisi, $nama_bus, (int)$kapasitas, $status_bus]);
if (!$result) {
    http_response_code(500);
    response("error", "Gagal menambahkan bus");
    return;
}
$data = pg_fetch_assoc($result);
http_response_code(201);
response("success", "Bus berhasil ditambahkan", $data);
