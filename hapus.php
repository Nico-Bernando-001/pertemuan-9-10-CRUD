<?php
require_once "config.php";
require_once "functions.php";

$id = $_GET['id'] ?? "";

if ($id === "" || !is_numeric($id)) {
    die("ID tidak valid.");
}

$mahasiswa = ambilMahasiswaById($conn, (int) $id);

if ($mahasiswa === null) {
    die("Data mahasiswa tidak ditemukan.");
}

$error = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (hapusMahasiswa($conn, (int) $id)) {
        header("Location: index.php?status=deleted");
        exit;
    }
    $error = "Data gagal dihapus.";
}

$pageTitle = "Hapus Mahasiswa";
include "components/header.php";
?>

<h1>Hapus Mahasiswa</h1>
<p><a href="index.php">← Kembali ke Daftar</a></p>
<hr>

<?php if ($error !== ""): ?>
    <div class="error-box"><?= htmlspecialchars($error) ?></div>
<?php endif; ?>

<div class="card">
    <p><strong>⚠️ Apakah Anda yakin ingin menghapus data mahasiswa berikut?</strong></p>

    <table>
        <tr><th>NIM</th><td><?= htmlspecialchars($mahasiswa['nim']) ?></td></tr>
        <tr><th>Nama</th><td><?= htmlspecialchars($mahasiswa['nama']) ?></td></tr>
        <tr><th>Email</th><td><?= htmlspecialchars($mahasiswa['email']) ?></td></tr>
        <tr>
            <th>Program Studi</th>
            <td><?= htmlspecialchars($mahasiswa['nama_prodi']) ?>
                (<?= htmlspecialchars($mahasiswa['kode_prodi']) ?>)</td>
        </tr>
        <tr><th>Tahun Angkatan</th><td><?= htmlspecialchars($mahasiswa['tahun_angkatan']) ?></td></tr>
    </table>

    <form method="POST" style="margin-top:20px;">
        <button type="submit" style="background:#c0392b;">Ya, Hapus</button>
        <a href="index.php" style="margin-left:10px;">Batal</a>
    </form>
</div>

<?php include "components/footer.php"; ?>