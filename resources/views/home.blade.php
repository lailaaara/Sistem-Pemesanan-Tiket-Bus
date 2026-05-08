@extends('layouts.app')

@section('title', 'BusMania - Pesan Tiket Bus Online')

@section('content')

    {{-- Hero Section --}}
    <section class="hero">
        <div class="hero-content">
            <h1 class="hero-title">TEMPAT PEMESANAN BUS NOMOR 1 DI INDONESIA</h1>
        </div>

        {{-- Search Widget --}}
        <div class="search-widget">
            <form action="/search" method="GET" class="search-form" id="searchForm">
                <div class="form-group">
                    <label for="from">Dari</label>
                    <div class="input-wrapper">
                        <i class="ph ph-map-pin input-icon"></i>
                        <select name="from" id="from" class="form-control" required>
                            <option value="" disabled selected>Pilih Kota Asal</option>
                            @foreach($locations as $kota)
                                <option value="{{ $kota }}">{{ $kota }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="form-group">
                    <label for="to">Ke</label>
                    <div class="input-wrapper">
                        <i class="ph ph-map-pin-line input-icon"></i>
                        <select name="to" id="to" class="form-control" required>
                            <option value="" disabled selected>Pilih Kota Tujuan</option>
                            @foreach($locations as $kota)
                                <option value="{{ $kota }}">{{ $kota }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="form-group">
                    <label for="date">Tanggal</label>
                    <div class="input-wrapper">
                        <i class="ph ph-calendar-blank input-icon"></i>
                        <input type="date" name="date" id="date" class="form-control" required>
                    </div>
                </div>

                <div class="form-group">
                    <label for="passenger_count">Penumpang</label>
                    <div class="input-wrapper">
                        <i class="ph ph-users input-icon"></i>
                        <select name="passenger_count" id="passenger_count" class="form-control">
                            @for($i=1; $i<=5; $i++)
                                <option value="{{ $i }}">{{ $i }} Penumpang</option>
                            @endfor
                        </select>
                    </div>
                </div>

                <button type="submit" class="btn btn-primary btn-search">
                    <i class="ph ph-magnifying-glass"></i> Cari Tiket
                </button>
            </form>
        </div>
    </section>

    {{-- Popular Routes --}}
    <section class="section" id="rute">
        <div class="section-header">
            <div>
                <h2 class="section-title">Rute Terpopuler</h2>
                <p class="section-subtitle">Pilihan rute yang paling sering dipesan oleh traveler.</p>
            </div>
            <a href="#" class="section-header-link">Lihat Semua Rute →</a>
        </div>

        <div class="routes-bento">
            {{-- Large Card: Jakarta — Bandung --}}
            <div class="route-card large">
                <img src="{{ asset('images/rute_jakarta_bandung.png') }}" alt="Jakarta - Bandung" class="route-img">
                <div class="route-badge">FAVORITE</div>
                <div class="route-overlay">
                    <div class="route-title">Jakarta — Bandung</div>
                    <div class="route-meta">
                        <span><i class="ph ph-clock"></i> Mulai dari 3 Jam</span>
                        <span class="route-price"><i class="ph ph-coins"></i> IDR 120.000</span>
                    </div>
                </div>
            </div>

            {{-- Small Card: Surabaya — Malang --}}
            <div class="route-card small">
                <img src="{{ asset('images/rute_surabaya_malang.png') }}" alt="Surabaya - Malang" class="route-img">
                <div class="route-overlay">
                    <div class="route-title">Surabaya — Malang</div>
                    <div class="route-price">IDR 85.000</div>
                </div>
            </div>

            {{-- Small Card: Bali — Surabaya --}}
            <div class="route-card small">
                <img src="{{ asset('images/rute_bali_surabaya.png') }}" alt="Bali - Surabaya" class="route-img">
                <div class="route-overlay">
                    <div class="route-title">Bali — Surabaya</div>
                    <div class="route-price">IDR 250.000</div>
                </div>
            </div>
        </div>
    </section>

    {{-- Features --}}
    <section class="features-section">
        <div class="features-grid">
            <div class="feature-card">
                <div class="feature-icon"><i class="ph ph-shield-check"></i></div>
                <h3 class="feature-title">Aman &amp; Terpercaya</h3>
                <p class="feature-desc">Sistem pembayaran terenkripsi dan mitra operator bus resmi.</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon"><i class="ph ph-device-mobile"></i></div>
                <h3 class="feature-title">E-Tiket Instan</h3>
                <p class="feature-desc">Tiket langsung dikirim ke email dan aplikasi setelah bayar.</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon"><i class="ph ph-headset"></i></div>
                <h3 class="feature-title">Bantuan 24/7</h3>
                <p class="feature-desc">Tim support kami siap membantu perjalanan Anda kapanpun.</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon"><i class="ph ph-percent"></i></div>
                <h3 class="feature-title">Harga Terbaik</h3>
                <p class="feature-desc">Berbagai promo menarik setiap hari untuk perjalanan hemat.</p>
            </div>
        </div>
    </section>

@endsection

@push('scripts')
<script>
    document.getElementById('searchForm').addEventListener('submit', function(e) {
        const from = document.getElementById('from').value;
        const to   = document.getElementById('to').value;
        if (from && to && from === to) {
            e.preventDefault();
            alert('Kota asal dan tujuan tidak boleh sama!');
        }
    });
    const dateInput = document.getElementById('date');
    dateInput.min = new Date().toISOString().split('T')[0];
</script>
@endpush
