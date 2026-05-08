@extends('layouts.admin')
@section('title', 'Laporan - BusMania')
@section('searchPlaceholder', 'Cari transaksi...')

@section('content')
{{-- Summary Stats --}}
<div class="laporan-stats">
    <div class="laporan-stat-card">
        <div class="laporan-stat-icon" style="background:#e6f7f0;color:var(--primary);"><i class="ph ph-wallet"></i></div>
        <div class="laporan-stat-label">Total Pendapatan</div>
        <div class="laporan-stat-value">Rp 482.500.000</div>
        <div class="laporan-stat-change up" style="color:var(--primary);">↗ 12.5% dari bulan lalu</div>
    </div>
    <div class="laporan-stat-card">
        <div class="laporan-stat-icon" style="background:#e6f0ff;color:#3b82f6;"><i class="ph ph-receipt"></i></div>
        <div class="laporan-stat-label">Total Transaksi</div>
        <div class="laporan-stat-value">3,248</div>
        <div class="laporan-stat-change up" style="color:var(--primary);">↗ 8.2% dari bulan lalu</div>
    </div>
    <div class="laporan-stat-card">
        <div class="laporan-stat-icon" style="background:#fef3e6;color:#f59e0b;"><i class="ph ph-ticket"></i></div>
        <div class="laporan-stat-label">Total Tiket Terjual</div>
        <div class="laporan-stat-value">12,890</div>
        <div class="laporan-stat-change down" style="color:#ef4444;">↘ 2.4% dari bulan lalu</div>
    </div>
</div>

{{-- Daily Data Table --}}
<div class="admin-card">
    <div class="admin-card-header">
        <div>
            <div class="admin-card-title">Ringkasan Data Harian</div>
            <div class="admin-card-subtitle">Data dikumpulkan berdasarkan rentang waktu terpilih</div>
        </div>
        <button class="filter-btn"><i class="ph ph-calendar"></i> Semua Tanggal <i class="ph ph-caret-down"></i></button>
    </div>
    <table class="admin-table">
        <thead>
            <tr>
                <th>Tanggal</th>
                <th>Jumlah Transaksi</th>
                <th>Jumlah Tiket Terjual</th>
                <th style="text-align:right">Total Pendapatan</th>
            </tr>
        </thead>
        <tbody>
            @foreach($laporanList as $l)
            <tr>
                <td>
                    <div class="td-name">{{ $l->tanggal }}</div>
                    <div class="td-rute-sub">{{ $l->hari }}</div>
                </td>
                <td>
                    <span class="badge badge-success" style="margin-right:0.5rem;">{{ $l->jml_transaksi }}</span>
                    <span style="font-size:0.78rem;font-weight:700;color:{{ $l->up ? 'var(--primary)' : '#ef4444' }};">{{ $l->perubahan }}</span>
                </td>
                <td>{{ $l->jml_tiket }} Tiket</td>
                <td class="td-total">Rp {{ number_format($l->total, 0, ',', '.') }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
    <div class="table-footer">
        <span>Menampilkan 4 dari 31 hari</span>
        <div class="pagination">
            <a href="#" class="page-btn"><i class="ph ph-caret-left"></i></a>
            <a href="#" class="page-btn active">1</a>
            <a href="#" class="page-btn">2</a>
            <a href="#" class="page-btn">3</a>
            <a href="#" class="page-btn"><i class="ph ph-caret-right"></i></a>
        </div>
    </div>
</div>
@endsection
