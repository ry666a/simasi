<?php
include '../../../../koneksi.php';
// Bagian cari-noop
if ($_GET['page'] == 'cari-noop') {
    // ... logic query Anda yang sudah ada ...
    // Pastikan mengembalikan: no_op, exact_code, exact_name
    echo json_encode($data); 
    exit;
}

// Bagian get-komponen
if ($_GET['page'] == 'get-komponen') {
    $no_op = $_GET['no_op'];
    $data = [];
    $sql = "SELECT b.id_produk_komponen_detail as id_komponen, b.nama_komponen 
            FROM mp_cutting a 
            INNER JOIN master_produk_komponen_detail b ON a.id_produk_komponen_detail = b.id_produk_komponen_detail
            WHERE a.no_op = '$no_op'";
    $query = mysqli_query($conn, $sql);
    while($row = mysqli_fetch_assoc($query)) { $data[] = $row; }
    echo json_encode($data);
    exit;
}