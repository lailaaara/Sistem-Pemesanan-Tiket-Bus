<?php
$id_admin = $_GET['id_admin'] ?? '';
if (!$id_admin) { http_response_code(400); response("error", "id_admin wajib"); return; }
$cekAdmin = pg_fetch_assoc(pg_query_params($conn, "SELECT role FROM users WHERE id_user=$1", [$id_admin]));
if (!$cekAdmin || $cekAdmin['role'] !== 'admin') { http_response_code(403); response("error", "Akses ditolak"); return; }
$limit = isset($_GET['limit']) && is_numeric($_GET['limit']) ? (int)$_GET['limit'] : 5;
$result = pg_query_params($conn, "
SELECT
    p.pemesanan_id, p.kode_booking, p.total_harga,
    p.status_pembayaran, p.tanggal_pemesanan, p.metode_pembayaran,
    u.nama AS nama_user,
    r.kota_asal, r.kota_tujuan
FROM pemesanan_pembayaran p
JOIN users  u ON p.user_id   = u.id_user
JOIN jadwal j ON p.jadwal_id = j.id_jadwal
JOIN rute   r ON j.rute_id   = r.rute_id
ORDER BY p.pemesanan_id DESC
LIMIT $1
", [$limit]);
$data = [];
while ($row = pg_fetch_assoc($result)) {
    $data[] = [
        "pemesanan_id"      => $row['pemesanan_id'],
        "kode_booking"      => $row['kode_booking'],
        "nama_user"         => $row['nama_user'],
        "rute"              => $row['kota_asal'] . ' → ' . $row['kota_tujuan'],
        "total_harga"       => (int)$row['total_harga'],
        "status_pembayaran" => $row['status_pembayaran'],
        "metode_pembayaran" => $row['metode_pembayaran'],
        "tanggal"           => $row['tanggal_pemesanan'],
    ];
}
response("success", "Transaksi terbaru", $data);
