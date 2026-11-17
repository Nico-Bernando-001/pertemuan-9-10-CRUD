UPDATE mahasiswa m
JOIN (
    SELECT id,
           ROW_NUMBER() OVER (
               PARTITION BY id_prodi, tahun_angkatan
               ORDER BY id
           ) AS rn
    FROM mahasiswa
) t ON m.id = t.id
SET m.nomor_urut = t.rn;

UPDATE mahasiswa m
JOIN program_studi p ON m.id_prodi = p.id_prodi
SET m.nim = CONCAT(p.kode_prodi, m.tahun_angkatan, LPAD(m.nomor_urut, 3, '0'));