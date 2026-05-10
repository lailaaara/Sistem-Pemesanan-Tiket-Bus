@extends('layouts.admin')
@section('title', 'Manajemen Operasional - BusMania')

@section('content')
@if (session('success'))
    <div style="background-color: #d4edda; border: 1px solid #c3e6cb; color: #155724; padding: 1rem; border-radius: 0.5rem; margin-bottom: 1rem;">
        <i class="ph ph-check-circle"></i> {{ session('success') }}
    </div>
@endif
@if (session('error'))
    <div style="background-color: #f8d7da; border: 1px solid #f5c6cb; color: #721c24; padding: 1rem; border-radius: 0.5rem; margin-bottom: 1rem;">
        <i class="ph ph-warning-circle"></i> {{ session('error') }}
    </div>
@endif

<div class="admin-page-header">
    <div>
        <h1 class="admin-page-title">Manajemen Operasional</h1>
        <p class="admin-page-subtitle">Kelola seluruh jadwal keberangkatan bus, pengaturan armada, dan pemantauan status operasional harian armada LajuBus secara real-time.</p>
    </div>
    <div class="admin-header-actions">
        <a href="{{ route('admin.tambah_rute') }}" class="btn-admin-cancel" style="margin-right:0.5rem;"><i class="ph ph-plus"></i> Tambah Rute</a>
        <a href="{{ route('admin.tambah_bus') }}" class="btn-admin-cancel" style="margin-right:0.5rem;"><i class="ph ph-plus"></i> Tambah Bus</a>
        <a href="{{ route('admin.tambah_jadwal') }}" class="btn-admin-save"><i class="ph ph-plus"></i> Tambah Jadwal Baru</a>
    </div>
</div>

{{-- Status Armada Cards --}}
<div class="ops-top-row">
    <div class="ops-status-card">
        <div class="ops-status-header">
            <div class="ops-status-title"><i class="ph ph-truck"></i> Status Armada Hari Ini</div>
            <span class="ops-live-badge">LIVE TRACKING</span>
        </div>
        <div class="ops-status-grid">
            @foreach($statusArmada as $s)
            <div class="ops-metric">
                <div class="ops-metric-label {{ $s->color }}">● {{ $s->label }}</div>
                <div class="ops-metric-value">{{ $s->value }}</div>
                <div class="ops-metric-sub">{{ $s->change }}</div>
            </div>
            @endforeach
        </div>
    </div>
    <div class="ops-ticket-card">
        <div class="ops-ticket-icon"><i class="ph ph-ticket"></i></div>
        <div class="ops-ticket-label">Tiket Terjual</div>
        <div class="ops-ticket-sub">Update real-time aplikasi</div>
        <div class="ops-ticket-value">{{ number_format($tiketHariIni, 0, ',', '.') }}</div>
        <div class="ops-ticket-unit">TOTAL TIKET HARI INI</div>
    </div>
</div>

{{-- Filters --}}
<div class="filter-row">
    <button class="filter-btn"><i class="ph ph-calendar"></i> Semua Tanggal <i class="ph ph-caret-down"></i></button>
    <button class="filter-btn"><i class="ph ph-funnel"></i> Semua Status <i class="ph ph-caret-down"></i></button>
</div>

<style>
    .ops-tabs {
        display: flex;
        gap: 1rem;
        margin-bottom: 1.5rem;
        border-bottom: 1px solid #eee;
        padding-bottom: 0.5rem;
    }
    .ops-tab {
        padding: 0.5rem 1rem;
        cursor: pointer;
        font-weight: 600;
        color: #666;
        border-bottom: 3px solid transparent;
        transition: all 0.3s ease;
    }
    .ops-tab.active {
        color: var(--primary);
        border-bottom: 3px solid var(--primary);
    }
    .tab-content {
        display: none;
    }
    .tab-content.active {
        display: block;
    }
    .trash-section-title {
        font-size: 1.1rem;
        font-weight: 600;
        margin-bottom: 1rem;
        margin-top: 1.5rem;
        display: flex;
        align-items: center;
        gap: 0.5rem;
        color: #ff6b6b;
    }
</style>

<div class="ops-tabs">
    <div class="ops-tab active" onclick="switchTab('jadwal-aktif', this)">Jadwal Aktif</div>
    <div class="ops-tab" onclick="switchTab('bus-aktif', this)">Daftar Bus</div>
    <div class="ops-tab" onclick="switchTab('rute-list', this)">Daftar Rute</div>
    <div class="ops-tab" style="color: #ff6b6b;" onclick="switchTab('sampah', this)"><i class="ph ph-trash"></i> Keranjang Sampah</div>
</div>

{{-- Jadwal Table --}}
<div id="jadwal-aktif" class="tab-content active admin-card">
    <table class="admin-table">
        <thead>
            <tr>
                <th>ID Jadwal</th>
                <th>Armada</th>
                <th>Rute Perjalanan</th>
                <th>Waktu Keberangkatan</th>
                <th>Kapasitas</th>
                <th>Status</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            @foreach($jadwalList as $j)
            @php
                $pct = $j->kapasitas_total > 0 ? round(($j->kapasitas_terisi / $j->kapasitas_total) * 100) : 0;
                $barColor = $pct >= 90 ? 'yellow' : ($j->status === 'DIBATALKAN' ? 'red' : 'green');
                $badgeClass = $j->status === 'AKTIF' ? 'badge-success' : ($j->status === 'SELESAI' ? 'badge-done' : 'badge-failed');
            @endphp
            <tr>
                <td class="td-id">{{ $j->id }}</td>
                <td>
                    <div style="display:flex;align-items:center;gap:0.5rem;">
                        <i class="ph ph-bus" style="font-size:1.1rem;color:var(--primary);"></i>
                        <span class="td-armada">{{ $j->armada }}</span>
                    </div>
                </td>
                <td>{{ $j->rute }}</td>
                <td>{{ $j->waktu }}</td>
                <td>
                    <div class="capacity-bar-wrap">
                        <div class="capacity-bar">
                            <div class="capacity-bar-fill {{ $barColor }}" style="width:{{ $pct }}%"></div>
                        </div>
                        <span class="capacity-text">{{ str_pad($j->kapasitas_terisi, 2, '0', STR_PAD_LEFT) }}/{{ $j->kapasitas_total }}</span>
                    </div>
                </td>
                <td><span class="badge {{ $badgeClass }}">{{ $j->status }}</span></td>
                <td style="display:flex;gap:0.5rem;">
                    <a href="{{ route('admin.edit_jadwal', $j->id_jadwal) }}" class="action-link" style="color:#0066ff;" title="Edit"><i class="ph ph-pencil"></i></a>
                    <form action="{{ route('admin.destroy_jadwal', $j->id_jadwal) }}" method="POST" style="display:inline;" onsubmit="return confirm('Pindahkan jadwal ini ke keranjang sampah?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="action-link" style="color:#ff6b6b;border:none;background:none;cursor:pointer;" title="Soft Delete"><i class="ph ph-trash"></i></button>
                    </form>
                </td>
            </tr>
            @endforeach
            @if($jadwalList->isEmpty())
            <tr><td colspan="7" style="text-align:center;padding:2rem;">Belum ada jadwal aktif.</td></tr>
            @endif
        </tbody>
    </table>
</div>

{{-- Bus Table --}}
<div id="bus-aktif" class="tab-content admin-card">
    <table class="admin-table">
        <thead>
            <tr>
                <th>ID Bus</th>
                <th>Nama Bus</th>
                <th>No Polisi</th>
                <th>Kelas</th>
                <th>Kapasitas</th>
                <th>Status</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            @foreach($busList as $bus)
            @php
                $badgeClass = $bus->status_bus === 'aktif' ? 'badge-success' : 'badge-failed';
            @endphp
            <tr>
                <td class="td-id">BUS-{{ $bus->bus_id }}</td>
                <td>
                    <div style="display:flex;align-items:center;gap:0.5rem;">
                        <i class="ph ph-bus" style="font-size:1.1rem;color:var(--primary);"></i>
                        <span class="td-armada">{{ $bus->nama_bus }}</span>
                    </div>
                </td>
                <td>{{ $bus->no_polisi }}</td>
                <td>{{ $bus->kelas }}</td>
                <td>{{ $bus->kapasitas }} Kursi</td>
                <td><span class="badge {{ $badgeClass }}">{{ strtoupper($bus->status_bus) }}</span></td>
                <td style="display:flex;gap:0.5rem;">
                    <a href="{{ route('admin.edit_bus', $bus->bus_id) }}" class="action-link" style="color:#0066ff;" title="Edit"><i class="ph ph-pencil"></i></a>
                    <form action="{{ route('admin.destroy_bus', $bus->bus_id) }}" method="POST" style="display:inline;" onsubmit="return confirm('Pindahkan bus ini ke keranjang sampah?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="action-link" style="color:#ff6b6b;border:none;background:none;cursor:pointer;" title="Soft Delete"><i class="ph ph-trash"></i></button>
                    </form>
                </td>
            </tr>
            @endforeach
            @if($busList->isEmpty())
            <tr><td colspan="7" style="text-align:center;padding:2rem;">Belum ada armada bus.</td></tr>
            @endif
        </tbody>
    </table>
</div>

{{-- Rute Table --}}
<div id="rute-list" class="tab-content admin-card">
    <table class="admin-table">
        <thead>
            <tr>
                <th>ID Rute</th>
                <th>Kota Asal</th>
                <th>Kota Tujuan</th>
                <th>Jarak (km)</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            @foreach($ruteList as $rute)
            <tr>
                <td class="td-id">RTE-{{ $rute->rute_id }}</td>
                <td>
                    <div style="display:flex;align-items:center;gap:0.5rem;">
                        <i class="ph ph-map-pin" style="font-size:1.1rem;color:var(--primary);"></i>
                        <span class="td-armada">{{ $rute->kota_asal }}</span>
                    </div>
                </td>
                <td>
                    <div style="display:flex;align-items:center;gap:0.5rem;">
                        <i class="ph ph-map-trifold" style="font-size:1.1rem;color:#20c997;"></i>
                        <span>{{ $rute->kota_tujuan }}</span>
                    </div>
                </td>
                <td>{{ number_format($rute->jarak_km, 0, ',', '.') }} km</td>
                <td style="display:flex;gap:0.5rem;">
                    <a href="{{ route('admin.edit_rute', $rute->rute_id) }}" class="action-link" style="color:#0066ff;" title="Edit"><i class="ph ph-pencil"></i></a>
                    <form action="{{ route('admin.destroy_rute', $rute->rute_id) }}" method="POST" style="display:inline;" onsubmit="return confirm('Hapus rute ini? Rute yang masih digunakan jadwal tidak bisa dihapus.');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="action-link" style="color:#ff6b6b;border:none;background:none;cursor:pointer;" title="Hapus"><i class="ph ph-trash"></i></button>
                    </form>
                </td>
            </tr>
            @endforeach
            @if($ruteList->isEmpty())
            <tr><td colspan="5" style="text-align:center;padding:2rem;">Belum ada data rute.</td></tr>
            @endif
        </tbody>
    </table>
</div>

{{-- Trash Section --}}
<div id="sampah" class="tab-content">
    <div class="trash-section-title"><i class="ph ph-calendar"></i> Sampah Jadwal</div>
    <div class="admin-card">
        <table class="admin-table">
            <thead>
                <tr>
                    <th>ID Jadwal</th>
                    <th>Armada</th>
                    <th>Rute Perjalanan</th>
                    <th>Waktu Dihapus</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach($trashedJadwalList as $j)
                <tr>
                    <td class="td-id">{{ $j->id }}</td>
                    <td>{{ $j->armada }}</td>
                    <td>{{ $j->rute }}</td>
                    <td>{{ $j->deleted_at }}</td>
                    <td style="display:flex;gap:0.5rem;">
                        <form action="{{ route('admin.restore_jadwal', $j->id_jadwal) }}" method="POST" style="display:inline;" onsubmit="return confirm('Pulihkan jadwal ini?');">
                            @csrf
                            <button type="submit" class="action-link" style="color:#20c997;border:none;background:none;cursor:pointer;" title="Restore"><i class="ph ph-arrow-counter-clockwise"></i> Restore</button>
                        </form>
                        <form action="{{ route('admin.force_destroy_jadwal', $j->id_jadwal) }}" method="POST" style="display:inline;" onsubmit="return confirm('Hapus permanen jadwal ini? TINDAKAN INI TIDAK BISA DIBATALKAN.');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="action-link" style="color:#ff6b6b;border:none;background:none;cursor:pointer;" title="Hard Delete"><i class="ph ph-trash"></i> Hard Delete</button>
                        </form>
                    </td>
                </tr>
                @endforeach
                @if($trashedJadwalList->isEmpty())
                <tr><td colspan="5" style="text-align:center;padding:2rem;">Keranjang sampah jadwal kosong.</td></tr>
                @endif
            </tbody>
        </table>
    </div>

    <div class="trash-section-title"><i class="ph ph-bus"></i> Sampah Armada Bus</div>
    <div class="admin-card">
        <table class="admin-table">
            <thead>
                <tr>
                    <th>ID Bus</th>
                    <th>Nama Bus</th>
                    <th>No Polisi</th>
                    <th>Waktu Dihapus</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach($trashedBusList as $bus)
                <tr>
                    <td class="td-id">BUS-{{ $bus->bus_id }}</td>
                    <td>{{ $bus->nama_bus }}</td>
                    <td>{{ $bus->no_polisi }}</td>
                    <td>{{ \Carbon\Carbon::parse($bus->deleted_at)->isoFormat('D MMM YYYY H:mm') }}</td>
                    <td style="display:flex;gap:0.5rem;">
                        <form action="{{ route('admin.restore_bus', $bus->bus_id) }}" method="POST" style="display:inline;" onsubmit="return confirm('Pulihkan bus ini?');">
                            @csrf
                            <button type="submit" class="action-link" style="color:#20c997;border:none;background:none;cursor:pointer;" title="Restore"><i class="ph ph-arrow-counter-clockwise"></i> Restore</button>
                        </form>
                        <form action="{{ route('admin.force_destroy_bus', $bus->bus_id) }}" method="POST" style="display:inline;" onsubmit="return confirm('Hapus permanen bus ini? TINDAKAN INI TIDAK BISA DIBATALKAN.');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="action-link" style="color:#ff6b6b;border:none;background:none;cursor:pointer;" title="Hard Delete"><i class="ph ph-trash"></i> Hard Delete</button>
                        </form>
                    </td>
                </tr>
                @endforeach
                @if($trashedBusList->isEmpty())
                <tr><td colspan="5" style="text-align:center;padding:2rem;">Keranjang sampah armada bus kosong.</td></tr>
                @endif
            </tbody>
        </table>
    </div>
</div>

<script>
    function switchTab(tabId, el) {
        document.querySelectorAll('.ops-tab').forEach(t => t.classList.remove('active'));
        el.classList.add('active');
        document.querySelectorAll('.tab-content').forEach(c => c.classList.remove('active'));
        document.getElementById(tabId).classList.add('active');
    }
</script>

{{-- Info Banner --}}
<div class="info-banner">
    <div>
        <div class="info-banner-icon"><i class="ph ph-arrows-clockwise"></i></div>
        <div class="info-banner-title">Informasi Sinkronisasi</div>
        <div class="info-banner-desc">Data jadwal terakhir diperbarui pada 2 Mei 2026 pukul 14:22 WIB. Seluruh perubahan pada status armada akan disinkronkan langsung ke pelanggan.</div>
    </div>
    <div style="position:relative;">
        <img src="{{ asset('images/hero_bus_bg.png') }}" class="info-banner-img" alt="BusMania Operational">
        <div class="info-banner-img-label">
            <strong>BusMania Operational</strong><br>
            <span style="font-size:0.72rem;opacity:0.8;">Mengutamakan Kepercayaan & Kenyamanan Perjalanan</span>
        </div>
    </div>
</div>
@endsection
