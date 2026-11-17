<?php
require_once "config.php";
require_once "functions.php";

$nama         = "";
$email        = "";
$tahunAngkatan = "";
$idProdi      = "";
$nimPreview   = "";
$errors       = [];

$resultProdi = ambilSemuaProdi($conn);

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $nama          = trim($_POST["nama"] ?? "");
    $email         = trim($_POST["email"] ?? "");
    $password      = trim($_POST["password"] ?? "");
    $tahunAngkatan = (int) ($_POST["tahun_angkatan"] ?? 0);
    $idProdi       = (int) ($_POST["id_prodi"] ?? 0);

    if ($nama === "")          $errors[] = "Nama wajib diisi.";
    if ($email === "")         $errors[] = "Email wajib diisi.";
    elseif (!filter_var($email, FILTER_VALIDATE_EMAIL))
                               $errors[] = "Format email tidak valid.";
    if ($password === "")      $errors[] = "Password wajib diisi.";
    if ($tahunAngkatan < 1900 || $tahunAngkatan > 2100)
                               $errors[] = "Tahun angkatan tidak valid.";
    if ($idProdi <= 0)         $errors[] = "Program studi wajib dipilih.";

    if (count($errors) === 0) {
        // NIM selalu di-generate server-side (tidak percaya input client)
        $nim = generateNIM($conn, $idProdi, $tahunAngkatan);

        if (!$nim) {
            $errors[] = "Gagal membuat NIM otomatis.";
        } else {
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
            $berhasil = tambahMahasiswa(
                $conn, $nim, $nama, $email, $hashedPassword, $tahunAngkatan, $idProdi
            );

            if ($berhasil) {
                header("Location: index.php");
                exit;
            }
            $errors[] = "Data gagal disimpan. NIM atau email mungkin sudah digunakan.";
        }
    }
}

$pageTitle = "Tambah Mahasiswa";
include "components/header.php";
?>

<h1>Tambah Mahasiswa</h1>
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
    <p>
        <label>NIM (otomatis)</label><br>
        <input type="text" id="nim" name="nim" value="<?= htmlspecialchars($nimPreview) ?>"
               readonly placeholder="Pilih Program Studi & Tahun Angkatan dulu"
               style="background:#f0f0f0;font-weight:bold;">
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
        <label>Password</label><br>
        <input type="password" name="password" required>
    </p>

    <p>
        <label>Tahun Angkatan</label><br>
        <input type="number" id="tahun_angkatan" name="tahun_angkatan"
               min="1900" max="2100" value="<?= htmlspecialchars($tahunAngkatan) ?>"
               required placeholder="contoh: 2025">
    </p>

    <p>
        <label>Program Studi</label><br>
        <select id="id_prodi" name="id_prodi" required>
            <option value="">-- Pilih Program Studi --</option>
            <?php while ($prodi = $resultProdi->fetch_assoc()): ?>
                <option value="<?= $prodi["id_prodi"] ?>"
                    <?= $idProdi == $prodi["id_prodi"] ? "selected" : "" ?>>
                    <?= htmlspecialchars($prodi["kode_prodi"] . " - " . $prodi["nama_prodi"]) ?>
                </option>
            <?php endwhile; ?>
        </select>
    </p>

    <button type="submit">Simpan</button>
</form>

<script src="assets/js/script.js"></script>

<?php include "components/footer.php"; ?>