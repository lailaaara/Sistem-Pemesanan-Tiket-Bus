@extends('layouts.app')

@section('title', 'Detail Tiket #' . $ticket->kode_booking . ' - BusMania')

@section('content')
<div class="tickets-container">

    {{-- Back Link --}}
    <div class="back-link-wrap">
        <a href="{{ route('booking.tickets') }}" class="back-link">
            <i class="ph ph-arrow-left"></i> Kembali ke Tiket Saya
        </a>
    </div>

    <div class="ticket-detail-grid">
        {{-- Main Ticket Details Area --}}
        <div class="ticket-detail-main">
            {{-- Green Top Bar / Header Card --}}
            <div class="ticket-detail-card">
                <div class="ticket-detail-green-header">
                    <div>
                        <div class="ticket-detail-code-label">KODE BOOKING</div>
                        <div class="ticket-detail-code">{{ $ticket->kode_booking }}</div>
                    </div>
                    <div class="ticket-detail-status">
                        <i class="ph ph-clock"></i> {{ $ticket->status }}
                    </div>
                </div>

                {{-- Journey Details --}}
                <div class="ticket-detail-body">
                    <h3 class="ticket-section-title"><i class="ph ph-map-trifold"></i> Detail Perjalanan</h3>
                    
                    <div class="journey-row-wrap">
                        {{-- Timeline --}}
                        <div class="journey-timeline">
                            <div class="journey-timeline-item">
                                <div class="timeline-dot dot-active"></div>
                                <div class="timeline-content">
                                    <div class="timeline-time">{{ $ticket->jam_berangkat }}</div>
                                    <div class="timeline-date">{{ $ticket->tanggal_berangkat }}</div>
                                    <div class="timeline-terminal">{{ $ticket->terminal_asal }}</div>
                                </div>
                            </div>
                            <div class="journey-timeline-item">
                                <div class="timeline-dot"></div>
                                <div class="timeline-content">
                                    <div class="timeline-time">{{ $ticket->jam_tiba }}</div>
                                    <div class="timeline-date">{{ $ticket->tanggal_tiba }}</div>
                                    <div class="timeline-terminal">{{ $ticket->terminal_tujuan }}</div>
                                </div>
                            </div>
                        </div>

                        {{-- Operator Info Card --}}
                        <div class="operator-info-card">
                            <div class="operator-card-label">OPERATOR &amp; KELAS</div>
                            <div class="operator-card-title">{{ $ticket->nama_bus }}</div>
                            <div class="operator-card-busno">Nomor Bus: {{ $ticket->no_bus }}</div>
                            
                            <div class="operator-card-divider"></div>
                            
                            <div class="operator-card-label">FASILITAS</div>
                            <div class="facilities-wrap">
                                @foreach($ticket->fasilitas as $facility)
                                <span class="facility-pill">
                                    @if($facility == 'AC')
                                        <i class="ph ph-wind"></i>
                                    @elseif($facility == 'Wiai')
                                        <i class="ph ph-wifi-high"></i>
                                    @else
                                        <i class="ph ph-chair"></i>
                                    @endif
                                    {{ $facility }}
                                </span>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Passengers Information Table --}}
                <div class="ticket-passengers-section">
                    <h3 class="ticket-section-title"><i class="ph ph-users"></i> Informasi Penumpang</h3>
                    <table class="passenger-table">
                        <thead>
                            <tr>
                                <th>Nama Penumpang</th>
                                <th>No. Kursi</th>
                                <th>Tipe Bus</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($ticket->penumpang as $penumpang)
                            <tr>
                                <td class="p-name">{{ $penumpang->nama }}</td>
                                <td class="p-seat"><span>{{ $penumpang->no_kursi }}</span></td>
                                <td class="p-type">{{ $penumpang->tipe_bus }}</td>
                                <td class="p-status"><span><i class="ph ph-check-circle"></i> {{ $penumpang->status }}</span></td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                {{-- Download E-ticket PDF Footer --}}
                <div class="ticket-detail-footer">
                    <button class="btn btn-primary-alt"><i class="ph ph-download"></i> Download E-Tiket (PDF)</button>
                </div>
            </div>
        </div>

        {{-- Sidebar Area --}}
        <div class="ticket-detail-sidebar">
            {{-- Payment Card --}}
            <div class="sidebar-card">
                <h4 class="sidebar-card-title">Rincian Pembayaran</h4>
                <div class="payment-row">
                    <span>Harga Tiket (1x)</span>
                    <span class="payment-val">Rp {{ number_format($ticket->harga_tiket, 0, ',', '.') }}</span>
                </div>
                <div class="payment-row">
                    <span>Biaya Layanan</span>
                    <span class="payment-val">Rp {{ number_format($ticket->biaya_layanan, 0, ',', '.') }}</span>
                </div>
                <div class="payment-divider"></div>
                <div class="payment-row total">
                    <span>Total Bayar</span>
                    <span class="payment-val total">Rp {{ number_format($ticket->total_bayar, 0, ',', '.') }}</span>
                </div>
                <div class="payment-success-msg">
                    <i class="ph ph-check-circle"></i>
                    <div>{{ $ticket->info_pembayaran }}</div>
                </div>
            </div>

            {{-- Google Maps Card --}}
            <div class="sidebar-card pad-none">
                <div class="map-placeholder">
                    <img src="{{ asset('images/hero_bus_bg.png') }}" class="map-img" alt="Map View">
                    <div class="map-marker"><i class="ph ph-map-pin-fill"></i></div>
                </div>
                <div class="map-body">
                    <h5 class="map-title">Terminal Pulo Gebang</h5>
                    <p class="map-desc">Pulo Gebang, Kec. Cakung, Kota Jakarta Timur, Daerah Khusus Ibukota Jakarta 13950</p>
                    <a href="https://maps.google.com" target="_blank" class="btn btn-outline-full">Lihat di Google Maps</a>
                </div>
            </div>

            {{-- Help Card --}}
            <div class="sidebar-card help-card">
                <h4 class="help-card-title">Butuh Bantuan?</h4>
                <p class="help-card-desc">Kami siap membantu perjalanan Anda 24/7.</p>
                <a href="https://wa.me/628123456789" target="_blank" class="help-card-link">
                    Hubungi CS via WhatsApp <i class="ph ph-arrow-square-out"></i>
                </a>
            </div>
        </div>
    </div>

</div>
@endsection
