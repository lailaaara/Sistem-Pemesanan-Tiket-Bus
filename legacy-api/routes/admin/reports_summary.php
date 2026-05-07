<?php
$id_admin = $_GET['id_admin'] ?? '';
if (!$id_admin) { http_response_code(400); response("error", "id_admin wajib"); return; }
$cekAdmin = pg_fetch_assoc(pg_query_params($conn, "SELECT role FROM users WHERE id_user=$1", [$id_admin]));
if (!$cekAdmin || $cekAdmin['role'] !== 'admin') { http_response_code(403); response("error", "Akses ditolak"); return; }
function getStatsBulan($conn, $interval_month = 0) {
    $where = "p.status_pembayaran = 'lunas'";
    if ($interval_month === 0) {
        $where .= " AND DATE_TRUNC('month', p.tanggal_bayar) = DATE_TRUNC('month', NOW())";
    } else {
        $where .= " AND DATE_TRUNC('month', p.tanggal_bayar) = DATE_TRUNC('month', NOW() - INTERVAL '$interval_month month')";
    }
    $q = pg_query($conn, "
        SELECT 
            COALESCE(SUM(p.total_harga), 0) AS total_pendapatan,
            COUNT(p.pemesanan_id) AS total_transaksi,
            COALESCE(SUM(p.jumlah_kursi), 0) AS total_tiket
        FROM pemesanan_pembayaran p
        WHERE $where
    ");
    return pg_fetch_assoc($q);
}
$bulanIni = getStatsBulan($conn, 0);
$bulanLalu = getStatsBulan($conn, 1);
function hitungPersentase($ini, $lalu) {
    if ($lalu == 0) return $ini > 0 ? 100 : 0;
    return round((($ini - $lalu) / $lalu) * 100, 1);
}
$persen_pendapatan = hitungPersentase($bulanIni['total_pendapatan'], $bulanLalu['total_pendapatan']);
$persen_transaksi  = hitungPersentase($bulanIni['total_transaksi'], $bulanLalu['total_transaksi']);
$persen_tiket      = hitungPersentase($bulanIni['total_tiket'], $bulanLalu['total_tiket']);
response("success", "Laporan ringkasan", [
    "bulan_ini" => [
        "total_pendapatan" => (int)$bulanIni['total_pendapatan'],
        "total_transaksi"  => (int)$bulanIni['total_transaksi'],
        "total_tiket"      => (int)$bulanIni['total_tiket']
    ],
    "bulan_lalu" => [
        "total_pendapatan" => (int)$bulanLalu['total_pendapatan'],
        "total_transaksi"  => (int)$bulanLalu['total_transaksi'],
        "total_tiket"      => (int)$bulanLalu['total_tiket']
    ],
    "pertumbuhan" => [
        "pendapatan" => $persen_pendapatan,
        "transaksi"  => $persen_transaksi,
        "tiket"      => $persen_tiket
    ]
]);
