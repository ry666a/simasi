<?php
include '../../../../koneksi.php';

header('Content-Type: application/json');

if (!isset($_POST['id']) || empty($_POST['id'])) {
    echo json_encode(['status' => 'error', 'message' => 'ID tidak ditemukan.']);
    exit;
}

$id = mysqli_real_escape_string($conn, $_POST['id']);

// Mulai Transaksi
mysqli_begin_transaction($conn);

try {
    // 1. List semua query hapus. 
    // Urutan: Hapus anak-anaknya dulu baru bapaknya (Header)
    $queries = [
        "DELETE FROM mp_cutting_realisasi_operator WHERE id_realisasi = '$id'",
        "DELETE FROM mp_cutting_realisasi_detail WHERE id_realisasi = '$id'",
        "DELETE FROM mp_cutting_realisasi_partikel WHERE id_realisasi = '$id'",
        "DELETE FROM mp_cutting_realisasi WHERE id = '$id'"
    ];

    foreach ($queries as $query) {
        if (!mysqli_query($conn, $query)) {
            throw new Exception("Gagal pada query: " . mysqli_error($conn));
        }
    }

    // Kita tidak perlu cek mysqli_affected_rows di tiap tabel
    // Selama query tidak error (syntax benar), maka proses lanjut.

    mysqli_commit($conn);
    echo json_encode([
        'status' => 'success',
        'message' => 'Data berhasil dihapus.'
    ]);

} catch (Exception $e) {
    mysqli_rollback($conn);
    echo json_encode([
        'status' => 'error',
        'message' => 'Gagal menghapus: ' . $e->getMessage()
    ]);
}

mysqli_close($conn);