@extends('layouts.admin')
@section('title', 'Dashboard Admin - BusMania')

@section('content')
<div class="admin-page-header">
    <div>
        <h1 class="admin-page-title">Ringkasan Dashboard</h1>
        <p class="admin-page-subtitle">Pantau performa operasional BusMania hari ini.</p>
    </div>
    <div class="admin-header-actions">
        <button class="filter-btn"><i class="ph ph-calendar"></i> Terakhir 30 Hari</button>
        <a href="{{ route('admin.laporan') }}" class="btn btn-primary-alt">Lihat Laporan</a>
    </div>
</div>

{{-- Stat Cards --}}
<div class="stat-cards">
    @foreach($stats as $s)
    <div class="stat-card">
        <div class="stat-card-top">
            <span class="stat-icon {{ $s->color }}"><i class="ph {{ $s->icon }}"></i></span>
            <span class="stat-change {{ $s->up === true ? 'up' : ($s->up === false ? 'down' : 'neutral') }}">
                @if($s->up === true) ↗ @elseif($s->up === false) ↘ @endif
                {{ $s->change }}
            </span>
        </div>
        <div class="stat-label">{{ $s->label }}</div>
        <div class="stat-value">{{ $s->value }}</div>
    </div>
    @endforeach
</div>

{{-- Chart Card --}}
<div class="admin-card">
    <div class="admin-card-header">
        <div>
            <div class="admin-card-title">Tren Penjualan Tiket Mingguan</div>
            <div class="admin-card-subtitle">Statistik volume transaksi 7 hari terakhir</div>
        </div>
        <div class="chart-legend">
            <span class="legend-item"><span class="legend-dot green"></span> Eksekutif</span>
            <span class="legend-item"><span class="legend-dot grey"></span> Ekonomi</span>
        </div>
    </div>
    <div class="chart-bars">
        @foreach($chartData as $bar)
        <div class="chart-bar-group">
            <div class="chart-bar-wrap">
                <div class="chart-bar eksekutif" style="height: {{ $bar['eksekutif'] * 2 }}px;"></div>
                <div class="chart-bar ekonomi" style="height: {{ $bar['ekonomi'] * 2 }}px;"></div>
            </div>
            <span class="chart-bar-label {{ $bar['day'] === 'RAB' ? 'active' : '' }}">{{ $bar['day'] }}</span>
        </div>
        @endforeach
    </div>
</div>

{{-- Recent Transactions Table --}}
<div class="admin-card">
    <div class="admin-card-header">
        <div>
            <div class="admin-card-title">Transaksi Terbaru</div>
            <div class="admin-card-subtitle">Daftar pesanan tiket yang masuk hari ini</div>
        </div>
        <a href="{{ route('admin.transaksi') }}" class="action-link">Lihat Semua Transaksi</a>
    </div>
    <table class="admin-table">
        <thead>
            <tr>
                <th>Nama Penumpang</th>
                <th>Rute</th>
                <th>Tanggal & Jam</th>
                <th>Metode</th>
                <th>Status</th>
                <th style="text-align:right">Total</th>
            </tr>
        </thead>
        <tbody>
            @foreach($recentTransactions as $t)
            <tr>
                <td class="td-name">{{ $t->nama }}</td>
                <td>
                    <div class="td-rute">{{ $t->rute }}</div>
                    <div class="td-rute-sub">{{ $t->kelas }}</div>
                </td>
                <td>
                    <div>{{ $t->tanggal }}</div>
                    <div class="td-rute-sub">{{ $t->jam }}</div>
                </td>
                <td>{{ $t->metode }}</td>
                <td>
                    <span class="badge {{ $t->status === 'Berhasil' ? 'badge-success' : ($t->status === 'Pending' ? 'badge-pending' : 'badge-failed') }}">{{ $t->status }}</span>
                </td>
                <td class="td-total">Rp {{ number_format($t->total, 0, ',', '.') }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection
