<?php
/**
 * ====================================================================================
 * FILE: cetak_ranking.php
 * MODUL: Cetak Laporan Hasil Ranking Terurut (Rank 1..N)
 * DESKRIPSI:
 * Menampilkan dokumen cetak resmi perankingan peserta bootcamp IT berdasarkan
 * akumulasi skor uji kompetensi terbobot dari peringkat 1 tertinggi hingga terendah.
 * Dilengkapi highlight 3 besar, Kop Surat PT Aigen Global Teknologi, dan tanda tangan HRD.
 * Sesuai Gambar 4.34 dokumen skripsi.
 * ====================================================================================
 */

require_once __DIR__ . '/config/database.php';
check_login();

// Mengambil seluruh hasil seleksi join peserta terurut berdasarkan RANKING ASC (1, 2, 3...)
$list_ranking = query("SELECT h.*, p.nama_peserta, p.jurusan, p.jenis_kelamin 
    FROM hasil_seleksi h
    JOIN peserta p ON h.id_peserta = p.id_peserta 
    ORDER BY h.ranking ASC");
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Data Hasil Ranking - PT Aigen Global Teknologi</title>
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
        <h3>LAPORAN DATA HASIL RANKING</h3>
        <small>Peringkat Seleksi Bootcamp Berdasarkan Evaluasi Nilai Uji Kompetensi Terbobot</small>
    </div>

    <!-- Tabel Data Hasil Ranking -->
    <table class="table-laporan">
        <thead>
            <tr>
                <th width="50">Ranking</th>
                <th width="80">ID Pelamar</th>
                <th>Nama Pelamar</th>
                <th>Jurusan</th>
                <th width="90">Jenis Kelamin</th>
                <th width="100">Total Nilai </th>
                <th width="90">Status</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($list_ranking as $r): ?>
                <tr style="<?= $r['ranking'] <= 3 ? 'font-weight:bold; background-color:#f8fafc;' : '' ?>">
                    <td class="text-center font-monospace">
                        <?= $r['ranking'] == 1 ? '🥇 #1' : ($r['ranking'] == 2 ? '🥈 #2' : ($r['ranking'] == 3 ? '🥉 #3' : '#' . $r['ranking'])) ?>
                    </td>
                    <td class="text-center font-monospace"><?= htmlspecialchars($r['id_peserta']) ?></td>
                    <td><?= htmlspecialchars($r['nama_peserta']) ?></td>
                    <td><?= htmlspecialchars($r['jurusan']) ?></td>
                    <td class="text-center"><?= htmlspecialchars($r['jenis_kelamin']) ?></td>
                    <td class="text-center font-monospace"><?= number_format($r['total_nilai'], 4) ?></td>
                    <td class="text-center">
                        <span style="<?= $r['status'] === 'Lolos' ? 'color:#15803d;' : 'color:#b91c1c;' ?>">
                            <?= htmlspecialchars($r['status']) ?>
                        </span>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <div style="font-size:11px; color:#555; margin-bottom:20px;">
        * <em>Peringkat 1 tertinggi diraih oleh <strong><?= htmlspecialchars($list_ranking[0]['nama_peserta'] ?? '-') ?></strong> dengan total nilai preferensi <strong><?= number_format($list_ranking[0]['total_nilai'] ?? 0, 4) ?></strong>.</em>
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
