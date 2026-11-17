USE student_portal;

-- ============================================
-- STEP 1: Tambah kode_prodi ke program_studi
-- ============================================
ALTER TABLE program_studi 
    ADD COLUMN kode_prodi VARCHAR(2) NULL AFTER id_prodi;

UPDATE program_studi SET kode_prodi = '42' WHERE nama_prodi = 'Sistem Informasi';
UPDATE program_studi SET kode_prodi = '41' WHERE nama_prodi = 'Informatika';
UPDATE program_studi SET kode_prodi = '21' WHERE nama_prodi = 'Teknik Elektro';

INSERT INTO program_studi (kode_prodi, nama_prodi) VALUES
    ('22', 'Teknik Industri'),
    ('24', 'Teknik Sipil');

ALTER TABLE program_studi 
    MODIFY COLUMN kode_prodi VARCHAR(2) NOT NULL,
    ADD UNIQUE KEY uk_kode_prodi (kode_prodi);

-- ============================================
-- STEP 2: Tambah tahun_angkatan & nomor_urut
-- ============================================
ALTER TABLE mahasiswa 
    ADD COLUMN tahun_angkatan INT NULL AFTER password,
    ADD COLUMN nomor_urut INT NULL AFTER tahun_angkatan;

-- Ambil dari NIM lama (format lama: 41 + 2026 + 001)
UPDATE mahasiswa SET 
    tahun_angkatan = CAST(SUBSTRING(nim, 3, 4) AS UNSIGNED),
    nomor_urut     = CAST(SUBSTRING(nim, 7, 3) AS UNSIGNED);

ALTER TABLE mahasiswa 
    MODIFY COLUMN tahun_angkatan INT NOT NULL,
    MODIFY COLUMN nomor_urut INT NOT NULL;

-- ============================================
-- STEP 3: Regenerate NIM sesuai kode_prodi baru
-- (pakai prefix TEMP dulu biar aman dari unique conflict)
-- ============================================
UPDATE mahasiswa SET nim = CONCAT('TEMP_', id);

UPDATE mahasiswa m
JOIN program_studi p ON m.id_prodi = p.id_prodi
SET m.nim = CONCAT(p.kode_prodi, m.tahun_angkatan, LPAD(m.nomor_urut, 3, '0'));

-- ============================================
-- STEP 4: Verifikasi
-- ============================================
SELECT p.kode_prodi, p.nama_prodi, m.nim, m.nama, m.tahun_angkatan, m.nomor_urut
FROM mahasiswa m
JOIN program_studi p ON m.id_prodi = p.id_prodi
ORDER BY m.nim;