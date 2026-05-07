<?php
$id_user = is_numeric($param1) ? (int) $param1 : null;
if (!$id_user) {
    http_response_code(400);
    response("error", "ID user tidak valid");
    return;
}
$result = pg_query_params($conn, "
    SELECT id_user, nama, email, role
    FROM users
    WHERE id_user = $1
", [$id_user]);
if (!$result || pg_num_rows($result) === 0) {
    http_response_code(404);
    response("error", "User tidak ditemukan");
    return;
}
$row = pg_fetch_assoc($result);
response("success", "Detail user", [
    "id_user" => $row['id_user'],
    "nama" => $row['nama'],
    "email" => $row['email'],
    "role" => $row['role'],
]);
