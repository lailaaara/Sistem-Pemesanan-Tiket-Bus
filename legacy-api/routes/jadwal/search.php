<?php
$from = trim($_GET['from'] ?? '');
$to = trim($_GET['to'] ?? '');
$date = trim($_GET['date'] ?? '');
if (!$from || !$to || !$date) {
    http_response_code(400);
    response("error", "Parameter from, to, dan date wajib diisi");
    return;
}
$passenger_count = isset($_GET['passenger_count']) && is_numeric($_GET['passenger_count']) ? (int)$_GET['passenger_count'] : 1;
$time_filter     = $_GET['time_filter'] ?? '';
$price_range     = $_GET['price_range'] ?? '';
$facilities      = $_GET['facilities']  ?? '';
$bus_operator    = $_GET['bus_operator'] ?? '';
$kelas           = $_GET['kelas']       ?? '';
$sort_by         = $_GET['sort_by']     ?? 'jam_asc';
$where = "WHERE LOWER(r.kota_asal) LIKE LOWER($1)
          AND LOWER(r.kota_tujuan) LIKE LOWER($2)
          AND j.tanggal_berangkat = $3
          AND j.status_jadwal = 'aktif'
          AND j.kursi_tersedia >= $4";
$params = ['%' . $from . '%', '%' . $to . '%', $date, $passenger_count];
$param_count = 4;
if ($time_filter) {
    if ($time_filter === 'pagi')  $where .= " AND j.jam_berangkat >= '00:00:00' AND j.jam_berangkat < '12:00:00'";
    if ($time_filter === 'siang') $where .= " AND j.jam_berangkat >= '12:00:00' AND j.jam_berangkat < '18:00:00'";
    if ($time_filter === 'malam') $where .= " AND j.jam_berangkat >= '18:00:00' AND j.jam_berangkat <= '23:59:59'";
}
if ($price_range) {
    $prices = explode('-', $price_range);
    if (count($prices) == 2) {
        $min = (int)$prices[0];
        $max = (int)$prices[1];
        $where .= " AND j.harga >= $" . (++$param_count) . " AND j.harga <= $" . (++$param_count);
        $params[] = $min;
        $params[] = $max;
    }
}
if ($facilities) {
    $where .= " AND LOWER(b.fasilitas) LIKE LOWER($" . (++$param_count) . ")";
    $params[] = '%' . $facilities . '%';
}
if ($bus_operator) {
    $where .= " AND LOWER(b.nama_bus) LIKE LOWER($" . (++$param_count) . ")";
    $params[] = '%' . $bus_operator . '%';
}
if ($kelas) {
    $where .= " AND LOWER(b.kelas) = LOWER($" . (++$param_count) . ")";
    $params[] = $kelas;
}
$order_sql = "ORDER BY j.jam_berangkat ASC";
if ($sort_by === 'jam_desc')   $order_sql = "ORDER BY j.jam_berangkat DESC";
if ($sort_by === 'harga_asc')  $order_sql = "ORDER BY j.harga ASC";
if ($sort_by === 'harga_desc') $order_sql = "ORDER BY j.harga DESC";
$result = pg_query_params($conn, "
SELECT
    j.id_jadwal, j.tanggal_berangkat, j.jam_berangkat, j.harga, j.kursi_tersedia,
    b.bus_id, b.nama_bus, b.kapasitas, b.kelas, b.fasilitas,
    r.rute_id, r.kota_asal, r.kota_tujuan
FROM jadwal j
JOIN bus  b ON j.bus_id  = b.bus_id
JOIN rute r ON j.rute_id = r.rute_id
$where
$order_sql
", $params);
if (!$result) {
    http_response_code(500);
    response("error", "Gagal mencari jadwal");
    return;
}
$data = [];
while ($row = pg_fetch_assoc($result)) {
    $data[] = [
        "id_jadwal"      => $row['id_jadwal'],
        "bus_id"         => $row['bus_id'],
        "bus"            => $row['nama_bus'],
        "kelas"          => $row['kelas'],
        "fasilitas"      => $row['fasilitas'],
        "kapasitas"      => $row['kapasitas'],
        "rute_id"        => $row['rute_id'],
        "asal"           => $row['kota_asal'],
        "tujuan"         => $row['kota_tujuan'],
        "tanggal"        => $row['tanggal_berangkat'],
        "jam"            => $row['jam_berangkat'],
        "harga"          => (int)$row['harga'],
        "kursi_tersedia" => (int)$row['kursi_tersedia'],
    ];
}
response("success", count($data) . " jadwal ditemukan", $data);
