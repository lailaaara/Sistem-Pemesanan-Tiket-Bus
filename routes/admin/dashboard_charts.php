<?php
$id_admin = requireAdmin($conn);
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
