<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Models\Bus;
use App\Models\Jadwal;

class AdminController extends Controller
{
    /**
     * Dashboard Admin — Ringkasan statistik operasional.
     */
    public function dashboard()
    {
        $totalPendapatan = DB::table('pemesanan_pembayaran')->whereMonth('tanggal_pemesanan', now()->month)->where('status_pembayaran', 'lunas')->sum('total_harga');
        $tiketTerjual = DB::table('tiket')->count();
        $totalBus = DB::table('bus')->count();
        $busAktif = DB::table('bus')->where('status_bus', 'aktif')->count();
        
        $stats = [
            (object)['label' => 'PENDAPATAN BULAN INI', 'value' => 'Rp ' . number_format($totalPendapatan, 0, ',', '.'), 'change' => 'Realtime', 'up' => true, 'icon' => 'ph-wallet', 'color' => 'green'],
            (object)['label' => 'TIKET TERJUAL',        'value' => number_format($tiketTerjual, 0, ',', '.'),    'change' => 'Realtime',  'up' => true, 'icon' => 'ph-ticket', 'color' => 'blue'],
            (object)['label' => 'ARMADA AKTIF',         'value' => "$busAktif / $totalBus", 'change' => 'Dari Total Bus',  'up' => null, 'icon' => 'ph-bus',    'color' => 'orange'],
            (object)['label' => 'TINGKAT KEPUASAN',     'value' => '4.8 / 5.0', 'change' => 'Bulan Ini',   'up' => false,'icon' => 'ph-star',   'color' => 'yellow'],
        ];

        $chartData = [
            ['day' => 'SEN', 'eksekutif' => rand(20, 80), 'ekonomi' => rand(10, 50)],
            ['day' => 'SEL', 'eksekutif' => rand(20, 80), 'ekonomi' => rand(10, 50)],
            ['day' => 'RAB', 'eksekutif' => rand(20, 80), 'ekonomi' => rand(10, 50)],
            ['day' => 'KAM', 'eksekutif' => rand(20, 80), 'ekonomi' => rand(10, 50)],
            ['day' => 'JUM', 'eksekutif' => rand(20, 80), 'ekonomi' => rand(10, 50)],
            ['day' => 'SAB', 'eksekutif' => rand(20, 80), 'ekonomi' => rand(10, 50)],
            ['day' => 'MIN', 'eksekutif' => rand(20, 80), 'ekonomi' => rand(10, 50)],
        ];

        $recentTransactions = DB::table('pemesanan_pembayaran as p')
            ->join('jadwal as j', 'p.jadwal_id', '=', 'j.id_jadwal')
            ->join('bus as b', 'j.bus_id', '=', 'b.bus_id')
            ->join('rute as r', 'j.rute_id', '=', 'r.rute_id')
            ->leftJoin('tiket as t', 'p.pemesanan_id', '=', 't.pemesanan_id')
            ->select(
                'p.pemesanan_id',
                'p.kode_booking',
                'p.tanggal_pemesanan',
                'p.total_harga',
                'p.status_pembayaran',
                'p.metode_pembayaran',
                'j.jam_berangkat',
                'b.kelas',
                'r.kota_asal',
                'r.kota_tujuan',
                DB::raw('MAX(t.nama_penumpang) as nama')
            )
            ->groupBy('p.pemesanan_id', 'p.kode_booking', 'p.tanggal_pemesanan', 'p.total_harga', 'p.status_pembayaran', 'p.metode_pembayaran', 'j.jam_berangkat', 'b.kelas', 'r.kota_asal', 'r.kota_tujuan')
            ->orderBy('p.tanggal_pemesanan', 'desc')
            ->limit(5)
            ->get()
            ->map(function ($item) {
                $statusMap = ['lunas' => 'Berhasil', 'pending' => 'Pending', 'batal' => 'Gagal'];
                return (object)[
                    'nama' => $item->nama ?: 'Guest',
                    'rute' => $item->kota_asal . ' → ' . $item->kota_tujuan,
                    'kelas' => $item->kelas . ' • ' . $item->kode_booking,
                    'tanggal' => Carbon::parse($item->tanggal_pemesanan)->isoFormat('D MMM YYYY'),
                    'jam' => Carbon::parse($item->jam_berangkat)->format('H:i') . ' WIB',
                    'metode' => strtoupper($item->metode_pembayaran),
                    'status' => $statusMap[$item->status_pembayaran] ?? 'Berhasil',
                    'total' => $item->total_harga
                ];
            });

        return view('admin.dashboard', compact('stats', 'chartData', 'recentTransactions'));
    }

    /**
     * Operasional — Manajemen jadwal dan armada bus.
     */
    public function operasional()
    {
        $jadwalDB = DB::table('jadwal as j')
            ->whereNull('j.deleted_at')
            ->join('bus as b', 'j.bus_id', '=', 'b.bus_id')
            ->join('rute as r', 'j.rute_id', '=', 'r.rute_id')
            ->select('j.*', 'b.nama_bus', 'b.kapasitas', 'r.kota_asal', 'r.kota_tujuan')
            ->orderBy('j.tanggal_berangkat', 'desc')
            ->get();

        $jadwalList = $jadwalDB->map(function($j) {
            $kapasitasTerisi = max(0, $j->kapasitas - $j->kursi_tersedia);
            return (object)[
                'id' => 'SCH-' . $j->id_jadwal,
                'id_jadwal' => $j->id_jadwal,
                'armada' => $j->nama_bus,
                'rute' => $j->kota_asal . ' — ' . $j->kota_tujuan,
                'waktu' => Carbon::parse($j->tanggal_berangkat)->isoFormat('D MMM YYYY') . ' ' . Carbon::parse($j->jam_berangkat)->format('H:i') . ' WIB',
                'kapasitas_terisi' => $kapasitasTerisi,
                'kapasitas_total' => $j->kapasitas,
                'status' => strtoupper($j->status_jadwal)
            ];
        });

        $busList = Bus::all();

        $trashedJadwalDB = DB::table('jadwal as j')
            ->whereNotNull('j.deleted_at')
            ->join('bus as b', 'j.bus_id', '=', 'b.bus_id')
            ->join('rute as r', 'j.rute_id', '=', 'r.rute_id')
            ->select('j.*', 'b.nama_bus', 'b.kapasitas', 'r.kota_asal', 'r.kota_tujuan')
            ->orderBy('j.deleted_at', 'desc')
            ->get();

        $trashedJadwalList = $trashedJadwalDB->map(function($j) {
            return (object)[
                'id' => 'SCH-' . $j->id_jadwal,
                'id_jadwal' => $j->id_jadwal,
                'armada' => $j->nama_bus,
                'rute' => $j->kota_asal . ' — ' . $j->kota_tujuan,
                'waktu' => Carbon::parse($j->tanggal_berangkat)->isoFormat('D MMM YYYY') . ' ' . Carbon::parse($j->jam_berangkat)->format('H:i') . ' WIB',
                'deleted_at' => Carbon::parse($j->deleted_at)->isoFormat('D MMM YYYY H:mm'),
            ];
        });

        $trashedBusList = Bus::onlyTrashed()->get();

        $statusArmada = [
            (object)['label' => 'JADWAL AKTIF', 'value' => $jadwalDB->where('status_jadwal', 'aktif')->count(), 'change' => 'Sedang/Akan Berjalan', 'color' => 'green'],
            (object)['label' => 'JADWAL SELESAI', 'value' => $jadwalDB->where('status_jadwal', 'selesai')->count(), 'change' => 'Selesai', 'color' => 'blue'],
            (object)['label' => 'DIBATALKAN', 'value' => $jadwalDB->where('status_jadwal', 'batal')->count(), 'change' => 'Dibatalkan', 'color' => 'red'],
        ];

        $tiketHariIni = DB::table('tiket as t')
            ->join('pemesanan_pembayaran as p', 't.pemesanan_id', '=', 'p.pemesanan_id')
            ->whereDate('p.tanggal_pemesanan', Carbon::today())
            ->count();

        return view('admin.operasional', compact('statusArmada', 'jadwalList', 'busList', 'trashedJadwalList', 'trashedBusList', 'tiketHariIni'));
    }

    /**
     * Tambah Jadwal Baru — Form pembuatan jadwal perjalanan.
     */
    public function tambahJadwal()
    {
        $buses = DB::table('bus')->where('status_bus', 'aktif')->get();
        $routes = DB::table('rute')->get();
        return view('admin.tambah_jadwal', compact('buses', 'routes'));
    }

    /**
     * Simpan Jadwal Baru
     */
    public function storeJadwal(Request $request)
    {
        $validated = $request->validate([
            'bus_id' => 'required|integer|exists:bus,bus_id',
            'rute_id' => 'required|integer|exists:rute,rute_id',
            'tanggal_berangkat' => 'required|date',
            'jam_berangkat' => 'required|date_format:H:i',
            'harga' => 'required|numeric|min:0',
            'kursi_tersedia' => 'required|integer|min:1',
        ]);

        Jadwal::create([
            'bus_id' => $validated['bus_id'],
            'rute_id' => $validated['rute_id'],
            'tanggal_berangkat' => $validated['tanggal_berangkat'],
            'jam_berangkat' => $validated['jam_berangkat'],
            'harga' => $validated['harga'],
            'kursi_tersedia' => $validated['kursi_tersedia'],
            'status_jadwal' => 'aktif',
            'id_admin' => auth()->id(),
        ]);

        return redirect()->route('admin.operasional')->with('success', 'Jadwal berhasil ditambahkan!');
    }

    /**
     * Edit Jadwal — Form edit jadwal.
     */
    public function editJadwal($id)
    {
        $jadwal = Jadwal::findOrFail($id);
        $buses = Bus::where('status_bus', 'aktif')->get();
        $routes = DB::table('rute')->get();
        return view('admin.edit_jadwal', compact('jadwal', 'buses', 'routes'));
    }

    /**
     * Update Jadwal
     */
    public function updateJadwal(Request $request, $id)
    {
        $jadwal = Jadwal::findOrFail($id);
        
        $validated = $request->validate([
            'bus_id' => 'required|integer|exists:bus,bus_id',
            'rute_id' => 'required|integer|exists:rute,rute_id',
            'tanggal_berangkat' => 'required|date',
            'jam_berangkat' => 'required|date_format:H:i',
            'harga' => 'required|numeric|min:0',
            'kursi_tersedia' => 'required|integer|min:1',
            'status_jadwal' => 'required|string|in:aktif,selesai,batal',
        ]);

        $jadwal->update($validated);

        return redirect()->route('admin.operasional')->with('success', 'Jadwal berhasil diperbarui!');
    }

    /**
     * Soft Delete Jadwal
     */
    public function destroyJadwal($id)
    {
        $jadwal = Jadwal::findOrFail($id);
        $jadwal->delete();

        return redirect()->route('admin.operasional')->with('success', 'Jadwal berhasil dihapus!');
    }

    /**
     * Force Delete Jadwal (Permanent)
     */
    public function forceDestroyJadwal($id)
    {
        $jadwal = Jadwal::withTrashed()->findOrFail($id);
        $jadwal->forceDelete();

        return redirect()->route('admin.operasional')->with('success', 'Jadwal berhasil dihapus permanen!');
    }

    /**
     * Restore Jadwal (Undo Soft Delete)
     */
    public function restoreJadwal($id)
    {
        $jadwal = Jadwal::withTrashed()->findOrFail($id);
        $jadwal->restore();

        return redirect()->route('admin.operasional')->with('success', 'Jadwal berhasil dipulihkan!');
    }

    /**
     * Tambah Bus Baru — Form pembuatan armada.
     */
    public function tambahBus()
    {
        return view('admin.tambah_bus');
    }

    /**
     * Simpan Bus Baru
     */
    public function storeBus(Request $request)
    {
        $validated = $request->validate([
            'no_polisi' => 'required|string|unique:bus,no_polisi',
            'nama_bus' => 'required|string',
            'kapasitas' => 'required|integer|min:1',
            'kelas' => 'required|string|in:Eksekutif,Ekonomi,Patas',
            'fasilitas' => 'nullable|string',
        ]);

        Bus::create([
            'no_polisi' => $validated['no_polisi'],
            'nama_bus' => $validated['nama_bus'],
            'kapasitas' => $validated['kapasitas'],
            'status_bus' => 'aktif',
            'kelas' => $validated['kelas'],
            'fasilitas' => $validated['fasilitas'] ?? 'AC,WiFi',
        ]);

        return redirect()->route('admin.operasional')->with('success', 'Bus berhasil ditambahkan!');
    }

    /**
     * Edit Bus — Form edit armada.
     */
    public function editBus($id)
    {
        $bus = Bus::findOrFail($id);
        return view('admin.edit_bus', compact('bus'));
    }

    /**
     * Update Bus
     */
    public function updateBus(Request $request, $id)
    {
        $bus = Bus::findOrFail($id);
        
        $validated = $request->validate([
            'no_polisi' => 'required|string|unique:bus,no_polisi,' . $id . ',bus_id',
            'nama_bus' => 'required|string',
            'kapasitas' => 'required|integer|min:1',
            'kelas' => 'required|string|in:Eksekutif,Ekonomi,Patas',
            'status_bus' => 'required|string|in:aktif,maintenance',
            'fasilitas' => 'nullable|string',
        ]);

        $bus->update($validated);

        return redirect()->route('admin.operasional')->with('success', 'Bus berhasil diperbarui!');
    }

    /**
     * Soft Delete Bus
     */
    public function destroyBus($id)
    {
        $bus = Bus::findOrFail($id);
        $bus->delete();

        return redirect()->route('admin.operasional')->with('success', 'Bus berhasil dihapus!');
    }

    /**
     * Force Delete Bus (Permanent)
     */
    public function forceDestroyBus($id)
    {
        $bus = Bus::withTrashed()->findOrFail($id);
        $bus->forceDelete();

        return redirect()->route('admin.operasional')->with('success', 'Bus berhasil dihapus permanen!');
    }

    /**
     * Restore Bus (Undo Soft Delete)
     */
    public function restoreBus($id)
    {
        $bus = Bus::withTrashed()->findOrFail($id);
        $bus->restore();

        return redirect()->route('admin.operasional')->with('success', 'Bus berhasil dipulihkan!');
    }

    /**
     * Transaksi — Daftar pemesanan dan pembayaran.
     */
    public function transaksi()
    {
        $transaksiDB = DB::table('pemesanan_pembayaran as p')
            ->join('jadwal as j', 'p.jadwal_id', '=', 'j.id_jadwal')
            ->join('rute as r', 'j.rute_id', '=', 'r.rute_id')
            ->leftJoin('tiket as t', 'p.pemesanan_id', '=', 't.pemesanan_id')
            ->select(
                'p.kode_booking as id',
                'p.tanggal_pemesanan',
                'p.total_harga',
                'p.status_pembayaran',
                'r.kota_asal',
                'r.kota_tujuan',
                DB::raw('MAX(t.nama_penumpang) as nama')
            )
            ->groupBy('p.kode_booking', 'p.tanggal_pemesanan', 'p.total_harga', 'p.status_pembayaran', 'r.kota_asal', 'r.kota_tujuan')
            ->orderBy('p.tanggal_pemesanan', 'desc')
            ->get();

        $transaksiList = $transaksiDB->map(function($t) {
            $statusMap = ['lunas' => 'BERHASIL', 'pending' => 'MENUNGGU', 'batal' => 'GAGAL'];
            return (object)[
                'id' => $t->id,
                'nama' => $t->nama ?: 'Guest',
                'rute' => $t->kota_asal . ' → ' . $t->kota_tujuan,
                'tanggal' => Carbon::parse($t->tanggal_pemesanan)->isoFormat('D MMM YYYY'),
                'status' => $statusMap[$t->status_pembayaran] ?? 'BERHASIL',
                'total' => $t->total_harga
            ];
        });

        $totalPendapatan = DB::table('pemesanan_pembayaran')->where('status_pembayaran', 'lunas')->sum('total_harga');
        $tiketTerjual = DB::table('tiket')->count();

        return view('admin.transaksi', compact('transaksiList', 'totalPendapatan', 'tiketTerjual'));
    }

    /**
     * Laporan — Ringkasan data harian dan laporan penjualan.
     */
    public function laporan()
    {
        $laporanDB = DB::table('pemesanan_pembayaran')
            ->select(
                DB::raw('DATE(tanggal_pemesanan) as date'),
                DB::raw('COUNT(pemesanan_id) as jml_transaksi'),
                DB::raw('SUM(jumlah_kursi) as jml_tiket'),
                DB::raw('SUM(total_harga) as total')
            )
            ->where('status_pembayaran', 'lunas')
            ->groupBy(DB::raw('DATE(tanggal_pemesanan)'))
            ->orderBy('date', 'desc')
            ->limit(10)
            ->get();

        $laporanList = $laporanDB->map(function($l) {
            return (object)[
                'tanggal' => Carbon::parse($l->date)->isoFormat('D MMM YYYY'),
                'hari' => Carbon::parse($l->date)->isoFormat('dddd'),
                'jml_transaksi' => $l->jml_transaksi,
                'perubahan' => '-',
                'up' => true,
                'jml_tiket' => $l->jml_tiket,
                'total' => $l->total
            ];
        });

        $totalPendapatan = DB::table('pemesanan_pembayaran')->where('status_pembayaran', 'lunas')->sum('total_harga');
        $totalTransaksi = DB::table('pemesanan_pembayaran')->where('status_pembayaran', 'lunas')->count();
        $totalTiket = DB::table('tiket')->count();

        return view('admin.laporan', compact('laporanList', 'totalPendapatan', 'totalTransaksi', 'totalTiket'));
    }
}

