<?php
$email = $body['email'] ?? $_POST['email'] ?? '';
$password = $body['password'] ?? $_POST['password'] ?? '';
if (!$email || !$password) {
    response("error", "Email dan password wajib diisi");
    return;
}
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    response("error", "Format email tidak valid");
    return;
}
$result = pg_query_params($conn, "
    SELECT id_user, nama, email, password, role
    FROM users
    WHERE email = $1
", [$email]);
if (!$result) {
    response("error", "Terjadi kesalahan server");
    return;
}
$user = pg_fetch_assoc($result);
if ($user && password_verify($password, $user['password'])) {
    unset($user['password']);
    response("success", "Login berhasil", $user);
} else {
    response("error", "Email atau password salah");
}