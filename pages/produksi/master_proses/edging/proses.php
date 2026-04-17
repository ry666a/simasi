<?php
include '../../../../koneksi.php';
// Load library PhpSpreadsheet (Pastikan sudah diinstall via composer)
require '../../../../vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\IOFactory;

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    mysqli_autocommit($conn, FALSE);
    $idadmin      = mysqli_real_escape_string($conn, $_POST['idadmin']);

    // 1. Validasi File
    if (!isset($_FILES['file_excel']['name']) || $_FILES['file_excel']['error'] !== 0) {
        mysqli_rollback($conn);
        echo json_encode(['status' => 'error', 'message' => 'File tidak ditemukan atau error.']);
        exit;
    }

    $fileName = $_FILES['file_excel']['name'];
    $fileTmp  = $_FILES['file_excel']['tmp_name'];
    $ext      = pathinfo($fileName, PATHINFO_EXTENSION);

    // Cek Ekstensi
    if (!in_array($ext, ['xlsx', 'xls'])) {
        mysqli_rollback($conn);
        echo json_encode(['status' => 'error', 'message' => 'Format file harus .xlsx atau .xls']);
        exit;
    }

    try {
        $spreadsheet = IOFactory::load($fileTmp);
        $sheetData   = $spreadsheet->getActiveSheet()->toArray();

        $countSuccess = 0;
        $countSkip    = 0;
        $id = nourut("mp_edging", "id");

        // Dimulai dari indeks 1 (Baris ke-2) karena Baris 1 biasanya Header/Judul
        for ($i = 1; $i <= 1; $i++) {
            $exact_code = pengaman($sheetData[$i][2]);
        }

        for ($x = 10; $x < count($sheetData); $x++) {
            $nomer    = pengaman($sheetData[$x][0]);
            $nama_komponen    = pengaman($sheetData[$x][1]);
            $jmlKomp          = pengaman($sheetData[$x][6]);

            if ($jmlKomp > 1) {
                mysqli_rollback($conn);
                echo json_encode(['status' => 'error', 'message' => 'Error Jumlah Komponen Harus 1: ' . $e->getMessage()]);
                exit;
            }

            // 1. Perbaikan Cek Numerik: Jika kolom nomer bukan angka, anggap data selesai
            if (!is_numeric($nomer)) {
                break; // Berhenti dari loop (selesai)
            }


            $cek = mysqli_query($conn, "SELECT 	id_produk_komponen_detail FROM master_produk_komponen_detail WHERE exact_code = '$exact_code' AND nama_komponen = '$nama_komponen' LIMIT 1");
            if (mysqli_num_rows($cek) <= 0) {
                mysqli_rollback($conn);
                echo json_encode(['status' => 'error', 'message' => 'Komponen ' . $nama_komponen . ' tidak ditemukan!']);
                exit;
            }
            $dtCek = mysqli_fetch_assoc($cek);
            $id_produk_komponen_detail = $dtCek['id_produk_komponen_detail'];


            // Jika kosong (''), otomatis jadi 0. Jika ada isinya, tetap pakai isinya.
            $edging_panjang           = pengaman($sheetData[$x][7]) ?: 0;
            $edging_lebar             = pengaman($sheetData[$x][8]) ?: 0;
            $groving_panjang          = pengaman($sheetData[$x][9]) ?: 0;
            $groving_lebar            = pengaman($sheetData[$x][10]) ?: 0;
            $edging_groving_panjang   = pengaman($sheetData[$x][11]) ?: 0;
            $edging_groving_lebar     = pengaman($sheetData[$x][12]) ?: 0;

            // Ini kolom yang di screenshot masih "None" defaultnya:
            $total_meter_komponen     = pengaman($sheetData[$x][13]) ?: 0;
            $total_ml_j600            = pengaman($sheetData[$x][14]) ?: 0;
            $total_ml_j800            = pengaman($sheetData[$x][15]) ?: 0;

            $waktu_setting_edging     = (float)(pengaman($sheetData[$x][16]) ?: 0);
            $waktu_setting_groving    = (float)(pengaman($sheetData[$x][17]) ?: 0);
            $total_waktu_setting      = $waktu_setting_edging + $waktu_setting_groving;

            $waktu_proses_j600        = (float)(pengaman($sheetData[$x][18]) ?: 0) - $total_waktu_setting;
            $waktu_proses_j800        = (float)(pengaman($sheetData[$x][19]) ?: 0) - $total_waktu_setting;

            $sql    =  mysqli_query($conn, "INSERT INTO mp_edging(
                        id,
                        exact_code,
                        id_produk_komponen_detail,
                        edging_panjang,
                        edging_lebar,
                        groving_panjang,
                        groving_lebar,
                        edging_groving_panjang,
                        edging_groving_lebar,
                        total_meter_komponen,
                        total_ml_j600,
                        total_ml_j800,
                        waktu_proses_j600,
                        waktu_proses_j800,
                        create_at,
                        id_admin
                        ) VALUES(
                        '$id',
                        '$exact_code',
                        '$id_produk_komponen_detail',
                        '$edging_panjang',
                        '$edging_lebar',
                        '$groving_panjang',
                        '$groving_lebar',
                        '$edging_groving_panjang',
                        '$edging_groving_lebar',
                        '$total_meter_komponen',
                        '$total_ml_j600',
                        '$total_ml_j800',
                        '$waktu_proses_j600',
                        '$waktu_proses_j800',
                        '$create_at',
                        '$idadmin'
                        )");
            $id++;
            $countSuccess++;

            // KUNCI UTAMA: Cek kalau insert gagal!
            if (!$sql) {
                mysqli_rollback($conn);
                echo json_encode([
                    'status' => 'error',
                    'message' => 'Gagal Insert di baris ' . $x . ': ' . mysqli_error($conn)
                ]);
                exit;
            }
        }


        mysqli_commit($conn);
        // Hapus semua output buffer (sampah warning/notice) sebelum kirim JSON
        if (ob_get_length()) ob_clean();
        echo json_encode([
            'status'  => 'success',
            'message' => "Berhasil: $countSuccess data berhasil ditambahkan"
        ]);
        exit;
    } catch (Exception $e) {
        mysqli_rollback($conn);
        echo json_encode(['status' => 'error', 'message' => 'Error membaca Excel: ' . $e->getMessage()]);
        exit;
    }
}
