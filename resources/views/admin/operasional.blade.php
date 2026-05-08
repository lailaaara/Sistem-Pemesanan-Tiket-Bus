@extends('layouts.admin')
@section('title', 'Manajemen Operasional - BusMania')

@section('content')
<div class="admin-page-header">
    <div>
        <h1 class="admin-page-title">Manajemen Operasional</h1>
        <p class="admin-page-subtitle">Kelola seluruh jadwal keberangkatan bus, pengaturan armada, dan pemantauan status operasional harian armada LajuBus secara real-time.</p>
    </div>
    <div class="admin-header-actions">
        <a href="{{ route('admin.tambah_jadwal') }}" class="btn-admin-save"><i class="ph ph-plus"></i> Tambah Jadwal Baru</a>
    </div>
</div>

{{-- Status Armada Cards --}}
<div class="ops-top-row">
    <div class="ops-status-card">
        <div class="ops-status-header">
            <div class="ops-status-title"><i class="ph ph-truck"></i> Status Armada Hari Ini</div>
            <span class="ops-live-badge">LIVE TRACKING</span>
        </div>
        <div class="ops-status-grid">
            @foreach($statusArmada as $s)
            <div class="ops-metric">
                <div class="ops-metric-label {{ $s->color }}">● {{ $s->label }}</div>
                <div class="ops-metric-value">{{ $s->value }}</div>
                <div class="ops-metric-sub">{{ $s->change }}</div>
            </div>
            @endforeach
        </div>
    </div>
    <div class="ops-ticket-card">
        <div class="ops-ticket-icon"><i class="ph ph-ticket"></i></div>
        <div class="ops-ticket-label">Tiket Terjual</div>
        <div class="ops-ticket-sub">Update real-time aplikasi</div>
        <div class="ops-ticket-value">1,248</div>
        <div class="ops-ticket-unit">TOTAL TIKET HARI INI</div>
    </div>
</div>

{{-- Filters --}}
<div class="filter-row">
    <button class="filter-btn"><i class="ph ph-calendar"></i> Semua Tanggal <i class="ph ph-caret-down"></i></button>
    <button class="filter-btn"><i class="ph ph-funnel"></i> Semua Status <i class="ph ph-caret-down"></i></button>
</div>

{{-- Jadwal Table --}}
<div class="admin-card">
    <table class="admin-table">
        <thead>
            <tr>
                <th>ID Jadwal</th>
                <th>Armada</th>
                <th>Rute Perjalanan</th>
                <th>Waktu Keberangkatan</th>
                <th>Kapasitas</th>
                <th>Status</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            @foreach($jadwalList as $j)
            @php
                $pct = round(($j->kapasitas_terisi / $j->kapasitas_total) * 100);
                $barColor = $pct >= 90 ? 'yellow' : ($j->status === 'DIBATALKAN' ? 'red' : 'green');
                $badgeClass = $j->status === 'AKTIF' ? 'badge-success' : ($j->status === 'SELESAI' ? 'badge-done' : 'badge-failed');
            @endphp
            <tr>
                <td class="td-id">#{{ $j->id }}</td>
                <td>
                    <div style="display:flex;align-items:center;gap:0.5rem;">
                        <i class="ph ph-bus" style="font-size:1.1rem;color:var(--primary);"></i>
                        <span class="td-armada">{{ $j->armada }}</span>
                    </div>
                </td>
                <td>{{ $j->rute }}</td>
                <td>{{ $j->waktu }}</td>
                <td>
                    <div class="capacity-bar-wrap">
                        <div class="capacity-bar">
                            <div class="capacity-bar-fill {{ $barColor }}" style="width:{{ $pct }}%"></div>
                        </div>
                        <span class="capacity-text">{{ str_pad($j->kapasitas_terisi, 2, '0', STR_PAD_LEFT) }}/{{ $j->kapasitas_total }}</span>
                    </div>
                </td>
                <td><span class="badge {{ $badgeClass }}">{{ $j->status }}</span></td>
                <td><a href="#" class="action-link">Lihat Detail</a></td>
            </tr>
            @endforeach
        </tbody>
    </table>
    <div class="table-footer">
        <span>Menampilkan 8 dari 124 jadwal</span>
        <div class="pagination">
            <a href="#" class="page-btn"><i class="ph ph-caret-left"></i></a>
            <a href="#" class="page-btn active">1</a>
            <a href="#" class="page-btn">2</a>
            <a href="#" class="page-btn">3</a>
            <a href="#" class="page-btn"><i class="ph ph-caret-right"></i></a>
        </div>
    </div>
</div>

{{-- Info Banner --}}
<div class="info-banner">
    <div>
        <div class="info-banner-icon"><i class="ph ph-arrows-clockwise"></i></div>
        <div class="info-banner-title">Informasi Sinkronisasi</div>
        <div class="info-banner-desc">Data jadwal terakhir diperbarui pada 2 Mei 2026 pukul 14:22 WIB. Seluruh perubahan pada status armada akan disinkronkan langsung ke pelanggan.</div>
    </div>
    <div style="position:relative;">
        <img src="{{ asset('images/hero_bus_bg.png') }}" class="info-banner-img" alt="BusMania Operational">
        <div class="info-banner-img-label">
            <strong>BusMania Operational</strong><br>
            <span style="font-size:0.72rem;opacity:0.8;">Mengutamakan Kepercayaan & Kenyamanan Perjalanan</span>
        </div>
    </div>
</div>
@endsection
