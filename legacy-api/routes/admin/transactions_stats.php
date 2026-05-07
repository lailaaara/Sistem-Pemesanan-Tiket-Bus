<?php
$id_admin = $_GET['id_admin'] ?? '';
if (!$id_admin) { http_response_code(400); response("error", "id_admin wajib"); return; }
$cekAdmin = pg_fetch_assoc(pg_query_params($conn, "SELECT role FROM users WHERE id_user=$1", [$id_admin]));
if (!$cekAdmin || $cekAdmin['role'] !== 'admin') { http_response_code(403); response("error", "Akses ditolak"); return; }
$where = "WHERE p.status_pembayaran = 'lunas'";
$params = [];
if (!empty($_GET['bulan'])) {
    $where .= " AND TO_CHAR(p.tanggal_bayar, 'YYYY-MM') = $1";
    $params[] = $_GET['bulan'];
}
$r = pg_fetch_assoc(pg_query_params($conn, "
SELECT
    COALESCE(SUM(p.total_harga), 0) AS total_pendapatan,
    COUNT(p.pemesanan_id)           AS total_transaksi,
    COALESCE(SUM(p.jumlah_kursi), 0) AS total_tiket
FROM pemesanan_pembayaran p
$where
", $params));
response("success", "Statistik transaksi", [
    "total_pendapatan" => (int)$r['total_pendapatan'],
    "total_transaksi"  => (int)$r['total_transaksi'],
    "total_tiket"      => (int)$r['total_tiket'],
]);
