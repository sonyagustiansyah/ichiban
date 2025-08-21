<?php
include 'config.php';

// Handle form submit
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $tanggal    = $conn->real_escape_string($_POST['tanggal']);
    $nama_pos   = $conn->real_escape_string($_POST['nama_pos']);
    $tipe       = $conn->real_escape_string($_POST['tipe']);
    $area       = $conn->real_escape_string($_POST['area']);
    $status     = $conn->real_escape_string($_POST['status']);
    $tujuan     = $conn->real_escape_string($_POST['tujuan']);
    $order_no   = $conn->real_escape_string($_POST['order']); // rename ke order_no
    $qty        = $conn->real_escape_string($_POST['qty']);
    $keterangan = $conn->real_escape_string($_POST['keterangan']);

    $sql = "INSERT INTO timestamp (tanggal, nama_pos, tipe, area, status, tujuan, `order`, qty, keterangan)
            VALUES ('$tanggal','$nama_pos','$tipe','$area','$status','$tujuan','$order_no','$qty','$keterangan')";
    $conn->query($sql);
}

// Ambil data outlet dari data_ichiban
$outlets = $conn->query("SELECT nama_pos, area FROM data_ichiban");

// Ambil daftar timestamp
$timestamps = $conn->query("SELECT * FROM timestamp ORDER BY tanggal DESC");
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Timestamp - Ichiban</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body>
<!-- Navbar -->
<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
  <div class="container-fluid">
    <a class="navbar-brand" href="dashboard.php">Ichiban</a>
    <div class="collapse navbar-collapse">
      <ul class="navbar-nav me-auto">
        <li class="nav-item"><a class="nav-link" href="daily_visit.php">Daily Visit</a></li>
        <li class="nav-item"><a class="nav-link active" href="timestamp.php">Timestamp</a></li>
        <li class="nav-item"><a class="nav-link" href="ichiban.php">Ichiban</a></li>
        <li class="nav-item"><a class="nav-link" href="stockcard.php">Stock Card</a></li>
      </ul>
    </div>
  </div>
</nav>

<div class="container mt-4">
  <h2 class="mb-4">Form Timestamp</h2>
  <div class="card mb-4">
    <div class="card-body">
      <form method="POST" class="row g-3">
        <div class="col-md-3">
          <label class="form-label">Tanggal</label>
          <input type="date" name="tanggal" class="form-control" required>
        </div>
        <div class="col-md-3">
          <label class="form-label">Nama Outlet</label>
          <select name="nama_pos" id="nama_pos" class="form-control" required>
            <option value="">-- Pilih Outlet --</option>
            <?php while($row = $outlets->fetch_assoc()): ?>
              <option value="<?= $row['nama_pos']; ?>" data-area="<?= $row['area']; ?>">
                <?= $row['nama_pos']; ?>
              </option>
            <?php endwhile; ?>
          </select>
        </div>
        <div class="col-md-3">
          <label class="form-label">Area</label>
          <input type="text" name="area" id="area" class="form-control" readonly>
        </div>
        <div class="col-md-3">
          <label class="form-label">Tipe</label>
          <select name="tipe" class="form-control" required>
            <option value="NOV">NOV</option>
            <option value="AO">AO</option>
            <option value="NO">NO</option>
            <option value="NOO">NOO</option>
            <option value="POS">POS</option>
          </select>
        </div>
        <div class="col-md-3">
          <label class="form-label">Status</label>
          <select name="status" class="form-control" required>
            <option value="Visit Tambahan">Visit Tambahan</option>
            <option value="Visit Wajib">Visit Wajib</option>
          </select>
        </div>
        <div class="col-md-3">
          <label class="form-label">Tujuan</label>
          <select name="tujuan" class="form-control" required>
            <option value="Visit">Visit</option>
            <option value="Visit Susulan">Visit Susulan</option>
          </select>
        </div>
        <div class="col-md-2">
          <label class="form-label">Order</label>
          <input type="text" name="order" class="form-control">
        </div>
        <div class="col-md-2">
          <label class="form-label">Qty</label>
          <input type="number" name="qty" class="form-control">
        </div>
        <div class="col-md-4">
          <label class="form-label">Keterangan</label>
          <input type="text" name="keterangan" class="form-control">
        </div>
        <div class="col-12">
          <button type="submit" class="btn btn-primary">Simpan</button>
        </div>
      </form>
    </div>
  </div>

  <h2>Daftar Timestamp</h2>
  <input class="form-control mb-3" id="searchInput" type="text" placeholder="Cari data...">

  <div class="table-responsive">
    <table class="table table-bordered table-striped">
      <thead class="table-dark">
        <tr>
          <th>No</th>
          <th>Tanggal</th>
          <th>Nama Outlet</th>
          <th>Tipe</th>
          <th>Area</th>
          <th>Status</th>
          <th>Tujuan</th>
          <th>Order</th>
          <th>Qty</th>
          <th>Keterangan</th>
        </tr>
      </thead>
      <tbody id="timestampTable">
        <?php $no=1; while($row = $timestamps->fetch_assoc()): ?>
        <tr>
          <td><?= $no++; ?></td>
          <td><?= $row['tanggal']; ?></td>
          <td><?= $row['nama_pos']; ?></td>
          <td><?= $row['tipe']; ?></td>
          <td><?= $row['area']; ?></td>
          <td><?= $row['status']; ?></td>
          <td><?= $row['tujuan']; ?></td>
          <td><?= $row['order']; ?></td>
          <td><?= $row['qty']; ?></td>
          <td><?= $row['keterangan']; ?></td>
        </tr>
        <?php endwhile; ?>
      </tbody>
    </table>
  </div>
</div>

<script>
// Auto isi area berdasarkan outlet
$("#nama_pos").on("change", function() {
  var area = $(this).find(':selected').data('area');
  $("#area").val(area);
});

// Filter pencarian
$("#searchInput").on("keyup", function() {
  var value = $(this).val().toLowerCase();
  $("#timestampTable tr").filter(function() {
    $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
  });
});
</script>
</body>
</html>