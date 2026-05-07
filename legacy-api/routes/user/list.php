<?php
$id_admin = $_GET['id_admin'] ?? '';
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
$where = '';
$params = [];
if (!empty($_GET['role'])) {
    $where = "WHERE role = $1";
    $params[] = $_GET['role'];
}
$result = pg_query_params($conn, "
    SELECT id_user, nama, email, role
    FROM users
    $where
    ORDER BY id_user ASC
", $params);
if (!$result) {
    http_response_code(500);
    response("error", "Gagal mengambil data user");
    return;
}
$data = [];
while ($row = pg_fetch_assoc($result)) {
    $data[] = [
        "id_user" => $row['id_user'],
        "nama" => $row['nama'],
        "email" => $row['email'],
        "role" => $row['role'],
    ];
}
response("success", count($data) . " user ditemukan", $data);
