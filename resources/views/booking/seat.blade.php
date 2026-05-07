@extends('layouts.app')

@section('title', 'Pilih Kursi - BusMania')

@section('content')
<div class="booking-page">
    {{-- Stepper --}}
    <div class="stepper">
        <div class="step active">
            <div class="step-num">1</div>
            <div class="step-label">Pilih Kursi</div>
        </div>
        <div class="step-line"></div>
        <div class="step">
            <div class="step-num">2</div>
            <div class="step-label">Pembayaran</div>
        </div>
        <div class="step-line"></div>
        <div class="step">
            <div class="step-num">3</div>
            <div class="step-label">Selesai</div>
        </div>
    </div>

    <div class="booking-layout">
        {{-- ── SEAT MAP ── --}}
        <div class="seat-map-container">
            <div class="seat-map-header">
                <h3>Denah Kursi Bus</h3>
                <div class="seat-legend">
                    <span class="legend-item"><span class="seat-demo available"></span> Tersedia</span>
                    <span class="legend-item"><span class="seat-demo selected"></span> Dipilih</span>
                    <span class="legend-item"><span class="seat-demo taken"></span> Terisi</span>
                </div>
            </div>

            <div class="bus-body">
                <div class="bus-front">
                    <span class="door-label"><i class="ph ph-door"></i> Pintu Masuk</span>
                    <div class="driver-seats">
                        <div class="driver-seat"><i class="ph ph-steering-wheel"></i><span>SUPIR</span></div>
                        <div class="driver-seat"><i class="ph ph-steering-wheel"></i><span>SUPIR</span></div>
                    </div>
                </div>
                <div class="seat-divider"></div>

                @php
                    // Generate kursi 1-6 (A,B,C,D) sesuai layout bus 2-2
                    $totalRows = $jadwal ? ceil($jadwal->kapasitas / 4) : 8;
                    $takenIds  = $kursiTerisi ?? [];
                @endphp

                @for($row = 1; $row <= $totalRows; $row++)
                <div class="seat-row">
                    @foreach(['A','B'] as $col)
                    @php $seatNo = $row . $col; @endphp
                    <button class="seat {{ in_array($seatNo, $takenIds) ? 'taken' : 'available' }}"
                            data-seat="{{ $seatNo }}"
                            {{ in_array($seatNo, $takenIds) ? 'disabled' : '' }}>
                        {{ $seatNo }}
                    </button>
                    @endforeach

                    <div class="seat-aisle"></div>

                    @foreach(['C','D'] as $col)
                    @php $seatNo = $row . $col; @endphp
                    <button class="seat {{ in_array($seatNo, $takenIds) ? 'taken' : 'available' }}"
                            data-seat="{{ $seatNo }}"
                            {{ in_array($seatNo, $takenIds) ? 'disabled' : '' }}>
                        {{ $seatNo }}
                    </button>
                    @endforeach
                </div>
                @endfor

                <div class="seat-divider"></div>
                <div class="door-label-bottom"><i class="ph ph-door"></i> Pintu Masuk</div>
            </div>
        </div>

        {{-- ── RINGKASAN ── --}}
        <div class="booking-summary">
            <div class="summary-card">
                <h3>Ringkasan Perjalanan</h3>

                <div class="journey-summary">
                    <div class="js-item">
                        <span class="js-dot departure"></span>
                        <div>
                            <div class="js-city">{{ $jadwal->kota_asal ?? 'Jakarta' }}</div>
                            <div class="js-time">{{ $jadwal ? \Carbon\Carbon::parse($jadwal->jam_berangkat)->format('l, d M Y • H:i') : '—' }}</div>
                        </div>
                    </div>
                    <div class="js-item">
                        <span class="js-dot arrival"></span>
                        <div>
                            <div class="js-city">{{ $jadwal->kota_tujuan ?? 'Surabaya' }}</div>
                            <div class="js-time">—</div>
                        </div>
                    </div>
                </div>

                <div class="summary-meta">
                    <div class="meta-row"><span>Bus Operator</span><span><strong>{{ ($jadwal->nama_bus ?? 'BusMania') . ' • ' . ($jadwal->kelas ?? 'Eksekutif') }}</strong></span></div>
                    <div class="meta-row"><span>Nomor Bus</span><span><strong>{{ $jadwal->no_polisi ?? 'LB-2045-A' }}</strong></span></div>
                </div>
            </div>

            <div class="summary-card">
                <h4>DETAIL KURSI & HARGA</h4>
                <div class="meta-row"><span>Kursi Terpilih</span><span id="selected-seat-display" class="seat-badge">—</span></div>
                <div class="meta-row"><span>Harga Tiket (1x)</span><span id="price-display">Rp {{ $jadwal ? number_format($jadwal->harga, 0, ',', '.') : '0' }}</span></div>
                <div class="meta-row"><span>Biaya Layanan</span><span>Rp 5.000</span></div>
                <div class="meta-row total"><span>Total Pembayaran</span><span id="total-display">Rp {{ $jadwal ? number_format($jadwal->harga + 5000, 0, ',', '.') : '5.000' }}</span></div>

                <a href="{{ route('booking.checkout', ['jadwal_id' => $jadwal->id_jadwal ?? '', 'kursi' => '']) }}"
                   id="btn-lanjut"
                   class="btn btn-primary btn-block mt-4 disabled-btn">
                    Lanjut ke Pembayaran
                </a>
                <p class="summary-note">Dengan melanjutkan, Anda menyetujui <a href="#">Syarat & Ketentuan</a> yang berlaku.</p>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    const harga = {{ $jadwal->harga ?? 0 }};
    const jadwalId = {{ $jadwal->id_jadwal ?? 0 }};
    let selectedSeats = [];

    document.querySelectorAll('.seat.available').forEach(btn => {
        btn.addEventListener('click', () => {
            btn.classList.toggle('selected');
            const seat = btn.dataset.seat;
            if (selectedSeats.includes(seat)) {
                selectedSeats = selectedSeats.filter(s => s !== seat);
            } else {
                selectedSeats.push(seat);
            }

            const count = selectedSeats.length;
            document.getElementById('selected-seat-display').textContent = count ? selectedSeats.join(', ') : '—';
            const total = (harga * count) + 5000;
            document.getElementById('total-display').textContent = 'Rp ' + total.toLocaleString('id-ID');

            const btnLanjut = document.getElementById('btn-lanjut');
            if (count > 0) {
                btnLanjut.classList.remove('disabled-btn');
                btnLanjut.href = `/booking/checkout?jadwal_id=${jadwalId}&kursi=${selectedSeats.join(',')}`;
            } else {
                btnLanjut.classList.add('disabled-btn');
            }
        });
    });
</script>
@endpush
