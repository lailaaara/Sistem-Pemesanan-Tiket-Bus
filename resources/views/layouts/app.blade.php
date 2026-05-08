<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'BusMania - Pesan Tiket Bus Online')</title>
    <meta name="description" content="Tempat pemesanan tiket bus nomor 1 di Indonesia. Pesan tiket bus cepat, aman, dan terpercaya.">
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    <script src="https://unpkg.com/@phosphor-icons/web"></script>
    @stack('styles')
</head>
<body>

    <!-- ══════════════════════════════════════
         NAVBAR
    ══════════════════════════════════════ -->
    <nav class="navbar">
        <a href="/" class="nav-brand">BusMania</a>

        <div class="nav-links">
            <a href="/" class="nav-link {{ request()->is('/') ? 'active' : '' }}">Cari Tiket</a>
            <a href="{{ route('booking.tickets') }}" class="nav-link {{ request()->is('tickets*') ? 'active' : '' }}">Tiket Saya</a>
            <a href="#" class="nav-link">Bantuan</a>
        </div>

        <div class="nav-actions">
            @auth
                <a href="{{ route('admin.dashboard') }}" class="btn btn-outline" style="border-color: var(--primary); color: var(--primary);">Dashboard Admin</a>
                <form action="{{ route('logout') }}" method="POST" style="display:inline;">
                    @csrf
                    <button type="submit" class="btn btn-primary" style="padding: 0.5rem 1rem;">Logout</button>
                </form>
            @else
                <a href="{{ route('login') }}" class="btn btn-outline" style="border-color: transparent; color: var(--text-muted); font-size: 0.85rem;">
                    <i class="ph ph-shield-check"></i> Admin
                </a>
            @endauth
        </div>
            <!-- Dropdown Profil -->
            <div class="profile-dropdown" id="profileDropdown" style="display:none;">
                <div class="profile-dropdown-header">
                    <span class="profile-avatar"><i class="ph ph-user-circle-fill"></i></span>
                    <div>
                        <div class="profile-name" id="dropdownName">Anak Undip</div>
                        <div class="profile-email" id="dropdownEmail">emailanakundip@email.com</div>
                    </div>
                </div>
                <div class="profile-dropdown-divider"></div>
                <button class="profile-logout-btn" onclick="openModal('modalLogout')">
                    <i class="ph ph-sign-out"></i> Keluar
                </button>
            </div>
        </div>
    </nav>

    <!-- ══════════════════════════════════════
         MODAL OVERLAY (backdrop)
    ══════════════════════════════════════ -->
    <div class="modal-overlay" id="modalOverlay" onclick="closeAllModals()"></div>

    <!-- ── Modal Login ── -->
    <div class="modal" id="modalLogin">
        <div class="modal-header">
            <h2 class="modal-title">Login BusMania</h2>
            <button class="modal-close" onclick="closeAllModals()"><i class="ph ph-x"></i></button>
        </div>
        <div class="modal-divider"></div>
        <div class="modal-body">
            <form id="formLogin" onsubmit="handleLogin(event)">
                <div class="modal-form-group">
                    <label>Email</label>
                    <div class="modal-input-wrapper">
                        <i class="ph ph-envelope modal-input-icon"></i>
                        <input type="email" id="loginEmail" placeholder="contoh@email.com" required class="modal-input">
                    </div>
                </div>
                <div class="modal-form-group">
                    <label>Password</label>
                    <div class="modal-input-wrapper">
                        <i class="ph ph-lock modal-input-icon"></i>
                        <input type="password" id="loginPassword" placeholder="••••••••" required class="modal-input">
                    </div>
                </div>
                <div id="loginError" class="modal-error" style="display:none;"></div>
                <button type="submit" class="btn btn-primary btn-block modal-btn">Masuk</button>
            </form>
            <p class="modal-switch-text">Belum punya akun? <button onclick="switchModal('modalLogin','modalRegister')" class="modal-link">Daftar sekarang</button></p>
        </div>
    </div>

    <!-- ── Modal Register ── -->
    <div class="modal" id="modalRegister">
        <div class="modal-header">
            <h2 class="modal-title">Daftar Akun Baru</h2>
            <button class="modal-close" onclick="closeAllModals()"><i class="ph ph-x"></i></button>
        </div>
        <div class="modal-divider"></div>
        <div class="modal-body">
            <form id="formRegister" onsubmit="handleRegister(event)">
                <div class="modal-form-group">
                    <label>Username</label>
                    <div class="modal-input-wrapper">
                        <i class="ph ph-user modal-input-icon"></i>
                        <input type="text" id="regName" placeholder="Masukkan username" required class="modal-input">
                    </div>
                </div>
                <div class="modal-form-group">
                    <label>Email</label>
                    <div class="modal-input-wrapper">
                        <i class="ph ph-envelope modal-input-icon"></i>
                        <input type="email" id="regEmail" placeholder="contoh@email.com" required class="modal-input">
                    </div>
                </div>
                <div class="modal-form-group">
                    <label>Password</label>
                    <div class="modal-input-wrapper">
                        <i class="ph ph-lock modal-input-icon"></i>
                        <input type="password" id="regPassword" placeholder="Min. 8 karakter" required class="modal-input">
                    </div>
                </div>
                <div id="regError" class="modal-error" style="display:none;"></div>
                <button type="submit" class="btn btn-primary btn-block modal-btn">Daftar Sekarang</button>
            </form>
            <p class="modal-switch-text">Sudah punya akun? <button onclick="switchModal('modalRegister','modalLogin')" class="modal-link">Masuk di sini</button></p>
        </div>
    </div>

    <!-- ── Modal Logout ── -->
    <div class="modal modal-sm" id="modalLogout">
        <div class="modal-body text-center">
            <div class="logout-icon-wrap">
                <div class="logout-icon"><i class="ph ph-sign-out"></i></div>
            </div>
            <h3 class="logout-title">Keluar dari Akun?</h3>
            <p class="logout-desc">Apakah Anda yakin ingin keluar? Anda harus masuk kembali untuk memesan tiket bus.</p>
            <button onclick="handleLogout()" class="btn btn-primary btn-block modal-btn">Ya, Keluar</button>
            <button onclick="closeAllModals()" class="btn btn-outline btn-block modal-btn-secondary">Batal</button>
        </div>
    </div>

    <!-- ══════════════════════════════════════
         MAIN CONTENT
    ══════════════════════════════════════ -->
    <main>
        @yield('content')
    </main>

    <!-- ══════════════════════════════════════
         FOOTER
    ══════════════════════════════════════ -->
    <footer class="footer">
        <div>
            <div class="footer-brand">BusMania</div>
            <div class="footer-text">&copy; {{ date('Y') }} BusMania. Kepercayaan dalam Perjalanan.</div>
        </div>
        <div class="footer-links">
            <a href="#" class="footer-link">Syarat &amp; Ketentuan</a>
            <a href="#" class="footer-link">Kebijakan Privasi</a>
            <a href="#" class="footer-link">Hubungi Kami</a>
        </div>
    </footer>

    <!-- ══════════════════════════════════════
         AUTH SCRIPTS (global)
    ══════════════════════════════════════ -->
    <script>
        // ── Modal helpers ──────────────────────────────
        function openModal(id) {
            closeAllModals(false);
            document.getElementById(id).classList.add('active');
            document.getElementById('modalOverlay').classList.add('active');
            document.body.style.overflow = 'hidden';
        }
        function closeAllModals(restoreScroll = true) {
            document.querySelectorAll('.modal').forEach(m => m.classList.remove('active'));
            document.getElementById('modalOverlay').classList.remove('active');
            if (restoreScroll) document.body.style.overflow = '';
            closeDropdown();
        }
        function switchModal(from, to) {
            document.getElementById(from).classList.remove('active');
            document.getElementById(to).classList.add('active');
        }

        // ── Profile Dropdown ───────────────────────────
        function toggleDropdown() {
            const dd = document.getElementById('profileDropdown');
            dd.style.display = dd.style.display === 'none' ? 'block' : 'none';
        }
        function closeDropdown() {
            const dd = document.getElementById('profileDropdown');
            if (dd) dd.style.display = 'none';
        }
        document.addEventListener('click', function(e) {
            const btn = document.getElementById('avatarBtn');
            const dd  = document.getElementById('profileDropdown');
            if (btn && dd && !btn.contains(e.target) && !dd.contains(e.target)) {
                closeDropdown();
            }
        });

        // ── Simulasi Auth (pakai sessionStorage, nanti ganti dengan Laravel Session) ──
        function handleLogin(e) {
            e.preventDefault();
            const email = document.getElementById('loginEmail').value;
            const pass  = document.getElementById('loginPassword').value;
            // Validasi sementara (nanti diganti dengan AJAX ke /api/auth/login)
            if (email && pass.length >= 6) {
                const name = email.split('@')[0];
                setLoggedIn(name, email);
                closeAllModals();
            } else {
                showError('loginError', 'Email atau password tidak valid.');
            }
        }
        function handleRegister(e) {
            e.preventDefault();
            const name  = document.getElementById('regName').value;
            const email = document.getElementById('regEmail').value;
            const pass  = document.getElementById('regPassword').value;
            if (name && email && pass.length >= 8) {
                setLoggedIn(name, email);
                closeAllModals();
            } else {
                showError('regError', 'Pastikan semua field diisi dan password minimal 8 karakter.');
            }
        }
        function handleLogout() {
            sessionStorage.removeItem('busmania_user');
            document.getElementById('navGuest').style.display = 'flex';
            document.getElementById('navUser').style.display = 'none';
            closeAllModals();
        }
        function setLoggedIn(name, email) {
            sessionStorage.setItem('busmania_user', JSON.stringify({ name, email }));
            document.getElementById('navGuest').style.display = 'none';
            document.getElementById('navUser').style.display = 'flex';
            document.getElementById('navUserName').textContent = name;
            document.getElementById('dropdownName').textContent = name;
            document.getElementById('dropdownEmail').textContent = email;
        }
        function showError(id, msg) {
            const el = document.getElementById(id);
            el.textContent = msg;
            el.style.display = 'block';
        }

        // Restore login state on page load
        window.addEventListener('DOMContentLoaded', () => {
            const user = sessionStorage.getItem('busmania_user');
            if (user) {
                const { name, email } = JSON.parse(user);
                setLoggedIn(name, email);
            }
        });
    </script>

    @stack('scripts')
</body>
</html>
