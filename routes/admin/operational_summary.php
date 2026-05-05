<?php
$id_admin = $_GET['id_admin'] ?? '';
if (!$id_admin) { http_response_code(400); response("error", "id_admin wajib"); return; }
$cekAdmin = pg_fetch_assoc(pg_query_params($conn, "SELECT role FROM users WHERE id_user=$1", [$id_admin]));
if (!$cekAdmin || $cekAdmin['role'] !== 'admin') { http_response_code(403); response("error", "Akses ditolak"); return; }
$qJadwal = pg_query($conn, "
    SELECT status_jadwal, COUNT(*) as total 
    FROM jadwal 
    WHERE tanggal_berangkat = CURRENT_DATE
    GROUP BY status_jadwal
");
$status_jadwal = ['aktif' => 0, 'nonaktif' => 0, 'berjalan' => 0, 'tiba' => 0];
while ($row = pg_fetch_assoc($qJadwal)) {
    $s = strtolower($row['status_jadwal']);
    if (isset($status_jadwal[$s])) {
        $status_jadwal[$s] = (int)$row['total'];
    } else {
        $status_jadwal[$s] = (int)$row['total'];
    }
}
$qLive = pg_query($conn, "
    SELECT 
        SUM(CASE WHEN jam_berangkat > CURRENT_TIME THEN 1 ELSE 0 END) as menunggu,
        SUM(CASE WHEN jam_berangkat <= CURRENT_TIME AND jam_berangkat >= CURRENT_TIME - INTERVAL '12 hours' THEN 1 ELSE 0 END) as berjalan,
        SUM(CASE WHEN jam_berangkat < CURRENT_TIME - INTERVAL '12 hours' THEN 1 ELSE 0 END) as tiba
    FROM jadwal
    WHERE tanggal_berangkat = CURRENT_DATE AND status_jadwal = 'aktif'
");
$live = pg_fetch_assoc($qLive);
$rTiket = pg_fetch_assoc(pg_query($conn, "
    SELECT COUNT(*) AS total FROM tiket t
    JOIN pemesanan_pembayaran p ON t.pemesanan_id = p.pemesanan_id
    WHERE p.status_pembayaran = 'lunas'
      AND DATE(p.tanggal_bayar) = CURRENT_DATE
"));
response("success", "Operasional hari ini", [
    "armada" => [
        "menunggu_keberangkatan" => (int)$live['menunggu'],
        "sedang_berjalan"        => (int)$live['berjalan'],
        "telah_tiba"             => (int)$live['tiba'],
        "pemeliharaan"           => 0 
    ],
    "tiket_terjual_hari_ini" => (int)$rTiket['total']
]);
