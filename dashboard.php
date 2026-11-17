<?php
require_once "config.php";
require_once "functions.php";

// Proteksi: hanya user yang sudah login
if (empty($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header("Location: login.php");
    exit;
}

$nim       = $_SESSION['nim'];
$mahasiswa = ambilMahasiswaByNIM($conn, $nim);

if (!$mahasiswa) {
    // session tidak valid (data dihapus dsb)
    session_destroy();
    header("Location: login.php");
    exit;
}

$nilaiAkhir = hitungNilaiAkhir(
    $mahasiswa['nilai_tugas'],
    $mahasiswa['nilai_uts'],
    $mahasiswa['nilai_uas']
);
$grade  = tentukanGrade($nilaiAkhir);
$status = tentukanStatusKelulusan($nilaiAkhir, $mahasiswa['kehadiran']);

$pageTitle = "Dashboard Mahasiswa";
include "components/header.php";
?>

<h1>Dashboard Mahasiswa</h1>
<p>Selamat datang, <strong><?= htmlspecialchars($mahasiswa['nama']) ?></strong>!</p>
<hr>

<div class="dashboard-grid">
    <div class="card">
        <h3>Data Pribadi</h3>
        <table>
            <tr><th>NIM</th><td><?= htmlspecialchars($mahasiswa['nim']) ?></td></tr>
            <tr><th>Nama</th><td><?= htmlspecialchars($mahasiswa['nama']) ?></td></tr>
            <tr><th>Email</th><td><?= htmlspecialchars($mahasiswa['email']) ?></td></tr>
            <tr><th>Program Studi</th><td><?= htmlspecialchars($mahasiswa['nama_prodi']) ?></td></tr>
            <tr><th>Tahun Angkatan</th><td><?= htmlspecialchars($mahasiswa['tahun_angkatan']) ?></td></tr>
        </table>
    </div>

    <div class="card">
        <h3>Nilai Akademik</h3>
        <table>
            <tr><th>Nilai Tugas</th><td><?= number_format($mahasiswa['nilai_tugas'], 2) ?></td></tr>
            <tr><th>Nilai UTS</th><td><?= number_format($mahasiswa['nilai_uts'], 2) ?></td></tr>
            <tr><th>Nilai UAS</th><td><?= number_format($mahasiswa['nilai_uas'], 2) ?></td></tr>
            <tr><th>Kehadiran</th><td><?= (int)$mahasiswa['kehadiran'] ?>%</td></tr>
            <tr class="highlight">
                <th>Nilai Akhir</th>
                <td><?= number_format($nilaiAkhir, 2) ?></td>
            </tr>
            <tr class="highlight">
                <th>Grade</th>
                <td><strong><?= $grade ?></strong></td>
            </tr>
            <tr class="highlight">
                <th>Status Kelulusan</th>
                <td>
                    <span class="badge <?= $status === 'LULUS' ? 'badge-lulus' : 'badge-gagal' ?>">
                        <?= $status ?>
                    </span>
                </td>
            </tr>
        </table>
    </div>
</div>

<p style="margin-top:20px;font-size:13px;color:#666;">
    <strong>Rumus Nilai Akhir:</strong> (Tugas × 30%) + (UTS × 30%) + (UAS × 40%)<br>
    <strong>Status LULUS:</strong> Nilai Akhir ≥ 60 <em>dan</em> Kehadiran ≥ 75%
</p>

<?php include "components/footer.php"; ?>