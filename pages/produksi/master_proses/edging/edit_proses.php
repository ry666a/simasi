<?php
session_start();
include '../../../../koneksi.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    
    // 1. Mulai Transaksi
    mysqli_autocommit($conn, FALSE);

    try {
        // Ambil data array dari form
        $ids        = $_POST['id_edging'] ?? [];
        $edg_pj     = $_POST['edg_pj'] ?? [];
        $edg_lb     = $_POST['edg_lb'] ?? [];
        $grov_pj    = $_POST['grov_pj'] ?? [];
        $grov_lb    = $_POST['grov_lb'] ?? [];
        $eg_pj      = $_POST['eg_pj'] ?? [];
        $eg_lb      = $_POST['eg_lb'] ?? [];
        $tot_meter  = $_POST['tot_meter'] ?? [];
        $wkt_j600   = $_POST['wkt_j600'] ?? [];
        $wkt_j800   = $_POST['wkt_j800'] ?? [];

        if (empty($ids)) {
            throw new Exception("Tidak ada data yang diupdate.");
        }

        // 2. Looping data untuk update baris demi baris
        foreach ($ids as $key => $id) {
            $id_edging    = mysqli_real_escape_string($conn, $id);
            
            // Proteksi: Konversi ke float/int agar jika kosong jadi 0 (Anti Error 1366)
            $pj_e         = (int)$edg_pj[$key];
            $lb_e         = (int)$edg_lb[$key];
            $pj_g         = (int)$grov_pj[$key];
            $lb_g         = (int)$grov_lb[$key];
            $pj_eg        = (int)$eg_pj[$key];
            $lb_eg        = (int)$eg_lb[$key];
            
            // Kolom Decimal/Float
            $meter        = (float)str_replace(',', '', $tot_meter[$key]);
            $j600         = (float)str_replace(',', '', $wkt_j600[$key]);
            $j800         = (float)str_replace(',', '', $wkt_j800[$key]);

            $sql = "UPDATE mp_edging SET 
                        edging_panjang = '$pj_e',
                        edging_lebar = '$lb_e',
                        groving_panjang = '$pj_g',
                        groving_lebar = '$lb_g',
                        edging_groving_panjang = '$pj_eg',
                        edging_groving_lebar = '$lb_eg',
                        total_meter_komponen = '$meter',
                        waktu_proses_j600 = '$j600',
                        waktu_proses_j800 = '$j800'
                    WHERE id = '$id_edging'";

            if (!mysqli_query($conn, $sql)) {
                throw new Exception("Gagal update data ID $id: " . mysqli_error($conn));
            }
        }

        // 3. Jika semua sukses, Commit
        mysqli_commit($conn);
        echo json_encode([
            'status' => 'success',
            'message' => 'Seluruh data komponen berhasil diperbarui!'
        ]);

    } catch (Exception $e) {
        // Jika ada satu saja yang gagal, batalkan semua
        mysqli_rollback($conn);
        echo json_encode([
            'status' => 'error',
            'message' => $e->getMessage()
        ]);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid Request']);
}