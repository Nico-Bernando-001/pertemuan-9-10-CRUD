<?php
require_once "config.php";
require_once "functions.php";

$result = ambilSemuaMahasiswa($conn);
$pageTitle = "Data Mahasiswa";

$statusMessages = [
    'added'   => 'Data mahasiswa berhasil ditambahkan.',
    'updated' => 'Data mahasiswa berhasil diperbarui.',
    'deleted' => 'Data mahasiswa berhasil dihapus.',
];
$status = $_GET['status'] ?? '';

include "components/header.php";
?>

<h1>Data Mahasiswa</h1>
<p><a href="tambah.php" class="btn">+ Tambah Mahasiswa</a></p>

<?php if (isset($statusMessages[$status])): ?>
    <div class="success-box"><?= htmlspecialchars($statusMessages[$status]) ?></div>
<?php endif; ?>

<hr>

<?php if ($result && $result->num_rows > 0): ?>
    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>NIM</th>
                <th>Nama</th>
                <th>Email</th>
                <th>Program Studi</th>
                <th>Tahun</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
        <?php $no = 1; while ($row = $result->fetch_assoc()): ?>
            <tr>
                <td><?= $no++ ?></td>
                <td><?= htmlspecialchars($row["nim"]) ?></td>
                <td><?= htmlspecialchars($row["nama"]) ?></td>
                <td><?= htmlspecialchars($row["email"]) ?></td>
                <td><?= htmlspecialchars($row["nama_prodi"]) ?></td>
                <td><?= htmlspecialchars($row["tahun_angkatan"]) ?></td>
                <td class="actions">
                    <a href="edit.php?id=<?= (int)$row['id'] ?>" class="btn-edit">Edit</a>
                    <a href="hapus.php?id=<?= (int)$row['id'] ?>" class="btn-delete">Hapus</a>
                </td>
            </tr>
        <?php endwhile; ?>
        </tbody>
    </table>
<?php else: ?>
    <p>Belum ada data mahasiswa.</p>
<?php endif; ?>

<?php include "components/footer.php"; ?>