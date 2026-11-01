<?php
/**
 * ====================================================================================
 * FILE: cetak_seleksi.php
 * MODUL: Cetak Laporan Hasil Seleksi Kelulusan
 * DESKRIPSI:
 * Menampilkan dokumen cetak rekapitulasi status kelulusan peserta (Lolos / Tidak Lolos)
 * beserta nilai preferensi  lengkap dengan Kop Surat resmi PT Aigen Global Teknologi.
 * Sesuai Gambar 4.33 dokumen skripsi.
 * ====================================================================================
 */

require_once __DIR__ . '/config/database.php';
check_login();

// Mengambil seluruh data hasil seleksi join data peserta terurut berdasarkan ID
$list_hasil = query("SELECT h.*, p.nama_peserta, p.jurusan, p.jenis_kelamin 
    FROM hasil_seleksi h
    JOIN peserta p ON h.id_peserta = p.id_peserta 
    ORDER BY p.id_peserta ASC");
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Data Hasil Seleksi - PT Aigen Global Teknologi</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="assets/css/print.css">
</head>
<body>

<div class="print-toolbar no-print">
    <a href="laporan.php" style="text-decoration:none; color:#333; font-size:14px; font-weight:bold;">
        <i class="fa-solid fa-arrow-left"></i> Kembali ke Menu Laporan
    </a>
    <button onclick="window.print();" style="background:#0d6efd; color:#fff; border:none; padding:8px 16px; border-radius:5px; cursor:pointer; font-weight:bold;">
        <i class="fa-solid fa-print"></i> Cetak / Simpan PDF
    </button>
</div>

<div class="print-sheet">
    <!-- Kop Surat Resmi PT Aigen Global Teknologi -->
    <div class="kop-surat">
        <img src="assets/logo-aigen.jpg" class="kop-logo" alt="Logo PT Aigen">
        <div class="kop-text">
            <h2>PT AIGEN GLOBAL TEKNOLOGI</h2>
            <p>Jl. Raya Rawa Buntu No.18D, Ciater, Kec. Serpong,<br>Kota Tangerang Selatan, Banten 15310</p>
        </div>
        <img src="assets/logo-aigen.jpg" class="kop-logo" alt="Logo PT Aigen">
    </div>

    <!-- Judul Dokumen -->
    <div class="laporan-title">
        <h3>LAPORAN DATA HASIL SELEKSI</h3>
        <small>Hasil Evaluasi Kelayakan Calon Anggota Bootcamp Tenaga Kerja IT</small>
    </div>

    <!-- Tabel Data Hasil Seleksi -->
    <table class="table-laporan">
        <thead>
            <tr>
                <th width="40">No</th>
                <th width="80">ID Pelamar</th>
                <th>Nama Pelamar</th>
                <th>Jurusan</th>
                <th width="100">Jenis Kelamin</th>
                <th width="110">Total Nilai </th>
                <th width="100">Status</th>
            </tr>
        </thead>
        <tbody>
            <?php 
            $no = 1;
            foreach ($list_hasil as $h): 
            ?>
                <tr>
                    <td class="text-center"><?= $no++ ?></td>
                    <td class="text-center font-monospace"><strong><?= htmlspecialchars($h['id_peserta']) ?></strong></td>
                    <td><?= htmlspecialchars($h['nama_peserta']) ?></td>
                    <td><?= htmlspecialchars($h['jurusan']) ?></td>
                    <td class="text-center"><?= htmlspecialchars($h['jenis_kelamin']) ?></td>
                    <td class="text-center font-monospace"><strong><?= number_format($h['total_nilai'], 4) ?></strong></td>
                    <td class="text-center">
                        <strong><?= htmlspecialchars($h['status']) ?></strong>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <!-- Tanda Tangan HRD -->
    <div class="ttd-box">
        <div class="tanggal">Jakarta, <?= tgl_indo(date('Y-m-d')) ?></div>
        <div class="jabatan">HRD PT Aigen Global Teknologi</div>
        <div class="nama">( _________________________ )</div>
    </div>
</div>

</body>
</html>
