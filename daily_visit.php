<?php
session_start();
include "config.php";

if (!isset($_SESSION['username'])) {
    header("Location: index.php");
    exit;
}

$success = $error = "";

// Ambil data outlet (untuk autocomplete opsional)
$outlets = $conn->query("SELECT id, nama_pos, alamat, area FROM data_ichiban ORDER BY nama_pos ASC");

// Simpan data
if (isset($_POST['submit'])) {
    $tanggal_visit = trim($_POST['tanggal_visit']);
    $nama_pos      = trim($_POST['nama_pos']);
    $alamat        = trim($_POST['alamat']);
    $area          = trim($_POST['area']);

    if ($tanggal_visit && $nama_pos && $alamat && $area) {
        // Cek duplikat
        $stmt = $conn->prepare("SELECT id FROM daily_visit WHERE tanggal_visit = ? AND nama_pos = ?");
        $stmt->bind_param("ss", $tanggal_visit, $nama_pos);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            $error = "❌ Data kunjungan untuk outlet <b>" . htmlspecialchars($nama_pos) . "</b> pada tanggal <b>$tanggal_visit</b> sudah ada.";
        } else {
            $stmt = $conn->prepare("INSERT INTO daily_visit (tanggal_visit, nama_pos, alamat, area) VALUES (?, ?, ?, ?)");
            $stmt->bind_param("ssss", $tanggal_visit, $nama_pos, $alamat, $area);

            if ($stmt->execute()) {
                $success = "Data daily visit berhasil disimpan.";
            } else {
                $error = "Terjadi kesalahan saat simpan data.";
            }
        }
        $stmt->close();
    } else {
        $error = "Semua field wajib diisi.";
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Daily Visit</title>
  <meta name="viewport" content="width=device-width, initial-scale=1"> <!-- Responsive -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <style>
    body { background-color: #f8f9fa; }
    .card { border-radius: 12px; }
    .table th, .table td { vertical-align: middle; }
    #outletList { max-height: 250px; overflow-y: auto; }
  </style>
</head>
<body>
<!-- Navbar -->
<nav class="navbar navbar-expand-lg navbar-dark bg-dark sticky-top">
  <div class="container-fluid">
    <a class="navbar-brand" href="dashboard.php">SSS</a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarNav">
      <ul class="navbar-nav me-auto">
        <li class="nav-item"><a class="nav-link" href="dashboard.php">Dashboard</a></li>
        <li class="nav-item"><a class="nav-link active" href="daily_visit.php">Daily Visit</a></li>
        <li class="nav-item"><a class="nav-link" href="timestamp.php">Timestamp</a></li>
        <li class="nav-item"><a class="nav-link" href="ichiban.php">SSS</a></li>
        <li class="nav-item"><a class="nav-link" href="stockcard.php">Stock Card</a></li>
      </ul>
      <ul class="navbar-nav">
        <li class="nav-item"><a class="nav-link" href="profile.php">Profile</a></li>
        <li class="nav-item"><span class="navbar-text text-white me-3"><?= htmlspecialchars($_SESSION['username']); ?> (<?= htmlspecialchars($_SESSION['role']); ?>)</span></li>
        <li class="nav-item"><a class="btn btn-danger btn-sm mt-1" href="logout.php">Logout</a></li>
      </ul>
    </div>
  </div>
</nav>

<div class="container my-4">
  <!-- Form Tambah -->
  <div class="card shadow mb-4">
    <div class="card-body">
      <h1 class="mb-3">Daily Visit</h1>
      <?php if($success): ?><div class="alert alert-success"><?= $success ?></div><?php endif; ?>
      <?php if($error): ?><div class="alert alert-danger"><?= $error ?></div><?php endif; ?>

      <form method="post">
        <div class="mb-3">
          <label class="form-label">Tanggal Kunjungan</label>
          <input type="date" name="tanggal_visit" class="form-control" required>
        </div>
        <div class="mb-3 position-relative">
          <label class="form-label">Nama Outlet</label>
          <input type="text" id="nama_pos" name="nama_pos" class="form-control" autocomplete="off" required>
          <div id="outletList" class="list-group position-absolute w-100"></div>
        </div>
        <div class="mb-3">
          <label class="form-label">Alamat</label>
          <input type="text" name="alamat" id="alamat" class="form-control" readonly required>
        </div>
        <div class="mb-3">
          <label class="form-label">Area</label>
          <input type="text" name="area" id="area" class="form-control" readonly required>
        </div>
        <button type="submit" name="submit" class="btn btn-primary">Simpan</button>
      </form>
    </div>
  </div>

  <!-- Daftar Daily Visit -->
  <div class="card shadow">
    <div class="card-body">
      <h1 class="mb-3">Daftar Daily Visit</h1>

      <!-- Filter -->
      <form method="get" class="row g-2 mb-3">
        <div class="col-md-6 col-sm-8">
          <input type="text" name="keyword" class="form-control" 
                 placeholder="Cari nama outlet / area / tanggal"
                 value="<?= isset($_GET['keyword']) ? htmlspecialchars($_GET['keyword']) : '' ?>">
        </div>
        <div class="col-md-3 col-sm-4 d-flex gap-2">
          <button type="submit" class="btn btn-primary"><i class="bi bi-search"></i></button>
          <a href="daily_visit.php" class="btn btn-warning"><i class="bi bi-arrow-repeat"></i></a>
        </div>
      </form>

      <div class="mb-3">
        <a href="export_daily_visit.php" class="btn btn-success">Export Excel</a>
      </div>

      <div class="table-responsive">
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
            // Pagination
            $limit = 5;
            $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
            $offset = ($page - 1) * $limit;
            $keyword = isset($_GET['keyword']) ? "%" . $_GET['keyword'] . "%" : "%";

            // Hitung total data
            $stmt = $conn->prepare("SELECT COUNT(*) AS total FROM daily_visit WHERE nama_pos LIKE ? OR area LIKE ? OR tanggal_visit LIKE ?");
            $stmt->bind_param("sss", $keyword, $keyword, $keyword);
            $stmt->execute();
            $countResult = $stmt->get_result()->fetch_assoc();
            $total_rows = $countResult['total'];
            $total_pages = ceil($total_rows / $limit);

            // Query data
            $stmt = $conn->prepare("SELECT * FROM daily_visit WHERE nama_pos LIKE ? OR area LIKE ? OR tanggal_visit LIKE ? ORDER BY tanggal_visit DESC LIMIT ? OFFSET ?");
            $stmt->bind_param("sssii", $keyword, $keyword, $keyword, $limit, $offset);
            $stmt->execute();
            $daily_visits = $stmt->get_result();

            $no = $offset + 1;
            if ($daily_visits->num_rows > 0) {
              while($dv = $daily_visits->fetch_assoc()) { ?>
                <tr>
                  <td><?= $no++; ?></td>
                  <td><?= htmlspecialchars($dv['tanggal_visit']); ?></td>
                  <td><?= htmlspecialchars($dv['nama_pos']); ?></td>
                  <td><?= htmlspecialchars($dv['alamat']); ?></td>
                  <td><?= htmlspecialchars($dv['area']); ?></td>
                </tr>
              <?php }
            } else { ?>
              <tr><td colspan="5" class="text-center">Tidak ada data</td></tr>
            <?php } ?>
          </tbody>
        </table>
      </div>

      <!-- Pagination -->
      <nav>
        <ul class="pagination justify-content-center flex-wrap">
          <li class="page-item <?= ($page <= 1) ? 'disabled' : '' ?>">
            <a class="page-link" href="?page=<?= $page-1 ?>&keyword=<?= urlencode($_GET['keyword'] ?? '') ?>">Previous</a>
          </li>
          <?php for($i = 1; $i <= $total_pages; $i++): ?>
            <li class="page-item <?= ($i == $page) ? 'active' : '' ?>">
              <a class="page-link" href="?page=<?= $i ?>&keyword=<?= urlencode($_GET['keyword'] ?? '') ?>"><?= $i ?></a>
            </li>
          <?php endfor; ?>
          <li class="page-item <?= ($page >= $total_pages) ? 'disabled' : '' ?>">
            <a class="page-link" href="?page=<?= $page+1 ?>&keyword=<?= urlencode($_GET['keyword'] ?? '') ?>">Next</a>
          </li>
        </ul>
      </nav>
    </div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
$(document).ready(function(){
    $("#nama_pos").keyup(function(){
        var query = $(this).val();
        if(query != ""){
            $.ajax({
                url: "search_outlet.php",
                method: "POST",
                data: {query:query},
                success: function(data){
                    $("#outletList").fadeIn().html(data);
                }
            });
        } else {
            $("#outletList").fadeOut();
        }
    });

    $(document).on("click", ".outlet-item", function(e){
        e.preventDefault();
        $("#nama_pos").val($(this).data("nama"));
        $("#alamat").val($(this).data("alamat"));
        $("#area").val($(this).data("area"));
        $("#outletList").fadeOut();
    });
});
</script>
</body>
</html>