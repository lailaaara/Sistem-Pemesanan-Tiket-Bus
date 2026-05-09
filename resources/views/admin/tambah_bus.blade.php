@extends('layouts.admin')
@section('title', 'Tambah Bus Baru - BusMania')

@section('content')
<h1 class="admin-page-title" style="margin-bottom:1.75rem;">Tambah Bus Baru</h1>

@if ($errors->any())
    <div class="alert alert-danger">
        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<form action="{{ route('admin.store_bus') }}" method="POST">
    @csrf
    {{-- Informasi Bus --}}
    <div class="form-section">
        <div class="form-section-title"><i class="ph ph-bus"></i> Informasi Armada Baru</div>
        <div class="form-row">
            <div class="form-group-admin">
                <label>Nomor Polisi</label>
                <div class="admin-input-icon-wrap">
                    <i class="ph ph-ticket"></i>
                    <input type="text" name="no_polisi" placeholder="Contoh: B 1234 ABC" required>
                </div>
                @error('no_polisi')
                    <span style="color: red; font-size: 0.85rem;">{{ $message }}</span>
                @enderror
            </div>
            <div class="form-group-admin">
                <label>Nama Bus / Armada</label>
                <div class="admin-input-icon-wrap">
                    <i class="ph ph-bus"></i>
                    <input type="text" name="nama_bus" placeholder="Contoh: Laju Prima A1" required>
                </div>
                @error('nama_bus')
                    <span style="color: red; font-size: 0.85rem;">{{ $message }}</span>
                @enderror
            </div>
        </div>
    </div>

    {{-- Spesifikasi Bus --}}
    <div class="form-section">
        <div class="form-section-title"><i class="ph ph-gear"></i> Spesifikasi Bus</div>
        <div class="form-row">
            <div class="form-group-admin">
                <label>Kapasitas Kursi</label>
                <div class="admin-input-icon-wrap">
                    <i class="ph ph-armchair"></i>
                    <input type="number" name="kapasitas" placeholder="45" value="45" min="1" required>
                </div>
                @error('kapasitas')
                    <span style="color: red; font-size: 0.85rem;">{{ $message }}</span>
                @enderror
            </div>
            <div class="form-group-admin">
                <label>Kategori Layanan</label>
                <div class="admin-input-icon-wrap">
                    <select name="kelas" class="admin-input" style="border:none;padding:0;" required>
                        <option value="">Pilih Kategori...</option>
                        <option value="Eksekutif">Eksekutif</option>
                        <option value="Ekonomi">Ekonomi</option>
                        <option value="Patas">Patas</option>
                    </select>
                </div>
                @error('kelas')
                    <span style="color: red; font-size: 0.85rem;">{{ $message }}</span>
                @enderror
            </div>
        </div>
    </div>

    {{-- Fasilitas --}}
    <div class="form-section">
        <div class="form-section-title"><i class="ph ph-package"></i> Fasilitas Tersedia</div>
        <div class="form-group-admin">
            <label>Daftar Fasilitas (pisahkan dengan koma)</label>
            <textarea name="fasilitas" class="admin-input" placeholder="Contoh: AC, WiFi, Toilet, Selimut, Makan" rows="3">AC, WiFi</textarea>
            @error('fasilitas')
                <span style="color: red; font-size: 0.85rem;">{{ $message }}</span>
            @enderror
        </div>
    </div>

    {{-- Footer Actions --}}
    <div class="form-footer">
        <button type="button" class="btn-admin-cancel" onclick="history.back()"><i class="ph ph-x"></i> Batal</button>
        <button type="submit" class="btn-admin-save"><i class="ph ph-floppy-disk"></i> Simpan Bus</button>
    </div>
</form>
@endsection
