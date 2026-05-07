<?php
$pemesanan_id = is_numeric($param1) ? (int)$param1 : null;
if (!$pemesanan_id) {
    http_response_code(400);
    response("error", "ID pemesanan tidak valid");
    return;
}
$resPesan = pg_query_params($conn, "
SELECT p.pemesanan_id, p.kode_booking, p.tanggal_pemesanan, p.jumlah_kursi,
       p.total_harga, p.status_pembayaran, p.metode_pembayaran, p.tanggal_bayar,
       u.nama AS nama_user, u.email,
       j.tanggal_berangkat, j.jam_berangkat, j.harga AS harga_per_kursi,
       b.nama_bus, b.no_polisi, b.kelas, b.fasilitas,
       r.kota_asal, r.kota_tujuan, r.jarak_km
FROM pemesanan_pembayaran p
JOIN users  u ON p.user_id   = u.id_user
JOIN jadwal j ON p.jadwal_id = j.id_jadwal
JOIN bus    b ON j.bus_id    = b.bus_id
JOIN rute   r ON j.rute_id   = r.rute_id
WHERE p.pemesanan_id = $1
", [$pemesanan_id]);
if (!$resPesan || pg_num_rows($resPesan) === 0) {
    http_response_code(404);
    response("error", "Pemesanan tidak ditemukan");
    return;
}
$p = pg_fetch_assoc($resPesan);
$resTiket = pg_query_params($conn, "
SELECT t.tiket_id, t.kode_tiket, t.status_tiket, t.nama_penumpang, t.no_hp,
       k.no_kursi
FROM tiket t
JOIN kursi k ON t.id_kursi = k.id_kursi
WHERE t.pemesanan_id = $1
ORDER BY k.no_kursi ASC
", [$pemesanan_id]);
$tikets = [];
while ($row = pg_fetch_assoc($resTiket)) {
    $tikets[] = $row;
}
header('Content-Type: text/html; charset=utf-8');
echo '<!DOCTYPE html><html><head><meta charset="utf-8">
<title>E-Tiket - ' . htmlspecialchars($p['kode_booking']) . '</title>
<style>
  body { font-family: Arial, sans-serif; margin: 0; padding: 20px; background: #f5f5f5; color: #333; }
  .ticket { background: white; max-width: 700px; margin: auto; border-radius: 12px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.15); overflow: hidden; }
  .header { background: linear-gradient(135deg, #1a73e8, #0d47a1); color: white;
            padding: 24px 30px; }
  .header h1 { margin: 0; font-size: 22px; }
  .header p  { margin: 4px 0 0; opacity: 0.85; font-size: 13px; }
  .kode { font-size: 28px; font-weight: bold; letter-spacing: 4px; margin-top: 10px; }
  .body  { padding: 24px 30px; }
  .row   { display: flex; justify-content: space-between; margin-bottom: 12px;
           border-bottom: 1px dashed #eee; padding-bottom: 12px; }
  .label { color: #888; font-size: 12px; margin-bottom: 2px; }
  .value { font-weight: bold; font-size: 15px; }
  .route { text-align: center; padding: 20px 0; font-size: 22px; font-weight: bold; }
  .arrow { color: #1a73e8; margin: 0 12px; }
  .tiket-item { background: #f8f9ff; border-left: 4px solid #1a73e8;
                padding: 12px 16px; margin: 8px 0; border-radius: 4px; }
  .footer { background: #f5f5f5; padding: 16px 30px; text-align: center;
            font-size: 12px; color: #888; }
  .badge { display: inline-block; background: #e8f5e9; color: #2e7d32;
           padding: 4px 12px; border-radius: 20px; font-size: 12px; font-weight: bold; }
</style></head><body>
<div class="ticket">
  <div class="header">
    <h1>🚌 E-Tiket Bus</h1>
    <p>Sistem Pemesanan Tiket Bus</p>
    <div class="kode">' . htmlspecialchars($p['kode_booking']) . '</div>
  </div>
  <div class="body">
    <div class="route">
      ' . htmlspecialchars($p['kota_asal']) . '
      <span class="arrow">→</span>
      ' . htmlspecialchars($p['kota_tujuan']) . '
    </div>
    <div class="row">
      <div><div class="label">Bus</div><div class="value">' . htmlspecialchars($p['nama_bus']) . ' (' . htmlspecialchars($p['kelas']) . ')</div></div>
      <div><div class="label">No. Polisi</div><div class="value">' . htmlspecialchars($p['no_polisi']) . '</div></div>
    </div>
    <div class="row">
      <div><div class="label">Tanggal Berangkat</div><div class="value">' . $p['tanggal_berangkat'] . '</div></div>
      <div><div class="label">Jam</div><div class="value">' . $p['jam_berangkat'] . ' WIB</div></div>
    </div>
    <div class="row">
      <div><div class="label">Penumpang</div><div class="value">' . htmlspecialchars($p['nama_user']) . '</div></div>
      <div><div class="label">Jumlah Kursi</div><div class="value">' . $p['jumlah_kursi'] . ' kursi</div></div>
    </div>
    <div class="row">
      <div><div class="label">Total Harga</div><div class="value">Rp ' . number_format($p['total_harga'], 0, ',', '.') . '</div></div>
      <div><div class="label">Status</div><div class="value"><span class="badge">✅ ' . strtoupper($p['status_pembayaran']) . '</span></div></div>
    </div>
    <h3>Detail Kursi</h3>';
foreach ($tikets as $t) {
    echo '<div class="tiket-item">
      <strong>' . htmlspecialchars($t['kode_tiket']) . '</strong> — Kursi ' . htmlspecialchars($t['no_kursi']) . '<br>
      <small>Penumpang: ' . (htmlspecialchars($t['nama_penumpang']) ?: '-') . ' | HP: ' . (htmlspecialchars($t['no_hp']) ?: '-') . '</small>
    </div>';
}
echo '
    <p style="color:#888;font-size:12px;margin-top:20px;">
      Fasilitas: ' . htmlspecialchars($p['fasilitas']) . '
    </p>
  </div>
  <div class="footer">
    Dicetak pada: ' . date('d/m/Y H:i') . ' WIB &nbsp;|&nbsp; Jaga tiket ini baik-baik
  </div>
</div>
</body></html>';
exit;
