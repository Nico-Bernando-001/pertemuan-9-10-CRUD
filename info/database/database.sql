-- ============================================
-- PEMROGRAMAN WEB LANJUTAN I
-- PERTEMUAN 6
-- BASIS DATA RELASIONAL DAN SQL
-- ============================================


-- ============================================
-- 1. CREATE DATABASE
-- DDL
-- ============================================

CREATE DATABASE student_portal;

USE student_portal;


-- ============================================
-- 2. CREATE TABLE PROGRAM STUDI
-- DDL
-- ============================================

CREATE TABLE program_studi (
    id_prodi INT AUTO_INCREMENT PRIMARY KEY,
    nama_prodi VARCHAR(100) NOT NULL UNIQUE
);


-- ============================================
-- 3. CREATE TABLE MAHASISWA
-- DDL
-- ============================================

CREATE TABLE mahasiswa (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nim VARCHAR(20) NOT NULL UNIQUE,
    nama VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    id_prodi INT NOT NULL,
    nilai_tugas DECIMAL(5,2) DEFAULT 0,
    nilai_uts DECIMAL(5,2) DEFAULT 0,
    nilai_uas DECIMAL(5,2) DEFAULT 0,
    kehadiran INT DEFAULT 0,

    FOREIGN KEY (id_prodi)
        REFERENCES program_studi(id_prodi)
);

#
#
#

-- ============================================
-- 4. INSERT PROGRAM STUDI
-- DML
-- ============================================
INSERT INTO program_studi
    (nama_prodi)
VALUES
    ('Sistem Informasi'),
    ('Informatika'),
    ('Teknik Elektro');



-- ============================================
-- 5. INSERT MAHASISWA
-- DML
-- ============================================
INSERT INTO mahasiswa (
    nim,
    nama,
    email,
    password,
    id_prodi,
    nilai_tugas,
    nilai_uts,
    nilai_uas,
    kehadiran
)
VALUES
(
    '412026001',
    'Andi Wijaya',
    'andi@ukrida.ac.id',
    '123456',
    1,
    80,
    78,
    90,
    85
),
(
    '412026002',
    'Budi Santoso',
    'budi@ukrida.ac.id',
    '123456',
    1,
    75,
    70,
    82,
    80
),
(
    '412026003',
    'Citra Lestari',
    'citra@ukrida.ac.id',
    '123456',
    1,
    90,
    88,
    92,
    95
),
(
    '412026004',
    'Dimas Saputra',
    'dimas@ukrida.ac.id',
    '123456',
    2,
    65,
    68,
    70,
    75
),
(
    '412026005',
    'Eka Putri',
    'eka@ukrida.ac.id',
    '123456',
    2,
    88,
    85,
    87,
    90
);

#
#
#

-- ============================================
-- 6. PERHITUNGAN NILAI AKHIR
-- PENGAYAAN
-- ============================================

SELECT
    nim,
    nama,
    nilai_tugas,
    nilai_uts,
    nilai_uas,
    (
        nilai_tugas * 0.30 +
        nilai_uts * 0.30 +
        nilai_uas * 0.40
    ) AS nilai_akhir
FROM mahasiswa;