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

$errors = [];

// Nilai awal form = data dari DB
$nama       = $mahasiswa['nama'];
$email      = $mahasiswa['email'];
$tahun      = (int) $mahasiswa['tahun_angkatan'];
$idProdi    = (int) $mahasiswa['id_prodi'];
$nimPreview = $mahasiswa['nim'];   // default: NIM lama

$resultProdi = ambilSemuaProdi($conn);

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Ambil input
    $nama    = trim($_POST['nama'] ?? "");
    $email   = trim($_POST['email'] ?? "");
    $tahun   = (int) ($_POST['tahun_angkatan'] ?? 0);
    $idProdi = (int) ($_POST['id_prodi'] ?? 0);

    // Validasi
    if ($nama === "")   $errors[] = "Nama wajib diisi.";
    if ($email === "")  $errors[] = "Email wajib diisi.";
    elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = "Format email tidak valid.";
    if ($tahun < 1900 || $tahun > 2100) $errors[] = "Tahun angkatan tidak valid.";
    if ($idProdi <= 0)  $errors[] = "Program studi wajib dipilih.";

    if (count($errors) === 0) {
        // ==============================================
        // TENTUKAN NIM — kunci logika edit
        // ==============================================
        $oldScope = [(int)$mahasiswa['id_prodi'], (int)$mahasiswa['tahun_angkatan']];
        $newScope = [$idProdi, $tahun];

        if ($oldScope === $newScope) {
            // Scope tidak berubah → NIM TETAP
            $nim       = $mahasiswa['nim'];
            $nomorUrut = (int) $mahasiswa['nomor_urut'];
        } else {
            // Scope berubah → regenerate NIM di scope baru
            $nim = generateNIM($conn, $idProdi, $tahun);
            if ($nim === null) {
                $errors[] = "Gagal membuat NIM otomatis.";
            } else {
                $nomorUrut  = (int) substr($nim, -3);
                $nimPreview = $nim;
            }
        }

        // Update jika tidak ada error baru
        if (count($errors) === 0) {
            $ok = updateMahasiswa(
                $conn, (int)$id,
                $nim, $nama, $email, $tahun, $nomorUrut, $idProdi
            );
            if ($ok) {
                header("Location: index.php?status=updated");
                exit;
            }
            $errors[] = "Data gagal diperbarui. NIM atau email mungkin sudah dipakai.";
        }
    }
}

$pageTitle = "Edit Mahasiswa";
include "components/header.php";
?>

<h1>Edit Mahasiswa</h1>
<p><a href="index.php">← Kembali ke Daftar</a></p>
<hr>

<?php if (count($errors) > 0): ?>
    <div class="error-box">
        <h3>Terjadi Kesalahan</h3>
        <ul>
            <?php foreach ($errors as $e): ?>
                <li><?= htmlspecialchars($e) ?></li>
            <?php endforeach; ?>
        </ul>
    </div>
<?php endif; ?>

<form method="POST" class="card">
    <!-- id tersembunyi supaya JS tahu ini mode edit -->
    <input type="hidden" id="edit_id" value="<?= (int)$mahasiswa['id'] ?>">

    <p>
        <label>NIM (otomatis)</label><br>
        <input type="text" id="nim" name="nim"
               value="<?= htmlspecialchars($nimPreview) ?>"
               readonly
               style="background:#f0f0f0;font-weight:bold;">
        <small style="color:#666;display:block;margin-top:4px;">
            NIM akan berubah otomatis jika <strong>Program Studi</strong>
            atau <strong>Tahun Angkatan</strong> diubah.
        </small>
    </p>

    <p>
        <label>Nama</label><br>
        <input type="text" name="nama" value="<?= htmlspecialchars($nama) ?>" required>
    </p>

    <p>
        <label>Email</label><br>
        <input type="email" name="email" value="<?= htmlspecialchars($email) ?>" required>
    </p>

    <p>
        <label>Tahun Angkatan</label><br>
        <input type="number" id="tahun_angkatan" name="tahun_angkatan"
               min="1900" max="2100" value="<?= htmlspecialchars($tahun) ?>" required>
    </p>

    <p>
        <label>Program Studi</label><br>
        <select id="id_prodi" name="id_prodi" required>
            <option value="">-- Pilih Program Studi --</option>
            <?php while ($prodi = $resultProdi->fetch_assoc()): ?>
                <option value="<?= $prodi['id_prodi'] ?>"
                    <?= $idProdi == $prodi['id_prodi'] ? 'selected' : '' ?>>
                    <?= htmlspecialchars($prodi['kode_prodi'] . ' - ' . $prodi['nama_prodi']) ?>
                </option>
            <?php endwhile; ?>
        </select>
    </p>

    <button type="submit">Simpan Perubahan</button>
    <a href="index.php" style="margin-left:10px;">Batal</a>
</form>

<script src="assets/js/script.js"></script>

<?php include "components/footer.php"; ?>