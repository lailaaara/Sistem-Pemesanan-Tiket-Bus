<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BookingController extends Controller
{
    /**
     * Halaman hasil pencarian jadwal bus.
     */
    public function search(Request $request)
    {
        $from = $request->input('from', 'Jakarta');
        $to   = $request->input('to', 'Surabaya');
        $date = $request->input('date', now()->format('Y-m-d'));
        $pax  = $request->input('passenger_count', 1);

        // Query jadwal dari DB
        $results = DB::table('jadwal as j')
            ->join('bus as b', 'j.bus_id', '=', 'b.bus_id')
            ->join('rute as r', 'j.rute_id', '=', 'r.rute_id')
            ->select(
                'j.id_jadwal', 'j.tanggal_berangkat', 'j.jam_berangkat',
                'j.harga', 'j.kursi_tersedia', 'j.status_jadwal',
                'b.nama_bus', 'b.kelas', 'b.fasilitas', 'b.kapasitas',
                'r.kota_asal', 'r.kota_tujuan', 'r.jarak_km'
            )
            ->whereRaw('LOWER(r.kota_asal) LIKE ?', ['%' . strtolower($from) . '%'])
            ->whereRaw('LOWER(r.kota_tujuan) LIKE ?', ['%' . strtolower($to) . '%'])
            ->where('j.tanggal_berangkat', $date)
            ->where('j.status_jadwal', 'aktif')
            ->where('j.kursi_tersedia', '>=', (int)$pax)
            ->orderBy('j.jam_berangkat')
            ->get();

        return view('booking.search', compact('results', 'from', 'to', 'date', 'pax'));
    }

    /**
     * Halaman pilih kursi.
     */
    public function seat(Request $request)
    {
        $jadwalId = $request->input('jadwal_id');

        // Ambil detail jadwal dari DB
        $jadwal = DB::table('jadwal as j')
            ->join('bus as b', 'j.bus_id', '=', 'b.bus_id')
            ->join('rute as r', 'j.rute_id', '=', 'r.rute_id')
            ->select('j.*', 'b.nama_bus', 'b.kelas', 'b.no_polisi', 'b.kapasitas',
                     'r.kota_asal', 'r.kota_tujuan')
            ->where('j.id_jadwal', $jadwalId)
            ->first();

        // Ambil kursi dari DB; jika kosong gunakan dummy
        $kursiTerisi = DB::table('tiket as t')
            ->join('pemesanan_pembayaran as p', 't.pemesanan_id', '=', 'p.pemesanan_id')
            ->join('kursi as k', 't.id_kursi', '=', 'k.id_kursi')
            ->where('p.jadwal_id', $jadwalId)
            ->whereIn('p.status_pembayaran', ['lunas', 'pending'])
            ->pluck('k.no_kursi')
            ->toArray();

        return view('booking.seat', compact('jadwal', 'kursiTerisi'));
    }

    /**
     * Halaman checkout / pembayaran.
     */
    public function checkout(Request $request)
    {
        $jadwalId  = $request->input('jadwal_id');
        $kursiNos  = $request->input('kursi', '');

        $jadwal = DB::table('jadwal as j')
            ->join('bus as b', 'j.bus_id', '=', 'b.bus_id')
            ->join('rute as r', 'j.rute_id', '=', 'r.rute_id')
            ->select('j.*', 'b.nama_bus', 'b.kelas', 'r.kota_asal', 'r.kota_tujuan')
            ->where('j.id_jadwal', $jadwalId)
            ->first();

        $metodePembayaran = [
            ['id' => 'va',     'label' => 'Virtual Account',  'sub' => 'Transfer otomatis dari BCA, Mandiri, BNI', 'icon' => 'ph-bank'],
            ['id' => 'ewallet','label' => 'E-Wallet',         'sub' => 'Gopay, OVO, ShopeePay, Dana',              'icon' => 'ph-device-mobile'],
            ['id' => 'cc',     'label' => 'Kartu Kredit',     'sub' => 'Visa, Mastercard, JCB',                    'icon' => 'ph-credit-card'],
        ];

        return view('booking.checkout', compact('jadwal', 'kursiNos', 'metodePembayaran'));
    }

    /**
     * Proses form checkout.
     */
    public function process(Request $request)
    {
        $request->validate([
            'nama_penumpang' => 'required|string',
            'no_identitas'   => 'required|string',
            'no_hp'          => 'required|string',
            'email'          => 'nullable|email',
            'jadwal_id'      => 'required|integer',
            'kursi'          => 'required|string',
            'total_harga'    => 'required|numeric'
        ]);

        $jadwalId = $request->input('jadwal_id');
        $kursiArray = explode(',', $request->input('kursi'));
        $jumlahKursi = count($kursiArray);
        $totalHarga = $request->input('total_harga');

        $jadwal = DB::table('jadwal')->where('id_jadwal', $jadwalId)->first();
        if (!$jadwal) {
            return redirect()->back()->with('error', 'Jadwal tidak ditemukan');
        }

        DB::beginTransaction();
        try {
            // 1. Insert Pemesanan
            $kodeBooking = 'LJB-' . strtoupper(substr(uniqid(), -6));
            $pemesananId = DB::table('pemesanan_pembayaran')->insertGetId([
                'jadwal_id' => $jadwalId,
                'kode_booking' => $kodeBooking,
                'tanggal_pemesanan' => now(),
                'jumlah_kursi' => $jumlahKursi,
                'total_harga' => $totalHarga,
                'metode_pembayaran' => $request->input('metode_pembayaran', 'va'),
                'tanggal_bayar' => now(),
                'status_pembayaran' => 'lunas'
            ], 'pemesanan_id');

            // 2. Insert Tiket untuk setiap kursi
            foreach ($kursiArray as $noKursi) {
                $noKursi = trim($noKursi);
                
                // Cari atau buat id_kursi di tabel kursi
                $kursi = DB::table('kursi')
                    ->where('id_bus', $jadwal->bus_id)
                    ->where('no_kursi', $noKursi)
                    ->first();
                    
                if ($kursi) {
                    $idKursi = $kursi->id_kursi;
                } else {
                    $idKursi = DB::table('kursi')->insertGetId([
                        'id_bus' => $jadwal->bus_id,
                        'no_kursi' => $noKursi
                    ], 'id_kursi');
                }

                DB::table('tiket')->insert([
                    'pemesanan_id' => $pemesananId,
                    'id_kursi' => $idKursi,
                    'kode_tiket' => $kodeBooking . '-' . $noKursi,
                    'status_tiket' => 'aktif',
                    'nama_penumpang' => $request->input('nama_penumpang'),
                    'no_hp' => $request->input('no_hp'),
                    'no_identitas' => $request->input('no_identitas')
                ]);
            }

            // 3. Kurangi kursi tersedia di jadwal
            if ($jadwal->kursi_tersedia >= $jumlahKursi) {
                DB::table('jadwal')
                    ->where('id_jadwal', $jadwalId)
                    ->decrement('kursi_tersedia', $jumlahKursi);
            }

            DB::commit();

            // Simpan kode booking ke session agar otomatis muncul di "Tiket Saya" tanpa perlu login
            $recentBookings = session()->get('recent_bookings', []);
            if (!in_array($kodeBooking, $recentBookings)) {
                $recentBookings[] = $kodeBooking;
                session()->put('recent_bookings', $recentBookings);
            }

            return redirect()->route('booking.success', ['kode' => $kodeBooking]);
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * Halaman pembayaran berhasil.
     */
    public function success(Request $request)
    {
        $kodeBooking = $request->input('kode');
        
        $pemesanan = DB::table('pemesanan_pembayaran as p')
            ->join('jadwal as j', 'p.jadwal_id', '=', 'j.id_jadwal')
            ->join('bus as b', 'j.bus_id', '=', 'b.bus_id')
            ->join('rute as r', 'j.rute_id', '=', 'r.rute_id')
            ->where('p.kode_booking', $kodeBooking)
            ->select('p.*', 'j.tanggal_berangkat', 'j.jam_berangkat', 'b.nama_bus', 'b.kelas', 'r.kota_asal', 'r.kota_tujuan')
            ->first();

        if (!$pemesanan) {
            // Fallback ke dummy jika tidak ditemukan
            $booking = (object)[
                'kode_booking'    => $kodeBooking ?: 'LJB992834',
                'nama_penumpang'  => 'Anak Undip',
                'no_kursi'        => '2A',
                'kota_asal'       => 'Jakarta',
                'terminal_asal'   => 'Terminal Pulo Gebang',
                'kota_tujuan'     => 'Surabaya',
                'terminal_tujuan' => 'Terminal Bungurasih',
                'tanggal'         => '27 Mei 2026, 08:30 WIB',
                'nama_bus'        => 'LajuBus',
                'kelas'           => 'Executive',
            ];
            return view('booking.success', compact('booking'));
        }

        $tiketList = DB::table('tiket as t')
            ->join('kursi as k', 't.id_kursi', '=', 'k.id_kursi')
            ->where('t.pemesanan_id', $pemesanan->pemesanan_id)
            ->get();

        $kursiNos = $tiketList->pluck('no_kursi')->implode(', ');
        $penumpang = $tiketList->first()->nama_penumpang ?? 'Anonim';

        $booking = (object)[
            'kode_booking'    => $pemesanan->kode_booking,
            'nama_penumpang'  => $penumpang,
            'no_kursi'        => $kursiNos,
            'kota_asal'       => $pemesanan->kota_asal,
            'terminal_asal'   => 'Terminal ' . $pemesanan->kota_asal,
            'kota_tujuan'     => $pemesanan->kota_tujuan,
            'terminal_tujuan' => 'Terminal ' . $pemesanan->kota_tujuan,
            'tanggal'         => \Carbon\Carbon::parse($pemesanan->tanggal_berangkat)->isoFormat('D MMM YYYY') . ', ' . \Carbon\Carbon::parse($pemesanan->jam_berangkat)->format('H:i') . ' WIB',
            'nama_bus'        => $pemesanan->nama_bus,
            'kelas'           => $pemesanan->kelas,
        ];

        return view('booking.success', compact('booking'));
    }

    /**
     * Halaman Kelola Tiket Perjalanan (Tiket Saya).
     */
    public function ticketsIndex(Request $request)
    {
        $recentBookings = session()->get('recent_bookings', []);

        $activeTickets = [];
        $pastTickets = [];

        if (!empty($recentBookings)) {
            // Ambil data pemesanan riil dari DB
            $tickets = DB::table('pemesanan_pembayaran as p')
                ->join('jadwal as j', 'p.jadwal_id', '=', 'j.id_jadwal')
                ->join('bus as b', 'j.bus_id', '=', 'b.bus_id')
                ->join('rute as r', 'j.rute_id', '=', 'r.rute_id')
                ->whereIn('p.kode_booking', $recentBookings)
                ->select(
                    'p.pemesanan_id as id',
                    'p.kode_booking',
                    'b.nama_bus',
                    'b.kelas',
                    'r.kota_asal',
                    'r.kota_tujuan',
                    'j.tanggal_berangkat',
                    'j.jam_berangkat',
                    'p.status_pembayaran'
                )
                ->orderBy('j.tanggal_berangkat', 'desc')
                ->get();

            foreach ($tickets as $ticket) {
                // Ambil nomor kursi
                $seats = DB::table('tiket as t')
                    ->join('kursi as k', 't.id_kursi', '=', 'k.id_kursi')
                    ->where('t.pemesanan_id', $ticket->id)
                    ->pluck('k.no_kursi')
                    ->toArray();
                
                $ticket->no_kursi = implode(', ', $seats);

                // Format tanggal berangkat dan jam
                $ticket->tanggal_berangkat_formatted = \Carbon\Carbon::parse($ticket->tanggal_berangkat)->isoFormat('D MMM YYYY');
                $ticket->jam_berangkat_formatted = \Carbon\Carbon::parse($ticket->jam_berangkat)->format('H:i') . ' WIB';
                $ticket->jam_tiba_formatted = \Carbon\Carbon::parse($ticket->jam_berangkat)->addHours(10)->format('H:i') . ' WIB';

                // Tentukan status dan kelas status berdasarkan tanggal keberangkatan
                $departureDateTime = \Carbon\Carbon::parse($ticket->tanggal_berangkat . ' ' . $ticket->jam_berangkat);
                $isPast = $departureDateTime->isPast();

                if ($isPast) {
                    $ticket->status = 'Selesai';
                    $ticket->status_class = 'status-success';
                    $pastTickets[] = $ticket;
                } else {
                    $ticket->status = 'Menunggu Keberangkatan';
                    $ticket->status_class = 'status-pending';
                    $activeTickets[] = $ticket;
                }
            }
        }

        return view('booking.tickets_index', compact('activeTickets', 'pastTickets'));
    }

    /**
     * Proses pencarian tiket tamu berdasarkan Nomor HP.
     */
    public function searchTicket(Request $request)
    {
        $request->validate([
            'no_hp' => 'required|string',
        ]);

        $noHp = trim($request->input('no_hp'));

        // Cari semua tiket yang memiliki nomor HP tersebut
        $bookings = DB::table('tiket as t')
            ->join('pemesanan_pembayaran as p', 't.pemesanan_id', '=', 'p.pemesanan_id')
            ->where('t.no_hp', $noHp)
            ->select('p.kode_booking')
            ->distinct()
            ->get();

        if ($bookings->isEmpty()) {
            return redirect()->back()->with('error', 'Tidak ada tiket yang terdaftar dengan Nomor HP tersebut.');
        }

        $recentBookings = session()->get('recent_bookings', []);
        foreach ($bookings as $b) {
            if (!in_array($b->kode_booking, $recentBookings)) {
                $recentBookings[] = $b->kode_booking;
            }
        }
        session()->put('recent_bookings', $recentBookings);

        return redirect()->route('booking.tickets')->with('success', 'Berhasil memuat ' . count($bookings) . ' tiket Anda!');
    }

    /**
     * Halaman Detail Tiket Perjalanan.
     */
    public function ticketsDetail($id)
    {
        $pemesanan = DB::table('pemesanan_pembayaran as p')
            ->join('jadwal as j', 'p.jadwal_id', '=', 'j.id_jadwal')
            ->join('bus as b', 'j.bus_id', '=', 'b.bus_id')
            ->join('rute as r', 'j.rute_id', '=', 'r.rute_id')
            ->where('p.pemesanan_id', $id)
            ->select(
                'p.*', 
                'j.tanggal_berangkat', 
                'j.jam_berangkat', 
                'b.nama_bus', 
                'b.kelas', 
                'b.no_polisi', 
                'b.fasilitas', 
                'r.kota_asal', 
                'r.kota_tujuan'
            )
            ->first();

        if (!$pemesanan) {
            return redirect()->route('booking.tickets')->with('error', 'Detail tiket tidak ditemukan.');
        }

        $tiketList = DB::table('tiket as t')
            ->join('kursi as k', 't.id_kursi', '=', 'k.id_kursi')
            ->where('t.pemesanan_id', $id)
            ->get();

        $penumpangList = [];
        foreach ($tiketList as $t) {
            $penumpangList[] = (object)[
                'nama' => $t->nama_penumpang,
                'no_kursi' => $t->no_kursi,
                'tipe_bus' => $pemesanan->kelas,
                'status' => 'Terkonfirmasi'
            ];
        }

        // Get facilities
        $fasilitas = [];
        if (!empty($pemesanan->fasilitas)) {
            $decoded = json_decode($pemesanan->fasilitas, true);
            if (is_array($decoded)) {
                $fasilitas = $decoded;
            } else {
                $fasilitas = array_map('trim', explode(',', $pemesanan->fasilitas));
            }
        }
        if (empty($fasilitas)) {
            $fasilitas = ['Wiai', 'AC', 'Leg Rest'];
        }

        // Parse status_pembayaran to human-readable status
        $statusText = 'Menunggu Keberangkatan';

        // Departure and arrival dates
        $tanggalBerangkat = \Carbon\Carbon::parse($pemesanan->tanggal_berangkat)->isoFormat('dddd, D MMM YYYY');
        $tanggalTiba = \Carbon\Carbon::parse($pemesanan->tanggal_berangkat)->isoFormat('dddd, D MMM YYYY'); // Default same day

        $ticket = (object)[
            'id' => $pemesanan->pemesanan_id,
            'kode_booking' => $pemesanan->kode_booking,
            'status' => $statusText,
            'nama_bus' => $pemesanan->nama_bus,
            'no_bus' => $pemesanan->no_polisi ?? 'LB-2045-A',
            'jam_berangkat' => \Carbon\Carbon::parse($pemesanan->jam_berangkat)->format('H:i'),
            'tanggal_berangkat' => $tanggalBerangkat,
            'terminal_asal' => 'Terminal Pulo Gebang, ' . $pemesanan->kota_asal,
            'jam_tiba' => \Carbon\Carbon::parse($pemesanan->jam_berangkat)->addHours(10)->format('H:i'),
            'tanggal_tiba' => $tanggalTiba,
            'terminal_tujuan' => 'Terminal Bungurasih, ' . $pemesanan->kota_tujuan,
            'fasilitas' => $fasilitas,
            'penumpang' => $penumpangList,
            'harga_tiket' => count($tiketList) > 0 ? ($pemesanan->total_harga / count($tiketList)) : $pemesanan->total_harga,
            'biaya_layanan' => 5000,
            'total_bayar' => $pemesanan->total_harga + 5000,
            'info_pembayaran' => 'Pembayaran berhasil menggunakan ' . ($pemesanan->metode_pembayaran == 'va' ? 'Transfer Bank BCA' : strtoupper($pemesanan->metode_pembayaran)) . ' pada ' . \Carbon\Carbon::parse($pemesanan->tanggal_bayar ?? $pemesanan->tanggal_pemesanan)->isoFormat('D MMM YYYY, H:i') . ' WIB.'
        ];

        return view('booking.tickets_detail', compact('ticket'));
    }
}

