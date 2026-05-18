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

- Semua query menggunakan prepared statement dan sanitasi input dasar.
- Project dibuat dengan tema konser modern, warna cerah, dan UI ramah portofolio.
- Jika post upload belum tersedia, tampilan akan menampilkan placeholder fallback.
