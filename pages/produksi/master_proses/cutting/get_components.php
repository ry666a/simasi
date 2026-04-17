<?php
include '../../../../koneksi.php';

$exact_code = $_POST['exact_code'] ?? 0;
$query = mysqli_query($conn, "SELECT a.*, 
                    IFNULL(b.runtime, '0') AS runtime 
                FROM 
                    master_produk_komponen_detail a
                LEFT JOIN 
                    mp_cutting b ON a.id_produk_komponen_detail = b.id_produk_komponen_detail
                WHERE 
                    a.exact_code = '$exact_code' 
                ORDER BY 
                    a.nama_komponen ASC");

$data = [];
while($row = mysqli_fetch_assoc($query)) {
    $data[] = $row;
}

header('Content-Type: application/json');
echo json_encode($data);