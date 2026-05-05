<?php
$user_id    = $body['user_id']    ?? $_POST['user_id']    ?? '';
$kode_promo = strtoupper(trim($body['kode_promo'] ?? $_POST['kode_promo'] ?? ''));
$total      = $body['total_harga'] ?? $_POST['total_harga'] ?? 0; 
if (!$user_id || !$kode_promo) {
    http_response_code(400);
    response("error", "user_id dan kode_promo wajib diisi");
    return;
}
$result = pg_query_params($conn, "
    SELECT * FROM promo
    WHERE kode_promo = $1
      AND status_promo = 'aktif'
      AND (berlaku_mulai IS NULL OR berlaku_mulai <= CURRENT_DATE)
      AND (berlaku_sampai IS NULL OR berlaku_sampai >= CURRENT_DATE)
", [$kode_promo]);
if (!$result || pg_num_rows($result) === 0) {
    http_response_code(404);
    response("error", "Kode promo tidak valid atau sudah kadaluarsa");
    return;
}
$promo = pg_fetch_assoc($result);
if ($promo['terpakai'] >= $promo['kuota']) {
    http_response_code(409);
    response("error", "Kuota promo sudah habis");
    return;
}
if ($total < $promo['min_pembelian']) {
    http_response_code(400);
    response("error", "Minimum pembelian untuk promo ini adalah Rp " . number_format($promo['min_pembelian'], 0, ',', '.'));
    return;
}
$diskon = 0;
if ($promo['tipe_diskon'] === 'persen') {
    $diskon = ($total * $promo['nilai_diskon']) / 100;
    if ($promo['maks_diskon'] > 0 && $diskon > $promo['maks_diskon']) {
        $diskon = $promo['maks_diskon'];
    }
} else {
    $diskon = $promo['nilai_diskon'];
}
$diskon = (int)$diskon;
$total_setelah_diskon = max(0, (int)$total - $diskon);
response("success", "Kode promo berhasil diklaim", [
    "kode_promo"          => $promo['kode_promo'],
    "deskripsi"           => $promo['deskripsi'],
    "tipe_diskon"         => $promo['tipe_diskon'],
    "nilai_diskon"        => $promo['nilai_diskon'],
    "diskon_nominal"      => $diskon,
    "total_sebelum"       => (int)$total,
    "total_setelah"       => $total_setelah_diskon,
]);
