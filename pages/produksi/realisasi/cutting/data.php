<?php
include '../../../../koneksi.php';

// Pastikan header JSON selalu ada
header('Content-Type: application/json');

$page = $_GET['page'] ?? '';

// --- 1. CARI NO OP (AUTOCOMPLETE MULTIPLE) ---
if ($page == 'cari-noop') {
    $q       = mysqli_real_escape_string($conn, $_GET['q']);
    $exact   = mysqli_real_escape_string($conn, $_GET['exact_code'] ?? '');
    $exclude = mysqli_real_escape_string($conn, $_GET['exclude'] ?? '');

    $excludeArr = array_filter(array_map('trim', explode(',', $exclude)));

    $sql = "SELECT DISTINCT no_op, exact_code, exact_name 
            FROM transaksi_mps 
            WHERE no_op LIKE '%$q%'";

    if (!empty($exact)) {
        $sql .= " AND exact_code = '$exact'";
    }

    if (!empty($excludeArr)) {
        $ops = "'" . implode("','", $excludeArr) . "'";
        $sql .= " AND no_op NOT IN ($ops)";
    }

    $sql .= " LIMIT 10";

    $query = mysqli_query($conn, $sql);
    $data = [];
    while ($row = mysqli_fetch_assoc($query)) {
        $data[] = $row;
    }
    echo json_encode($data);
    exit;
}

// --- 2. GET KOMPONEN AWAL (UNTUK INPUT ATAS) ---
if ($page == 'get-komponen-awal') {
    $no_op = mysqli_real_escape_string($conn, $_GET['no_op']);

    // Mengambil id dari mp_cutting (id_cutting) dan nama dari master view
    $sql = "SELECT a.id as id_cutting, b.nama_komponen 
            FROM mp_cutting a 
            INNER JOIN master_produk_komponen_detail b ON a.id_produk_komponen_detail = b.id_produk_komponen_detail
            WHERE a.no_op = '$no_op'
            ORDER BY b.nama_komponen ASC";

    $query = mysqli_query($conn, $sql);
    $data = [];
    while ($row = mysqli_fetch_assoc($query)) {
        $data[] = $row;
    }
    echo json_encode($data);
    exit;
}

// --- 3. LIST TMP (GRID BAWAH) ---
if ($page == 'list-tmp') {
    $idadmin = mysqli_real_escape_string($conn, $_GET['idadmin'] ?? '');

    // Join yang benar berdasarkan struktur dump Boss:
    // tmp.id_cutting -> mp_cutting.id -> master_produk_komponen_detail.id_produk_komponen_detail
    $sql = "SELECT 
                t.id, 
                t.no_op, 
                t.id_cutting, 
                t.jml_realisasi, 
                t.waktu_aktual, 
                t.proses,
                k.nama_komponen,
                k.exact_name,
                k.id_komponen
            FROM mp_cutting_realisasi_detail_tmp t
            INNER JOIN mp_cutting c ON t.id_cutting = c.id
            INNER JOIN master_produk_komponen_detail k ON c.id_produk_komponen_detail = k.id_produk_komponen_detail
            WHERE t.id_admin = '$idadmin'
            ORDER BY t.id DESC";

    $query = mysqli_query($conn, $sql);
    $data = [];

    while ($row = mysqli_fetch_assoc($query)) {
        // --- LOGIKA HITUNG KEBUTUHAN PCS (LOOP INTERNAL) ---
        $ops = explode(',', $row['no_op']);
        $total_kebutuhan = 0;
        $id_komp = $row['id_komponen'];

        foreach ($ops as $op) {
            $op = trim($op);
            if (empty($op)) continue;

            // Ambil jml_op dari transaksi_mps
            $q_mps = mysqli_query($conn, "SELECT jml_op, exact_code FROM transaksi_mps WHERE no_op = '$op' LIMIT 1");
            $d_mps = mysqli_fetch_assoc($q_mps);

            if ($d_mps) {
                $jml_order = (float)$d_mps['jml_op'];
                $ex_code   = $d_mps['exact_code'];

                // Ambil standar jml_komponen dari master view
                $q_mst = mysqli_query($conn, "SELECT jml_komponen FROM master_produk_komponen_detail 
                                              WHERE exact_code = '$ex_code' AND id_komponen = '$id_komp' LIMIT 1");
                $d_mst = mysqli_fetch_assoc($q_mst);
                $std_keb = ($d_mst) ? (float)$d_mst['jml_komponen'] : 0;

                $total_kebutuhan += ($jml_order * $std_keb);
            }
        }

        $row['kebutuhan_pcs'] = $total_kebutuhan;
        $data[] = $row;
    }

    echo json_encode($data);
    exit;
}



// --- 4. EDIT ---
if ($page == 'get-komponen') {
    // Ambil ID TMP dari AJAX
    $id_tmp = mysqli_real_escape_string($conn, $_GET['id_tmp']);

    // 1. Ambil id_cutting dari baris TMP sebagai pintu masuk
    $get_tmp = mysqli_query($conn, "SELECT id_cutting FROM mp_cutting_realisasi_detail_tmp WHERE id = '$id_tmp' LIMIT 1");
    $row_tmp  = mysqli_fetch_assoc($get_tmp);

    $data = [];
    if ($row_tmp) {
        $id_cutting_acuan = $row_tmp['id_cutting'];

        // 2. Query ambil SEMUA komponen yang satu produk (satu No OP) 
        // dengan id_cutting yang ada di baris tersebut.
        // Kita join mp_cutting ke master_produk_komponen_detail
        $sql = "SELECT 
                    a.id as id_cutting, 
                    b.id_produk_komponen_detail as id_komponen, 
                    b.nama_komponen 
                FROM mp_cutting a 
                INNER JOIN master_produk_komponen_detail b ON a.id_produk_komponen_detail = b.id_produk_komponen_detail
                WHERE a.no_op = (SELECT no_op FROM mp_cutting WHERE id = '$id_cutting_acuan' LIMIT 1)
                ORDER BY b.nama_komponen ASC";

        $query = mysqli_query($conn, $sql);
        while ($row = mysqli_fetch_assoc($query)) {
            $data[] = $row;
        }
    }

    echo json_encode($data);
    exit;
}


if ($page == 'get-single-tmp') {
    $id = mysqli_real_escape_string($conn, $_GET['id']);
    $sql = "SELECT t.*, k.exact_name 
            FROM mp_cutting_realisasi_detail_tmp t
            INNER JOIN mp_cutting c ON t.id_cutting = c.id
            INNER JOIN master_produk_komponen_detail k ON c.id_produk_komponen_detail = k.id_produk_komponen_detail
            WHERE t.id = '$id' LIMIT 1";

    $query = mysqli_query($conn, $sql);
    echo json_encode(mysqli_fetch_assoc($query));
    exit;
}




if ($page == 'data-realisasi') {

    $draw    = $_POST['draw'] ?? 1;
    $start   = $_POST['start'] ?? 0;
    $length  = $_POST['length'] ?? 10;
    $search  = $_POST['search']['value'] ?? '';
    $filter_month = $_POST['filter_month_year'] ?? '';

    // ===== KUNCI PERBAIKAN SORTING =====
    // Definisikan array kolom sesuai urutan di HTML/JS agar sinkron
    $columns = [
        0 => 'no_transaksi',
        1 => 'tanggal',
        2 => 'nama_mesin',
        3 => 'total_komponen',
        4 => 'total_waktu_aktual',
        5 => 'ganti_palet',
        6 => 'downtime',
        7 => 'id' // Kolom aksi biasanya tidak di-sort
    ];

    // Ambil input order dari DataTables
    $orderIndex = $_POST['order'][0]['column'] ?? 0; // Indeks kolom
    $orderDir   = $_POST['order'][0]['dir'] ?? 'desc'; // asc atau desc
    $columnName = $columns[$orderIndex]; // Nama field di database

    $orderBy = " ORDER BY $columnName $orderDir ";
    // ===================================

    $where = " WHERE 1=1 ";

    if (!empty($filter_month)) {
        $filter_escaped = mysqli_real_escape_string($conn, $filter_month);
        $where .= " AND tanggal LIKE '$filter_escaped%' ";
    }

    if (!empty($search)) {
        $s = mysqli_real_escape_string($conn, $search);
        $where .= " AND (no_transaksi LIKE '%$s%' OR nama_mesin LIKE '%$s%') ";
    }

    // Count records
    $qTotal = mysqli_query($conn, "SELECT COUNT(id) as jml FROM mp_cutting_realisasi");
    $totalData = mysqli_fetch_assoc($qTotal)['jml'] ?? 0;

    $qFiltered = mysqli_query($conn, "SELECT COUNT(id) as jml FROM mp_cutting_realisasi $where");
    $totalFiltered = mysqli_fetch_assoc($qFiltered)['jml'] ?? 0;

    // Ambil Data dengan Order Dynamic
    $sql = "SELECT * FROM mp_cutting_realisasi $where $orderBy LIMIT $start, $length";
    $query = mysqli_query($conn, $sql);

    $data = [];
    while ($row = mysqli_fetch_assoc($query)) {
        $data[] = [
            // Di dalam loop data.php
            "no_transaksi"      => '
                                    <a href="javascript:void(0)" class="fw-bold text-primary btn-view-detail" data-id="' . $row['id'] . '">
                                        ' . $row['no_transaksi'] . '
                                    </a>',
            "tanggal"            => date('d/m/Y', strtotime($row['tanggal'])),
            "nama_mesin"         => $row['nama_mesin'],
            "total_komponen"     => number_format($row['total_komponen']),
            "total_waktu_aktual" => number_format($row['total_waktu_aktual']) . ' MIN',
            "ganti_palet"        => $row['ganti_palet'],
            "downtime"           => $row['downtime'] . ' MIN',
            "id"                 => $row['id']
        ];
    }

    echo json_encode([
        "draw"            => intval($draw),
        "recordsTotal"    => intval($totalData),
        "recordsFiltered" => intval($totalFiltered),
        "data"            => $data
    ]);
    exit;
}



// Tutup koneksi jika sudah tidak ada page yang cocok
exit;
