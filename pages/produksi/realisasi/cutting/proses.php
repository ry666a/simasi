<?php
include '../../../../koneksi.php';

$page = $_GET['page'] ?? '';

if ($page == 'add-tmp') {
    mysqli_autocommit($conn, FALSE);

    // Tangkap data
    $idadmin        = isset($_POST['idadmin']) ? pengaman($_POST['idadmin']) : '';
    $noop_string    = isset($_POST['no_op_raw']) ? pengaman($_POST['no_op_raw']) : '';
    $id_cutting     = isset($_POST['id_cutting']) ? pengaman($_POST['id_cutting']) : '';
    $jml_realisasi  = isset($_POST['jml_realisasi']) ? pengaman($_POST['jml_realisasi']) : 0;
    $waktu_aktual   = isset($_POST['waktu_aktual']) ? pengaman($_POST['waktu_aktual']) : 0;
    $proses         = isset($_POST['proses']) ? pengaman($_POST['proses']) : '';
    $create_at      = date('Y-m-d');

    if ($noop_string == '' || $id_cutting == '') {
        echo json_encode(['success' => false, 'message' => 'Data belum lengkap']);
        exit;
    }

    // 🔥 TIDAK DI-EXPLODE: Bersihkan saja spasi di sekitar koma agar rapi
    $clean_no_op = implode(',', array_filter(array_map('trim', explode(',', $noop_string))));
    
    $id = nourut("mp_cutting_realisasi_detail_tmp", "id");
    
    // Simpan satu baris meskipun OP-nya banyak
    $sql = "INSERT INTO mp_cutting_realisasi_detail_tmp (
                id, no_op, id_cutting, jml_realisasi, waktu_aktual, proses, create_at, id_admin
            ) VALUES (
                '$id', '$clean_no_op', '$id_cutting', '$jml_realisasi', '$waktu_aktual', '$proses', '$create_at', '$idadmin'
            )";
    
    if (mysqli_query($conn, $sql)) {
        mysqli_commit($conn);
        echo json_encode(['success' => true]);
    } else {
        mysqli_rollback($conn);
        echo json_encode(['success' => false, 'message' => mysqli_error($conn)]);
    }
    exit;
}

if ($page == 'copy-tmp') {
    // 1. Ambil ID yang mau dikopi dan ID Admin
    $id      = isset($_POST['id']) ? pengaman($_POST['id']) : '';
    $idadmin = isset($_POST['idadmin']) ? pengaman($_POST['idadmin']) : '';

    if (empty($id)) {
        echo json_encode(['success' => false, 'message' => 'ID tidak ditemukan']);
        exit;
    }

    // 2. Cari data sumber yang mau dikopi
    $query_source = mysqli_query($conn, "SELECT * FROM mp_cutting_realisasi_detail_tmp WHERE id = '$id' AND id_admin = '$idadmin'");
    $data = mysqli_fetch_assoc($query_source);

    if ($data) {
        // 3. Generate ID baru untuk baris hasil kopi
        $new_id = nourut("mp_cutting_realisasi_detail_tmp", "id");
        
        // Ambil data dari baris sumber
        $no_op         = mysqli_real_escape_string($conn, $data['no_op']);
        $id_cutting    = mysqli_real_escape_string($conn, $data['id_cutting']);
        $jml_realisasi = mysqli_real_escape_string($conn, $data['jml_realisasi']);
        $waktu_aktual  = mysqli_real_escape_string($conn, $data['waktu_aktual']);
        $proses        = mysqli_real_escape_string($conn, $data['proses']);
        $create_at     = date('Y-m-d');

        // 4. Insert sebagai baris baru
        $sql_copy = "INSERT INTO mp_cutting_realisasi_detail_tmp (
                        id, 
                        no_op, 
                        id_cutting, 
                        jml_realisasi, 
                        waktu_aktual, 
                        proses, 
                        create_at, 
                        id_admin
                    ) VALUES (
                        '$new_id', 
                        '$no_op', 
                        '$id_cutting', 
                        '$jml_realisasi', 
                        '$waktu_aktual', 
                        '$proses', 
                        '$create_at', 
                        '$idadmin'
                    )";

        if (mysqli_query($conn, $sql_copy)) {
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Gagal kopi data: ' . mysqli_error($conn)]);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'Data sumber tidak ditemukan']);
    }
    exit;
}

if (isset($_GET['page']) && $_GET['page'] == 'update-all-tmp') {
    // Ambil data dari POST Modal
    $id             = mysqli_real_escape_string($conn, $_POST['id']);
    $id_cutting     = mysqli_real_escape_string($conn, $_POST['id_cutting']); // ID dari mp_cutting
    $jml_realisasi  = mysqli_real_escape_string($conn, $_POST['jml_realisasi']);
    $waktu_aktual   = mysqli_real_escape_string($conn, $_POST['waktu_aktual']);
    $proses         = mysqli_real_escape_string($conn, $_POST['proses']);
    $idadmin        = mysqli_real_escape_string($conn, $_POST['idadmin']);

    // Query Update
    $sql = "UPDATE mp_cutting_realisasi_detail_tmp SET 
                id_cutting    = '$id_cutting',
                jml_realisasi = '$jml_realisasi',
                waktu_aktual  = '$waktu_aktual',
                proses        = '$proses',
                id_admin      = '$idadmin',
                update_at     = NOW()
            WHERE id = '$id'";

    if (mysqli_query($conn, $sql)) {
        echo json_encode(['success' => true, 'message' => 'Data diperbarui']);
    } else {
        echo json_encode(['success' => false, 'message' => mysqli_error($conn)]);
    }
    exit;
}

if ($page == 'delete-tmp') {
    $id      = isset($_GET['id']) ? pengaman($_GET['id']) : '';
    $idadmin = isset($_GET['idadmin']) ? pengaman($_GET['idadmin']) : '';

    if (!empty($id) && !empty($idadmin)) {
        // Hapus berdasarkan ID dan ID Admin agar aman
        $sql = mysqli_query($conn, "DELETE FROM mp_cutting_realisasi_detail_tmp WHERE id = '$id' AND id_admin = '$idadmin'");
        
        if ($sql) {
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false, 'message' => mysqli_error($conn)]);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'ID tidak ditemukan']);
    }
    exit;
}


if ($page == 'simpan-realisasi') {
    mysqli_autocommit($conn, FALSE);

    /* ================= HEADER DATA ================= */
    $idadmin      = mysqli_real_escape_string($conn, $_POST['idadmin']);
    $no_transaksi = auto_kode("mp_cutting_realisasi", "no_transaksi", "CUT" . date('ym'), 11);
    
    $tanggal      = mysqli_real_escape_string($conn, $_POST['tgl_realisasi']);
    $nama_mesin   = mysqli_real_escape_string($conn, $_POST['id_mesin']);
    $ganti_palet  = (int)($_POST['ganti_palet'] ?? 0);
    $downtime     = (float)($_POST['downtime'] ?? 0);
    $keterangan   = mysqli_real_escape_string($conn, $_POST['catatan']);
    $shift        = mysqli_real_escape_string($conn, $_POST['shift']);
    $waktu_tersedia = 480; // Default 1 shift atau sesuaikan logic Boss

    $operator     = $_POST['operator'] ?? [];


    $jml_operator = count($operator);

    if ($jml_operator == 0) {
        echo json_encode(['success' => false, 'message' => 'Pilih minimal 1 Operator!']);
        exit;
    }

    /* ================= AMBIL DATA TMP ================= */
    $qTmp = mysqli_query($conn, "SELECT * FROM mp_cutting_realisasi_detail_tmp WHERE id_admin = '$idadmin' ORDER BY id ASC");
    if (mysqli_num_rows($qTmp) == 0) {
        echo json_encode(['success' => false, 'message' => 'Detail Item masih kosong!']);
        exit;
    }

    /* ================= PREPARE ID ================= */
    $id_realisasi = nourut("mp_cutting_realisasi", "id");
    $id_detail    = nourut("mp_cutting_realisasi_detail", "id");
    $id_operator  = nourut("mp_cutting_realisasi_operator", "id");
    $id_partikel  = nourut("mp_cutting_realisasi_partikel", "id");

    $total_komponen = 0;
    $total_waktu    = 0;
    $create_at      = date('Y-m-d H:i:s');

    /* ================= LOOP DETAIL (TMP) ================= */
    while ($row = mysqli_fetch_assoc($qTmp)) {
        $id_cutting_acuan = $row['id_cutting'];
        $jml_realisasi_baris = (float)$row['jml_realisasi'];
        $waktu_baris = (float)$row['waktu_aktual'];
        $proses = $row['proses'];

        // Ambil No OP list dari baris ini
        $no_op_arr = array_filter(array_map('trim', explode(',', $row['no_op'])));
        
        // Cari ID Komponen (agar distribusi akurat meski beda OP tapi komponen sama)
        $q_komp = mysqli_query($conn, "SELECT id_produk_komponen_detail FROM mp_cutting WHERE id = '$id_cutting_acuan'");
        $d_komp = mysqli_fetch_assoc($q_komp);
        $id_pkd = $d_komp['id_produk_komponen_detail'];

        /* DISTRIBUSI FIFO KE OP-OP YANG TERLIBAT */
        $sisa_distribusi = $jml_realisasi_baris;
        
        // Query FIFO berdasarkan Tgl Packing (Sesuai kode lama Boss)
        $sql_fifo = "SELECT a.id, a.no_op, (b.jml_op * c.jml_komponen) as kapasitas_total
                     FROM mp_cutting a
                     JOIN transaksi_mps b ON a.no_op = b.no_op
                     JOIN master_produk_komponen_detail c ON a.id_produk_komponen_detail = c.id_produk_komponen_detail
                     WHERE a.no_op IN ('".implode("','", $no_op_arr)."') 
                     AND a.id_produk_komponen_detail = '$id_pkd'
                     ORDER BY b.tgl_packing ASC";
        
        $q_fifo = mysqli_query($conn, $sql_fifo);
        while ($fifo = mysqli_fetch_assoc($q_fifo)) {
            if ($sisa_distribusi <= 0) break;

            $id_c = $fifo['id'];
            
            // Hitung sisa kapasitas yang belum terealisasi sebelumnya
            $q_cek = mysqli_query($conn, "SELECT SUM(jml_realisasi) as sudah FROM mp_cutting_realisasi_detail WHERE id_cutting = '$id_c'");
            $d_cek = mysqli_fetch_assoc($q_cek);
            $sudah = (float)$d_cek['sudah'];
            $kapasitas_sisa = $fifo['kapasitas_total'] - $sudah;

            if ($kapasitas_sisa <= 0) continue;

            $diisi = min($sisa_distribusi, $kapasitas_sisa);
            $waktu_proporsional = ($diisi / $jml_realisasi_baris) * $waktu_baris;

            // INSERT DETAIL PER OP
            mysqli_query($conn, "INSERT INTO mp_cutting_realisasi_detail (id, id_realisasi, id_cutting, jml_realisasi, waktu_aktual, proses, create_at, id_admin) 
                                 VALUES ('$id_detail', '$id_realisasi', '$id_c', '$diisi', '$waktu_proporsional', '$proses', '$create_at', '$idadmin')");

            // INSERT OPERATOR PER DETAIL
            $jml_per_op = $diisi / $jml_operator;
            foreach ($operator as $op_val) {
                [$nip, $nama] = explode("±", $op_val);

                mysqli_query($conn, "INSERT INTO mp_cutting_realisasi_operator (id, nip, nama, id_realisasi, id_realisasi_detail, jml_realisasi_peroperator, kpi, create_at, id_admin) 
                                     VALUES ('$id_operator', '$nip', '$nama', '$id_realisasi', '$id_detail', '$jml_per_op', '0', '$create_at', '$idadmin')");
                $id_operator++;
            }

            $id_detail++;
            $sisa_distribusi -= $diisi;
        }

        if ($sisa_distribusi > 0.01) { // Toleransi float
            mysqli_rollback($conn);
            echo json_encode(['success' => false, 'message' => "Kapasitas OP tidak cukup untuk: " . $row['no_op']]);
            exit;
        }

        $total_komponen += $jml_realisasi_baris;
        $total_waktu += $waktu_baris;
    }

    /* ================= SIMPAN PARTIKEL ================= */
    $total_waktu_partikel = 0;
    if (isset($_POST['ketebalan'])) {
        foreach ($_POST['ketebalan'] as $k => $tebal) {
            $jml_lbr = (int)$_POST['jumlah'][$k];
            if ($tebal > 0 && $jml_lbr > 0) {
                // Logic pembagi Boss
                $p = 20;
                if ($tebal <= 2.5) $p = 200;
                elseif ($tebal <= 6) $p = 100;
                elseif ($tebal <= 9) $p = 70;
                elseif ($tebal <= 12) $p = 50;
                elseif ($tebal <= 15) $p = 40;
                elseif ($tebal <= 18) $p = 35;
                elseif ($tebal <= 25) $p = 25;
                
                $runtime = ($jml_lbr / $p) * 0.5;
                mysqli_query($conn, "INSERT INTO mp_cutting_realisasi_partikel (id, id_realisasi, ketebalan, jumlah, runtime, create_at, id_admin) 
                                     VALUES ('$id_partikel', '$id_realisasi', '$tebal', '$jml_lbr', '$runtime', '$create_at', '$idadmin')");
                $id_partikel++;
                $total_waktu_partikel += $runtime;
            }
        }
    }

    /* ================= FINAL HEADER ================= */
    $sql_h = "INSERT INTO mp_cutting_realisasi (id, no_transaksi, tanggal, nama_mesin, total_komponen, total_waktu_aktual, ganti_palet, downtime, total_waktu_partikel, waktu_tersedia, shift, keterangan, create_at, id_admin) 
              VALUES ('$id_realisasi', '$no_transaksi', '$tanggal', '$nama_mesin', '$total_komponen', '$total_waktu', '$ganti_palet', '$downtime', '$total_waktu_partikel', '$waktu_tersedia', '$shift', '$keterangan', '$create_at', '$idadmin')";
    

    if (mysqli_query($conn, $sql_h)) {
        mysqli_query($conn, "DELETE FROM mp_cutting_realisasi_detail_tmp WHERE id_admin = '$idadmin'");
        mysqli_commit($conn);
        echo json_encode(['success' => true, 'no_transaksi' => $no_transaksi]);
    } else {
        mysqli_rollback($conn);
        echo json_encode(['success' => false, 'message' => 'Gagal simpan Header']);
    }
    exit;
}