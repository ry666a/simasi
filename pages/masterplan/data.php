<?php
include '../../koneksi.php'; // Pastikan path ke koneksi benar

$filterMonthYear = $_POST['filter_month_year'] ?? '';

// 1. Ambil Parameter dengan proteksi null coalescing (??) agar tidak Undefined Index
$draw   = $_POST['draw'] ?? 0;
$start  = $_POST['start'] ?? 0;
$length = $_POST['length'] ?? 10;
$search = $_POST['search']['value'] ?? '';

// --- TAMBAHAN UNTUK SORTING DINAMIS ---
$orderColumnIndex = $_POST['order'][0]['column'] ?? 4; // Default kolom ke-4 (tgl_packing)
$orderDir         = $_POST['order'][0]['dir'] ?? 'desc';

// Mapping indeks kolom DataTables ke nama field di Database
$columns = [
    0 => 'no_op',
    1 => 'exact_code',
    2 => 'exact_name',
    3 => 'nama_buyer',
    4 => 'tgl_packing',
    5 => 'jumlah',
    6 => 'id_mps'
];
$orderBy = $columns[$orderColumnIndex] ?? 'tgl_packing';
// --------------------------------------

// 2. Hitung Total Data Tanpa Filter
$totalQuery = mysqli_query($conn, "SELECT COUNT(*) as total FROM transaksi_mps");
$totalData  = 0;
if ($totalQuery) {
    $res = mysqli_fetch_assoc($totalQuery);
    $totalData = $res['total'];
}

// 3. Query Dasar
$sql = "SELECT * FROM transaksi_mps WHERE 1=1";

// 4. Logika Pencarian
if (!empty($search)) {
    // Gunakan real_escape_string untuk keamanan dari SQL Injection
    $s = mysqli_real_escape_string($conn, $search);
    $sql .= " AND (no_op LIKE '%$s%' 
               OR exact_code LIKE '%$s%' 
               OR exact_name LIKE '%$s%' 
               OR nama_buyer LIKE '%$s%')";
}

// FILTER BULAN & TAHUN (DIPERBAIKI)
if (!empty($filterMonthYear)) {
    // Memecah 2026-04 menjadi Array [2026, 04]
    $parts = explode('-', $filterMonthYear);
    $year  = mysqli_real_escape_string($conn, $parts[0]);
    $month = mysqli_real_escape_string($conn, $parts[1]);

    $sql .= " AND YEAR(tgl_packing) = '$year' AND MONTH(tgl_packing) = '$month'";
}

// 5. Hitung data yang terfilter
$totalFilteredQuery = mysqli_query($conn, $sql);
$totalFiltered = 0;
if ($totalFilteredQuery) {
    $totalFiltered = mysqli_num_rows($totalFilteredQuery);
} else {
    // Jika query error, catat errornya untuk debug
    die(mysqli_error($conn));
}

// 6. Urutan dan Pagination (BAGIAN YANG DIPERBAIKI)
$sql .= " ORDER BY " . $orderBy . " " . $orderDir . " LIMIT " . intval($start) . ", " . intval($length);

$query = mysqli_query($conn, $sql);
$data = [];

if ($query) {
    while ($row = mysqli_fetch_assoc($query)) {
        $data[] = [
            "no_op"       => $row['no_op'] ?? '',
            "exact_code"  => $row['exact_code'] ?? '',
            "exact_name"  => $row['exact_name'] ?? '',
            "buyer"       => $row['nama_buyer'] ?? '',
            "tgl_packing" => (!empty($row['tgl_packing'])) ? date('d-m-Y', strtotime($row['tgl_packing'])) : '-',
            "jumlah"      => number_format($row['jumlah'] ?? 0),
            "id"          => $row['id_mps'] ?? 0
        ];
    }
}

// 7. Kirim Response JSON
$response = [
    "draw"            => intval($draw),
    "recordsTotal"    => intval($totalData),
    "recordsFiltered" => intval($totalFiltered),
    "data"            => $data
];

header('Content-Type: application/json');
echo json_encode($response);
