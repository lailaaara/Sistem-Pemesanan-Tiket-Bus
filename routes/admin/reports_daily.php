<?php
$id_admin = $_GET['id_admin'] ?? '';
if (!$id_admin) { http_response_code(400); response("error", "id_admin wajib"); return; }
$cekAdmin = pg_fetch_assoc(pg_query_params($conn, "SELECT role FROM users WHERE id_user=$1", [$id_admin]));
if (!$cekAdmin || $cekAdmin['role'] !== 'admin') { http_response_code(403); response("error", "Akses ditolak"); return; }
$start_date = $_GET['start_date'] ?? date('Y-m-01'); 
$end_date   = $_GET['end_date']   ?? date('Y-m-t');  
$result = pg_query_params($conn, "
SELECT 
    DATE(p.tanggal_bayar) AS tanggal,
    COUNT(p.pemesanan_id) AS jumlah_transaksi,
    COALESCE(SUM(p.jumlah_kursi), 0) AS jumlah_tiket,
    COALESCE(SUM(p.total_harga), 0) AS total_pendapatan
FROM pemesanan_pembayaran p
WHERE p.status_pembayaran = 'lunas'
  AND DATE(p.tanggal_bayar) >= $1
  AND DATE(p.tanggal_bayar) <= $2
GROUP BY DATE(p.tanggal_bayar)
ORDER BY DATE(p.tanggal_bayar) DESC
", [$start_date, $end_date]);
$data = [];
while ($row = pg_fetch_assoc($result)) {
    $data[] = [
        "tanggal"          => $row['tanggal'],
        "jumlah_transaksi" => (int)$row['jumlah_transaksi'],
        "jumlah_tiket"     => (int)$row['jumlah_tiket'],
        "total_pendapatan" => (int)$row['total_pendapatan'],
    ];
}
response("success", "Laporan harian", $data);
