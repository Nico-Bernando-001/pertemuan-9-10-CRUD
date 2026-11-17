<?php

$host = "localhost";
$user = "root";
$pass = "";
$db   = "student_portal";

$conn = new mysqli($host, $user, $pass, $db);

if ($conn->connect_error) {
    die("koneksi gagal: " . $conn->connect_error);
}

// Mulai session jika belum
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

?>