<?php
include '../../../../koneksi.php'; 

// 1. Ambil Parameter
$filterMonthYear = $_POST['filter_month_year'] ?? '';
$draw   = isset($_POST['draw']) ? intval($_POST['draw']) : 0;
$start  = isset($_POST['start']) ? intval($_POST['start']) : 0;
$length = isset($_POST['length']) ? intval($_POST['length']) : 10;
$search = $_POST['search']['value'] ?? '';

// --- SORTING DINAMIS ---
$orderColumnIndex = $_POST['order'][0]['column'] ?? 4; 
$orderDir         = $_POST['order'][0]['dir'] ?? 'desc';

$columns = [
    0 => 'c.no_op',
    1 => 'c.exact_code',
    2 => 'c.exact_name',
    3 => 'c.nama_buyer',
    4 => 'c.tgl_packing',
    5 => 'c.jml_op'
];
$orderBy = $columns[$orderColumnIndex] ?? 'c.tgl_packing';

// 2. Hitung Total Data Murni (Tanpa Filter Apapun)
// Menggunakan COUNT(DISTINCT) karena ada Group By di query utama
$sqlTotal = "SELECT COUNT(DISTINCT a.no_op) as total FROM mp_cutting a INNER JOIN transaksi_mps c ON a.no_op = c.no_op";
$resTotal = mysqli_fetch_assoc(mysqli_query($conn, $sqlTotal));
$totalData = $resTotal['total'] ?? 0;

// 3. Bangun Query Dasar
$sql = " FROM 
            mp_cutting a 
        INNER JOIN 
            transaksi_mps c ON a.no_op = c.no_op
        WHERE 
            1=1";

// 4. Tambahkan Logika Pencarian ke variabel $sql
if (!empty($search)) {
    $s = mysqli_real_escape_string($conn, $search);
    $sql .= " AND (c.no_op LIKE '%$s%' 
               OR c.exact_code LIKE '%$s%' 
               OR c.exact_name LIKE '%$s%' 
               OR c.nama_buyer LIKE '%$s%')";
}

// 5. Tambahkan Filter Bulan & Tahun ke variabel $sql
if (!empty($filterMonthYear)) {
    $timestamp = strtotime($filterMonthYear); 
    if ($timestamp) {
        $year  = mysqli_real_escape_string($conn, date('Y', $timestamp));
        $month = mysqli_real_escape_string($conn, date('m', $timestamp));
        $sql .= " AND YEAR(c.tgl_packing) = '$year' AND MONTH(c.tgl_packing) = '$month'";
    }
}

// 6. Hitung Total Data Terfilter (PENTING: Sebelum ada LIMIT dan GROUP BY)
$sqlFiltered = "SELECT COUNT(DISTINCT a.no_op) as total " . $sql;
$resFiltered = mysqli_fetch_assoc(mysqli_query($conn, $sqlFiltered));
$totalFiltered = $resFiltered['total'] ?? 0;

// 7. Query Final untuk Ambil Data
$sqlData = "SELECT 
            c.no_op,
            c.exact_code,
            c.exact_name,
            c.nama_buyer,
            c.tgl_packing,
            c.jml_op,
            MIN(a.id) AS id_cutting " 
        . $sql . 
        " GROUP BY c.no_op 
          ORDER BY $orderBy $orderDir 
          LIMIT $start, $length";

$query = mysqli_query($conn, $sqlData);
$data = [];

if ($query) {
    while ($row = mysqli_fetch_assoc($query)) {
        $data[] = [
            "no_op"       => $row['no_op'] ?? '',
            "exact_code"  => $row['exact_code'] ?? '',
            "exact_name"  => $row['exact_name'] ?? '',
            "buyer"       => $row['nama_buyer'] ?? '',
            "tgl_packing" => (!empty($row['tgl_packing'])) ? date('d-m-Y', strtotime($row['tgl_packing'])) : '-',
            "jumlah"      => number_format($row['jml_op'] ?? 0),
            "id"          => $row['no_op'] ?? 0
        ];
    }
}

// 8. Kirim Response JSON
$response = [
    "draw"            => intval($draw),
    "recordsTotal"    => intval($totalData),
    "recordsFiltered" => intval($totalFiltered),
    "data"            => $data
];

header('Content-Type: application/json');
echo json_encode($response);