<?php

namespace App\Http\Controllers;

use App\Models\Rute;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class HomeController extends Controller
{
    public function index()
    {
        // Get unique locations
        $kotaAsal = DB::table('rute')->select('kota_asal as kota')->distinct();
        $locations = DB::table('rute')
            ->select('kota_tujuan as kota')
            ->distinct()
            ->union($kotaAsal)
            ->orderBy('kota', 'asc')
            ->pluck('kota');

        // Get popular routes
        $popularRoutes = DB::table('rute')
            ->select(
                'rute.rute_id',
                'rute.kota_asal',
                'rute.kota_tujuan',
                'rute.jarak_km',
                'rute.gambar',
                DB::raw('COUNT(pemesanan_pembayaran.pemesanan_id) as jumlah_pemesanan'),
                DB::raw('MIN(jadwal.harga) as harga_mulai')
            )
            ->leftJoin('jadwal', 'jadwal.rute_id', '=', 'rute.rute_id')
            ->leftJoin('pemesanan_pembayaran', function ($join) {
                $join->on('pemesanan_pembayaran.jadwal_id', '=', 'jadwal.id_jadwal')
                     ->where('pemesanan_pembayaran.status_pembayaran', '=', 'lunas');
            })
            ->groupBy('rute.rute_id', 'rute.kota_asal', 'rute.kota_tujuan', 'rute.jarak_km', 'rute.gambar')
            ->orderByDesc('jumlah_pemesanan')
            ->limit(3)
            ->get();

        return view('home', compact('locations', 'popularRoutes'));
    }
}
