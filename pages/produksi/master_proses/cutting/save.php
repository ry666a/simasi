<?php
include '../../../../koneksi.php'; // Sesuaikan path koneksi Anda

// Set header JSON agar dibaca sebagai object oleh JQuery AJAX
header('Content-Type: application/json');

// Mulai Transaksi
mysqli_autocommit($conn, FALSE);

try {
    // 1. Ambil data utama
    $idadmin     = pengaman($_POST['idadmin']);
    $exact_code  = pengaman($_POST['exact_code']); // Menggunakan exact_code sesuai form kita
    $create_at   = date('Y-m-d H:i:s');

    // 2. Ambil data array (dari tabel komponen)
    // Catatan: Pastikan name di HTML adalah id_produk_komponen_detail[]
    $id_detail_arr = $_POST['id_produk_komponen_detail'] ?? [];
    $runtime_arr   = $_POST['runtime'] ?? [];

    // Validasi awal

    // 3. Looping Simpan Data
    for ($i = 0; $i < count($id_detail_arr); $i++) {
        $id_produk_komponen_detail = pengaman($id_detail_arr[$i]);
        $runtime                   = pengaman($runtime_arr[$i]);

        // Lewati jika runtime kosong atau nol (opsional, tergantung kebijakan)
        if ($runtime === "") {
               throw new Exception("Data runtime tidak lengkap.");
               exit;
        }

        // Ambil ID Baru jika manual
        $id = nourut("mp_cutting", "id");

        // SQL Query dengan ON DUPLICATE KEY UPDATE
        // Jadi kalau kombinasi unique key sudah ada, dia otomatis update runtime-nya saja
        $query = "INSERT INTO mp_cutting (
                    id,
                    exact_code,
                    id_produk_komponen_detail,
                    runtime,
                    create_at,
                    id_admin
                ) VALUES (
                    '$id',
                    '$exact_code',
                    '$id_produk_komponen_detail',
                    '$runtime',
                    '$create_at',
                    '$idadmin'
                ) ON DUPLICATE KEY UPDATE 
                    runtime     = '$runtime',
                    id_admin    = '$idadmin',
                    update_at   = '$create_at'";

        $execute = mysqli_query($conn, $query);

        if (!$execute) {
            throw new Exception("Gagal simpan di baris ke-" . ($i + 1) . ": " . mysqli_error($conn));
        }
    }

    // Jika semua oke, commit
    mysqli_commit($conn);
    echo json_encode(['status' => 'success', 'message' => 'Master proses berhasil disimpan!']);

} catch (Exception $e) {
    // Jika ada error, batalkan semua perubahan
    mysqli_rollback($conn);
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
}

// Kembalikan ke mode autocommit
mysqli_autocommit($conn, TRUE);
mysqli_close($conn);