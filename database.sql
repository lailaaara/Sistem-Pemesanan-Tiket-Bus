--
-- PostgreSQL database dump
--

\restrict syOdoLpmYOuC1Jv0acN4wKcID0B2KDOy09lKaQrrs8HHkZPp27gCEVCM2cqTOjQ

-- Dumped from database version 18.3
-- Dumped by pg_dump version 18.3

SET statement_timeout = 0;
SET lock_timeout = 0;
SET idle_in_transaction_session_timeout = 0;
SET transaction_timeout = 0;
SET client_encoding = 'UTF8';
SET standard_conforming_strings = on;
SELECT pg_catalog.set_config('search_path', '', false);
SET check_function_bodies = false;
SET xmloption = content;
SET client_min_messages = warning;
SET row_security = off;

SET default_tablespace = '';

SET default_table_access_method = heap;

--
-- Name: bus; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.bus (
    bus_id integer NOT NULL,
    no_polisi character varying(20),
    nama_bus character varying(100),
    kapasitas integer,
    status_bus character varying(20),
    kelas character varying(50) DEFAULT 'Ekonomi'::character varying,
    fasilitas text DEFAULT ''::text
);


ALTER TABLE public.bus OWNER TO postgres;

--
-- Name: bus_bus_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.bus_bus_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.bus_bus_id_seq OWNER TO postgres;

--
-- Name: bus_bus_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.bus_bus_id_seq OWNED BY public.bus.bus_id;


--
-- Name: jadwal; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.jadwal (
    id_jadwal integer NOT NULL,
    bus_id integer,
    rute_id integer,
    tanggal_berangkat date,
    jam_berangkat time without time zone,
    harga integer,
    kursi_tersedia integer,
    status_jadwal character varying(20),
    id_admin integer
);


ALTER TABLE public.jadwal OWNER TO postgres;

--
-- Name: jadwal_id_jadwal_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.jadwal_id_jadwal_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.jadwal_id_jadwal_seq OWNER TO postgres;

--
-- Name: jadwal_id_jadwal_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.jadwal_id_jadwal_seq OWNED BY public.jadwal.id_jadwal;


--
-- Name: kursi; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.kursi (
    id_kursi integer NOT NULL,
    no_kursi character varying(10),
    id_bus integer
);


ALTER TABLE public.kursi OWNER TO postgres;

--
-- Name: kursi_id_kursi_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.kursi_id_kursi_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.kursi_id_kursi_seq OWNER TO postgres;

--
-- Name: kursi_id_kursi_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.kursi_id_kursi_seq OWNED BY public.kursi.id_kursi;


--
-- Name: pemesanan_pembayaran; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.pemesanan_pembayaran (
    pemesanan_id integer NOT NULL,
    user_id integer,
    jadwal_id integer,
    kode_booking character varying(50),
    tanggal_pemesanan timestamp without time zone DEFAULT CURRENT_TIMESTAMP,
    jumlah_kursi integer,
    total_harga integer,
    metode_pembayaran character varying(50),
    tanggal_bayar timestamp without time zone,
    status_pembayaran character varying(20)
);


ALTER TABLE public.pemesanan_pembayaran OWNER TO postgres;

--
-- Name: pemesanan_pembayaran_pemesanan_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.pemesanan_pembayaran_pemesanan_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.pemesanan_pembayaran_pemesanan_id_seq OWNER TO postgres;

--
-- Name: pemesanan_pembayaran_pemesanan_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.pemesanan_pembayaran_pemesanan_id_seq OWNED BY public.pemesanan_pembayaran.pemesanan_id;


--
-- Name: promo; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.promo (
    promo_id integer NOT NULL,
    kode_promo character varying(50) NOT NULL,
    deskripsi text,
    tipe_diskon character varying(20) DEFAULT 'persen'::character varying,
    nilai_diskon integer NOT NULL,
    min_pembelian integer DEFAULT 0,
    maks_diskon integer DEFAULT 0,
    kuota integer DEFAULT 100,
    terpakai integer DEFAULT 0,
    berlaku_mulai date,
    berlaku_sampai date,
    status_promo character varying(20) DEFAULT 'aktif'::character varying
);


ALTER TABLE public.promo OWNER TO postgres;

--
-- Name: promo_promo_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.promo_promo_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.promo_promo_id_seq OWNER TO postgres;

--
-- Name: promo_promo_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.promo_promo_id_seq OWNED BY public.promo.promo_id;


--
-- Name: rute; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.rute (
    rute_id integer NOT NULL,
    kota_asal character varying(100),
    kota_tujuan character varying(100),
    jarak_km integer,
    id_admin integer,
    gambar character varying(255) DEFAULT ''::character varying
);


ALTER TABLE public.rute OWNER TO postgres;

--
-- Name: rute_rute_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.rute_rute_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.rute_rute_id_seq OWNER TO postgres;

--
-- Name: rute_rute_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.rute_rute_id_seq OWNED BY public.rute.rute_id;


--
-- Name: seat_hold; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.seat_hold (
    hold_id integer NOT NULL,
    id_kursi integer NOT NULL,
    id_jadwal integer NOT NULL,
    user_id integer NOT NULL,
    expired_at timestamp without time zone NOT NULL,
    created_at timestamp without time zone DEFAULT CURRENT_TIMESTAMP
);


ALTER TABLE public.seat_hold OWNER TO postgres;

--
-- Name: seat_hold_hold_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.seat_hold_hold_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.seat_hold_hold_id_seq OWNER TO postgres;

--
-- Name: seat_hold_hold_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.seat_hold_hold_id_seq OWNED BY public.seat_hold.hold_id;


--
-- Name: tiket; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.tiket (
    tiket_id integer NOT NULL,
    pemesanan_id integer,
    id_kursi integer,
    kode_tiket character varying(50),
    status_tiket character varying(20),
    nama_penumpang character varying(100) DEFAULT ''::character varying,
    no_hp character varying(20) DEFAULT ''::character varying,
    no_identitas character varying(50) DEFAULT ''::character varying
);


ALTER TABLE public.tiket OWNER TO postgres;

--
-- Name: tiket_tiket_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.tiket_tiket_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.tiket_tiket_id_seq OWNER TO postgres;

--
-- Name: tiket_tiket_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.tiket_tiket_id_seq OWNED BY public.tiket.tiket_id;


--
-- Name: users; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.users (
    id_user integer NOT NULL,
    nama character varying(100),
    email character varying(100),
    password text,
    role character varying(10),
    CONSTRAINT users_role_check CHECK (((role)::text = ANY ((ARRAY['admin'::character varying, 'user'::character varying])::text[])))
);


ALTER TABLE public.users OWNER TO postgres;

--
-- Name: users_id_user_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.users_id_user_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.users_id_user_seq OWNER TO postgres;

--
-- Name: users_id_user_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.users_id_user_seq OWNED BY public.users.id_user;


--
-- Name: bus bus_id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.bus ALTER COLUMN bus_id SET DEFAULT nextval('public.bus_bus_id_seq'::regclass);


--
-- Name: jadwal id_jadwal; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.jadwal ALTER COLUMN id_jadwal SET DEFAULT nextval('public.jadwal_id_jadwal_seq'::regclass);


--
-- Name: kursi id_kursi; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.kursi ALTER COLUMN id_kursi SET DEFAULT nextval('public.kursi_id_kursi_seq'::regclass);


--
-- Name: pemesanan_pembayaran pemesanan_id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.pemesanan_pembayaran ALTER COLUMN pemesanan_id SET DEFAULT nextval('public.pemesanan_pembayaran_pemesanan_id_seq'::regclass);


--
-- Name: promo promo_id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.promo ALTER COLUMN promo_id SET DEFAULT nextval('public.promo_promo_id_seq'::regclass);


--
-- Name: rute rute_id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.rute ALTER COLUMN rute_id SET DEFAULT nextval('public.rute_rute_id_seq'::regclass);


--
-- Name: seat_hold hold_id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.seat_hold ALTER COLUMN hold_id SET DEFAULT nextval('public.seat_hold_hold_id_seq'::regclass);


--
-- Name: tiket tiket_id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.tiket ALTER COLUMN tiket_id SET DEFAULT nextval('public.tiket_tiket_id_seq'::regclass);


--
-- Name: users id_user; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.users ALTER COLUMN id_user SET DEFAULT nextval('public.users_id_user_seq'::regclass);


--
-- Data for Name: bus; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.bus (bus_id, no_polisi, nama_bus, kapasitas, status_bus, kelas, fasilitas) FROM stdin;
1	H1234AA	Bus A	40	aktif	Eksekutif	AC, WiFi, Toilet, Selimut
\.


--
-- Data for Name: jadwal; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.jadwal (id_jadwal, bus_id, rute_id, tanggal_berangkat, jam_berangkat, harga, kursi_tersedia, status_jadwal, id_admin) FROM stdin;
1	1	1	2026-06-10	08:00:00	150000	38	aktif	1
2	1	1	2026-06-15	10:00:00	150000	40	aktif	1
\.


--
-- Data for Name: kursi; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.kursi (id_kursi, no_kursi, id_bus) FROM stdin;
1	A1	1
2	A2	1
3	A3	1
4	A4	1
\.


--
-- Data for Name: pemesanan_pembayaran; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.pemesanan_pembayaran (pemesanan_id, user_id, jadwal_id, kode_booking, tanggal_pemesanan, jumlah_kursi, total_harga, metode_pembayaran, tanggal_bayar, status_pembayaran) FROM stdin;
2	1	1	BOOK3127	2026-05-04 14:12:24.419998	2	300000	\N	\N	pending
\.


--
-- Data for Name: promo; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.promo (promo_id, kode_promo, deskripsi, tipe_diskon, nilai_diskon, min_pembelian, maks_diskon, kuota, terpakai, berlaku_mulai, berlaku_sampai, status_promo) FROM stdin;
1	PROMOSETIA	Diskon 20% untuk pelanggan setia	persen	20	100000	50000	100	0	2025-01-01	2025-12-31	aktif
2	DISKON50K	Potongan Rp 50.000 minimal beli 200K	nominal	50000	200000	0	50	0	2025-01-01	2025-12-31	aktif
3	NEWUSER10	Diskon 10% untuk pengguna baru	persen	10	0	30000	200	0	2025-01-01	2025-12-31	aktif
\.


--
-- Data for Name: rute; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.rute (rute_id, kota_asal, kota_tujuan, jarak_km, id_admin, gambar) FROM stdin;
1	Semarang	Jakarta	450	1	rute_1.jpg
\.


--
-- Data for Name: seat_hold; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.seat_hold (hold_id, id_kursi, id_jadwal, user_id, expired_at, created_at) FROM stdin;
\.


--
-- Data for Name: tiket; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.tiket (tiket_id, pemesanan_id, id_kursi, kode_tiket, status_tiket, nama_penumpang, no_hp, no_identitas) FROM stdin;
1	2	1	TIKET4575	aktif			
2	2	2	TIKET1516	aktif			
\.


--
-- Data for Name: users; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.users (id_user, nama, email, password, role) FROM stdin;
1	Budi	budi@gmail.com	$2y$10$Rmie69R7iP7cMeZR96xWa.C.qBMCDDC/JjXsjjDQleioe8VSxpPjm	admin
2	Clara Garcia	clara@mail.com	$2y$10$l1oSwhOyYHae3Ti4eNroRODC5Yjq2VHYqPpExsCNrUUy.hRcZsKHK	user
\.


--
-- Name: bus_bus_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.bus_bus_id_seq', 1, true);


--
-- Name: jadwal_id_jadwal_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.jadwal_id_jadwal_seq', 2, true);


--
-- Name: kursi_id_kursi_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.kursi_id_kursi_seq', 4, true);


--
-- Name: pemesanan_pembayaran_pemesanan_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.pemesanan_pembayaran_pemesanan_id_seq', 2, true);


--
-- Name: promo_promo_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.promo_promo_id_seq', 7, true);


--
-- Name: rute_rute_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.rute_rute_id_seq', 1, true);


--
-- Name: seat_hold_hold_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.seat_hold_hold_id_seq', 1, false);


--
-- Name: tiket_tiket_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.tiket_tiket_id_seq', 2, true);


--
-- Name: users_id_user_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.users_id_user_seq', 2, true);


--
-- Name: bus bus_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.bus
    ADD CONSTRAINT bus_pkey PRIMARY KEY (bus_id);


--
-- Name: jadwal jadwal_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.jadwal
    ADD CONSTRAINT jadwal_pkey PRIMARY KEY (id_jadwal);


--
-- Name: kursi kursi_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.kursi
    ADD CONSTRAINT kursi_pkey PRIMARY KEY (id_kursi);


--
-- Name: pemesanan_pembayaran pemesanan_pembayaran_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.pemesanan_pembayaran
    ADD CONSTRAINT pemesanan_pembayaran_pkey PRIMARY KEY (pemesanan_id);


--
-- Name: promo promo_kode_promo_key; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.promo
    ADD CONSTRAINT promo_kode_promo_key UNIQUE (kode_promo);


--
-- Name: promo promo_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.promo
    ADD CONSTRAINT promo_pkey PRIMARY KEY (promo_id);


--
-- Name: rute rute_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.rute
    ADD CONSTRAINT rute_pkey PRIMARY KEY (rute_id);


--
-- Name: seat_hold seat_hold_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.seat_hold
    ADD CONSTRAINT seat_hold_pkey PRIMARY KEY (hold_id);


--
-- Name: tiket tiket_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.tiket
    ADD CONSTRAINT tiket_pkey PRIMARY KEY (tiket_id);


--
-- Name: users users_email_key; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.users
    ADD CONSTRAINT users_email_key UNIQUE (email);


--
-- Name: users users_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.users
    ADD CONSTRAINT users_pkey PRIMARY KEY (id_user);


--
-- Name: idx_seat_hold_expired; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX idx_seat_hold_expired ON public.seat_hold USING btree (expired_at);


--
-- Name: idx_seat_hold_kursi_jadwal; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX idx_seat_hold_kursi_jadwal ON public.seat_hold USING btree (id_kursi, id_jadwal);


--
-- Name: jadwal jadwal_bus_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.jadwal
    ADD CONSTRAINT jadwal_bus_id_fkey FOREIGN KEY (bus_id) REFERENCES public.bus(bus_id);


--
-- Name: jadwal jadwal_id_admin_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.jadwal
    ADD CONSTRAINT jadwal_id_admin_fkey FOREIGN KEY (id_admin) REFERENCES public.users(id_user);


--
-- Name: jadwal jadwal_rute_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.jadwal
    ADD CONSTRAINT jadwal_rute_id_fkey FOREIGN KEY (rute_id) REFERENCES public.rute(rute_id);


--
-- Name: kursi kursi_id_bus_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.kursi
    ADD CONSTRAINT kursi_id_bus_fkey FOREIGN KEY (id_bus) REFERENCES public.bus(bus_id);


--
-- Name: pemesanan_pembayaran pemesanan_pembayaran_jadwal_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.pemesanan_pembayaran
    ADD CONSTRAINT pemesanan_pembayaran_jadwal_id_fkey FOREIGN KEY (jadwal_id) REFERENCES public.jadwal(id_jadwal);


--
-- Name: pemesanan_pembayaran pemesanan_pembayaran_user_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.pemesanan_pembayaran
    ADD CONSTRAINT pemesanan_pembayaran_user_id_fkey FOREIGN KEY (user_id) REFERENCES public.users(id_user);


--
-- Name: rute rute_id_admin_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.rute
    ADD CONSTRAINT rute_id_admin_fkey FOREIGN KEY (id_admin) REFERENCES public.users(id_user);


--
-- Name: seat_hold seat_hold_id_jadwal_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.seat_hold
    ADD CONSTRAINT seat_hold_id_jadwal_fkey FOREIGN KEY (id_jadwal) REFERENCES public.jadwal(id_jadwal) ON DELETE CASCADE;


--
-- Name: seat_hold seat_hold_id_kursi_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.seat_hold
    ADD CONSTRAINT seat_hold_id_kursi_fkey FOREIGN KEY (id_kursi) REFERENCES public.kursi(id_kursi) ON DELETE CASCADE;


--
-- Name: seat_hold seat_hold_user_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.seat_hold
    ADD CONSTRAINT seat_hold_user_id_fkey FOREIGN KEY (user_id) REFERENCES public.users(id_user) ON DELETE CASCADE;


--
-- Name: tiket tiket_id_kursi_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.tiket
    ADD CONSTRAINT tiket_id_kursi_fkey FOREIGN KEY (id_kursi) REFERENCES public.kursi(id_kursi);


--
-- Name: tiket tiket_pemesanan_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.tiket
    ADD CONSTRAINT tiket_pemesanan_id_fkey FOREIGN KEY (pemesanan_id) REFERENCES public.pemesanan_pembayaran(pemesanan_id);


--
-- PostgreSQL database dump complete
--

\unrestrict syOdoLpmYOuC1Jv0acN4wKcID0B2KDOy09lKaQrrs8HHkZPp27gCEVCM2cqTOjQ

