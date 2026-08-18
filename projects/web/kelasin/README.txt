# KelasIn — PHP + MySQL

## Struktur
KelasIn/
├── index.php
├── dashboard.php
├── class.php
├── class_add.php
├── attendance.php
├── recap.php
├── profile.php
├── logout.php
├── config/
│   ├── database.php
│   └── auth.php
├── includes/
│   ├── header.php
│   └── footer.php
├── assets/
│   ├── style.css
│   └── app.js
└── database/
    └── kelasin.sql

## Cara menjalankan di XAMPP
1. Copy folder `KelasIn` ke:
   `C:\xampp\htdocs\web\KelasIn\`
   Kalau folder `web` belum ada, buat sendiri.

2. Nyalakan Apache dan MySQL di XAMPP.

3. Buka:
   `http://localhost/phpmyadmin`

4. Import:
   `database/kelasin.sql`

   SQL ini otomatis membuat database `kelasin`, tabel, akun demo, dua kelas, beberapa siswa, dan contoh absensi.

5. Buka:
   `http://localhost/web/KelasIn/`

## Login demo
NISN: `1234567890`
Password: `12345`

## Yang sudah berfungsi
- Login/logout
- Dashboard Absen
- Daftar XI - RPL 1 dan XI - RPL 2
- Tambah kelas
- Tambah/hapus siswa
- Isi absensi berdasarkan tanggal
- Status Hadir / Izin / Sakit / Alpa
- Catatan absensi
- Rekapitulasi per tahun dan kelas
- Edit profil
- Responsive HP/laptop

Kalau project ini nanti dimasukkan ke portfolio utama, folder KelasIn cukup diletakkan di dalam folder `web` dan dibuka dari iframe/preview portfolio.
