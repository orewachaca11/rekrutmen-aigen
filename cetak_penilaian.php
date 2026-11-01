<?php
/**
 * ====================================================================================
 * FILE: cetak_penilaian.php
 * MODUL: Cetak Laporan Data Penilaian Pelamar (Matriks X)
 * DESKRIPSI:
 * Menampilkan dokumen cetak rekapitulasi penilaian setiap peserta pada 7 kriteria
 * evaluasi (C1 s/d C7) lengkap dengan Kop Surat resmi PT Aigen Global Teknologi.
 * Sesuai Gambar 4.32 dokumen skripsi.
 * ====================================================================================
 */

require_once __DIR__ . '/config/database.php';
check_login();

// Mengambil seluruh data penilaian join dengan data identitas peserta
$list_penilaian = query("SELECT pn.*, ps.nama_peserta, ps.jurusan, ps.jenis_kelamin 
    FROM penilaian pn 
    JOIN peserta ps ON pn.id_peserta = ps.id_peserta 
    ORDER BY pn.id_peserta ASC");
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Data Penilaian - PT Aigen Global Teknologi</title>
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

<div class="print-sheet" style="width: 297mm; min-height: 210mm;">
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
        <h3>LAPORAN DATA PENILAIAN PELAMAR</h3>
        <small>Rekapitulasi Penilaian Uji Teknis Hasil Uji Kompetensi Calon Karyawan Bootcamp</small>
    </div>

    <!-- Tabel Data Penilaian -->
    <table class="table-laporan">
        <thead>
            <tr>
                <th width="35">No</th>
                <th width="65">ID Pelamar</th>
                <th>Nama Pelamar</th>
                <th>Jurusan</th>
                <th width="50">JK</th>
                <th width="55" title="Skor Hackerrank Frontend">Skor FE</th>
                <th width="55" title="Skor Hackerrank Backend">Skor BE</th>
                <th width="50" title="Usia">Usia</th>
                <th width="65" title="Pendidikan Terakhir">Pendidikan</th>
                <th width="75" title="Lama Pengalaman Kerja">Pengalaman</th>
                <th width="75" title="Waktu Tempuh ke Perusahaan">Waktu Tempuh</th>
                <th width="65" title="Hasil Tes Tertulis">Hasil Tes</th>
            </tr>
        </thead>
        <tbody>
            <?php 
            $no = 1;
            foreach ($list_penilaian as $p): 
            ?>
                <tr>
                    <td class="text-center"><?= $no++ ?></td>
                    <td class="text-center font-monospace"><strong><?= htmlspecialchars($p['id_peserta']) ?></strong></td>
                    <td><?= htmlspecialchars($p['nama_peserta']) ?></td>
                    <td><?= htmlspecialchars($p['jurusan']) ?></td>
                    <td class="text-center"><?= $p['jenis_kelamin'] === 'Laki-Laki' ? 'L' : 'P' ?></td>
                    <td class="text-center font-monospace"><?= $p['c1'] ?></td>
                    <td class="text-center font-monospace"><?= $p['c2'] ?></td>
                    <td class="text-center font-monospace"><?= $p['c3'] ?></td>
                    <td class="text-center font-monospace"><?= $p['c4'] ?></td>
                    <td class="text-center font-monospace"><?= $p['c5'] ?></td>
                    <td class="text-center font-monospace"><?= $p['c6'] ?></td>
                    <td class="text-center font-monospace"><?= $p['c7'] ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <!-- Tanda Tangan HRD -->
    <div class="ttd-box">
        <div class="tanggal">Jakarta, <?= tgl_indo(date('Y-m-d')) ?></div>
        <div class="jabatan">Tim Penguji / HRD PT Aigen Global Teknologi</div>
        <div class="nama">( _________________________ )</div>
    </div>
</div>

</body>
</html>
