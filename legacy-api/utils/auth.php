<?php

function getCurrentUserId()
{
    global $body;
    return $_SERVER['HTTP_X_USER_ID'] ?? $_GET['user_id'] ?? $body['user_id'] ?? null;
}

function getCurrentAdminId()
{
    return $_SERVER['HTTP_X_ADMIN_ID'] ?? $_GET['id_admin'] ?? $_GET['admin_id'] ?? null;
}

function requireAuth()
{
    $userId = getCurrentUserId();
    if (!$userId) {
        http_response_code(401);
        response("error", "Autentikasi diperlukan. Kirim X-User-Id di header.");
        exit;
    }
    return $userId;
}

function requireAdmin($conn)
{
    $adminId = getCurrentAdminId();
    if (!$adminId) {
        http_response_code(401);
        response("error", "Akses admin diperlukan. Kirim X-Admin-Id di header.");
        exit;
    }

    $result = pg_query_params($conn, "SELECT role FROM users WHERE id_user = $1", [$adminId]);
    $user = pg_fetch_assoc($result);

    if (!$user || $user['role'] !== 'admin') {
        http_response_code(403);
        response("error", "Akses ditolak. Anda bukan admin.");
        exit;
    }

    return $adminId;
}
