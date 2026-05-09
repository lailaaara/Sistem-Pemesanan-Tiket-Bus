@extends('layouts.admin')
@section('title', 'Edit Jadwal - BusMania')

@section('content')
<h1 class="admin-page-title" style="margin-bottom:1.75rem;">Edit Jadwal</h1>

@if ($errors->any())
    <div class="alert alert-danger">
        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<form action="{{ route('admin.update_jadwal', $jadwal->id_jadwal) }}" method="POST">
    @csrf
    @method('PUT')
    
    {{-- Informasi Armada --}}
    <div class="form-section">
        <div class="form-section-title"><i class="ph ph-bus"></i> Informasi Armada</div>
        <div class="form-row">
            <div class="form-group-admin">
                <label>Pilih Bus</label>
                <div class="admin-input-icon-wrap">
                    <select name="bus_id" class="admin-input" style="border:none;padding:0;" required>
                        <option value="">Pilih Armada BusMania...</option>
                        @foreach($buses as $bus)
                            <option value="{{ $bus->bus_id }}" {{ $jadwal->bus_id === $bus->bus_id ? 'selected' : '' }}>{{ $bus->nama_bus }} ({{ $bus->no_polisi }})</option>
                        @endforeach
                    </select>
                </div>
                @error('bus_id')
                    <span style="color: red; font-size: 0.85rem;">{{ $message }}</span>
                @enderror
            </div>
        </div>
    </div>

    {{-- Detail Rute --}}
    <div class="form-section">
        <div class="form-section-title"><i class="ph ph-map-trifold"></i> Detail Rute Perjalanan</div>
        <div class="form-row">
            <div class="form-group-admin">
                <label>Pilih Rute</label>
                <div class="admin-input-icon-wrap">
                    <select name="rute_id" class="admin-input" style="border:none;padding:0;" required>
                        <option value="">Pilih Rute...</option>
                        @foreach($routes as $route)
                            <option value="{{ $route->rute_id }}" {{ $jadwal->rute_id === $route->rute_id ? 'selected' : '' }}>{{ $route->kota_asal }} → {{ $route->kota_tujuan }}</option>
                        @endforeach
                    </select>
                </div>
                @error('rute_id')
                    <span style="color: red; font-size: 0.85rem;">{{ $message }}</span>
                @enderror
            </div>
        </div>
    </div>

    {{-- Waktu & Harga --}}
    <div class="form-row">
        <div class="form-section">
            <div class="form-section-title"><i class="ph ph-clock"></i> Waktu Perjalanan</div>
            <div class="form-group-admin">
                <label>Tanggal Keberangkatan</label>
                <input type="date" name="tanggal_berangkat" value="{{ $jadwal->tanggal_berangkat }}" required>
                @error('tanggal_berangkat')
                    <span style="color: red; font-size: 0.85rem;">{{ $message }}</span>
                @enderror
            </div>
            <div class="form-group-admin">
                <label>Jam Keberangkatan</label>
                <input type="time" name="jam_berangkat" value="{{ $jadwal->jam_berangkat }}" required>
                @error('jam_berangkat')
                    <span style="color: red; font-size: 0.85rem;">{{ $message }}</span>
                @enderror
            </div>
        </div>

        <div class="form-section">
            <div class="form-section-title"><i class="ph ph-wallet"></i> Harga &amp; Kursi</div>
            <div class="form-group-admin">
                <label>Harga Tiket (Rp)</label>
                <div class="admin-input-icon-wrap">
                    <span style="font-weight:700;color:var(--text-muted);">Rp</span>
                    <input type="number" name="harga" value="{{ $jadwal->harga }}" min="0" required>
                </div>
                @error('harga')
                    <span style="color: red; font-size: 0.85rem;">{{ $message }}</span>
                @enderror
            </div>
            <div class="form-group-admin">
                <label>Total Kapasitas Kursi Tersedia</label>
                <div class="admin-input-icon-wrap">
                    <i class="ph ph-armchair"></i>
                    <input type="number" name="kursi_tersedia" value="{{ $jadwal->kursi_tersedia }}" min="1" required>
                </div>
                @error('kursi_tersedia')
                    <span style="color: red; font-size: 0.85rem;">{{ $message }}</span>
                @enderror
            </div>
        </div>
    </div>

    {{-- Status Jadwal --}}
    <div class="form-section">
        <div class="form-section-title"><i class="ph ph-info"></i> Status Jadwal</div>
        <div class="form-row">
            <div class="form-group-admin">
                <label>Status</label>
                <div class="admin-input-icon-wrap">
                    <select name="status_jadwal" class="admin-input" style="border:none;padding:0;" required>
                        <option value="">Pilih Status...</option>
                        <option value="aktif" {{ $jadwal->status_jadwal === 'aktif' ? 'selected' : '' }}>Aktif</option>
                        <option value="selesai" {{ $jadwal->status_jadwal === 'selesai' ? 'selected' : '' }}>Selesai</option>
                        <option value="batal" {{ $jadwal->status_jadwal === 'batal' ? 'selected' : '' }}>Batal</option>
                    </select>
                </div>
                @error('status_jadwal')
                    <span style="color: red; font-size: 0.85rem;">{{ $message }}</span>
                @enderror
            </div>
        </div>
    </div>

    {{-- Footer Actions --}}
    <div class="form-footer">
        <button type="button" class="btn-admin-cancel" onclick="history.back()"><i class="ph ph-x"></i> Batal</button>
        <button type="submit" class="btn-admin-save"><i class="ph ph-floppy-disk"></i> Simpan Perubahan</button>
    </div>
</form>
@endsection
