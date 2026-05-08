@extends('layouts.admin')
@section('title', 'Transaksi - BusMania')
@section('searchPlaceholder', 'Cari transaksi...')

@section('content')
{{-- Summary Cards --}}
<div class="trx-summary">
    <div class="trx-summary-card">
        <div class="trx-summary-icon"><i class="ph ph-wallet"></i></div>
        <div>
            <div class="trx-summary-label">TOTAL PENDAPATAN</div>
            <div class="trx-summary-value">Rp {{ number_format($totalPendapatan, 0, ',', '.') }}</div>
            <div class="trx-summary-change">↗ Total seluruhnya</div>
        </div>
    </div>
    <div class="trx-summary-card">
        <div class="trx-summary-icon"><i class="ph ph-ticket"></i></div>
        <div>
            <div class="trx-summary-label">TIKET TERJUAL</div>
            <div class="trx-summary-value">{{ number_format($tiketTerjual, 0, ',', '.') }} Tiket</div>
            <div class="trx-summary-sub"><i class="ph ph-calendar"></i> Update Real-time</div>
        </div>
    </div>
</div>

{{-- Transaction Table --}}
<div class="admin-card">
    <div class="admin-card-header">
        <div>
            <div class="admin-card-title">Transaksi Terbaru</div>
            <div class="admin-card-subtitle">Kelola dan pantau penjualan tiket bus terbaru Anda.</div>
        </div>
        <div style="display:flex;gap:0.75rem;">
            <button class="filter-btn">Semua Tanggal <i class="ph ph-caret-down"></i></button>
            <button class="filter-btn">Semua Status <i class="ph ph-caret-down"></i></button>
        </div>
    </div>
    <table class="admin-table">
        <thead>
            <tr>
                <th>ID Transaksi</th>
                <th>Nama</th>
                <th>Rute</th>
                <th>Tanggal</th>
                <th>Status</th>
                <th style="text-align:right">Total Harga</th>
            </tr>
        </thead>
        <tbody>
            @foreach($transaksiList as $t)
            @php
                $badgeClass = $t->status === 'BERHASIL' ? 'badge-success' : ($t->status === 'MENUNGGU' ? 'badge-pending' : 'badge-failed');
            @endphp
            <tr>
                <td class="td-id">{{ $t->id }}</td>
                <td class="td-name">{{ $t->nama }}</td>
                <td class="td-rute">{{ $t->rute }}</td>
                <td>{{ $t->tanggal }}</td>
                <td><span class="badge {{ $badgeClass }}">{{ $t->status }}</span></td>
                <td class="td-total">Rp {{ number_format($t->total, 0, ',', '.') }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
    <div class="table-footer">
        <span>MENAMPILKAN <strong>1 SAMPAI 5</strong> DARI <strong>1.248 ENTRI</strong></span>
        <div class="pagination">
            <a href="#" class="page-btn"><i class="ph ph-caret-left"></i></a>
            <a href="#" class="page-btn active">1</a>
            <a href="#" class="page-btn">2</a>
            <a href="#" class="page-btn">3</a>
            <span class="page-dots">...</span>
            <a href="#" class="page-btn">42</a>
            <a href="#" class="page-btn"><i class="ph ph-caret-right"></i></a>
        </div>
    </div>
</div>
@endsection
