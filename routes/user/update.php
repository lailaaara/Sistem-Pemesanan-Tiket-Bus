<?php
$id_user = is_numeric($param1) ? (int) $param1 : null;
if (!$id_user) {
    http_response_code(400);
    response("error", "ID user tidak valid");
    return;
}
$nama = trim($body['nama'] ?? $_POST['nama'] ?? '');
$email = trim($body['email'] ?? $_POST['email'] ?? '');
$password_baru = $body['password'] ?? $_POST['password'] ?? '';
$role = $body['role'] ?? $_POST['role'] ?? '';
$id_requester = $body['id_requester'] ?? $_POST['id_requester'] ?? ''; 
if (!$id_requester) {
    http_response_code(400);
    response("error", "id_requester wajib diisi");
    return;
}
$cekRequester = pg_query_params(
    $conn,
    "SELECT role FROM users WHERE id_user = $1",
    [$id_requester]
);
$requester = pg_fetch_assoc($cekRequester);
if (!$requester) {
    http_response_code(403);
    response("error", "Requester tidak ditemukan");
    return;
}
$isAdmin = $requester['role'] === 'admin';
if (!$isAdmin && $id_requester != $id_user) {
    http_response_code(403);
    response("error", "Akses ditolak. Hanya bisa update profil sendiri.");
    return;
}
if ($role && !$isAdmin) {
    http_response_code(403);
    response("error", "Hanya admin yang bisa mengubah role");
    return;
}
$cekUser = pg_query_params(
    $conn,
    "SELECT id_user, nama, email, role FROM users WHERE id_user = $1",
    [$id_user]
);
if (!$cekUser || pg_num_rows($cekUser) === 0) {
    http_response_code(404);
    response("error", "User tidak ditemukan");
    return;
}
$existing = pg_fetch_assoc($cekUser);
$nama = $nama ?: $existing['nama'];
$email = $email ?: $existing['email'];
$role = $role ?: $existing['role'];
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    http_response_code(400);
    response("error", "Format email tidak valid");
    return;
}
if ($email !== $existing['email']) {
    $cekEmail = pg_query_params(
        $conn,
        "SELECT 1 FROM users WHERE email = $1 AND id_user != $2",
        [$email, $id_user]
    );
    if (pg_num_rows($cekEmail) > 0) {
        http_response_code(409);
        response("error", "Email sudah digunakan user lain");
        return;
    }
}
if ($password_baru) {
    $hash = password_hash($password_baru, PASSWORD_DEFAULT);
    $result = pg_query_params($conn, "
        UPDATE users SET nama = $1, email = $2, password = $3, role = $4
        WHERE id_user = $5
        RETURNING id_user, nama, email, role
    ", [$nama, $email, $hash, $role, $id_user]);
} else {
    $result = pg_query_params($conn, "
        UPDATE users SET nama = $1, email = $2, role = $3
        WHERE id_user = $4
        RETURNING id_user, nama, email, role
    ", [$nama, $email, $role, $id_user]);
}
if (!$result) {
    http_response_code(500);
    response("error", "Gagal mengupdate user");
    return;
}
response("success", "Profil berhasil diupdate", pg_fetch_assoc($result));
