<?php
$id_admin = $_GET['id_admin'] ?? '';
if (!$id_admin) { http_response_code(400); response("error", "id_admin wajib"); return; }
$cekAdmin = pg_fetch_assoc(pg_query_params($conn, "SELECT role FROM users WHERE id_user=$1", [$id_admin]));
if (!$cekAdmin || $cekAdmin['role'] !== 'admin') { http_response_code(403); response("error", "Akses ditolak"); return; }
$rPendapatan = pg_fetch_assoc(pg_query($conn, "
    SELECT COALESCE(SUM(total_harga),0) AS total
    FROM pemesanan_pembayaran
    WHERE status_pembayaran='lunas'
      AND DATE_TRUNC('month', tanggal_bayar) = DATE_TRUNC('month', NOW())
"));
$rTiket = pg_fetch_assoc(pg_query($conn, "
    SELECT COUNT(*) AS total FROM tiket t
    JOIN pemesanan_pembayaran p ON t.pemesanan_id = p.pemesanan_id
    WHERE p.status_pembayaran = 'lunas'
      AND DATE_TRUNC('month', p.tanggal_bayar) = DATE_TRUNC('month', NOW())
"));
$rBus = pg_fetch_assoc(pg_query($conn,
    "SELECT COUNT(*) AS total FROM bus WHERE status_bus = 'aktif'"));
$rUser = pg_fetch_assoc(pg_query($conn,
    "SELECT COUNT(*) AS total FROM users WHERE role = 'user'"));
response("success", "Metrik dashboard", [
    "pendapatan_bulan_ini" => (int)$rPendapatan['total'],
    "tiket_terjual"        => (int)$rTiket['total'],
    "armada_aktif"         => (int)$rBus['total'],
    "total_pengguna"       => (int)$rUser['total'],
    "tingkat_kepuasan"     => 95, 
]);
