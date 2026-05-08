@extends('layouts.admin')
@section('title', 'Tambah Jadwal Baru - BusMania')

@section('content')
<h1 class="admin-page-title" style="margin-bottom:1.75rem;">Tambah Jadwal Baru</h1>

<form>
    {{-- Informasi Armada --}}
    <div class="form-section">
        <div class="form-section-title"><i class="ph ph-bus"></i> Informasi Armada</div>
        <div class="form-row">
            <div class="form-group-admin">
                <label>Pilih Bus</label>
                <div class="admin-input-icon-wrap">
                    <select class="admin-input" style="border:none;padding:0;">
                        <option>Pilih Armada LajuBus...</option>
                        <option>Laju Prima A1</option>
                        <option>Kencana Luxury 02</option>
                        <option>Agra Mas Jetbus</option>
                    </select>
                </div>
            </div>
            <div class="form-group-admin">
                <label>Kategori Layanan</label>
                <div class="admin-input-icon-wrap">
                    <select class="admin-input" style="border:none;padding:0;">
                        <option>Pilih Kategori...</option>
                        <option>Eksekutif</option>
                        <option>Ekonomi</option>
                        <option>Patas</option>
                    </select>
                </div>
            </div>
        </div>
    </div>

    {{-- Detail Rute --}}
    <div class="form-section">
        <div class="form-section-title"><i class="ph ph-map-trifold"></i> Detail Rute Perjalanan</div>
        <div class="form-row">
            <div class="form-group-admin">
                <label>Kota Keberangkatan</label>
                <div class="admin-input-icon-wrap">
                    <i class="ph ph-map-pin"></i>
                    <input type="text" placeholder="Contoh: Jakarta">
                </div>
            </div>
            <div class="form-group-admin">
                <label>Kota Tujuan</label>
                <div class="admin-input-icon-wrap">
                    <i class="ph ph-navigation-arrow"></i>
                    <input type="text" placeholder="Contoh: Surabaya">
                </div>
            </div>
        </div>
    </div>

    {{-- Waktu & Harga --}}
    <div class="form-row">
        <div class="form-section">
            <div class="form-section-title"><i class="ph ph-clock"></i> Waktu Perjalanan</div>
            <div class="form-group-admin">
                <label>Tanggal Keberangkatan</label>
                <input type="date" class="admin-input">
            </div>
            <div class="form-group-admin">
                <label>Jam Keberangkatan</label>
                <input type="time" class="admin-input">
            </div>
        </div>

        <div class="form-section">
            <div class="form-section-title"><i class="ph ph-wallet"></i> Harga &amp; Kursi</div>
            <div class="form-group-admin">
                <label>Harga Tiket (Rp)</label>
                <div class="admin-input-icon-wrap">
                    <span style="font-weight:700;color:var(--text-muted);">Rp</span>
                    <input type="number" placeholder="0" value="0">
                </div>
            </div>
            <div class="form-group-admin">
                <label>Total Kapasitas Kursi</label>
                <div class="admin-input-icon-wrap">
                    <i class="ph ph-armchair"></i>
                    <input type="number" placeholder="40" value="40">
                </div>
            </div>
        </div>
    </div>

    {{-- Fasilitas --}}
    <div class="form-section">
        <div class="form-section-title"><i class="ph ph-package"></i> Fasilitas Tersedia</div>
        <div class="facility-grid">
            <div class="facility-item selected">
                <div class="facility-item-icon"><i class="ph ph-snowflake"></i></div>
                <div class="facility-item-label">AC</div>
            </div>
            <div class="facility-item selected">
                <div class="facility-item-icon"><i class="ph ph-wifi-high"></i></div>
                <div class="facility-item-label">WiFi</div>
            </div>
            <div class="facility-item">
                <div class="facility-item-icon"><i class="ph ph-toilet"></i></div>
                <div class="facility-item-label">Toilet</div>
            </div>
            <div class="facility-item">
                <div class="facility-item-icon"><i class="ph ph-bed"></i></div>
                <div class="facility-item-label">Selimut</div>
            </div>
            <div class="facility-item">
                <div class="facility-item-icon"><i class="ph ph-fork-knife"></i></div>
                <div class="facility-item-label">Makan</div>
            </div>
        </div>
    </div>

    {{-- Footer Actions --}}
    <div class="form-footer">
        <button type="button" class="btn-admin-cancel" onclick="history.back()"><i class="ph ph-x"></i> Batal</button>
        <button type="submit" class="btn-admin-save"><i class="ph ph-floppy-disk"></i> Simpan Jadwal</button>
    </div>
</form>
@endsection
