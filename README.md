# Concert Ticket Booking System

Project website fullstack sederhana menggunakan PHP Native, MySQL, Bootstrap 5, HTML5, CSS3, dan JavaScript.

## Fitur Utama

- Login / Register / Logout
- Session role admin dan customer
- Dashboard admin lengkap
- CRUD event konser (kode event, nama, artis, lokasi, tanggal, jam, harga, kuota, poster, deskripsi, status)
- Manajemen user customer
- Manajemen booking dan transaksi
- Upload bukti pembayaran customer
- Laporan transaksi dengan filter tanggal dan tombol print
- Search event, filter status, pagination, dan badge status warna berbeda

## Struktur Folder

- `/auth` - halaman autentikasi
- `/admin` - halaman panel admin
- `/customer` - halaman customer
- `/config` - konfigurasi database dan session
- `/includes` - komponen reusable (header, footer, navbar, sidebar, helper)
- `/assets/css` - file CSS custom
- `/assets/js` - file JavaScript
- `/assets/images` - direktori gambar upload
- `/database` - file SQL seed data

## Instalasi

1. Tempatkan folder proyek di `E:\XAMPP\htdocs\Concert Ticket Booking System`
2. Jalankan XAMPP dan aktifkan Apache serta MySQL
3. Buat database baru dengan import file SQL: `database/concert_ticket_booking.sql`
4. Buka `config/database.php` dan sesuaikan koneksi jika perlu
5. Akses proyek melalui browser:
   - `http://localhost/Concert%20Ticket%20Booking%20System`

## Login Default

- Admin:
  - Email: `admin@concert.com`
  - Password: `password`

- Customer:
  - Email: `rizky@mail.com`
  - Password: `password123`

## Catatan

- Semua query menggunakan **prepared statement** dan sanitasi input dasar.
- Project dibuat dengan tema konser modern, warna cerah, dan UI ramah portofolio.
- Poster/Payment/Avatar menggunakan folder upload di `assets/images/`.

---

## Ringkasan Fitur Lengkap

### 1) Autentikasi
- Register akun customer.
- Login dengan role **admin** dan **customer**.
- Logout.

### 2) Admin
- **CRUD Event**: tambah, edit, hapus, dan tampilkan daftar event.
- **Manajemen Booking**: melihat transaksi booking.
- **Laporan**: rekap transaksi dengan filter tanggal serta aksi print.
- **Manajemen User**: melihat data user customer.

### 3) Customer
- Lihat daftar event (search + filter status + pagination).
- Lihat detail event.
- Booking tiket.
- Upload bukti pembayaran.
- Melihat tiket/invoice (sesuai alur halaman yang tersedia).

### 4) UI/UX
- Menggunakan **Bootstrap 5** untuk komponen dan layout responsif.
- Menampilkan badge status event/booking dengan warna berbeda.
- Fallback tampilan poster jika file gambar belum tersedia.

---

## Struktur Folder (Detail)
- `auth/`: login & register
- `admin/`: dashboard admin, crud event, booking, reports, users
- `customer/`: dashboard customer, events, event detail, booking, invoice/ticket, upload payment
- `config/`: koneksi database & session helper
- `includes/`: header/navbar/footer/sidebar dan helper functions
- `assets/`: CSS/JS dan folder gambar (`posters/`, `payments/`, `avatars/`)
- `database/`: SQL seed & struktur tabel

---

## Konfigurasi & Instalasi (Lebih detail)

1. Pastikan **Apache** dan **MySQL** di XAMPP aktif.
2. Buat database baru bernama: `concert_ticket_booking`.
3. Import file SQL:
   - `database/concert_ticket_booking.sql`
4. Cek dan sesuaikan:
   - `config/database.php` (DB_HOST, DB_USER, DB_PASS, DB_NAME)
5. Akses via browser:
   - `http://localhost/Concert%20Ticket%20Booking%20System`

---

## Catatan Implementasi

- Poster/Payment/Avatar mengikuti aturan `.gitignore` (file upload user tidak ikut terkomit ke GitHub), tetapi folder kosong tetap tersimpan menggunakan `.gitkeep`.
- Jika poster yang dimaksud belum tersedia, UI menggunakan **placeholder fallback**.
- Prepared statement digunakan pada query berbasis input pengguna.



