# Desa Digital Sugihwaras

Aplikasi monolit berbasis Laravel 8 + Vue 3 untuk portal informasi desa beserta panel adminnya. Proyek ini meniru alur dan tata letak situs [tamang.digitaldesa.id](https://tamang.digitaldesa.id/) dengan nuansa warna biru dan hijau.

## Fitur Utama
- Halaman publik: beranda, profil desa, berita, dan galeri.
- Panel admin untuk mengelola berita, layanan digital, statistik, album galeri, perangkat desa, serta pengaturan tema dan konten.
- API terintegrasi menggunakan Laravel Sanctum-ready endpoints.
- Database seed berisi konten contoh (statistik, layanan, berita, galeri, perangkat desa) agar aplikasi bisa langsung dieksplorasi.

## Prasyarat
Pastikan perangkat sudah memiliki:
- PHP 7.4 atau lebih baru (cli & ekstensi standar Laravel).
- Composer 2.x.
- Node.js 18+ dan npm.
- SQLite (atau siapkan koneksi database lain yang kompatibel dengan Laravel).

## Struktur Direktori
`
repo-root/
+-- backend/            # Proyek Laravel + Vue (monolit)
+-- MOCK UP ...pdf
`
Semua perintah berikut dijalankan di dalam direktori ackend/.

## Langkah Instalasi
1. **Clone atau salin repo ini** ke mesin lokal Anda.
2. **Masuk ke direktori backend**
   `ash
   cd backend
   `
3. **Pasang dependensi PHP**
   `ash
   composer install
   `
4. **Pasang dependensi JavaScript**
   `ash
   npm install
   `
5. **Salin berkas environment** (opsional jika belum tersedia)
   `ash
   copy .env.example .env    # Windows
   # cp .env.example .env    # Mac/Linux
   `
6. **Konfigurasi environment**
   - Secara default .env sudah diset ke SQLite.
   - Jika ingin memakai database lain, sesuaikan variabel DB_*.
7. **Siapkan database**
   `ash
   php artisan migrate --seed
   `
   Perintah ini akan membuat tabel dan mengisi data contoh.
8. **Build asset front-end untuk produksi (opsional)**
   `ash
   npm run build
   `
   Untuk mode pengembangan gunakan 
pm run dev atau 
pm run build --watch (via 
pm run dev).

## Menjalankan Aplikasi
Buka dua terminal terpisah di folder ackend/:
1. **Jalankan Laravel**
   `ash
   php artisan serve
   `
   Secara default akan aktif di http://127.0.0.1:8000.
2. **Jalankan Vite dev server**
   `ash
   npm run dev
   `
   Vite akan menyediakan hot reload untuk komponen Vue.

Setelah kedua proses berjalan, buka browser ke http://127.0.0.1:8000 untuk melihat situs publik dan panel admin (/admin).

## Akun & Autentikasi
Saat ini panel admin tidak memerlukan autentikasi (akses langsung). Tambahkan mekanisme login sesuai kebutuhan sebelum deployment produksi.

## Perintah Penting Lain
- Jalankan ulang seeder dengan fresh migrate:
  `ash
  php artisan migrate:fresh --seed
  `
- Membersihkan cache konfigurasi:
  `ash
  php artisan config:cache
  php artisan route:cache
  `
- Menjalankan linting Tailwind/Vite (opsional):
  `ash
  npm run build
  `

## Catatan Deployment
- Gunakan 
pm run build lalu php artisan serve melalui web server (Nginx/Apache) di lingkungan produksi.
- Pastikan direktori storage/ dan ootstrap/cache/ memiliki izin tulis.
- Ganti URL dan kredensial pada .env sesuai domain produksi.

Selamat mengembangkan Aplikasi Desa Digital!
