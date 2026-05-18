# Panduan Asesmen BNSP – Junior Web Programmer (Concert Ticket Booking System)

Dokumen ini dibuat untuk membantu kamu menjelaskan project secara runtut saat asesmen BNSP. Bahasanya dibuat sederhana, mudah dihafal, dan mengikuti kompetensi Junior Web Programmer.

---

## A. Ringkasan Project (yang bisa kamu ucapkan saat mulai presentasi)
Project ini adalah **Sistem Booking Tiket Konser** berbasis **PHP Native + MySQL**.

Fitur utamanya:
- **Login/Register/Logout** dengan **role**: admin dan customer
- **Admin**: kelola event (CRUD), lihat booking, laporan transaksi
- **Customer**: lihat event, detail event, booking tiket, upload bukti pembayaran
- UI memakai **Bootstrap 5** agar tampilan rapi dan responsif

---

## B. Flow Project dari Awal sampai Akhir (ceritakan berurutan)

### 1) Pengguna membuka halaman
- Browser membuka `index.php`.
- Halaman menampilkan daftar event + filter pencarian (search & status).
- Jika belum login, tombol menuju register disiapkan.

### 2) Login / Register
- Customer membuat akun lewat `auth/register.php`.
- Login lewat `auth/login.php`.
- Setelah login:
  - role **admin** diarahkan ke `admin/dashboard.php`
  - role **customer** diarahkan ke `customer/dashboard.php`

### 3) Customer memilih event
- Customer melihat daftar event di `customer/events.php`.
- Customer klik **Detail** → `customer/event_detail.php`.
- Customer melakukan **booking** (biasanya melalui page booking yang terhubung ke detail).

### 4) Customer mengunggah bukti pembayaran
- Customer upload bukti pembayaran lewat `customer/upload_payment.php`.

### 5) Admin memproses transaksi
- Admin melihat daftar booking di `admin/bookings.php`.
- Admin bisa mengubah status transaksi dan melihat laporan.
- Laporan transaksi tersedia di `admin/reports.php`.

### 6) Customer melihat tiket/invoice
- Customer dapat melihat tiket/hasil booking (mis. `customer/ticket.php` atau `customer/invoice.php`) sesuai alur di project.

---

## C. Struktur Folder Project (jelaskan fungsi folder)

- `auth/`
  - Halaman **login** dan **register** user.
- `admin/`
  - Halaman panel **admin** (dashboard, kelola event, booking, laporan, manajemen user).
- `customer/`
  - Halaman panel **customer** (dashboard, list event, detail event, booking, upload payment, tiket/invoice).
- `config/`
  - Konfigurasi **database** dan **session**.
- `includes/`
  - Komponen reusable: header, navbar, sidebar, footer, fungsi helper.
- `assets/`
  - CSS, JS, dan gambar.
  - `assets/images/posters/` poster event (sebagian diabaikan dari commit sesuai `.gitignore`).
  - `assets/images/payments/` upload bukti pembayaran.
  - `assets/images/avatars/` foto profil.
- `database/`
  - SQL file untuk membuat tabel dan seed data: `database/concert_ticket_booking.sql`

---

## D. Fungsi File Penting (contoh yang paling sering ditanya)

### 1) `index.php`
- Halaman utama.
- Mengambil data event dari database.
- Memakai filter `search` dan `status` + pagination.
- Menampilkan kartu event (nama, artis, lokasi, tanggal/jam, harga, badge status).

### 2) `config/database.php`
- Berisi konfigurasi koneksi MySQL.
- Menyiapkan koneksi `mysqli` dan set charset.

### 3) `includes/functions.php`
- Helper untuk aplikasi.
- Contoh di project: `bindParams()` untuk membantu prepared statement.

### 4) `auth/register.php`
- Validasi input user (nama, email, phone, password).
- Mengecek email belum terdaftar.
- Hash password.
- Menyimpan user ke tabel `users`.

### 5) `auth/login.php`
- Validasi login.
- Mencocokkan email → ambil data user.
- Verifikasi password dengan `password_verify`.
- Set session: id, role, nama, email, foto.
- Redirect sesuai role.

### 6) `admin/dashboard.php`
- Menampilkan ringkasan data (jumlah event, customer, booking, total transaksi).
- Mengambil aktivitas terbaru dari database.

### 7) `customer/events.php` dan `customer/event_detail.php`
- Menampilkan list event + filter.
- `event_detail` menampilkan info lengkap event dan menyiapkan booking.

### 8) `customer/upload_payment.php`
- Mengunggah bukti pembayaran customer.
- Menyimpan referensi upload ke database.

### 9) `admin/events.php`
- CRUD event (lihat, tambah, edit, delete).

> Catatan saat asesmen: kamu tidak perlu hafal seluruh baris. Cukup jelaskan “fungsi besarnya” sesuai poin di atas.

---

## E. Alur Login (yang mudah dihafal)
1. User mengisi email dan password.
2. Sistem cari user berdasarkan email di tabel `users`.
3. Password dicek:
   - database menyimpan password dalam bentuk hash
   - input dicek menggunakan `password_verify`
4. Jika benar:
   - sistem menyimpan data ke **session**
   - sistem redirect ke halaman sesuai role

---

## F. Penjelasan Session (cara menjelaskannya simpel)
- Session dipakai untuk “ingat user sedang login”.
- Data yang disimpan biasanya:
  - `user_id`
  - `user_role` (admin/customer)
  - `user_name`, `user_email`, `user_photo`
- Saat user mengakses halaman admin, sistem mengecek role melalui helper.

---

## G. Penjelasan CRUD (Admin mengelola data event)

### CRUD untuk Event (umumnya di `admin/events.php`, `admin/add_event.php`, `admin/edit_event.php`, `admin/delete_event.php`)
- **Create (Tambah)**: admin isi form event baru → simpan ke database.
- **Read (Lihat)**: admin menampilkan daftar event dari database.
- **Update (Edit)**: admin mengubah data event → update ke database.
- **Delete (Hapus)**: admin menghapus event → hapus dari database.

Flow CRUD yang bisa kamu ucapkan:
- Semua perubahan data terjadi melalui request POST (untuk create/update) dan query DELETE.
- Setelah aksi sukses, biasanya ada redirect kembali ke halaman daftar.

---

## H. Koneksi Database (yang sering ditanya)
- Koneksi dilakukan dari `config/database.php`.
- Menggunakan MySQLi.
- Ada prepared statement untuk query yang butuh parameter.

Kalimat mudah:
> “Koneksi database dibuat sekali di config, lalu dipakai di halaman lain supaya data event, user, dan booking bisa diambil dan disimpan.”

---

## I. Relasi Tabel / Database (penjelasan relasi tanpa terlalu teknis)
Relasi umumnya seperti ini (konsepnya):
- **users** → menyimpan data user (admin/customer)
- **events** → menyimpan data konser
- **bookings** → transaksi booking customer untuk event tertentu

Artinya:
- Satu user bisa punya banyak booking.
- Satu event bisa dipesan oleh banyak user.
- Tabel `bookings` menghubungkan `users` dan `events`.

Untuk menjelaskan:
> “Tabel bookings itu seperti ‘jembatan’ antara customer dan event: siapa memesan event apa.”

---

## J. Bootstrap / Library yang digunakan
- Menggunakan **Bootstrap 5** untuk UI.
- Bootstrap membantu:
  - layout responsif (grid)
  - komponen (card, button, form)
  - navbar dan styling cepat
- Ada juga Bootstrap Icons dari CDN (untuk ikon tampilan).

---

## K. Debugging yang mungkin terjadi (contoh skenario + jawaban)

### 1) Halaman kosong / error saat koneksi DB
- Cek `config/database.php`:
  - DB name, user, password
  - apakah MySQL sudah jalan

### 2) Error upload gambar tidak tampil
- Cek folder `assets/images/...`.
- Pastikan `.gitignore` tidak membuat gambar tidak ter-upload ke GitHub (di project ini memang dibuat sengaja).

### 3) Prepared statement error
- Biasanya karena jumlah parameter tidak cocok dengan query.
- Solusi: cocokkan jumlah `?` dengan `bind_param`.

### 4) Session tidak bekerja
- Pastikan session dimulai sesuai `config/session.php`.
- Pastikan role check benar.

---

## L. Best Practice sederhana yang dipakai
- Menggunakan **prepared statement** (lebih aman dari SQL injection).
- Password disimpan pakai **hash** (bukan password plaintext).
- Input divalidasi sederhana (email, password, dll).
- Pemakaian komponen reusable (`includes/header.php`, `includes/navbar.php`) agar kode lebih rapi.

---

## M. Struktur Data yang digunakan
Struktur data dalam project ini umumnya:
- Data dari database ditampilkan ke UI sebagai:
  - array hasil query (mis. `$events->fetch_assoc()`)
- Parameter pencarian/pagination:
  - string filter `search`, `status`
  - angka `page`, `limit`, `offset`

Intinya:
> “Data event dan booking datang dari database dalam bentuk baris, lalu ditampilkan satu per satu ke HTML.”

---

## N. Keamanan Dasar Project
- **Prepared statement** untuk query parameter.
- **Password hashing** pada register.
- **Role-based access**:
  - admin hanya boleh akses halaman admin
  - customer hanya boleh akses halaman customer
- Upload file diarahkan ke folder khusus (walau untuk demo, tetap jelaskan batasan: misalnya validasi tipe file bisa ditingkatkan).

---

## O. Daftar Pertanyaan Assessor yang PALING mungkin ditanyakan + Jawaban sederhana

> Format ini bagus untuk kamu hafal cepat (langsung jawab 1-2 paragraf pendek).

### 1) “Project ini apa fungsinya?”
**Jawab:** Project ini untuk booking tiket konser. User bisa register dan login. Customer bisa lihat event, booking tiket, upload bukti bayar. Admin bisa kelola event dan memproses booking.

### 2) “Kenapa pakai role admin dan customer?”
**Jawab:** Agar akses fitur berbeda. Admin mengelola data event dan transaksi, sedangkan customer hanya mengakses fitur booking dan melihat tiket.

### 3) “Alur login seperti apa?”
**Jawab:** User login dengan email dan password. Sistem cari user di database, cek password memakai `password_verify`. Kalau benar, sistem buat session dan redirect sesuai role.

### 4) “Bagaimana keamanan password?”
**Jawab:** Password tidak disimpan mentah. Saat register, password di-hash dulu. Saat login, password input dicek dibandingkan dengan hash tersebut.

### 5) “Bagaimana keamanan query database?”
**Jawab:** Project memakai prepared statement dengan parameter. Ini mengurangi risiko SQL injection.

### 6) “CRUD itu apa yang kamu buat?”
**Jawab:** CRUD untuk event: admin bisa tambah event, lihat daftar event, edit event, dan hapus event.

### 7) “Jelaskan relasi tabel bookings dengan event dan user.”
**Jawab:** Booking itu menghubungkan user dan event. Satu booking milik satu user dan satu event, sehingga bisa diketahui siapa memesan event apa.

### 8) “Kenapa pakai Bootstrap?”
**Jawab:** Supaya tampilan lebih rapi dan responsif. Komponen UI jadi cepat dibuat dan konsisten.

### 9) “Kalau halaman admin tidak muncul, apa yang dicek?”
**Jawab:** Cek apakah session user_role benar. Pastikan login sebagai admin dan pengecekan role berjalan.

### 10) “Kalau demo upload bukti pembayaran gagal, apa penyebabnya?”
**Jawab:** Cek folder upload di `assets/images/payments/`, pastikan ada hak akses, dan pastikan path upload benar.

### 11) “Bagaimana pagination bekerja?”
**Jawab:** Sistem membagi data event per halaman menggunakan `LIMIT` dan `OFFSET`, lalu menampilkan tombol halaman sesuai total data.

### 12) “Bagaimana filter search dan status?”
**Jawab:** Sistem menerima query parameter dari URL. Data disaring pakai kondisi pada SQL berdasarkan keyword (search) dan nilai status.

### 13) “Apa yang membuat project kamu terstruktur?”
**Jawab:** Ada folder terpisah untuk admin, customer, auth, config, dan includes. Ada helper functions juga agar kode tidak berulang.

### 14) “Dokumentasi programnya ada?”
**Jawab:** Ada README yang menjelaskan setup dan fitur. Selain itu kode terstruktur per halaman.

### 15) “Kalau kamu lupa syntax, cara kamu memperbaiki?”
**Jawab:** Saya cek error message, lalu cari bagian yang salah (misalnya nama variabel atau tanda kurung). Setelah itu saya cocokkan dengan pola kode di halaman lain yang mirip.

---

## P. Simulasi Tanya Jawab (Assessor vs Peserta)

### Sesi 1: Dasar Project
**Assessor:** “Project kamu tentang apa?”
**Peserta:** “Ini sistem booking tiket konser. Customer bisa login, lihat event, booking, dan upload bukti pembayaran. Admin bisa kelola event dan memproses transaksi.”

### Sesi 2: Login & Session
**Assessor:** “Bagaimana alur login?”
**Peserta:** “User isi email dan password. Sistem ambil data user dari database, cek password dengan hash, lalu simpan session dan redirect berdasarkan role.”

### Sesi 3: CRUD
**Assessor:** “CRUD yang mana kamu buat?”
**Peserta:** “CRUD event untuk admin. Admin bisa tambah, lihat, edit, dan hapus event.”

### Sesi 4: Debugging
**Assessor:** “Kalau error muncul saat demo, kamu lakukan apa?”
**Peserta:** “Saya lihat pesan error, cek bagian query atau session, lalu cek koneksi database dan path file upload. Jika perlu saya bandingkan dengan halaman lain yang mirip.”

---

## Q. Kemungkinan Asessor Minta Revisi + Cara Jawab

1) **Minta perjelas keamanan upload**
- Jawab: “Di demo ini upload diarahkan ke folder tertentu. Jika diminta, saya bisa tambah validasi tipe file dan ukuran file, serta batasi ekstensi.”

2) **Minta perjelas penanganan error**
- Jawab: “Saya bisa tambah try-catch dan pesan error yang lebih jelas untuk koneksi DB dan proses transaksi.”

3) **Minta perjelas struktur tabel**
- Jawab: “Booking menghubungkan user dan event. Tabel bookings menyimpan user_id dan event_id, plus status dan total tiket.”

---

## R. Error yang Sering Muncul Saat Demo + Solusi Singkat

- **Database connection failed**
  - Solusi: pastikan MySQL jalan, cek `DB_NAME` dan kredensial.

- **Undefined variable / class not found**
  - Solusi: pastikan `require_once` path includes benar dan urutan include sesuai.

- **Upload tidak tampil**
  - Solusi: pastikan file tersimpan di folder yang sama dengan path yang dipanggil.

- **Page blank setelah login**
  - Solusi: cek session start dan redirect URL `getBaseUrl()`.

---

## S. Tips Presentasi Biar Terlihat Paham

- Pakai kalimat “flow story” bukan baca kode.
- Saat ditanya file, jawab format:
  - “Fungsi file X adalah… (1 kalimat)”
  - “Dipakai untuk proses Y (1 kalimat)”
- Tunjukkan demo singkat:
  1) index.php list event
  2) register → login
  3) customer booking → upload payment
  4) admin kelola event atau lihat booking

---

## T. Checklist Hafalan Cepat (versi super singkat)
- **Fitur:** login/register, booking, upload payment, CRUD event, laporan.
- **Role:** admin mengelola, customer memesan.
- **Session:** simpan user_id + user_role.
- **DB:** ambil data event, simpan booking, ambil relasi.
- **Keamanan:** prepared statement + password hashing.
- **CRUD:** create/read/update/delete pada event.
- **UI:** Bootstrap 5.
- **Debug:** cek error, DB jalan, session benar, path upload.

---

# Penutup
Dokumen ini sudah disiapkan agar kamu bisa menjawab pertanyaan assessor dengan bahasa sederhana dan runtut. Saat demo, fokus ke “alur” dan “alasan” kenapa fitur dibuat seperti itu.

