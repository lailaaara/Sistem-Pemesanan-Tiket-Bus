<?php
$user_id = $_GET['user_id'] ?? $_SERVER['HTTP_X_USER_ID'] ?? '';
if (!$user_id || !is_numeric($user_id)) {
    http_response_code(401);
    response("error", "user_id wajib diisi");
    return;
}
$result = pg_query_params($conn,
    "SELECT id_user, nama, email, role FROM users WHERE id_user = $1",
    [$user_id]);
if (!$result || pg_num_rows($result) === 0) {
    http_response_code(404);
    response("error", "User tidak ditemukan");
    return;
}
$row = pg_fetch_assoc($result);
response("success", "Profil user", [
    "id_user" => $row['id_user'],
    "nama"    => $row['nama'],
    "email"   => $row['email'],
    "role"    => $row['role'],
]);
