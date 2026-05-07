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
                <span class="sb-label">TANGGAL</span>
                <span class="sb-value">{{ \Carbon\Carbon::parse($date)->isoFormat('ddd, D MMM YYYY') }}</span>
            </div>
        </div>
        <a href="/" class="btn btn-outline-sm">
            <i class="ph ph-pencil"></i> Ubah Pencarian
        </a>
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
            <div class="ticket-card-main">
                <div class="bus-info">
                    <div class="bus-logo-placeholder"><i class="ph ph-bus"></i></div>
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
                        <div class="journey-duration">— LANGSUNG —</div>
                        <div class="journey-dot-line"><span></span><i class="ph ph-bus-fill"></i><span></span></div>
                    </div>
                    <div class="journey-time text-right">
                        <span class="time">—</span>
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
                    <div class="price-amount">IDR {{ number_format($r->harga, 0, ',', '.') }}</div>
                    <div class="price-per">Harga per orang</div>
                </div>
                <a href="{{ route('booking.seat', ['jadwal_id' => $r->id_jadwal]) }}" class="btn btn-primary btn-block">Pilih Kursi</a>
                @if($r->kursi_tersedia <= 5)
                <div class="seats-warning"><i class="ph ph-fire"></i> Sisa {{ $r->kursi_tersedia }} Kursi!!</div>
                @else
                <div class="seats-available"><i class="ph ph-chair"></i> {{ $r->kursi_tersedia }} Kursi tersedia</div>
                @endif
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
