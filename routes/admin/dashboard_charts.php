<?php
$id_admin = $_GET['id_admin'] ?? '';
if (!$id_admin) { http_response_code(400); response("error", "id_admin wajib"); return; }
$cekAdmin = pg_fetch_assoc(pg_query_params($conn, "SELECT role FROM users WHERE id_user=$1", [$id_admin]));
if (!$cekAdmin || $cekAdmin['role'] !== 'admin') { http_response_code(403); response("error", "Akses ditolak"); return; }
$result = pg_query($conn, "
SELECT
    TO_CHAR(DATE(p.tanggal_bayar), 'YYYY-MM-DD') AS tanggal,
    TO_CHAR(DATE(p.tanggal_bayar), 'DD Mon')      AS label,
    COUNT(p.pemesanan_id)    AS total_transaksi,
    COALESCE(SUM(p.total_harga), 0) AS total_pendapatan,
    SUM(CASE WHEN b.kelas = 'Eksekutif' THEN p.jumlah_kursi ELSE 0 END) AS tiket_eksekutif,
    SUM(CASE WHEN b.kelas != 'Eksekutif' OR b.kelas IS NULL THEN p.jumlah_kursi ELSE 0 END) AS tiket_ekonomi
FROM pemesanan_pembayaran p
JOIN jadwal j ON p.jadwal_id = j.id_jadwal
JOIN bus    b ON j.bus_id    = b.bus_id
WHERE p.status_pembayaran = 'lunas'
  AND p.tanggal_bayar >= NOW() - INTERVAL '7 days'
GROUP BY DATE(p.tanggal_bayar)
ORDER BY DATE(p.tanggal_bayar) ASC
");
$data = [];
while ($row = pg_fetch_assoc($result)) {
    $data[] = [
        "tanggal"          => $row['tanggal'],
        "label"            => $row['label'],
        "total_transaksi"  => (int)$row['total_transaksi'],
        "total_pendapatan" => (int)$row['total_pendapatan'],
        "tiket_eksekutif"  => (int)$row['tiket_eksekutif'],
        "tiket_ekonomi"    => (int)$row['tiket_ekonomi'],
    ];
}
response("success", "Data chart 7 hari terakhir", $data);
