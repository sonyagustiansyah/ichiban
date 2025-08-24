<?php
// Koneksi database
$host = "localhost";
$user = "root";
$pass = "";
$db   = "ichiban_app";

$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}

// Header untuk file Excel
header("Content-Type: application/vnd.ms-excel; charset=utf-8");
header("Content-Disposition: attachment; filename=timestamp_export.xls");
header("Pragma: no-cache");
header("Expires: 0");

// Query data
$sql = "SELECT id, tanggal, nama_pos, area, tipe, status, tujuan, `order`, qty, keterangan, foto 
        FROM timestamp ORDER BY id ASC";
$result = $conn->query($sql);

// Cetak tabel
echo "<table border='1'>";
echo "<tr>
        <th>No</th>
        <th>Tanggal</th>
        <th>Nama Outlet</th>
        <th>Area</th>
        <th>Tipe</th>
        <th>Status</th>
        <th>Tujuan</th>
        <th>Order</th>
        <th>Qty</th>
        <th>Keterangan</th>
        <th>Foto</th>
      </tr>";

$no = 1;
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        echo "<tr>
                <td>".$no."</td>
                <td>".$row['tanggal']."</td>
                <td>".$row['nama_pos']."</td>
                <td>".$row['area']."</td>
                <td>".$row['tipe']."</td>
                <td>".$row['status']."</td>
                <td>".$row['tujuan']."</td>
                <td>".$row['order']."</td>
                <td>".$row['qty']."</td>
                <td>".$row['keterangan']."</td>
                <td>".$row['foto']."</td>
              </tr>";
        $no++;
    }
}
echo "</table>";

$conn->close();
?>