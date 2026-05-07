@extends('layouts.app')

@section('title', 'Pembayaran - BusMania')

@section('content')
<div class="booking-page">
    {{-- Stepper --}}
    <div class="stepper">
        <div class="step done"><div class="step-num"><i class="ph ph-check"></i></div><div class="step-label">Pilih Kursi</div></div>
        <div class="step-line active"></div>
        <div class="step active"><div class="step-num">2</div><div class="step-label">Pembayaran</div></div>
        <div class="step-line"></div>
        <div class="step"><div class="step-num">3</div><div class="step-label">Selesai</div></div>
    </div>

    <div class="checkout-layout">
        {{-- ── KIRI: FORM ── --}}
        <div class="checkout-form-section">

            {{-- Detail Penumpang --}}
            <div class="form-card">
                <h3><i class="ph ph-user"></i> Detail Penumpang</h3>
                <div class="form-grid-2">
                    <div class="form-group">
                        <label>Nama Lengkap</label>
                        <input type="text" class="form-control" placeholder="Sesuai KTP">
                    </div>
                    <div class="form-group">
                        <label>No. Identitas</label>
                        <input type="text" class="form-control" placeholder="NIK">
                    </div>
                    <div class="form-group">
                        <label>No. HP</label>
                        <div class="input-phone">
                            <span class="phone-prefix">+62</span>
                            <input type="text" class="form-control" placeholder="8123456789">
                        </div>
                    </div>
                    <div class="form-group">
                        <label>Email</label>
                        <input type="email" class="form-control" placeholder="email@contoh.com">
                    </div>
                </div>
            </div>

            {{-- Metode Pembayaran --}}
            <div class="form-card">
                <h3><i class="ph ph-credit-card"></i> Metode Pembayaran</h3>
                @foreach($metodePembayaran as $metode)
                <label class="payment-method-item {{ $loop->index === 1 ? 'selected' : '' }}">
                    <div class="pm-left">
                        <i class="ph {{ $metode['icon'] }} pm-icon"></i>
                        <div>
                            <div class="pm-label">{{ $metode['label'] }}</div>
                            <div class="pm-sub">{{ $metode['sub'] }}</div>
                        </div>
                    </div>
                    <input type="radio" name="metode_pembayaran" value="{{ $metode['id'] }}" {{ $loop->index === 1 ? 'checked' : '' }}>
                </label>
                @endforeach

                <div class="payment-secure-note">
                    <i class="ph ph-shield-check"></i>
                    <div>
                        <strong>Pembayaran Aman & Terenkripsi</strong>
                        <p>Semua transaksi diproses melalui gateway pembayaran yang aman dengan enkripsi SSL 256-bit.</p>
                    </div>
                </div>
            </div>
        </div>

        {{-- ── KANAN: RINGKASAN ── --}}
        <div class="booking-summary">
            <div class="summary-card">
                <div class="summary-card-header">
                    <h3>Ringkasan Pesanan</h3>
                    <span class="order-id-badge">ID Pesanan: LJB-992834</span>
                </div>

                <div class="order-detail-item">
                    <strong>{{ $jadwal->nama_bus ?? 'LajuBus' }} • {{ $jadwal->kelas ?? 'Executive' }}</strong>
                    <span class="order-date">{{ $jadwal ? \Carbon\Carbon::parse($jadwal->tanggal_berangkat)->isoFormat('D MMM YYYY') : '—' }}</span>
                </div>
                <div class="order-route">
                    <span>{{ $jadwal->kota_asal ?? 'Jakarta' }}</span>
                    <i class="ph ph-arrow-right"></i>
                    <span>{{ $jadwal->kota_tujuan ?? 'Surabaya' }}</span>
                </div>
                <div class="order-seat"><i class="ph ph-chair"></i> Kursi: <strong>{{ $kursiNos ?: '2A' }}</strong></div>

                <div class="summary-divider"></div>

                <div class="meta-row"><span>Harga Tiket (x1)</span><span>Rp {{ $jadwal ? number_format($jadwal->harga, 0, ',', '.') : '300.000' }}</span></div>
                <div class="meta-row"><span>Biaya Layanan</span><span>Rp 5.000</span></div>
                <div class="meta-row total"><span>Total Bayar</span><span>Rp {{ $jadwal ? number_format($jadwal->harga + 5000, 0, ',', '.') : '305.000' }}</span></div>

                <a href="{{ route('booking.success') }}" class="btn btn-primary btn-block mt-4">
                    <i class="ph ph-lock"></i> Bayar Sekarang
                </a>
                <p class="summary-note">Dengan menekan tombol di atas, Anda menyetujui <a href="#">Syarat & Ketentuan</a> kami.</p>
            </div>
        </div>
    </div>
</div>
@endsection
