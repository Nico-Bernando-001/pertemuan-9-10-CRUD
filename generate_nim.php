<?php
require_once "config.php";
require_once "functions.php";

header('Content-Type: application/json');

$idProdi = (int) ($_GET['id_prodi'] ?? 0);
$tahun   = (int) ($_GET['tahun'] ?? 0);
$id      = (int) ($_GET['id'] ?? 0);   // opsional, hanya ada di mode edit

if ($idProdi <= 0 || $tahun < 1900 || $tahun > 2100) {
    echo json_encode(['nim' => '', 'mode' => 'invalid']);
    exit;
}

// MODE EDIT: kalau scope tidak berubah, kembalikan NIM lama
if ($id > 0) {
    $mahasiswa = ambilMahasiswaById($conn, $id);
    if (!$mahasiswa) {
        echo json_encode(['nim' => '', 'mode' => 'not_found']);
        exit;
    }
    $scopeUnchanged =
        ((int)$mahasiswa['id_prodi'] === $idProdi
         && (int)$mahasiswa['tahun_angkatan'] === $tahun);

    if ($scopeUnchanged) {
        echo json_encode(['nim' => $mahasiswa['nim'], 'mode' => 'unchanged']);
        exit;
    }
}

// MODE TAMBAH atau SCOPE BERUBAH: generate baru
$nim = generateNIM($conn, $idProdi, $tahun);
echo json_encode(['nim' => $nim ?? '', 'mode' => 'new']);