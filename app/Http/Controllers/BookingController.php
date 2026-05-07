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
            ->where('p.jadwal_id', $jadwalId)
            ->whereIn('p.status_pembayaran', ['lunas', 'pending'])
            ->pluck('t.id_kursi')
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
     * Halaman pembayaran berhasil.
     */
    public function success(Request $request)
    {
        // Data dummy — nanti diganti dari session/DB setelah proses pembayaran
        $booking = (object)[
            'kode_booking'    => $request->input('kode', 'LJB992834'),
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
}
