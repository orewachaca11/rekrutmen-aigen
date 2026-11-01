# Sistem Informasi Rekrutmen dan Uji Kompetensi Calon Karyawan Bootcamp IT
## PT Aigen Global Teknologi Tangerang

Aplikasi web terintegrasi untuk pengelolaan proses rekrutmen, pelaksanaan uji kompetensi teknis, serta evaluasi pemeringkatan kelulusan calon karyawan bootcamp IT berbasis web.

---

## 👥 Tim Pengembang Sistem (Kelompok)
1. **Raden Muhammad Prakoso** (NPM: 202343501622) — *Project Lead, Analisis Sistem & Dokumentasi UI/UX*
2. **Muchammad Daffa Syaidan** (NPM: 202343501578) — *Modul Manajemen Data Pelamar & 7 Aspek Uji Kompetensi*
3. **Muhammad Firmansyah** (NPM: 202343501599) — *Modul Penilaian Penguji Teknis & Engine Cetak Laporan Resmi*

**Program Studi Informatika — Fakultas Teknik dan Ilmu Komputer**  
**Universitas Indraprasta PGRI (UNINDRA) Jakarta — Tahun Akademik 2025/2026**

---

## 🚀 Fitur Utama Sistem

1. **Autentikasi Multi-Role & Session Guard**
   - Hak akses pemisahan peran antara **Administrator (HRD)** dan **Tim Penguji Teknis (Assessor)**.
2. **Manajemen Data Pelamar (Peserta Bootcamp)**
   - Pengelolaan 30 data master kandidat pelamar bootcamp IT (CRUD lengkap).
3. **Konfigurasi 7 Aspek Uji Kompetensi**
   - C1: Logika Coding Frontend
   - C2: Algoritma & Basis Data Backend
   - C3: Usia & Kesiapan Kerja
   - C4: Pendidikan Terakhir
   - C5: Pengalaman Proyek/Portofolio
   - C6: Efisiensi & Waktu Tempuh
   - C7: Tes Tertulis IT & Problem Solving
4. **Workspace Lembar Penilaian Penguji**
   - Form input skor evaluasi teknis berskala terstandarisasi 1 sampai 4.
5. **Engine Evaluasi Nilai & Passing Grade**
   - Komputasi otomatis akumulasi skor terbobot dan penentuan status kelulusan berdasarkan ambang batas **Passing Grade 0.70 (70%)**.
6. **Dashboard Analitik & Visualisasi Grafik**
   - Diagram batang interaktif (Chart.js) untuk memetakan capaian skor seluruh kandidat.
7. **Penerbitan 5 Format Laporan Resmi**
   - Laporan Data Pelamar, Laporan Aspek Kompetensi, Laporan Penilaian Penguji, Laporan Peringkat Seleksi, dan Laporan Rekapitulasi Kelulusan Resmi ber-Kop Surat PT Aigen Global Teknologi dan kolom tanda tangan pimpinan.

---

## 🛠️ Panduan Instalasi (XAMPP & MySQL)

### Opsi 1: Setup Otomatis (1-Klik via Browser)
1. Buka browser dan akses alamat:
   ```
   http://localhost/rekrutmen-aigen/setup.php
   ```
2. Klik tombol **"Jalankan Setup Database Otomatis"**.
3. Selesai! Database `rekrutmen_aigen`, tabel, dan seluruh 30 data pelamar awal langsung terpasang.

### Opsi 2: Import Manual via phpMyAdmin
1. Buka browser ke `http://localhost/phpmyadmin/`.
2. Buat database baru bernama `rekrutmen_aigen`.
3. Klik menu **Import**, pilih file:
   ```
   database/rekrutmen_aigen.sql
   ```
4. Klik **Kirim (Go)**.

---

## 🔑 Kredensial Akun Login Default

| Role | Username | Password | Nama Pengguna | Wewenang |
|:---|:---|:---|:---|:---|
| **Admin** | `admin` | `admin123` | Raden Muhammad Prakoso | Akses Penuh (Master Data, Evaluasi & Laporan) |
| **Admin** | `daffa` | `daffa123` | Muchammad Daffa Syaidan | Akses Penuh (Master Data & Aspek Nilai) |
| **Admin** | `firman` | `firman123` | Muhammad Firmansyah | Akses Penuh (Penilaian & Cetak Laporan) |
| **Penguji** | `penguji` | `penguji123` | Tim Penguji Bootcamp IT | Input Skor Uji Teknis Pelamar |

---

## 📁 Struktur Direktori Proyek

```
rekrutmen-aigen/
├── assets/                  # CSS, gambar logo PT Aigen, & aset grafis
├── config/                  # Koneksi database MySQL & session helper
├── database/                # Skrip rekrutmen_aigen.sql
├── docs/                    # Dokumen SRS & 5 Dokumentasi Diagram UML (.docx & .png)
├── includes/                # Template header, sidebar, navbar, & footer
├── dashboard.php            # Beranda analitik sistem
├── pengguna.php             # Kelola akun admin & penguji
├── peserta.php              # Kelola 30 data master pelamar
├── kriteria.php             # Konfigurasi 7 aspek kompetensi
├── penilaian.php            # Workspace penilaian penguji
├── perhitungan.php          # Engine evaluasi skor terbobot & ranking
├── hasil.php                # Rekapitulasi kelulusan pelamar
├── laporan.php              # Menu cetak dokumen resmi
├── cetak_*.php              # Template cetak 5 format laporan
└── setup.php                # Installer database 1-klik
```
