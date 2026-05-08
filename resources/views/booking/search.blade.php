@extends('layouts.app')

@section('title', 'BusMania - Hasil Pencarian')

@section('content')
{{-- Search Summary Bar --}}
<div class="search-bar-summary">
    <div class="sb-inner">
        <div class="sb-item">
            <i class="ph ph-map-pin"></i>
            <div>
                <span class="sb-label">ASAL</span>
                <span class="sb-value">{{ $from }}</span>
            </div>
        </div>
        <i class="ph ph-arrow-right sb-arrow"></i>
        <div class="sb-item">
            <i class="ph ph-map-pin-line"></i>
            <div>
                <span class="sb-label">TUJUAN</span>
                <span class="sb-value">{{ $to }}</span>
            </div>
        </div>
        <div class="sb-divider"></div>
        <div class="sb-item">
            <i class="ph ph-calendar-blank"></i>
            <div>
            <div>
                <span class="sb-label">TANGGAL</span>
                <span class="sb-value">{{ \Carbon\Carbon::parse($date)->isoFormat('ddd, D MMM YYYY') }}</span>
            </div>
        </div>
        <div style="margin-left: auto;">
            <a href="/" class="btn btn-outline-sm" style="border-color: transparent; color: var(--text-main);">
                <strong>Ubah Pencarian</strong> <i class="ph ph-pencil-simple" style="font-size:1.1rem; color:var(--text-muted);"></i>
            </a>
        </div>
    </div>
</div>

<div class="search-layout">

    {{-- ── SIDEBAR FILTER ── --}}
    <aside class="filter-sidebar">
        <div class="filter-header">
            <h3>Filter</h3>
            <button class="link-btn text-primary text-sm">Hapus Semua</button>
        </div>

        {{-- Waktu Keberangkatan --}}
        <div class="filter-group">
            <h4>Waktu Keberangkatan</h4>
            <div class="time-filter-grid">
                <label class="time-chip"><input type="checkbox" name="waktu" value="pagi"> <i class="ph ph-sun"></i><span>Pagi</span><small>06:00 - 12:00</small></label>
                <label class="time-chip active"><input type="checkbox" name="waktu" value="siang" checked> <i class="ph ph-sun-horizon"></i><span>Siang</span><small>12:00 - 18:00</small></label>
                <label class="time-chip"><input type="checkbox" name="waktu" value="malam"> <i class="ph ph-moon"></i><span>Malam</span><small>18:00 - 00:00</small></label>
                <label class="time-chip"><input type="checkbox" name="waktu" value="dinihari"> <i class="ph ph-moon-stars"></i><span>Dini Hari</span><small>00:00 - 06:00</small></label>
            </div>
        </div>

        {{-- Harga --}}
        <div class="filter-group">
            <h4>Harga (IDR)</h4>
            <div class="price-range-labels">
                <span>Rp 50rb</span><span>Rp 1jt+</span>
            </div>
            <input type="range" class="price-range-slider" min="50000" max="1000000" value="1000000">
        </div>

        {{-- Fasilitas --}}
        <div class="filter-group">
            <h4>Fasilitas</h4>
            <label class="check-item"><input type="checkbox" checked> AC</label>
            <label class="check-item"><input type="checkbox"> WiFi</label>
            <label class="check-item"><input type="checkbox" checked> Toilet</label>
            <label class="check-item"><input type="checkbox"> Snack & Air Mineral</label>
            <label class="check-item"><input type="checkbox"> Reclining Seat</label>
        </div>

        {{-- Operator Bus --}}
        <div class="filter-group">
            <h4>Operator Bus</h4>
            <label class="check-item"><input type="checkbox" checked> LajuBus</label>
            <label class="check-item"><input type="checkbox"> Pahala Kencana</label>
            <label class="check-item"><input type="checkbox"> Rosalia Indah</label>
            <label class="check-item"><input type="checkbox"> Sinar Jaya</label>
        </div>

        {{-- Titik Jemput --}}
        <div class="filter-group" style="border-bottom: none; padding-bottom: 0;">
            <h4>Titik Jemput</h4>
            <label class="check-item"><input type="checkbox"> Pulo Gebang</label>
        </div>
    </aside>

    {{-- ── HASIL PENCARIAN ── --}}
    <div class="search-results">
        <div class="results-header">
            <p class="results-count">Menampilkan <strong>{{ $results->count() }}</strong> jadwal tersedia untuk <strong>"{{ $from }}"</strong></p>
            <div class="sort-control">
                <span>Urutkan:</span>
                <select>
                    <option>Harga Terendah</option>
                    <option>Harga Tertinggi</option>
                    <option>Berangkat Paling Awal</option>
                    <option>Berangkat Paling Akhir</option>
                </select>
            </div>
        </div>

        @forelse($results as $r)
        <div class="ticket-card">
            <div class="ticket-card-body">
                <div class="ticket-card-main">
                    <div class="bus-info">
                        <div class="bus-logo-placeholder">
                            @if($r->gambar ?? false)
                                <img src="{{ asset('storage/'.$r->gambar) }}" alt="{{ $r->nama_bus }}" style="width:100%; height:100%; object-fit:cover; border-radius:8px;">
                            @else
                                <i class="ph ph-bus"></i>
                            @endif
                        </div>
                        <div>
                            <div class="bus-name">{{ $r->nama_bus }}</div>
                            <div class="bus-class">{{ $r->kelas }}</div>
                        </div>
                    </div>

                    <div class="journey-info">
                        <div class="journey-time">
                            <span class="time">{{ \Carbon\Carbon::parse($r->jam_berangkat)->format('H:i') }}</span>
                            <span class="city-code">{{ strtoupper(substr($r->kota_asal, 0, 3)) }}</span>
                        </div>
                        <div class="journey-line">
                            <div class="journey-duration">10j 00m</div>
                            <div class="journey-dot-line"><span></span><i class="ph ph-circle-fill" style="font-size:0.5rem;"></i><span></span></div>
                            <div style="font-size: 0.65rem; color: var(--text-muted); margin-top:4px; font-weight:600; letter-spacing:0.5px;">LANGSUNG</div>
                        </div>
                        <div class="journey-time text-right">
                            <span class="time">{{ \Carbon\Carbon::parse($r->jam_berangkat)->addHours(10)->format('H:i') }}</span>
                            <span class="city-code">{{ strtoupper(substr($r->kota_tujuan, 0, 3)) }}</span>
                        </div>
                    </div>

                    <div class="facilities-tags">
                        @foreach(array_slice(explode(',', $r->fasilitas), 0, 3) as $fas)
                            <span class="fac-tag">{{ strtoupper(trim($fas)) }}</span>
                        @endforeach
                    </div>
                </div>

                <div class="ticket-card-right">
                    <div class="price-block">
                        <div style="text-decoration: line-through; color: var(--text-muted); font-size: 0.85rem; text-align: right; margin-bottom: 2px;">Rp {{ number_format($r->harga + 35000, 0, ',', '.') }}</div>
                        <div class="price-amount">IDR {{ number_format($r->harga, 0, ',', '.') }}</div>
                        <div class="price-per" style="text-align: right;">Hemat 10% hari ini</div>
                    </div>
                    <a href="{{ route('booking.seat', ['jadwal_id' => $r->id_jadwal]) }}" class="btn btn-primary btn-block">Pilih Kursi</a>
                </div>
            </div>
            
            <div class="ticket-card-footer">
                <div class="footer-links">
                    <a href="#">Detail Rute</a>
                    <a href="#">Detail Bus</a>
                </div>
                <div class="footer-status">
                    @if($r->kursi_tersedia <= 5)
                        <span class="seats-warning"><i class="ph ph-lightning"></i> Sisa {{ $r->kursi_tersedia }} Kursi!</span>
                    @else
                        <span class="seats-available"><i class="ph ph-chair"></i> {{ $r->kursi_tersedia }} Kursi tersedia</span>
                    @endif
                </div>
            </div>
        </div>
        @empty
        <div class="empty-state">
            <i class="ph ph-magnifying-glass"></i>
            <p>Tidak ada jadwal ditemukan untuk rute <strong>{{ $from }} → {{ $to }}</strong> pada tanggal yang dipilih.</p>
            <a href="/" class="btn btn-primary mt-4">Cari Rute Lain</a>
        </div>
        @endforelse
    </div>
</div>
@endsection
