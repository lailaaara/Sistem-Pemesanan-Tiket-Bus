<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class JadwalController extends Controller
{
    public function search(Request $request)
    {
        $from = $request->input('from', '');
        $to = $request->input('to', '');
        $date = $request->input('date', '');
        
        if (!$from || !$to || !$date) {
            return response()->json([
                'status' => 'error',
                'message' => 'Parameter from, to, dan date wajib diisi'
            ], 400);
        }

        $passengerCount = $request->input('passenger_count', 1);

        // Here we recreate the logic from the native PHP legacy code
        $query = DB::table('jadwal as j')
            ->join('bus as b', 'j.bus_id', '=', 'b.bus_id')
            ->join('rute as r', 'j.rute_id', '=', 'r.rute_id')
            ->select(
                'j.id_jadwal', 'j.tanggal_berangkat', 'j.jam_berangkat', 'j.harga', 'j.kursi_tersedia',
                'b.bus_id', 'b.nama_bus', 'b.kapasitas', 'b.kelas', 'b.fasilitas',
                'r.rute_id', 'r.kota_asal', 'r.kota_tujuan'
            )
            ->whereRaw('LOWER(r.kota_asal) LIKE ?', ['%' . strtolower($from) . '%'])
            ->whereRaw('LOWER(r.kota_tujuan) LIKE ?', ['%' . strtolower($to) . '%'])
            ->where('j.tanggal_berangkat', $date)
            ->where('j.status_jadwal', 'aktif')
            ->where('j.kursi_tersedia', '>=', $passengerCount);

        // Fetch data
        $results = $query->orderBy('j.jam_berangkat', 'asc')->get();

        $data = $results->map(function ($row) {
            return [
                "id_jadwal"      => $row->id_jadwal,
                "bus_id"         => $row->bus_id,
                "bus"            => $row->nama_bus,
                "kelas"          => $row->kelas,
                "fasilitas"      => $row->fasilitas,
                "kapasitas"      => $row->kapasitas,
                "rute_id"        => $row->rute_id,
                "asal"           => $row->kota_asal,
                "tujuan"         => $row->kota_tujuan,
                "tanggal"        => $row->tanggal_berangkat,
                "jam"            => $row->jam_berangkat,
                "harga"          => (int)$row->harga,
                "kursi_tersedia" => (int)$row->kursi_tersedia,
            ];
        });

        return response()->json([
            'status' => 'success',
            'message' => $data->count() . ' jadwal ditemukan',
            'data' => $data
        ]);
    }
}
