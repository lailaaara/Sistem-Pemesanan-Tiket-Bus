-- ============================================
-- DATABASE: sistem_pemesanan_bus
-- ============================================
CREATE DATABASE sistem_pemesanan_bus;
-- Setelah database dibuat, pilih database di pgAdmin GUI sebelum menjalankan tabel/data
-- Dibuat Hening wijaya Imanda
-- ============================================
-- TABEL: bus
-- ============================================
CREATE TABLE public.bus (
    bus_id SERIAL PRIMARY KEY,
    no_polisi VARCHAR(20),
    nama_bus VARCHAR(100),
    kapasitas INT,
    status_bus VARCHAR(20),
    kelas VARCHAR(50) DEFAULT 'Ekonomi',
    fasilitas TEXT DEFAULT ''
);

-- ============================================
-- TABEL: jadwal
-- ============================================
CREATE TABLE public.jadwal (
    id_jadwal SERIAL PRIMARY KEY,
    bus_id INT,
    rute_id INT,
    tanggal_berangkat DATE,
    jam_berangkat TIME,
    harga NUMERIC(12,2),
    kursi_tersedia INT,
    status_jadwal VARCHAR(20),
    id_admin INT
);

-- ============================================
-- TABEL: kursi
-- ============================================
CREATE TABLE IF NOT EXISTS public.kursi (
    id_kursi SERIAL PRIMARY KEY,
    no_kursi VARCHAR(10),
    id_bus INT
);
-- ============================================
-- TABEL: pemesanan_pembayaran
-- ============================================
CREATE TABLE public.pemesanan_pembayaran (
    pemesanan_id SERIAL PRIMARY KEY,
    user_id INT,
    jadwal_id INT,
    kode_booking VARCHAR(50),
    tanggal_pemesanan TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    jumlah_kursi INT,
    total_harga NUMERIC(12,2),
    metode_pembayaran VARCHAR(50),
    tanggal_bayar TIMESTAMP,
    status_pembayaran VARCHAR(20)
);
-- Dibuat Yosia Mario Hematang
-- ============================================
-- TABEL: tiket
-- ============================================
CREATE TABLE public.tiket (
    tiket_id SERIAL PRIMARY KEY,
    pemesanan_id INT,
    id_kursi INT,
    kode_tiket VARCHAR(50),
    status_tiket VARCHAR(20),
    nama_penumpang VARCHAR(100) DEFAULT '',
    no_hp VARCHAR(20) DEFAULT '',
    no_identitas VARCHAR(50) DEFAULT ''
);

-- ============================================
-- TABEL: rute
-- ============================================
CREATE TABLE public.rute (
    rute_id SERIAL PRIMARY KEY,
    kota_asal VARCHAR(100),
    kota_tujuan VARCHAR(100),
    jarak_km INT,
    id_admin INT,
    gambar VARCHAR(255) DEFAULT ''
);

-- ============================================
-- TABEL: users
-- ============================================
CREATE TABLE public.users (
    id_user SERIAL PRIMARY KEY,
    nama VARCHAR(100),
    email VARCHAR(100),
    password TEXT,
    role VARCHAR(10) CHECK (role IN ('admin','user'))
);

-- ============================================
-- SAMPLE DATA: bus
-- ============================================
INSERT INTO public.bus (no_polisi, nama_bus, kapasitas, status_bus, kelas, fasilitas) VALUES
('H1234AA','Bus A',40,'aktif','Eksekutif','AC, WiFi, Toilet, Selimut'),
('H5678BB','Bus B',35,'aktif','Ekonomi','AC, WiFi'),
('H9012CC','Bus C',50,'maintenance','Eksekutif','AC, WiFi, Toilet');

-- ============================================
-- SAMPLE DATA: rute
-- ============================================
INSERT INTO public.rute (kota_asal, kota_tujuan, jarak_km, id_admin, gambar) VALUES
('Semarang','Jakarta',450,1,'rute_1.jpg'),
('Semarang','Surabaya',312,1,'rute_2.jpg'),
('Semarang','Yogyakarta',114,1,'rute_3.jpg');

-- ============================================
-- SAMPLE DATA: jadwal
-- ============================================
INSERT INTO public.jadwal (bus_id, rute_id, tanggal_berangkat, jam_berangkat, harga, kursi_tersedia, status_jadwal, id_admin) VALUES
(1,1,'2026-06-10','08:00:00',150000,38,'aktif',1),
(2,2,'2026-06-15','10:00:00',150000,40,'aktif',1);

-- ============================================
-- SAMPLE DATA: kursi
-- ============================================
INSERT INTO public.kursi (no_kursi,id_bus) VALUES
('A1',1),('A2',1),('A3',1),('B1',2),('B2',2);

-- ============================================
-- SAMPLE DATA: users
-- ============================================
INSERT INTO public.users (nama,email,password,role) VALUES
('Budi','budi@gmail.com','$2y$10$hashadmin','admin'),
('Clara Garcia','clara@mail.com','$2y$10$hashuser','user');

-- ============================================
-- SAMPLE DATA: pemesanan_pembayaran
-- ============================================
INSERT INTO public.pemesanan_pembayaran (user_id,jadwal_id,kode_booking,jumlah_kursi,total_harga,metode_pembayaran,status_pembayaran) VALUES
(1,1,'BOOK3127',2,300000,'Transfer Bank','pending');

-- ============================================
-- SAMPLE DATA: tiket
-- ============================================
INSERT INTO public.tiket (pemesanan_id,id_kursi,kode_tiket,status_tiket) VALUES
(1,1,'TIKET4575','aktif'),
(1,2,'TIKET1516','aktif');

-- ============================================
-- TABEL: promo
-- ============================================
CREATE TABLE public.promo (
    promo_id SERIAL PRIMARY KEY,
    kode_promo VARCHAR(50) UNIQUE,
    deskripsi TEXT,
    tipe_diskon VARCHAR(20) CHECK (tipe_diskon IN ('persen','nominal')),
    nilai_diskon NUMERIC(12,2),
    min_pembelian NUMERIC(12,2) DEFAULT 0,
    maks_diskon NUMERIC(12,2) DEFAULT 0,
    kuota INT,
    terpakai INT DEFAULT 0,
    status_promo VARCHAR(20) DEFAULT 'aktif',
    berlaku_mulai DATE,
    berlaku_sampai DATE
);

-- ============================================
-- SAMPLE DATA: promo
-- ============================================
INSERT INTO public.promo (kode_promo, deskripsi, tipe_diskon, nilai_diskon, min_pembelian, maks_diskon, kuota, berlaku_mulai, berlaku_sampai) VALUES
('DISKON10', 'Diskon 10% minimal pembelian 100rb', 'persen', 10, 100000, 50000, 100, '2024-01-01', '2026-12-31'),
('HEMAT50K', 'Potongan langsung 50rb minimal pembelian 200rb', 'nominal', 50000, 200000, 0, 50, '2024-01-01', '2026-12-31');
