<?php

/* ============================================
   QUERY DATA
   ============================================ */

function ambilSemuaMahasiswa($conn) {
    $sql = "SELECT
                mahasiswa.id,
                mahasiswa.nim,
                mahasiswa.nama,
                mahasiswa.email,
                mahasiswa.tahun_angkatan,
                program_studi.kode_prodi,
                program_studi.nama_prodi
            FROM mahasiswa
            JOIN program_studi ON mahasiswa.id_prodi = program_studi.id_prodi
            ORDER BY mahasiswa.nim ASC";
    return $conn->query($sql);
}

function ambilSemuaProdi($conn) {
    return $conn->query("SELECT * FROM program_studi ORDER BY nama_prodi ASC");
}

function ambilProdiById($conn, $id) {
    $stmt = $conn->prepare("SELECT * FROM program_studi WHERE id_prodi = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    return $stmt->get_result()->fetch_assoc();
}

/**
 * Ambil satu mahasiswa by id — JOIN prodi.
 * Return null jika tidak ditemukan.
 */
function ambilMahasiswaById($conn, $id) {
    $stmt = $conn->prepare(
        "SELECT mahasiswa.*,
                program_studi.nama_prodi,
                program_studi.kode_prodi
         FROM mahasiswa
         JOIN program_studi ON mahasiswa.id_prodi = program_studi.id_prodi
         WHERE mahasiswa.id = ?"
    );
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows === 0) return null;
    return $result->fetch_assoc();
}

/* ============================================
   GENERATE NIM
   kode_prodi(2) + tahun_angkatan(4) + nomor_urut(3)
   ============================================ */

function generateNIM($conn, $idProdi, $tahunAngkatan) {
    $stmt = $conn->prepare("SELECT kode_prodi FROM program_studi WHERE id_prodi = ?");
    $stmt->bind_param("i", $idProdi);
    $stmt->execute();
    $res = $stmt->get_result();
    if ($res->num_rows === 0) return null;
    $kode = $res->fetch_assoc()['kode_prodi'];

    $stmt = $conn->prepare(
        "SELECT MAX(nomor_urut) AS max_urut
         FROM mahasiswa
         WHERE id_prodi = ? AND tahun_angkatan = ?"
    );
    $stmt->bind_param("ii", $idProdi, $tahunAngkatan);
    $stmt->execute();
    $row = $stmt->get_result()->fetch_assoc();
    $nextUrut = ((int)($row['max_urut'] ?? 0)) + 1;

    return $kode . $tahunAngkatan . str_pad($nextUrut, 3, '0', STR_PAD_LEFT);
}

/* ============================================
   INSERT MAHASISWA
   ============================================ */

function tambahMahasiswa($conn, $nim, $nama, $email, $password, $tahunAngkatan, $idProdi) {
    $nomorUrut = (int) substr($nim, -3);

    $stmt = $conn->prepare(
        "INSERT INTO mahasiswa
            (nim, nama, email, password, tahun_angkatan, nomor_urut, id_prodi)
         VALUES (?, ?, ?, ?, ?, ?, ?)"
    );
    if (!$stmt) return false;

    $stmt->bind_param("ssssiii", $nim, $nama, $email, $password, $tahunAngkatan, $nomorUrut, $idProdi);
    return $stmt->execute();
}

/* ============================================
   UPDATE & DELETE MAHASISWA
   ============================================ */

/**
 * Update mahasiswa termasuk NIM, tahun, dan nomor_urut.
 * NIM dan nomor_urut sudah ditentukan oleh caller (edit.php).
 */
function updateMahasiswa($conn, $id, $nim, $nama, $email, $tahunAngkatan, $nomorUrut, $idProdi) {
    $stmt = $conn->prepare(
        "UPDATE mahasiswa
         SET nim = ?, nama = ?, email = ?,
             tahun_angkatan = ?, nomor_urut = ?, id_prodi = ?
         WHERE id = ?"
    );
    if (!$stmt) return false;

    $stmt->bind_param("sssiiii", $nim, $nama, $email, $tahunAngkatan, $nomorUrut, $idProdi, $id);
    return $stmt->execute();
}

function hapusMahasiswa($conn, $id) {
    $stmt = $conn->prepare("DELETE FROM mahasiswa WHERE id = ?");
    $stmt->bind_param("i", $id);
    return $stmt->execute();
}

/* ============================================
   LOGIN & FETCH BY NIM (tidak berubah)
   ============================================ */

function loginMahasiswa($conn, $email, $password) {
    $stmt = $conn->prepare("SELECT * FROM mahasiswa WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $res = $stmt->get_result();
    if ($res->num_rows === 0) return false;

    $row = $res->fetch_assoc();
    if (password_verify($password, $row['password']) || $password === $row['password']) {
        return $row;
    }
    return false;
}

function ambilMahasiswaByNIM($conn, $nim) {
    $stmt = $conn->prepare(
        "SELECT mahasiswa.*, program_studi.kode_prodi, program_studi.nama_prodi
         FROM mahasiswa
         JOIN program_studi ON mahasiswa.id_prodi = program_studi.id_prodi
         WHERE mahasiswa.nim = ?"
    );
    $stmt->bind_param("s", $nim);
    $stmt->execute();
    return $stmt->get_result()->fetch_assoc();
}

/* ============================================
   PERHITUNGAN AKADEMIK (tidak berubah)
   ============================================ */

function hitungNilaiAkhir($tugas, $uts, $uas) {
    return ((float)$tugas * 0.30) + ((float)$uts * 0.30) + ((float)$uas * 0.40);
}

function tentukanGrade($nilaiAkhir) {
    if ($nilaiAkhir >= 85) return 'A';
    if ($nilaiAkhir >= 70) return 'B';
    if ($nilaiAkhir >= 60) return 'C';
    if ($nilaiAkhir >= 50) return 'D';
    return 'E';
}

function tentukanStatusKelulusan($nilaiAkhir, $kehadiran) {
    return ($nilaiAkhir >= 60 && $kehadiran >= 75) ? 'LULUS' : 'TIDAK LULUS';
}