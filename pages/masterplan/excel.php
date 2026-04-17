<?php
include '../../koneksi.php';

// Ambil parameter filter dari URL (GET)
$search = $_GET['search'] ?? '';
$monthYear = $_GET['month_year'] ?? '';
$line = $_GET['line'] ?? '';

// Header agar browser mendownload sebagai Excel
header("Content-Type: application/vnd-ms-excel");
header("Content-Disposition: attachment; filename=Masterplan_Production_" . date('Y-m-d') . ".xls");

// 1. Query Dasar (Sesuai struktur data.php Anda)
$sql = "SELECT * FROM transaksi_mps WHERE 1=1";

// 2. Filter Pencarian
if (!empty($search)) {
    $s = mysqli_real_escape_string($conn, $search);
    $sql .= " AND (no_op LIKE '%$s%' OR exact_code LIKE '%$s%' OR exact_name LIKE '%$s%' OR nama_buyer LIKE '%$s%')";
}

// 3. Filter Bulan & Tahun
if (!empty($monthYear)) {
    $parts = explode('-', $monthYear);
    $year  = mysqli_real_escape_string($conn, $parts[0]);
    $month = mysqli_real_escape_string($conn, $parts[1]);
    $sql .= " AND YEAR(tgl_packing) = '$year' AND MONTH(tgl_packing) = '$month'";
}

// 4. Urutan data terbaru
$sql .= " ORDER BY tgl_packing DESC";

$query = mysqli_query($conn, $sql);
?>

<table border="1">
    <thead>
        <tr style="background-color: #f2f2f2; font-weight: bold;">
            <th>No OP</th>
            <th>Exact Code</th>
            <th>Exact Name</th>
            <th>Buyer</th>
            <th>Tgl Packing</th>
            <th>Jumlah</th>
        </tr>
    </thead>
    <tbody>
        <?php while ($row = mysqli_fetch_assoc($query)): ?>
        <tr>
            <td><?php echo $row['no_op']; ?></td>
            <td><?php echo $row['exact_code']; ?></td>
            <td><?php echo $row['exact_name']; ?></td>
            <td><?php echo $row['nama_buyer']; ?></td>
            <td><?php echo date('d-m-Y', strtotime($row['tgl_packing'])); ?></td>
            <td><?php echo $row['jml_op']; ?></td>
        </tr>
        <?php endwhile; ?>
    </tbody>
</table>