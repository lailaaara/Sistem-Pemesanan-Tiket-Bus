<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AdminController extends Controller
{
    /**
     * Dashboard Admin — Ringkasan statistik operasional.
     */
    public function dashboard()
    {
        $stats = [
            (object)['label' => 'PENDAPATAN BULAN INI', 'value' => 'Rp 1.42M', 'change' => '+12.5%', 'up' => true, 'icon' => 'ph-wallet', 'color' => 'green'],
            (object)['label' => 'TIKET TERJUAL',        'value' => '12,840',    'change' => '+8.2%',  'up' => true, 'icon' => 'ph-ticket', 'color' => 'blue'],
            (object)['label' => 'ARMADA AKTIF',         'value' => '156 / 160', 'change' => 'Tetap',  'up' => null, 'icon' => 'ph-bus',    'color' => 'orange'],
            (object)['label' => 'TINGKAT KEPUASAN',     'value' => '4.8 / 5.0', 'change' => '-0.3',   'up' => false,'icon' => 'ph-star',   'color' => 'yellow'],
        ];

        $chartData = [
            ['day' => 'SEN', 'eksekutif' => 40, 'ekonomi' => 25],
            ['day' => 'SEL', 'eksekutif' => 55, 'ekonomi' => 35],
            ['day' => 'RAB', 'eksekutif' => 80, 'ekonomi' => 50],
            ['day' => 'KAM', 'eksekutif' => 45, 'ekonomi' => 30],
            ['day' => 'JUM', 'eksekutif' => 60, 'ekonomi' => 40],
            ['day' => 'SAB', 'eksekutif' => 35, 'ekonomi' => 20],
            ['day' => 'MIN', 'eksekutif' => 50, 'ekonomi' => 28],
        ];

        $recentTransactions = [
            (object)['nama' => 'nama 1', 'rute' => 'Jakarta → Surabaya',    'kelas' => 'Eksekutif • Kursi 12A', 'tanggal' => '1 Mei 2026', 'jam' => '08:30 WIB', 'metode' => 'Transfer Bank',     'status' => 'Berhasil', 'total' => 450000],
            (object)['nama' => 'nama 2', 'rute' => 'Bandung → Jogja',       'kelas' => 'Ekonomi • Kursi 05C',   'tanggal' => '1 Mei 2026', 'jam' => '09:15 WIB', 'metode' => 'E-Wallet (Gopay)',   'status' => 'Pending',  'total' => 225000],
            (object)['nama' => 'nama 3', 'rute' => 'Solo → Jakarta',        'kelas' => 'Eksekutif • Kursi 02B', 'tanggal' => '1 Mei 2026', 'jam' => '10:00 WIB', 'metode' => 'Kartu Kredit',       'status' => 'Berhasil', 'total' => 380000],
            (object)['nama' => 'nama 4', 'rute' => 'Malang → Surabaya',     'kelas' => 'Patas • Kursi 18D',     'tanggal' => '1 Mei 2026', 'jam' => '10:45 WIB', 'metode' => 'Transfer Bank',      'status' => 'Berhasil', 'total' => 120000],
            (object)['nama' => 'nama 5', 'rute' => 'Semarang → Denpasar',   'kelas' => 'Eksekutif Plus • Kursi 01A', 'tanggal' => '1 Mei 2026', 'jam' => '11:30 WIB', 'metode' => 'E-Wallet (Dana)', 'status' => 'Gagal', 'total' => 650000],
        ];

        return view('admin.dashboard', compact('stats', 'chartData', 'recentTransactions'));
    }

    /**
     * Operasional — Manajemen jadwal dan armada bus.
     */
    public function operasional()
    {
        $statusArmada = [
            (object)['label' => 'BERJALAN', 'value' => 42, 'change' => '+5 Jam', 'color' => 'green'],
            (object)['label' => 'TIBA',     'value' => 18, 'change' => 'On Schedule', 'color' => 'blue'],
            (object)['label' => 'PEMELIHARAAN', 'value' => '03', 'change' => 'Depot', 'color' => 'red'],
        ];

        $jadwalList = [
            (object)['id' => 'SCH-1002', 'armada' => 'Laju Prima A1',       'rute' => 'Jakarta — Yogyakarta',    'waktu' => '24 Mei 2024 19:30 WIB', 'kapasitas_terisi' => 32, 'kapasitas_total' => 40, 'status' => 'AKTIF'],
            (object)['id' => 'SCH-1003', 'armada' => 'Kencana Luxury 02',   'rute' => 'Surabaya — Semarang',     'waktu' => '24 Mei 2024 20:00 WIB', 'kapasitas_terisi' => 40, 'kapasitas_total' => 40, 'status' => 'SELESAI'],
            (object)['id' => 'SCH-1004', 'armada' => 'Agra Mas Jetbus',     'rute' => 'Bandung — Malang',        'waktu' => '25 Mei 2024 08:00 WIB', 'kapasitas_terisi' => 5,  'kapasitas_total' => 36, 'status' => 'DIBATALKAN'],
            (object)['id' => 'SCH-1005', 'armada' => 'Laju Prima B3',       'rute' => 'Jakarta — Malang',        'waktu' => '25 Mei 2024 14:00 WIB', 'kapasitas_terisi' => 18, 'kapasitas_total' => 40, 'status' => 'AKTIF'],
            (object)['id' => 'SCH-1006', 'armada' => 'Kencana Luxury 05',   'rute' => 'Surabaya — Denpasar',     'waktu' => '25 Mei 2024 21:00 WIB', 'kapasitas_terisi' => 27, 'kapasitas_total' => 30, 'status' => 'AKTIF'],
            (object)['id' => 'SCH-1007', 'armada' => 'Agra Mas Double Deck','rute' => 'Bandung — Yogyakarta',    'waktu' => '26 Mei 2024 07:30 WIB', 'kapasitas_terisi' => 5,  'kapasitas_total' => 52, 'status' => 'AKTIF'],
            (object)['id' => 'SCH-1008', 'armada' => 'Laju Prima C1',       'rute' => 'Denpasar — Surabaya',     'waktu' => '26 Mei 2024 09:00 WIB', 'kapasitas_terisi' => 40, 'kapasitas_total' => 40, 'status' => 'SELESAI'],
            (object)['id' => 'SCH-1009', 'armada' => 'Kencana Luxury 01',   'rute' => 'Yogyakarta — Jakarta',    'waktu' => '26 Mei 2024 22:00 WIB', 'kapasitas_terisi' => 18, 'kapasitas_total' => 30, 'status' => 'AKTIF'],
        ];

        return view('admin.operasional', compact('statusArmada', 'jadwalList'));
    }

    /**
     * Tambah Jadwal Baru — Form pembuatan jadwal perjalanan.
     */
    public function tambahJadwal()
    {
        return view('admin.tambah_jadwal');
    }

    /**
     * Transaksi — Daftar pemesanan dan pembayaran.
     */
    public function transaksi()
    {
        $transaksiList = [
            (object)['id' => 'TRX-88291', 'nama' => 'Ahmad Subardjo', 'rute' => 'Jakarta → Bandung',    'tanggal' => '24 Mei 2024', 'status' => 'BERHASIL',  'total' => 150000],
            (object)['id' => 'TRX-88292', 'nama' => 'Siti Aminah',    'rute' => 'Surabaya → Malang',     'tanggal' => '24 Mei 2024', 'status' => 'MENUNGGU',  'total' => 85000],
            (object)['id' => 'TRX-88293', 'nama' => 'Budi Santoso',   'rute' => 'Yogyakarta → Solo',     'tanggal' => '23 Mei 2024', 'status' => 'BERHASIL',  'total' => 45000],
            (object)['id' => 'TRX-88294', 'nama' => 'Dewi Sartika',   'rute' => 'Jakarta → Semarang',    'tanggal' => '23 Mei 2024', 'status' => 'GAGAL',     'total' => 220000],
            (object)['id' => 'TRX-88295', 'nama' => 'Eko Prasetyo',   'rute' => 'Denpasar → Gilimanuk',  'tanggal' => '22 Mei 2024', 'status' => 'BERHASIL',  'total' => 110000],
        ];

        return view('admin.transaksi', compact('transaksiList'));
    }

    /**
     * Laporan — Ringkasan data harian dan laporan penjualan.
     */
    public function laporan()
    {
        $laporanList = [
            (object)['tanggal' => '25 Okt 2023', 'hari' => 'Rabu',   'jml_transaksi' => 124, 'perubahan' => '+5%',  'up' => true,  'jml_tiket' => 480, 'total' => 18450000],
            (object)['tanggal' => '24 Okt 2023', 'hari' => 'Selasa', 'jml_transaksi' => 118, 'perubahan' => '-2%',  'up' => false, 'jml_tiket' => 452, 'total' => 17210000],
            (object)['tanggal' => '23 Okt 2023', 'hari' => 'Senin',  'jml_transaksi' => 135, 'perubahan' => '+12%', 'up' => true,  'jml_tiket' => 512, 'total' => 21050000],
            (object)['tanggal' => '22 Okt 2023', 'hari' => 'Minggu', 'jml_transaksi' => 142, 'perubahan' => '+8%',  'up' => true,  'jml_tiket' => 540, 'total' => 23800000],
        ];

        return view('admin.laporan', compact('laporanList'));
    }
}
