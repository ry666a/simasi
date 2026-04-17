<?php
include '../../../../koneksi.php'; 

$draw   = isset($_POST['draw']) ? intval($_POST['draw']) : 0;
$start  = isset($_POST['start']) ? intval($_POST['start']) : 0;
$length = isset($_POST['length']) ? intval($_POST['length']) : 10;
$search = $_POST['search']['value'] ?? '';

$orderColumnIndex = $_POST['order'][0]['column'] ?? 1; 
$orderDir         = $_POST['order'][0]['dir'] ?? 'asc';

// Sesuaikan alias ke 'c' karena 'b' tidak ada
$columns = [
    0 => 'a.exact_code',
    1 => 'a.exact_code',
    2 => 'c.exact_name',
    3 => 'c.nama_buyer',
];
$orderBy = $columns[$orderColumnIndex] ?? 'c.exact_name';

// 2. Total Data Murni
$sqlTotal = "SELECT COUNT(DISTINCT a.exact_code) as total FROM mp_edging a INNER JOIN transaksi_mps c ON a.exact_code = c.exact_code";
$resTotal = mysqli_fetch_assoc(mysqli_query($conn, $sqlTotal));
$totalData = $resTotal['total'] ?? 0;

// 3. Query Dasar (Hapus alias b, semua lari ke c)
$sqlBase = " FROM 
                mp_edging a 
            INNER JOIN 
                transaksi_mps c ON a.exact_code = c.exact_code
            WHERE 
                1=1";

if (!empty($search)) {
    $s = mysqli_real_escape_string($conn, $search);
    $sqlBase .= " AND (a.exact_code LIKE '%$s%' 
                  OR c.exact_name LIKE '%$s%' 
                  OR c.nama_buyer LIKE '%$s%')";
}

// 6. Total Terfilter
$sqlFiltered = "SELECT COUNT(DISTINCT a.exact_code) as total " . $sqlBase;
$resFiltered = mysqli_fetch_assoc(mysqli_query($conn, $sqlFiltered));
$totalFiltered = $resFiltered['total'] ?? 0;

// 7. Query Final (Ambil kolom dari c)
$sqlData = "SELECT 
            a.exact_code,
            c.exact_name,
            c.nama_buyer" 
        . $sqlBase . 
        " GROUP BY a.exact_code 
          ORDER BY $orderBy $orderDir 
          LIMIT $start, $length";

$query = mysqli_query($conn, $sqlData);
$data = [];

if ($query) {
    while ($row = mysqli_fetch_assoc($query)) {
        $data[] = [
            "exact_code"  => $row['exact_code'],
            "exact_name"  => $row['exact_name'],
            "buyer"       => $row['nama_buyer'],
            "id"          => $row['exact_code']
        ];
    }
} else {
    // Debug jika query gagal
    // die(mysqli_error($conn)); 
}

$response = [
    "draw"            => intval($draw),
    "recordsTotal"    => intval($totalData),
    "recordsFiltered" => intval($totalFiltered),
    "data"            => $data
];

header('Content-Type: application/json');
echo json_encode($response);