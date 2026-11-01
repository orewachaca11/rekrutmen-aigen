<?php
/**
 * ====================================================================================
 * FILE: cetak_kriteria.php
 * MODUL: Cetak Laporan Data Aspek Kompetensi & Bobot
 * DESKRIPSI:
 * Menampilkan dokumen cetak formal Laporan Aspek Kompetensi, Bobot Preferensi ,
 * persentase bobot, dan keterangan sifat kriteria (Benefit / Cost) dengan
 * Kop Surat resmi PT Aigen Global Teknologi Tangerang. Sesuai Gambar 4.31 skripsi.
 * ====================================================================================
 */

require_once __DIR__ . '/config/database.php';
check_login();

// Mengambil seluruh data kriteria evaluasi dari database
$list_kriteria = query("SELECT * FROM kriteria ORDER BY kode_kriteria ASC");
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Data Aspek Kompetensi - PT Aigen Global Teknologi</title>
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
        <h3>LAPORAN DATA ASPEK KOMPETENSI</h3>
        <small>Standar Bobot & Parameter Seleksi Uji Kompetensi</small>
    </div>

    <!-- Tabel Data Aspek Kompetensi -->
    <table class="table-laporan">
        <thead>
            <tr>
                <th width="80">Kode</th>
                <th>Nama Aspek Kompetensi</th>
                <th width="120">Tipe Aspek Kompetensi</th>
                <th width="120">Bobot Preferensi </th>
                <th width="120">Persentase Bobot</th>
            </tr>
        </thead>
        <tbody>
            <?php 
            $total_w = 0;
            foreach ($list_kriteria as $k): 
                $total_w += $k['bobot'];
            ?>
                <tr>
                    <td class="text-center"><strong><?= htmlspecialchars($k['kode_kriteria']) ?></strong></td>
                    <td><?= htmlspecialchars($k['nama_kriteria']) ?></td>
                    <td class="text-center"><?= htmlspecialchars($k['tipe']) ?></td>
                    <td class="text-center font-monospace"><strong><?= number_format($k['bobot'], 2) ?></strong></td>
                    <td class="text-center"><?= ($k['bobot'] * 100) ?>%</td>
                </tr>
            <?php endforeach; ?>
        </tbody>
        <tfoot>
            <tr style="font-weight:bold; background-color:#f1f5f9;">
                <td colspan="3" class="text-right">TOTAL BOBOT PENILAIAN:</td>
                <td class="text-center font-monospace"><?= number_format($total_w, 2) ?></td>
                <td class="text-center"><?= ($total_w * 100) ?>%</td>
            </tr>
        </tfoot>
    </table>

    <!-- Keterangan Aspek Kompetensi (Sesuai Layout Gambar 4.31 Dokumen) -->
    <div style="font-size:12px; margin-bottom:30px; background:#f9fafb; padding:12px; border:1px solid #ddd; border-radius:4px;">
        <strong>Keterangan Tipe Aspek Kompetensi:</strong>
        <table style="width:100%; margin-top:5px; font-size:11px;">
            <?php foreach ($list_kriteria as $k): ?>
                <tr>
                    <td width="50"><strong><?= $k['kode_kriteria'] ?></strong></td>
                    <td width="220">= <?= $k['nama_kriteria'] ?></td>
                    <td>(<em><?= $k['tipe'] ?></em>)</td>
                </tr>
            <?php endforeach; ?>
        </table>
    </div>

    <!-- Tanda Tangan HRD -->
    <div class="ttd-box">
        <div class="tanggal">Jakarta, <?= tgl_indo(date('Y-m-d')) ?></div>
        <div class="jabatan">HRD PT Aigen Global Teknologi</div>
        <div class="nama">( _________________________ )</div>
    </div>
</div>

</body>
</html>
