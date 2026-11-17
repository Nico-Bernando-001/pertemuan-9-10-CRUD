document.addEventListener('DOMContentLoaded', function () {
    const nimInput = document.getElementById('nim');
    if (!nimInput) return;   // tidak di halaman tambah/edit? skip

    const prodiSelect = document.getElementById('id_prodi');
    const tahunInput  = document.getElementById('tahun_angkatan');
    const editIdInput = document.getElementById('edit_id');   // hanya ada di edit.php

    function updateNIM() {
        const prodi = prodiSelect.value;
        const tahun = tahunInput.value;

        if (!prodi || !/^\d{4}$/.test(tahun)) {
            nimInput.placeholder = 'Pilih Program Studi & Tahun Angkatan dulu';
            if (!editIdInput) nimInput.value = '';  // mode tambah: kosongkan
            return;
        }

        let url = `generate_nim.php?id_prodi=${encodeURIComponent(prodi)}&tahun=${encodeURIComponent(tahun)}`;
        if (editIdInput && editIdInput.value) {
            url += `&id=${encodeURIComponent(editIdInput.value)}`;
        }

        fetch(url)
            .then(res => res.json())
            .then(data => {
                nimInput.value = data.nim || '';
                console.log(`[Preview NIM] prodi=${prodi} tahun=${tahun} mode=${data.mode} → ${data.nim}`);
            })
            .catch(() => { nimInput.value = ''; });
    }

    prodiSelect.addEventListener('change', updateNIM);
    tahunInput.addEventListener('input', updateNIM);
    updateNIM();   // sinkronkan saat halaman load
});