<?php
$user_id       = $body['user_id']        ?? '';
$jadwal_id     = $body['jadwal_id']      ?? '';
$kursi         = $body['kursi']          ?? []; 
$metode_bayar  = $body['metode_bayar']   ?? 'transfer';
$kode_promo    = strtoupper(trim($body['kode_promo'] ?? ''));
if (!$user_id || !$jadwal_id || empty($kursi)) {
    http_response_code(400);
    response("error", "user_id, jadwal_id, dan kursi wajib diisi");
    return;
}
$qJadwal = pg_query_params($conn,
    "SELECT id_jadwal, harga, kursi_tersedia, status_jadwal FROM jadwal WHERE id_jadwal = $1",
    [$jadwal_id]);
$jadwal = pg_fetch_assoc($qJadwal);
if (!$jadwal) {
    http_response_code(404);
    response("error", "Jadwal tidak ditemukan");
    return;
}
if ($jadwal['status_jadwal'] !== 'aktif') {
    http_response_code(400);
    response("error", "Jadwal tidak aktif");
    return;
}
if ($jadwal['kursi_tersedia'] < count($kursi)) {
    http_response_code(409);
    response("error", "Kursi tidak mencukupi");
    return;
}
$id_kursi_list = array_column($kursi, 'id_kursi');
if (empty($id_kursi_list)) {
    http_response_code(400);
    response("error", "Data kursi tidak valid");
    return;
}
$cekPesan = pg_query_params($conn, "
    SELECT t.id_kursi FROM tiket t
    JOIN pemesanan_pembayaran p ON t.pemesanan_id = p.pemesanan_id
    WHERE p.jadwal_id = $1
      AND t.id_kursi = ANY($2)
      AND p.status_pembayaran != 'dibatalkan'
", [$jadwal_id, '{' . implode(',', $id_kursi_list) . '}']);
if (pg_num_rows($cekPesan) > 0) {
    http_response_code(409);
    response("error", "Salah satu kursi sudah dipesan");
    return;
}
$total = $jadwal['harga'] * count($kursi);
$diskon = 0;
if ($kode_promo) {
    $qPromo = pg_query_params($conn, "
        SELECT * FROM promo
        WHERE kode_promo = $1 AND status_promo = 'aktif'
          AND (berlaku_sampai IS NULL OR berlaku_sampai >= CURRENT_DATE)
          AND terpakai < kuota
    ", [$kode_promo]);
    if (pg_num_rows($qPromo) > 0) {
        $promo = pg_fetch_assoc($qPromo);
        if ($total >= $promo['min_pembelian']) {
            if ($promo['tipe_diskon'] === 'persen') {
                $diskon = ($total * $promo['nilai_diskon']) / 100;
                if ($promo['maks_diskon'] > 0) $diskon = min($diskon, $promo['maks_diskon']);
            } else {
                $diskon = $promo['nilai_diskon'];
            }
            pg_query_params($conn,
                "UPDATE promo SET terpakai = terpakai + 1 WHERE kode_promo = $1",
                [$kode_promo]);
        }
    }
}
$total_bayar = max(0, $total - (int)$diskon);
$kode        = 'BOOK' . strtoupper(substr(uniqid(), -6)) . rand(10, 99);
$resPesan = pg_query_params($conn, "
    INSERT INTO pemesanan_pembayaran
        (user_id, jadwal_id, kode_booking, jumlah_kursi, total_harga,
         metode_pembayaran, tanggal_bayar, status_pembayaran)
    VALUES ($1, $2, $3, $4, $5, $6, NOW(), 'lunas')
    RETURNING pemesanan_id, kode_booking, total_harga, status_pembayaran
", [$user_id, $jadwal_id, $kode, count($kursi), $total_bayar, $metode_bayar]);
if (!$resPesan) {
    http_response_code(500);
    response("error", "Gagal membuat pemesanan");
    return;
}
$pesan        = pg_fetch_assoc($resPesan);
$pemesanan_id = $pesan['pemesanan_id'];
$tiket_list   = [];
foreach ($kursi as $k) {
    $id_k          = $k['id_kursi']       ?? 0;
    $nama_penumpang= $k['nama_penumpang'] ?? '';
    $no_hp         = $k['no_hp']          ?? '';
    $no_identitas  = $k['no_identitas']   ?? '';
    $kode_tiket    = 'TKT' . strtoupper(substr(uniqid(), -6)) . rand(10, 99);
    $resTiket = pg_query_params($conn, "
        INSERT INTO tiket (pemesanan_id, id_kursi, kode_tiket, status_tiket,
                           nama_penumpang, no_hp, no_identitas)
        VALUES ($1, $2, $3, 'aktif', $4, $5, $6)
        RETURNING tiket_id, kode_tiket
    ", [$pemesanan_id, $id_k, $kode_tiket, $nama_penumpang, $no_hp, $no_identitas]);
    $t = pg_fetch_assoc($resTiket);
    $tiket_list[] = ["tiket_id" => $t['tiket_id'], "kode_tiket" => $t['kode_tiket'],
                     "id_kursi" => $id_k, "nama_penumpang" => $nama_penumpang];
}
pg_query_params($conn,
    "UPDATE jadwal SET kursi_tersedia = kursi_tersedia - $1 WHERE id_jadwal = $2",
    [count($kursi), $jadwal_id]);
pg_query_params($conn,
    "DELETE FROM seat_hold WHERE id_jadwal = $1 AND user_id = $2",
    [$jadwal_id, $user_id]);
http_response_code(201);
response("success", "Checkout berhasil, pembayaran dikonfirmasi", [
    "pemesanan_id"      => $pesan['pemesanan_id'],
    "kode_booking"      => $pesan['kode_booking'],
    "total_harga"       => (int)$pesan['total_harga'],
    "diskon"            => (int)$diskon,
    "metode_pembayaran" => $metode_bayar,
    "status_pembayaran" => $pesan['status_pembayaran'],
    "tiket"             => $tiket_list,
]);
