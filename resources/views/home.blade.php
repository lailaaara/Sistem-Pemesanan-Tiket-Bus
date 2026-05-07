<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BusMania - Pesan Tiket Bus Online</title>
    <meta name="description" content="Tempat pemesanan tiket bus nomor 1 di Indonesia. Pesan tiket bus cepat, aman, dan terpercaya.">
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    <!-- Load Phosphor Icons for modern minimalist icons -->
    <script src="https://unpkg.com/@phosphor-icons/web"></script>
</head>
<body>

    <!-- Navbar -->
    <nav class="navbar">
        <a href="/" class="nav-brand">BusMania</a>
        <div class="nav-links">
            <a href="#" class="nav-link active">Cari Tiket</a>
            <a href="#" class="nav-link">Rute</a>
            <a href="#" class="nav-link">Informasi</a>
        </div>
        <div class="nav-actions">
            <a href="#" class="btn btn-outline">Masuk</a>
            <a href="#" class="btn btn-primary">Daftar</a>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="hero">
        <div class="hero-content">
            <h1 class="hero-title">TEMPAT PEMESANAN BUS NOMOR 1 DI INDONESIA</h1>
        </div>

        <!-- Search Widget -->
        <div class="search-widget">
            <form action="/api/jadwal/search" method="GET" class="search-form" id="searchForm">
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

    <!-- Popular Routes Section -->
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
                        // Mocking image just in case the DB has random strings. 
                        // You can replace this with actual storage paths later.
                        $imgSrc = asset('images/hero_bus_bg.png'); // Fallback
                        if(str_contains($route->gambar, 'http')) {
                            $imgSrc = $route->gambar;
                        } elseif ($route->gambar) {
                            $imgSrc = asset('storage/' . $route->gambar);
                        }
                    @endphp
                    <img src="{{ $imgSrc }}" alt="{{ $route->kota_asal }} - {{ $route->kota_tujuan }}" class="route-img">
                </div>
                <div class="route-content">
                    <h3 class="route-title">{{ $route->kota_asal }} &mdash; {{ $route->kota_tujuan }}</h3>
                    <div style="display:flex; justify-content: space-between; align-items:flex-end;">
                        <span style="font-size: 0.9rem; opacity: 0.9;">
                            <i class="ph ph-clock"></i> Mulai dari 3 Jam
                        </span>
                        <div class="route-price">IDR {{ number_format($route->harga_mulai, 0, ',', '.') }}</div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </section>

    <!-- Features Section -->
    <section class="features-section">
        <div class="features-grid">
            <div class="feature-card">
                <div class="feature-icon"><i class="ph ph-shield-check"></i></div>
                <h3 class="feature-title">Aman & Terpercaya</h3>
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

    <!-- Footer -->
    <footer class="footer">
        <div>
            <div class="footer-brand">BusMania</div>
            <div class="footer-text">&copy; {{ date('Y') }} BusMania. Kepercayaan dalam Perjalanan.</div>
        </div>
        <div class="footer-links">
            <a href="#" class="footer-link">Syarat & Ketentuan</a>
            <a href="#" class="footer-link">Kebijakan Privasi</a>
            <a href="#" class="footer-link">Hubungi Kami</a>
        </div>
    </footer>

    <script>
        // Simple client-side validation
        document.getElementById('searchForm').addEventListener('submit', function(e) {
            const from = document.getElementById('from').value;
            const to = document.getElementById('to').value;
            
            if(from && to && from === to) {
                e.preventDefault();
                alert('Kota asal dan tujuan tidak boleh sama!');
            }
        });
        
        // Set min date to today
        const dateInput = document.getElementById('date');
        const today = new Date().toISOString().split('T')[0];
        dateInput.min = today;
    </script>
</body>
</html>
