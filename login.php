<?php
require_once "config.php";
require_once "functions.php";

// Sudah login? langsung ke dashboard
if (!empty($_SESSION['logged_in']) && $_SESSION['logged_in'] === true) {
    header("Location: dashboard.php");
    exit;
}

$email = "";
$error = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $email    = trim($_POST["email"] ?? "");
    $password = trim($_POST["password"] ?? "");

    if ($email === "" || $password === "") {
        $error = "Email dan password wajib diisi.";
    } else {
        $user = loginMahasiswa($conn, $email, $password);

        if ($user) {
            $_SESSION['logged_in'] = true;
            $_SESSION['nim']       = $user['nim'];
            $_SESSION['nama']      = $user['nama'];
            header("Location: dashboard.php");
            exit;
        }
        $error = "Email atau password salah.";
    }
}

$pageTitle = "Login Mahasiswa";
include "components/header.php";
?>

<h1>Login Mahasiswa</h1>
<hr>

<?php if ($error !== ""): ?>
    <div class="error-box"><?= htmlspecialchars($error) ?></div>
<?php endif; ?>

<form method="POST" class="card">
    <p>
        <label>Email</label><br>
        <input type="email" name="email" value="<?= htmlspecialchars($email) ?>" required>
    </p>
    <p>
        <label>Password</label><br>
        <input type="password" name="password" required>
    </p>
    <button type="submit">Login</button>
</form>

<p style="margin-top:15px;font-size:13px;color:#666;">
    Contoh akun: <code>andi@ukrida.ac.id</code> / <code>123456</code>
</p>

<?php include "components/footer.php"; ?>