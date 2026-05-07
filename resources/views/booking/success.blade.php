@extends('layouts.app')

@section('title', 'Pembayaran Berhasil - BusMania')

@section('content')
<div class="success-page">

    {{-- Icon Centang --}}
    <div class="success-icon-wrap">
        <div class="success-icon">
            <i class="ph ph-check-circle-fill"></i>
        </div>
    </div>

    <h1 class="success-title">Pembayaran Berhasil!</h1>
    <p class="success-subtitle">E-Tiket Anda telah diterbitkan dan siap digunakan.</p>

    {{-- E-Ticket Card --}}
    <div class="eticket-card">
        <div class="eticket-header">
            <div class="eticket-brand">
                <div class="eticket-brand-name">BusMania</div>
                <div class="eticket-bus-name">{{ $booking->nama_bus }}</div>
            </div>
            <div class="eticket-booking-code">
                <div class="eticket-label">Kode Booking</div>
                <div class="eticket-code">{{ $booking->kode_booking }}</div>
            </div>
        </div>

        <div class="eticket-body">
            <div class="eticket-row">
                <div class="eticket-info-item">
                    <span class="eticket-info-label">Penumpang</span>
                    <span class="eticket-info-value">{{ $booking->nama_penumpang }}</span>
                </div>
                <div class="eticket-info-item text-right">
                    <span class="eticket-info-label">Nomor Kursi</span>
                    <span class="eticket-info-value">{{ $booking->no_kursi }}</span>
                </div>
            </div>

            <div class="eticket-route">
                <div class="eticket-city-block">
                    <span class="eticket-city-label">Dari</span>
                    <span class="eticket-city-name">{{ $booking->kota_asal }}</span>
                    <span class="eticket-terminal">{{ $booking->terminal_asal }}</span>
                </div>
                <div class="eticket-bus-icon">
                    <i class="ph ph-bus-fill"></i>
                </div>
                <div class="eticket-city-block text-right">
                    <span class="eticket-city-label">Ke</span>
                    <span class="eticket-city-name">{{ $booking->kota_tujuan }}</span>
                    <span class="eticket-terminal">{{ $booking->terminal_tujuan }}</span>
                </div>
            </div>

            <div class="eticket-separator">
                <span class="notch left"></span>
                <div class="dashed-line"></div>
                <span class="notch right"></span>
            </div>

            <div class="eticket-footer-info">
                <span class="eticket-info-label">Waktu Keberangkatan</span>
                <div class="eticket-departure-time">
                    <i class="ph ph-calendar-blank"></i>
                    <span>{{ $booking->tanggal }}</span>
                </div>
            </div>
        </div>
    </div>

    {{-- Info Note --}}
    <div class="info-note">
        <i class="ph ph-info"></i>
        <p>Penting: Mohon tiba di terminal minimal 30 menit sebelum jadwal keberangkatan untuk proses validasi tiket dan bagasi.</p>
    </div>

    {{-- Action Buttons --}}
    <div class="success-actions">
        <button class="btn btn-primary btn-block" onclick="window.print()">
            <i class="ph ph-download-simple"></i> Unduh Tiket (PDF)
        </button>
        <a href="/" class="btn btn-outline btn-block">
            <i class="ph ph-house"></i> Kembali ke Beranda
        </a>
    </div>
</div>
@endsection
