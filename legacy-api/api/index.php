<?php
require_once __DIR__ . "/../config/database.php";
require_once __DIR__ . "/../utils/response.php";
require_once __DIR__ . "/../utils/auth.php";
$method = $_SERVER['REQUEST_METHOD'];
$basePath = '/api/';
$requestUri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$pos = strpos($requestUri, $basePath);
$path = ($pos !== false) ? substr($requestUri, $pos + strlen($basePath)) : '';
$path = trim($path, '/');
$segments = $path !== '' ? explode('/', $path) : [];
if (isset($segments[0]) && $segments[0] === 'v1') {
    array_shift($segments);
}
$resource = $segments[0] ?? '';
$param1 = $segments[1] ?? '';
$param2 = $segments[2] ?? '';
$param3 = $segments[3] ?? '';
$body = json_decode(file_get_contents('php://input'), true) ?? [];
function dispatch($file)
{
    global $conn, $method, $resource, $param1, $param2, $param3, $body;
    $fullPath = __DIR__ . "/../routes/" . $file;
    if (file_exists($fullPath)) {
        require $fullPath;
    } else {
        response("error", "Route handler tidak ditemukan: $file");
    }
}
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization, X-User-Id, X-Admin-Id");
if ($method === 'OPTIONS') {
    http_response_code(200);
    exit;
}
switch ($resource) {
    case 'auth':
        switch ($param1) {
            case 'register':
                dispatch('auth/register.php');
                break;
            case 'login':
                dispatch('auth/login.php');
                break;
            case 'logout':
                dispatch('auth/logout.php');
                break;
            default:
                response("error", "Auth endpoint tidak ditemukan");
                break;
        }
        break;
    case 'locations':
        if ($method === 'GET')
            dispatch('locations/get.php');
        else
            response("error", "Method tidak diizinkan");
        break;
    case 'payments':
        if ($param1 === 'methods' && $method === 'GET')
            dispatch('pembayaran/methods.php');
        else
            response("error", "Endpoint tidak ditemukan");
        break;
    case 'promos':
        if ($param1 === 'claim' && $method === 'POST')
            dispatch('promo/claim.php');
        else
            response("error", "Endpoint tidak ditemukan");
        break;
    case 'admin':
        if ($param1 === 'dashboard') {
            if ($param2 === 'metrics')
                dispatch('admin/dashboard_metrics.php');
            elseif ($param2 === 'charts' && $param3 === 'sales')
                dispatch('admin/dashboard_charts.php');
            else
                response("error", "Endpoint dashboard tidak ditemukan");
        } elseif ($param1 === 'transactions') {
            if ($param2 === 'recent')
                dispatch('admin/transactions_recent.php');
            elseif ($param2 === 'stats')
                dispatch('admin/transactions_stats.php');
            elseif (!$param2)
                dispatch('pemesanan/list.php');
            else
                response("error", "Endpoint transactions tidak ditemukan");
        } elseif ($param1 === 'reports') {
            if ($param2 === 'summary')
                dispatch('admin/reports_summary.php');
            elseif ($param2 === 'daily')
                dispatch('admin/reports_daily.php');
            else
                response("error", "Endpoint reports tidak ditemukan");
        } elseif ($param1 === 'operational') {
            if ($param2 === 'summary')
                dispatch('admin/operational_summary.php');
            else
                response("error", "Endpoint operational tidak ditemukan");
        } elseif ($param1 === 'categories') {
            dispatch('admin/categories.php');
        } elseif ($param1 === 'fleets') {
            dispatch('bus/list.php');
        } elseif ($param1 === 'schedules') {
            if ($method === 'GET')
                dispatch('jadwal/get.php');
            elseif ($method === 'POST')
                dispatch('jadwal/create.php');
            else
                response("error", "Method tidak diizinkan");
        } else {
            response("error", "Endpoint admin tidak ditemukan");
        }
        break;
    case 'users':
        $id = is_numeric($param1) ? (int) $param1 : null;
        if ($param1 === 'profile') {
            if ($method === 'GET')
                dispatch('user/profile.php');
            else
                response("error", "Method tidak diizinkan");
        } elseif (!$param1) {
            if ($method === 'GET')
                dispatch('user/list.php');
            else
                response("error", "Method tidak diizinkan");
        } else {
            switch ($method) {
                case 'GET':
                    dispatch('user/detail.php');
                    break;
                case 'PUT':
                    dispatch('user/update.php');
                    break;
                case 'DELETE':
                    dispatch('user/delete.php');
                    break;
                default:
                    response("error", "Method tidak diizinkan");
                    break;
            }
        }
        break;
    case 'buses':
        $id = is_numeric($param1) ? (int) $param1 : null;
        if (!$param1) {
            switch ($method) {
                case 'GET':
                    dispatch('bus/list.php');
                    break;
                case 'POST':
                    dispatch('bus/create.php');
                    break;
                default:
                    response("error", "Method tidak diizinkan");
                    break;
            }
        } elseif ($param2 === 'kursi') {
            dispatch('bus/kursi.php');
        } else {
            switch ($method) {
                case 'GET':
                    dispatch('bus/detail.php');
                    break;
                case 'PUT':
                    dispatch('bus/update.php');
                    break;
                case 'DELETE':
                    dispatch('bus/delete.php');
                    break;
                default:
                    response("error", "Method tidak diizinkan");
                    break;
            }
        }
        break;
    case 'kursi':
        $id = is_numeric($param1) ? (int) $param1 : null;
        if (!$param1) {
            switch ($method) {
                case 'GET':
                    dispatch('kursi/list.php');
                    break;
                case 'POST':
                    dispatch('kursi/create.php');
                    break;
                default:
                    response("error", "Method tidak diizinkan");
                    break;
            }
        } else {
            switch ($method) {
                case 'GET':
                    dispatch('kursi/detail.php');
                    break;
                case 'PUT':
                    dispatch('kursi/update.php');
                    break;
                case 'DELETE':
                    dispatch('kursi/delete.php');
                    break;
                default:
                    response("error", "Method tidak diizinkan");
                    break;
            }
        }
        break;
    case 'rute':
    case 'routes':
        $id = is_numeric($param1) ? (int) $param1 : null;
        if ($param1 === 'popular') {
            dispatch('rute/popular.php');
        } elseif (!$param1) {
            switch ($method) {
                case 'GET':
                    dispatch('rute/list.php');
                    break;
                case 'POST':
                    dispatch('rute/create.php');
                    break;
                default:
                    response("error", "Method tidak diizinkan");
                    break;
            }
        } else {
            switch ($method) {
                case 'GET':
                    dispatch('rute/detail.php');
                    break;
                case 'PUT':
                    dispatch('rute/update.php');
                    break;
                case 'DELETE':
                    dispatch('rute/delete.php');
                    break;
                default:
                    response("error", "Method tidak diizinkan");
                    break;
            }
        }
        break;
    case 'jadwal':
    case 'schedules':
        if (!$param1) {
            switch ($method) {
                case 'GET':
                    dispatch('jadwal/get.php');
                    break;
                case 'POST':
                    dispatch('jadwal/create.php');
                    break;
                default:
                    response("error", "Method tidak diizinkan");
                    break;
            }
        } elseif ($param1 === 'search') {
            dispatch('jadwal/search.php');
        } elseif (is_numeric($param1) && ($param2 === 'kursi-tersedia' || $param2 === 'seats')) {
            dispatch('jadwal/kursi_tersedia.php');
        } elseif (is_numeric($param1)) {
            switch ($method) {
                case 'GET':
                    dispatch('jadwal/detail.php');
                    break;
                case 'PUT':
                    dispatch('jadwal/update.php');
                    break;
                case 'DELETE':
                    dispatch('jadwal/delete.php');
                    break;
                default:
                    response("error", "Method tidak diizinkan");
                    break;
            }
        } else {
            response("error", "Endpoint jadwal tidak ditemukan");
        }
        break;
    case 'pemesanan':
    case 'bookings':
        if (!$param1) {
            switch ($method) {
                case 'GET':
                    dispatch('pemesanan/list.php');
                    break;
                case 'POST':
                    dispatch('pemesanan/create.php');
                    break;
                default:
                    response("error", "Method tidak diizinkan");
                    break;
            }
        } elseif ($param1 === 'me' || $param1 === 'my-tickets') {
            dispatch('pemesanan/me.php');
        } elseif ($param1 === 'checkout') {
            dispatch('pemesanan/checkout.php');
        } elseif ($param1 === 'hold-seat') {
            dispatch('pemesanan/hold_seat.php');
        } elseif (is_numeric($param1) && $param2 === 'bayar') {
            dispatch('pembayaran/bayar.php');
        } elseif (is_numeric($param1) && $param2 === 'summary') {
            dispatch('pemesanan/detail.php');
        } elseif (is_numeric($param1) && $param2 === 'download-pdf') {
            dispatch('pemesanan/download_pdf.php');
        } elseif (is_numeric($param1)) {
            switch ($method) {
                case 'GET':
                    dispatch('pemesanan/detail.php');
                    break;
                case 'DELETE':
                    dispatch('pemesanan/cancel.php');
                    break;
                default:
                    response("error", "Method tidak diizinkan");
                    break;
            }
        } else {
            response("error", "Endpoint pemesanan tidak ditemukan");
        }
        break;
    case 'tiket':
        if (!$param1) {
            dispatch('tiket/list.php');
        } elseif ($param1 === 'me') {
            dispatch('tiket/me.php');
        } elseif ($param1 === 'kode' && $param2) {
            dispatch('tiket/cek_kode.php');
        } elseif (is_numeric($param1) && $param2 === 'status') {
            dispatch('tiket/update_status.php');
        } elseif (is_numeric($param1)) {
            dispatch('tiket/detail.php');
        } else {
            response("error", "Endpoint tiket tidak ditemukan");
        }
        break;
    default:
        http_response_code(404);
        response("error", "Endpoint tidak ditemukan: /$resource");
        break;
}