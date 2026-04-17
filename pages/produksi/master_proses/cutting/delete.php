<?php
include '../../../../koneksi.php'; // Pastikan path koneksi benar

// Set header JSON agar direspon sebagai object oleh AJAX
header('Content-Type: application/json');

// Cek apakah ada ID yang dikirim
if (!isset($_POST['id']) || empty($_POST['id'])) {
    echo json_encode(['status' => 'error', 'message' => 'ID tidak ditemukan.']);
    exit;
}

$id = mysqli_real_escape_string($conn, $_POST['id']);

// Mulai Transaksi (Agar jika satu gagal, semua batal)
mysqli_autocommit($conn, FALSE);

try {
    // 1. Opsi A: Jika ingin menghapus SATU baris spesifik berdasarkan ID
    $sql = "DELETE FROM mp_cutting WHERE no_op = '$id'";

    /* Opsi B: Jika 'id' yang dikirim adalah No OP dan ingin hapus SEMUA komponen di OP tersebut:
       $sql = "DELETE FROM mp_cutting WHERE no_op = '$id'"; 
    */

    $execute = mysqli_query($conn, $sql);

    if (!$execute) {
        throw new Exception("Gagal menghapus data dari database: " . mysqli_error($conn));
    }

    // Cek apakah ada baris yang terhapus
    if (mysqli_affected_rows($conn) === 0) {
        throw new Exception("Data tidak ditemukan atau sudah terhapus.");
    }

    // Jika berhasil, commit transaksi
    mysqli_commit($conn);
    echo json_encode([
        'status' => 'success',
        'message' => 'Data berhasil dihapus secara permanen.'
    ]);
} catch (Exception $e) {
    // Jika gagal, batalkan semua perubahan
    mysqli_rollback($conn);
    echo json_encode([
        'status' => 'error',
        'message' => $e->getMessage()
    ]);
}

// Kembalikan ke mode autocommit dan tutup koneksi
mysqli_autocommit($conn, TRUE);
mysqli_close($conn);
