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
    <section class="section">
        <div class="section-header">
            <h2 class="section-title">Rute Terpopuler</h2>
            <p class="section-subtitle">Pilihan rute yang paling sering dipesan oleh traveler.</p>
        </div>

        <div class="routes-grid">
            @foreach($popularRoutes as $route)
            <div class="route-card">
                <div class="route-img-wrapper">
                    <div class="route-badge">FAVORITE</div>
                    @php
                        $imgSrc = asset('images/hero_bus_bg.png');
                        if(str_contains($route->gambar ?? '', 'http')) {
                            $imgSrc = $route->gambar;
                        } elseif ($route->gambar) {
                            $imgSrc = asset('storage/' . $route->gambar);
                        }
                    @endphp
                    <img src="{{ $imgSrc }}" alt="{{ $route->kota_asal }} - {{ $route->kota_tujuan }}" class="route-img">
                </div>
                <div class="route-content">
                    <h3 class="route-title">{{ $route->kota_asal }} &mdash; {{ $route->kota_tujuan }}</h3>
                    <div style="display:flex; justify-content:space-between; align-items:flex-end;">
                        <span style="font-size:0.9rem; opacity:0.9;"><i class="ph ph-clock"></i> Mulai dari 3 Jam</span>
                        <div class="route-price">IDR {{ number_format($route->harga_mulai, 0, ',', '.') }}</div>
                    </div>
                </div>
            </div>
            @endforeach
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
