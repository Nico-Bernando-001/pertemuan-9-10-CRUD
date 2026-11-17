<?php
if (session_status() === PHP_SESSION_NONE) session_start();
$isLoggedIn  = !empty($_SESSION['logged_in']) && $_SESSION['logged_in'] === true;
$currentPage = basename($_SERVER['PHP_SELF']);
$title       = $pageTitle ?? 'Student Portal';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= htmlspecialchars($title) ?></title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
<header>
    <h1>🎓 Student Portal</h1>
    <nav>
        <a href="index.php">Daftar Mahasiswa</a>
        <?php if ($isLoggedIn): ?>
            <a href="dashboard.php">Dashboard</a>
            <a href="logout.php" class="logout">Logout</a>
            <span class="user-info">Halo, <?= htmlspecialchars($_SESSION['nama'] ?? '') ?></span>
        <?php else: ?>
            <a href="login.php">Login</a>
        <?php endif; ?>
    </nav>
</header>
<main>