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

    {{-- Alerts for success/error notifications --}}
    @if(session('success'))
    <div style="background: #e6f7f0; border: 1px solid rgba(10, 79, 59, 0.15); color: var(--primary); border-radius: 12px; padding: 1rem 1.5rem; margin-bottom: 1.5rem; display: flex; align-items: center; gap: 0.75rem; font-weight: 600; font-size: 0.9rem;">
        <i class="ph ph-check-circle" style="font-size: 1.25rem;"></i>
        {{ session('success') }}
    </div>
    @endif

    @if(session('error'))
    <div style="background: #fef2f2; border: 1px solid #fecaca; color: #dc2626; border-radius: 12px; padding: 1rem 1.5rem; margin-bottom: 1.5rem; display: flex; align-items: center; gap: 0.75rem; font-weight: 600; font-size: 0.9rem;">
        <i class="ph ph-warning-circle" style="font-size: 1.25rem;"></i>
        {{ session('error') }}
    </div>
    @endif

    {{-- Pencarian Tiket Berdasarkan Nomor HP --}}
    <div style="background: var(--surface); border: 1px solid var(--border); border-radius: 16px; padding: 1.75rem; margin-bottom: 2rem; box-shadow: 0 4px 12px rgba(0, 0, 0, 0.01);">
        <form action="{{ route('booking.tickets_search') }}" method="POST" style="display: flex; flex-wrap: wrap; gap: 1.25rem; align-items: center; justify-content: space-between;">
            @csrf
            <div style="display: flex; align-items: center; gap: 1rem;">
                <div style="width: 44px; height: 44px; border-radius: 10px; background: rgba(10, 79, 59, 0.06); display: flex; align-items: center; justify-content: center; color: var(--primary); font-size: 1.25rem;">
                    <i class="ph ph-magnifying-glass"></i>
                </div>
                <div>
                    <h3 style="font-size: 1rem; font-weight: 800; color: var(--text-main); margin-bottom: 0.15rem; text-align: left;">Cari Tiket Anda</h3>
                    <p style="font-size: 0.8rem; color: var(--text-muted); margin: 0; text-align: left;">Masukkan Nomor HP yang digunakan saat pemesanan untuk melacak tiket Anda.</p>
                </div>
            </div>
            <div style="display: flex; gap: 0.75rem; flex: 1; max-width: 480px; align-items: center;">
                <div class="modal-input-wrapper" style="background: var(--background); flex: 1; border-radius: 10px; margin: 0; padding: 0.55rem 0.9rem;">
                    <i class="ph ph-phone modal-input-icon"></i>
                    <input type="text" name="no_hp" placeholder="Contoh: 08123456789" required class="modal-input" style="background: transparent;">
                </div>
                <button type="submit" class="btn btn-primary-alt" style="height: 40px; padding: 0 1.5rem; font-size: 0.85rem; border-radius: 10px; display: inline-flex; align-items: center; gap: 0.5rem; font-weight: 700;">
                    <i class="ph ph-magnifying-glass"></i> Cari Tiket
                </button>
            </div>
        </form>
    </div>

    {{-- Tickets List --}}
    @if(count($activeTickets) > 0 || count($pastTickets) > 0)
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
                        <span class="ticket-row-date">{{ $ticket->tanggal_berangkat_formatted }}, {{ $ticket->jam_berangkat_formatted }}</span>
                    </div>
                    <div class="ticket-row-arrow-wrap">
                        <div class="ticket-row-line"></div>
                        <i class="ph ph-arrow-right ticket-row-arrow-icon"></i>
                    </div>
                    <div class="ticket-row-station">
                        <span class="ticket-row-city">{{ $ticket->kota_tujuan }}</span>
                        <span class="ticket-row-date">{{ $ticket->jam_tiba_formatted }}</span>
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
                    <span class="ticket-row-class">{{ $ticket->kelas }} • Kursi {{ $ticket->no_kursi }}</span>
                </div>
                <div class="ticket-row-route-wrap">
                    <div class="ticket-row-station text-muted-alt">
                        <span class="ticket-row-city">{{ $ticket->kota_asal }}</span>
                        <span class="ticket-row-date">{{ $ticket->tanggal_berangkat_formatted }}, {{ $ticket->jam_berangkat_formatted }}</span>
                    </div>
                    <div class="ticket-row-arrow-wrap text-muted-alt">
                        <div class="ticket-row-line"></div>
                        <i class="ph ph-arrow-right ticket-row-arrow-icon"></i>
                    </div>
                    <div class="ticket-row-station text-muted-alt">
                        <span class="ticket-row-city">{{ $ticket->kota_tujuan }}</span>
                        <span class="ticket-row-date">{{ $ticket->jam_tiba_formatted }}</span>
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
    @else
    {{-- Empty State --}}
    <div style="text-align: center; padding: 4.5rem 2rem; background: var(--surface); border: 1px solid var(--border); border-radius: 20px; box-shadow: 0 4px 20px rgba(0, 0, 0, 0.01); margin-bottom: 3rem;">
        <div style="width: 80px; height: 80px; border-radius: 50%; background: #f4faf8; display: inline-flex; align-items: center; justify-content: center; color: var(--primary); font-size: 2.5rem; margin-bottom: 1.5rem;">
            <i class="ph ph-ticket"></i>
        </div>
        <h3 style="font-size: 1.3rem; font-weight: 800; color: var(--text-main); margin-bottom: 0.5rem;">Belum Ada Tiket yang Terdaftar</h3>
        <p style="color: var(--text-muted); font-size: 0.95rem; max-width: 500px; margin: 0 auto 1.75rem; line-height: 1.6;">
            Anda belum melakukan pemesanan tiket pada browser ini atau sesi Anda telah berakhir. Masukkan Nomor HP Anda pada form pencarian di atas untuk memuat tiket Anda secara otomatis.
        </p>
        <a href="/" class="btn btn-primary-alt" style="display: inline-flex; margin: 0 auto; padding: 0.75rem 1.75rem; border-radius: 10px; font-weight: 700; gap: 0.5rem; text-decoration: none;">
            <i class="ph ph-compass"></i> Cari Rute Perjalanan Baru
        </a>
    </div>
    @endif

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
