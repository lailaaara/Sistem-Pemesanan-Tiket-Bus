@extends('layouts.app')

@section('title', 'Kelola Tiket Perjalanan - BusMania')

@section('content')
<div class="tickets-container">
    
    {{-- Header Section --}}
    <div class="tickets-header-section">
        <div>
            <h1 class="tickets-title">Kelola Tiket Perjalanan</h1>
            <p class="tickets-subtitle">Cek jadwal, status, dan riwayat pesanan tiket Anda.</p>
        </div>
        <div>
            <button class="btn btn-outline-alt"><i class="ph ph-clock-counter-clockwise"></i> Riwayat Perjalanan</button>
        </div>
    </div>

    {{-- Tickets List --}}
    <div class="tickets-list">
        {{-- Active Tickets --}}
        @foreach($activeTickets as $ticket)
        <div class="ticket-row-card">
            <div class="ticket-row-icon-wrap">
                <i class="ph ph-bus ticket-bus-icon"></i>
            </div>
            <div class="ticket-row-details">
                <div class="ticket-row-bus-info">
                    <span class="ticket-row-operator">{{ $ticket->nama_bus }}</span>
                    <span class="ticket-row-class">{{ $ticket->kelas }} • Kursi {{ $ticket->no_kursi }}</span>
                </div>
                <div class="ticket-row-route-wrap">
                    <div class="ticket-row-station">
                        <span class="ticket-row-city">{{ $ticket->kota_asal }}</span>
                        <span class="ticket-row-date">{{ $ticket->tanggal_berangkat }}, {{ $ticket->jam_berangkat }}</span>
                    </div>
                    <div class="ticket-row-arrow-wrap">
                        <div class="ticket-row-line"></div>
                        <i class="ph ph-arrow-right ticket-row-arrow-icon"></i>
                    </div>
                    <div class="ticket-row-station">
                        <span class="ticket-row-city">{{ $ticket->kota_tujuan }}</span>
                        <span class="ticket-row-date">{{ $ticket->jam_tiba }}</span>
                    </div>
                </div>
            </div>
            <div class="ticket-row-status-wrap">
                <span class="status-badge {{ $ticket->status_class }}">{{ $ticket->status }}</span>
                <a href="{{ route('booking.tickets_detail', $ticket->id) }}" class="btn btn-primary-alt">
                    Lihat Detail <i class="ph ph-arrow-right"></i>
                </a>
            </div>
        </div>
        @endforeach

        {{-- Past Tickets (Selesai) --}}
        @foreach($pastTickets as $ticket)
        <div class="ticket-row-card past-ticket-card">
            <div class="ticket-row-icon-wrap">
                <i class="ph ph-clock-counter-clockwise ticket-bus-icon"></i>
            </div>
            <div class="ticket-row-details">
                <div class="ticket-row-bus-info">
                    <span class="ticket-row-operator">{{ $ticket->nama_bus }}</span>
                    <span class="ticket-row-class">{{ $ticket->kelas }} • {{ $ticket->tanggal_berangkat }}</span>
                </div>
                <div class="ticket-row-route-wrap">
                    <div class="ticket-row-station text-muted-alt">
                        <span class="ticket-row-city">{{ $ticket->kota_asal }}</span>
                        <span class="ticket-row-date">{{ $ticket->jam_berangkat }}</span>
                    </div>
                    <div class="ticket-row-arrow-wrap text-muted-alt">
                        <div class="ticket-row-line"></div>
                        <i class="ph ph-arrow-right ticket-row-arrow-icon"></i>
                    </div>
                    <div class="ticket-row-station text-muted-alt">
                        <span class="ticket-row-city">{{ $ticket->kota_tujuan }}</span>
                        <span class="ticket-row-date">{{ $ticket->jam_tiba }}</span>
                    </div>
                </div>
            </div>
            <div class="ticket-row-status-wrap">
                <span class="status-badge {{ $ticket->status_class }}">{{ $ticket->status }}</span>
                <a href="{{ route('booking.tickets_detail', $ticket->id) }}" class="btn btn-outline-grey">
                    Detail Riwayat
                </a>
            </div>
        </div>
        @endforeach
    </div>

    {{-- Promo Banner --}}
    <div class="promo-banner-wrap">
        <div class="promo-banner">
            <div class="promo-badge">DISKON KHUSUS</div>
            <h2 class="promo-banner-title">Hemat 20% untuk Perjalanan Selanjutnya!</h2>
            <p class="promo-banner-desc">Gunakan kode PROMOSETIA saat memesan tiket di rute favoritmu.</p>
            <button class="btn btn-white">Klaim Promo</button>
        </div>
    </div>

</div>
@endsection
