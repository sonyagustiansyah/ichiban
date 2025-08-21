<?php
session_start();
include "config.php";

if (!isset($_SESSION['username'])) {
    header("Location: index.php");
    exit;
}

// Ambil data outlet dari tabel data_ichiban
$outlets = mysqli_query($conn, "SELECT id, nama_pos, alamat, area FROM data_ichiban ORDER BY nama_pos ASC");

$success = $error = "";

// Proses simpan data
if (isset($_POST['submit'])) {
    $tanggal_visit = $_POST['tanggal_visit'];
    $nama_pos      = $_POST['nama_pos'];
    $alamat        = $_POST['alamat'];
    $area          = $_POST['area'];

    $sql = "INSERT INTO daily_visit (tanggal_visit, nama_pos, alamat, area) 
            VALUES ('$tanggal_visit', '$nama_pos', '$alamat', '$area')";
    if (mysqli_query($conn, $sql)) {
        $success = "Data daily visit berhasil disimpan.";
    } else {
        $error = "Terjadi kesalahan: " . mysqli_error($conn);
    }
}

// Ambil data daily visit untuk ditampilkan
$daily_visits = mysqli_query($conn, "SELECT * FROM daily_visit ORDER BY tanggal_visit DESC");
?>
<!DOCTYPE html>
<html>
<head>
    <title>Daily Visit</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body>
<!-- Navbar -->
<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
  <div class="container-fluid">
    <a class="navbar-brand" href="dashboard.php">Dashboard</a>
    <div class="collapse navbar-collapse" id="navbarNav">
      <ul class="navbar-nav me-auto">
        <li class="nav-item"><a class="nav-link active" href="daily_visit.php">Daily Visit</a></li>
        <li class="nav-item"><a class="nav-link" href="timestamp.php">Timestamp</a></li>
        <li class="nav-item"><a class="nav-link" href="ichiban.php">Ichiban</a></li>
        <li class="nav-item"><a class="nav-link" href="stockcard.php">Stock Card</a></li>
      </ul>
      <ul class="navbar-nav">
        <li class="nav-item"><a class="nav-link" href="profile.php">Profile</a></li>
        <li class="nav-item"><span class="navbar-text text-white me-3"><?= $_SESSION['username']; ?> (<?= $_SESSION['role']; ?>)</span></li>
        <li class="nav-item"><a class="btn btn-danger btn-sm mt-1" href="logout.php">Logout</a></li>
      </ul>
    </div>
  </div>
</nav>

<div class="container mt-4">
  <div class="row">
    <div class="col-md-5">
      <div class="card shadow-lg">
        <div class="card-body">
          <h4 class="mb-3">Tambah Daily Visit</h4>
          <?php if($success): ?><div class="alert alert-success"><?= $success ?></div><?php endif; ?>
          <?php if($error): ?><div class="alert alert-danger"><?= $error ?></div><?php endif; ?>

          <form method="post">
            <div class="mb-3">
              <label class="form-label">Tanggal Kunjungan</label>
              <input type="date" name="tanggal_visit" class="form-control" required>
            </div>
            <div class="mb-3">
              <label class="form-label">Nama Outlet</label>
              <select name="nama_pos" id="nama_pos" class="form-select" required>
                <option value="">-- Pilih Outlet --</option>
                <?php while($o = mysqli_fetch_assoc($outlets)) { ?>
                  <option value="<?= $o['nama_pos']; ?>" data-alamat="<?= $o['alamat']; ?>" data-area="<?= $o['area']; ?>">
                    <?= $o['nama_pos']; ?>
                  </option>
                <?php } ?>
              </select>
            </div>
            <div class="mb-3">
              <label class="form-label">Alamat</label>
              <input type="text" name="alamat" id="alamat" class="form-control" readonly required>
            </div>
            <div class="mb-3">
              <label class="form-label">Area</label>
              <input type="text" name="area" id="area" class="form-control" readonly required>
            </div>
            <button type="submit" name="submit" class="btn btn-primary w-100">Simpan</button>
          </form>
        </div>
      </div>
    </div>

    <div class="col-md-7">
        <div class="card shadow-lg">
            <div class="card-body">
            <h4 class="mb-3">Daftar Daily Visit</h4>

            <!-- Filter Pencarian -->
            <form method="get" class="row g-2 mb-3">
                <div class="col-md-4">
                <input type="text" name="keyword" class="form-control" 
                        placeholder="Cari nama outlet / area / tanggal"
                        value="<?= isset($_GET['keyword']) ? $_GET['keyword'] : '' ?>">
                </div>
                <div class="col-md-1">
                <button type="submit" class="btn btn-outline-secondary w-100">🔎</button>
                </div>
                <div class="col-md-1">
                <a href="daily_visit.php" class="btn btn-outline-secondary w-100">🔄</a>
                </div>
            </form>

            <div class="d-flex justify-content-between mb-3">
                <a href="export_daily_visit.php" class="btn btn-success">
                    Export Excel
                </a>
            </div>

            <table class="table table-bordered table-striped">
                <thead class="table-dark">
                <tr>
                    <th>No</th>
                    <th>Tanggal</th>
                    <th>Nama Outlet</th>
                    <th>Alamat</th>
                    <th>Area</th>
                </tr>
                </thead>
                <tbody>
                <?php
                // Pencarian
                $where = "";
                if (isset($_GET['keyword']) && $_GET['keyword'] != "") {
                    $keyword = mysqli_real_escape_string($conn, $_GET['keyword']);
                    $where = "WHERE nama_pos LIKE '%$keyword%' 
                                OR area LIKE '%$keyword%' 
                                OR tanggal_visit LIKE '%$keyword%'";
                }

                $daily_visits = mysqli_query($conn, "SELECT * FROM daily_visit $where ORDER BY tanggal_visit DESC");
                $no = 1;
                if (mysqli_num_rows($daily_visits) > 0) {
                    while($dv = mysqli_fetch_assoc($daily_visits)) { ?>
                    <tr>
                        <td><?= $no++; ?></td>
                        <td><?= $dv['tanggal_visit']; ?></td>
                        <td><?= $dv['nama_pos']; ?></td>
                        <td><?= $dv['alamat']; ?></td>
                        <td><?= $dv['area']; ?></td>
                    </tr>
                <?php }
                } else { ?>
                    <tr><td colspan="5" class="text-center">Data tidak ditemukan</td></tr>
                <?php } ?>
                </tbody>
            </table>
            </div>
        </div>
    </div>

<script>
// Autofill alamat & area sesuai outlet terpilih
$("#nama_pos").on("change", function(){
    var alamat = $(this).find(':selected').data("alamat");
    var area   = $(this).find(':selected').data("area");
    $("#alamat").val(alamat);
    $("#area").val(area);
});
</script>

</body>
</html>