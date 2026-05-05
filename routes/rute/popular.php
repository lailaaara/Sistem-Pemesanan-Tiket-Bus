<?php
$limit = isset($_GET['limit']) && is_numeric($_GET['limit']) ? (int)$_GET['limit'] : 6;
$result = pg_query_params($conn, "
SELECT
    r.rute_id,
    r.kota_asal,
    r.kota_tujuan,
    r.jarak_km,
    r.gambar,
    COUNT(p.pemesanan_id) AS jumlah_pemesanan,
    MIN(j.harga)          AS harga_mulai
FROM rute r
LEFT JOIN jadwal              j ON j.rute_id   = r.rute_id
LEFT JOIN pemesanan_pembayaran p ON p.jadwal_id = j.id_jadwal
    AND p.status_pembayaran = 'lunas'
GROUP BY r.rute_id, r.kota_asal, r.kota_tujuan, r.jarak_km, r.gambar
ORDER BY jumlah_pemesanan DESC
LIMIT $1
", [$limit]);
if (!$result) {
    http_response_code(500);
    response("error", "Gagal mengambil rute populer");
    return;
}
$data = [];
while ($row = pg_fetch_assoc($result)) {
    $data[] = [
        "rute_id"           => $row['rute_id'],
        "kota_asal"         => $row['kota_asal'],
        "kota_tujuan"       => $row['kota_tujuan'],
        "jarak_km"          => $row['jarak_km'],
        "gambar"            => $row['gambar'],
        "jumlah_pemesanan"  => (int)$row['jumlah_pemesanan'],
        "harga_mulai"       => $row['harga_mulai'] ? (int)$row['harga_mulai'] : null,
    ];
}
response("success", "Rute populer", $data);
