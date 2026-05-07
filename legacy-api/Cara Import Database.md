
1. Buka **pgAdmin** di komputer kamu.
2. Buat database baru dengan nama `tiket_bus`.
3. Klik kanan pada database `tiket_bus` -> pilih **Query Tool**.
4. Buka file `database.sql` yang ada di dalam repositori ini, lalu *copy* semua isinya.
5. *Paste* ke dalam Query Tool pgAdmin, blok semua teksnya, lalu tekan **F5** (Execute).

*(Cara alternatif via terminal: `psql -U postgres -d tiket_bus -f database.sql`)*

### 3. Setup Konfigurasi Database (PHP)
Buka file `utils/.env` dan pastikan `DATABASE_URI` sudah sesuai dengan kredensial PostgreSQL kamu. Jika menggunakan database lokal:
```env
DATABASE_URI = "host=localhost port=5432 dbname=tiket_bus user=postgres password=password_kamu sslmode=disable"
```
Atau jika menggunakan format URL:
```env
DATABASE_URI = "postgresql://postgres:password_kamu@localhost:5432/tiket_bus?sslmode=disable"
```
Sistem akan otomatis membaca file `.env` melalui `config/database.php`.

### 4. Test API
Base URL API kita adalah:
`http://localhost/tiket-bus-ta/Sistem-Pemesanan-Tiket-Bus/api/v1/`
*(Sesuaikan dengan nama folder di htdocs kalian masing-masing)*

Semua endpoint siap dipakai untuk integrasi ke Frontend!
