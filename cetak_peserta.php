<?php
/**
 * ====================================================================================
 * FILE: cetak_peserta.php
 * MODUL: Cetak Laporan Data Pelamar
 * DESKRIPSI:
 * Menampilkan dokumen cetak formal Laporan Data Pelamar Bootcamp IT dengan
 * Kop Surat resmi PT Aigen Global Teknologi Tangerang, tabel data bergaris,
 * dan blok tanda tangan HRD. Siap dicetak langsung atau disimpan ke format PDF.
 * Sesuai Gambar 4.30 dokumen skripsi.
 * ====================================================================================
 */

require_once __DIR__ . '/config/database.php';
check_login();

// Mengambil seluruh data peserta terdaftar dari database
$list_peserta = query("SELECT * FROM peserta ORDER BY id_peserta ASC");
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Data Pelamar - PT Aigen Global Teknologi</title>
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
        <h3>LAPORAN DATA PELAMAR</h3>
        <small>Penerimaan Anggota Bootcamp Tenaga Kerja IT</small>
    </div>

    <!-- Tabel Data -->
    <table class="table-laporan">
        <thead>
            <tr>
                <th width="40">No</th>
                <th width="70">ID Pelamar</th>
                <th>Nama Pelamar</th>
                <th>Jurusan</th>
                <th>Alamat</th>
                <th width="85">Tanggal Lahir</th>
                <th width="80">Jenis Kelamin</th>
                <th width="90">No Telepon</th>
            </tr>
        </thead>
        <tbody>
            <?php 
            $no = 1;
            foreach ($list_peserta as $p): 
            ?>
                <tr>
                    <td class="text-center"><?= $no++ ?></td>
                    <td class="text-center font-monospace"><strong><?= htmlspecialchars($p['id_peserta']) ?></strong></td>
                    <td><?= htmlspecialchars($p['nama_peserta']) ?></td>
                    <td><?= htmlspecialchars($p['jurusan']) ?></td>
                    <td><?= htmlspecialchars($p['alamat']) ?></td>
                    <td class="text-center"><?= date('d/m/Y', strtotime($p['tanggal_lahir'])) ?></td>
                    <td class="text-center"><?= htmlspecialchars($p['jenis_kelamin']) ?></td>
                    <td class="text-center"><?= htmlspecialchars($p['no_telepon']) ?></td>
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
