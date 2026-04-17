<?php
$host = "localhost";
$user = "root";
$pass = "";
$db   = "gm_simasi";

$conn = mysqli_connect($host, $user, $pass, $db);

if (!$conn) {
    die("Koneksi gagal: " . mysqli_connect_error());
}

// Hilangkan '/' di akhir agar lebih fleksibel saat pemanggilan
$base_url = "http://localhost/simasi_new"; 
$create_at = date('Y-m-d H:i:s');


function pengaman($data)
{
    global $conn;
    $filter = mysqli_real_escape_string($conn, strtoupper(trim($data)));
    return $filter;
}

function nourut($table, $kode)
{
    global $conn;
    $sql_nourut   = mysqli_query($conn, "SELECT max($kode) as maxKode
															FROM $table");
    $row     = mysqli_fetch_array($sql_nourut);
    return $row['maxKode'] + 1;
}


function auto_kode($table, $column, $prefix, $totalLength)
{
    global $conn;

    // contoh prefix: CUT2601
    $prefixLength = strlen($prefix);

    $query = mysqli_query($conn, "
        SELECT $column 
        FROM $table
        WHERE $column LIKE '$prefix%'
        ORDER BY $column DESC
        LIMIT 1
    ");

    if (mysqli_num_rows($query) == 0) {
        $urutan = 1;
    } else {
        $row = mysqli_fetch_assoc($query);
        $lastCode = $row[$column];

        // 🔥 ambil angka SETELAH prefix
        $number = substr($lastCode, $prefixLength);
        $urutan = (int)$number + 1;
    }

    // hitung panjang angka
    $numberLength = $totalLength - $prefixLength;

    // padding nol
    $urutanPadded = str_pad($urutan, $numberLength, '0', STR_PAD_LEFT);

    return $prefix . $urutanPadded;
}


?>