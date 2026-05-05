
1. Buka **pgAdmin** di komputer kamu.
2. Buat database baru dengan nama `tiket_bus`.
3. Klik kanan pada database `tiket_bus` -> pilih **Query Tool**.
4. Buka file `database.sql` yang ada di dalam repositori ini, lalu *copy* semua isinya.
5. *Paste* ke dalam Query Tool pgAdmin, blok semua teksnya, lalu tekan **F5** (Execute).

*(Cara alternatif via terminal: `psql -U postgres -d tiket_bus -f database.sql`)*

### 3. Setup Konfigurasi Database (PHP)
Buka file `config/database.php` dan pastikan konfigurasi password PostgreSQL sesuai dengan password di komputer kamu:
```php
$host     = "localhost";
$port     = "5432";
$dbname   = "tiket_bus";
$user     = "postgres";
$password = "password_kalian"; // <- UBAH INI JIKA BEDA
```

### 4. Test API
Base URL API kita adalah:
`http://localhost/tiket-bus-ta/Sistem-Pemesanan-Tiket-Bus/api/v1/`
*(Sesuaikan dengan nama folder di htdocs kalian masing-masing)*

Semua endpoint siap dipakai untuk integrasi ke Frontend!
