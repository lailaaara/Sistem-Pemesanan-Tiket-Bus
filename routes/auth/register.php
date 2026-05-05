<?php
$nama = $_POST['nama'] ?? '';
$email = $_POST['email'] ?? '';
$password = $_POST['password'] ?? '';
if (!$nama || !$email || !$password) {
    response("error", "Data tidak lengkap");
    return;
}
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    response("error", "Format email tidak valid");
    return;
}
$check = pg_query_params(
    $conn,
    "SELECT 1 FROM users WHERE email = $1",
    [$email]
);
if (pg_num_rows($check) > 0) {
    response("error", "Email sudah terdaftar");
    return;
}
$hash = password_hash($password, PASSWORD_DEFAULT);
$result = pg_query_params($conn, "
    INSERT INTO users (nama, email, password, role)
    VALUES ($1, $2, $3, 'user')
    RETURNING id_user, nama, email, role
", [$nama, $email, $hash]);
if ($result) {
    $user = pg_fetch_assoc($result);
    response("success", "Register berhasil", $user);
} else {
    response("error", "Register gagal");
}