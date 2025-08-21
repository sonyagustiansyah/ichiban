<?php
include 'config.php';

// Set header untuk download Excel
header("Content-Type: application/vnd.ms-excel");
header("Content-Disposition: attachment; filename=daily_visit_" . date('Y-m-d') . ".xls");
header("Pragma: no-cache");
header("Expires: 0");

// Query data
$query = mysqli_query($conn, "SELECT * FROM daily_visit ORDER BY tanggal_visit DESC");

// Buat tabel untuk Excel
echo "<table border='1'>";
echo "<tr>
        <th>No</th>
        <th>Tanggal</th>
        <th>Nama Outlet</th>
        <th>Alamat</th>
        <th>Area</th>
      </tr>";

$no = 1;
while ($row = mysqli_fetch_assoc($query)) {
    echo "<tr>";
    echo "<td>" . $no++ . "</td>";
    echo "<td>" . $row['tanggal_visit'] . "</td>";
    echo "<td>" . $row['nama_pos'] . "</td>";
    echo "<td>" . $row['alamat'] . "</td>";
    echo "<td>" . $row['area'] . "</td>";
    echo "</tr>";
}

echo "</table>";
?>