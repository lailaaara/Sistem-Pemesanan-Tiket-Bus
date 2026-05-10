@extends('layouts.admin')
@section('title', 'Edit Rute - BusMania')

@section('content')
<h1 class="admin-page-title" style="margin-bottom:1.75rem;">Edit Rute</h1>

@if ($errors->any())
    <div class="alert alert-danger">
        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<form action="{{ route('admin.update_rute', $rute->rute_id) }}" method="POST">
    @csrf
    @method('PUT')
    
    {{-- Informasi Rute --}}
    <div class="form-section">
        <div class="form-section-title"><i class="ph ph-map-pin"></i> Informasi Rute Perjalanan</div>
        <div class="form-row">
            <div class="form-group-admin">
                <label>Kota Asal</label>
                <div class="admin-input-icon-wrap">
                    <i class="ph ph-map-pin"></i>
                    <input type="text" name="kota_asal" value="{{ $rute->kota_asal }}" required>
                </div>
                @error('kota_asal')
                    <span style="color: red; font-size: 0.85rem;">{{ $message }}</span>
                @enderror
            </div>
            <div class="form-group-admin">
                <label>Kota Tujuan</label>
                <div class="admin-input-icon-wrap">
                    <i class="ph ph-map-trifold"></i>
                    <input type="text" name="kota_tujuan" value="{{ $rute->kota_tujuan }}" required>
                </div>
                @error('kota_tujuan')
                    <span style="color: red; font-size: 0.85rem;">{{ $message }}</span>
                @enderror
            </div>
        </div>
    </div>

    {{-- Jarak Tempuh --}}
    <div class="form-section">
        <div class="form-section-title"><i class="ph ph-path"></i> Detail Jarak</div>
        <div class="form-row">
            <div class="form-group-admin">
                <label>Jarak Tempuh (km)</label>
                <div class="admin-input-icon-wrap">
                    <i class="ph ph-ruler"></i>
                    <input type="number" name="jarak_km" value="{{ $rute->jarak_km }}" min="1" required>
                </div>
                @error('jarak_km')
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
