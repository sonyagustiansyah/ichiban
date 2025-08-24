<?php
session_start();
include 'config.php';

if (!isset($_SESSION['username'])) {
  header("Location: login.php");
  exit();
}

$success = $error = "";

// Simpan data
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $tanggal          = $_POST['tanggal'];
    $kode             = $_POST['kode'];
    $nama_toko        = $_POST['nama_toko'];
    $alamat           = $_POST['alamat'];
    $area             = $_POST['area'];
    $nama_sales       = $_POST['nama_sales'];
    $cop_class        = $_POST['cop_class']; // gunakan 'class 1' s/d 'class 6'
    $diskon           = $_POST['diskon'];
    $top_day          = $_POST['top_day'];
    $order_set        = $_POST['order_set'];
    $supply_set       = $_POST['supply_set'];
    $tanggal_kirim    = $_POST['tanggal_kirim'];
    $tanggal_diterima = $_POST['tanggal_diterima'];

    // AR deadline = tanggal + TOP(day)
    $ar_deadline = null;
    if (!empty($tanggal) && is_numeric($top_day)) {
        $ar_deadline = date('Y-m-d', strtotime($tanggal . " +$top_day days"));
    }

    $sql = "INSERT INTO ichiban_orders
            (tanggal, kode, nama_toko, alamat, area, nama_sales, cop_class, diskon, top_day, order_set, supply_set, tanggal_kirim, tanggal_diterima, ar_deadline)
            VALUES
            ('$tanggal', '$kode', '$nama_toko', '$alamat', '$area', '$nama_sales', '$cop_class', '$diskon', '$top_day', '$order_set', '$supply_set', '$tanggal_kirim', '$tanggal_diterima', '$ar_deadline')";

    if (mysqli_query($conn, $sql)) {
        $success = "Data berhasil disimpan.";
    } else {
        $error = "Gagal menyimpan data: " . mysqli_error($conn);
    }
}

// Filter pencarian
$where = "1";
if (isset($_GET['cari'])) {
    $keyword = mysqli_real_escape_string($conn, $_GET['keyword']);
    if ($keyword != "") {
        $where .= " AND (nama_toko LIKE '%$keyword%' OR area LIKE '%$keyword%' OR ar_deadline LIKE '%$keyword%')";
    }
}

// Export Excel
if (isset($_GET['export'])) {
    header("Content-Type: application/vnd.ms-excel");
    header("Content-Disposition: attachment; filename=ichiban_orders.xls");
    $res = mysqli_query($conn, "SELECT * FROM ichiban_orders WHERE $where ORDER BY id DESC");
    echo "Tanggal\tKode\tNama Toko\tAlamat\tArea\tNama Sales\tCOP\tDiskon\tTOP\tOrder\tSupply\tTanggal Kirim\tTanggal Diterima\tAR Deadline\n";
    while ($r = mysqli_fetch_assoc($res)) {
        echo "$r[tanggal]\t$r[kode]\t$r[nama_toko]\t$r[alamat]\t$r[area]\t$r[nama_sales]\t$r[cop_class]\t$r[diskon]\t$r[top_day]\t$r[order_set]\t$r[supply_set]\t$r[tanggal_kirim]\t$r[tanggal_diterima]\t$r[ar_deadline]\n";
    }
    exit();
}

// Tentukan jumlah data per halaman
$limit = 5;

// Ambil halaman saat ini dari URL, default = 1
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$start = ($page - 1) * $limit;

// Ambil kata kunci pencarian
$keyword = isset($_GET['keyword']) ? $_GET['keyword'] : "";

// Query untuk hitung total data
$sql_count = "SELECT COUNT(*) AS total FROM ichiban_orders 
              WHERE nama_toko LIKE '%$keyword%' 
              OR area LIKE '%$keyword%' 
              OR ar_deadline LIKE '%$keyword%'";
$result_count = mysqli_query($conn, $sql_count);
$row_count = mysqli_fetch_assoc($result_count);
$total_data = $row_count['total'];

// Hitung total halaman
$total_pages = ceil($total_data / $limit);

// Query data sesuai halaman
$sql = "SELECT * FROM ichiban_orders 
        WHERE nama_toko LIKE '%$keyword%' 
        OR area LIKE '%$keyword%' 
        OR ar_deadline LIKE '%$keyword%'
        ORDER BY id DESC 
        LIMIT $start, $limit";
$result = mysqli_query($conn, $sql);

// Ambil data untuk tabel
$orders = mysqli_query($conn, "SELECT * FROM ichiban_orders WHERE $where ORDER BY id DESC");
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <title>Ichiban - Orders</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">
  <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
</head>
<body>
<!-- Navbar -->
<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
  <div class="container-fluid">
    <a class="navbar-brand" href="dashboard.php">SSS</a>

    <!-- Tombol Hamburger -->
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" 
      data-bs-target="#navbarNav" aria-controls="navbarNav" 
      aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>

    <!-- Menu Navbar -->
    <div class="collapse navbar-collapse" id="navbarNav">
      <ul class="navbar-nav me-auto mb-2 mb-lg-0">
        <li class="nav-item"><a class="nav-link" href="dashboard.php">Dashboard</a></li>
        <li class="nav-item"><a class="nav-link" href="daily_visit.php">Daily Visit</a></li>
        <li class="nav-item"><a class="nav-link" href="timestamp.php">Timestamp</a></li>
        <li class="nav-item"><a class="nav-link active" href="ichiban.php">SSS</a></li>
        <li class="nav-item"><a class="nav-link" href="stockcard.php">Stock Card</a></li>
      </ul>

      <!-- Menu kanan -->
      <ul class="navbar-nav ms-auto mb-2 mb-lg-0">
        <li class="nav-item"><a class="nav-link" href="profile.php">Profile</a></li>
        <li class="nav-item">
          <span class="navbar-text text-white me-2">
            <?= $_SESSION['username']; ?> (<?= $_SESSION['role']; ?>)
          </span>
        </li>
        <li class="nav-item">
          <a class="btn btn-danger btn-sm mt-1" href="logout.php">Logout</a>
        </li>
      </ul>
    </div>
  </div>
</nav>

<div class="container my-4">

  <!-- Form -->
  <div class="card shadow mb-4">
    <div class="card-body">
      <h1 class="mb-3">Order</h1>
      <?php if($success): ?><div class="alert alert-success py-2"><?= $success ?></div><?php endif; ?>
      <?php if($error): ?><div class="alert alert-danger py-2"><?= $error ?></div><?php endif; ?>

      <form method="POST" class="row g-3">
        <div class="col-md-3">
          <label class="form-label">Tanggal (Order)</label>
          <input type="date" name="tanggal" id="tanggal" class="form-control" required>
        </div>
        <div class="col-md-3">
          <label class="form-label">Kode</label>
          <input type="text" name="kode" class="form-control" required>
        </div>
        <div class="col-md-6 position-relative">
            <label class="form-label">Nama Toko</label>
            <input type="text" name="nama_toko" id="nama_toko" class="form-control" autocomplete="off" required>
            <div id="tokoList" class="list-group position-absolute w-100"></div>
        </div>
        <div class="col-md-6">
            <label class="form-label">Alamat</label>
            <input type="text" name="alamat" id="alamat" class="form-control" required>
        </div>
            <div class="col-md-3">
            <label class="form-label">Area</label>
            <input type="text" name="area" id="area" class="form-control" required>
        </div>
        <div class="col-md-3">
          <label class="form-label">Nama Sales</label>
          <input type="text" name="nama_sales" class="form-control" required>
        </div>
        <div class="col-md-3">
          <label class="form-label">COP</label>
          <select name="cop_class" class="form-select" required>
            <option value="">-- Pilih COP --</option>
            <option value="class 1">Class 1</option>
            <option value="class 2">Class 2</option>
            <option value="class 3">Class 3</option>
            <option value="class 4">Class 4</option>
            <option value="class 5">Class 5</option>
            <option value="class 6">Class 6</option>
          </select>
        </div>
        <div class="col-md-2">
          <label class="form-label">Diskon (%)</label>
          <input type="number" step="0.01" name="diskon" class="form-control" required>
        </div>
        <div class="col-md-2">
          <label class="form-label">TOP (day)</label>
          <input type="number" name="top_day" id="top_day" class="form-control" min="0" required>
        </div>
        <div class="col-md-2">
          <label class="form-label">Order (set)</label>
          <input type="number" name="order_set" class="form-control" min="0" required>
        </div>
        <div class="col-md-2">
          <label class="form-label">Supply (set)</label>
          <input type="number" name="supply_set" class="form-control" min="0" required>
        </div>
        <div class="col-md-3">
          <label class="form-label">Tanggal Kirim</label>
          <input type="date" name="tanggal_kirim" class="form-control" required>
        </div>
        <div class="col-md-3">
          <label class="form-label">Tanggal Barang Diterima</label>
          <input type="date" name="tanggal_diterima" class="form-control" required>
        </div>

        <!-- AR Deadline (auto, readonly) -->
        <div class="col-md-3">
          <label class="form-label">AR Deadline (Tanggal + TOP)</label>
          <input type="date" id="ar_deadline" class="form-control" readonly required>
        </div>

        <div class="col-12">
          <button type="submit" class="btn btn-primary">Simpan</button>
        </div>
      </form>
    </div>
  </div>

  <!-- Tabel -->
  <div class="card shadow">
      <div class="card-body">
        <h1 class="mb-3">Daftar Order</h1>
        <form method="GET" class="d-flex mb-3">
            <input type="text" name="keyword" class="form-control me-2"
                placeholder="Cari Nama Toko / Area / AR Deadline"
                value="<?= isset($_GET['keyword']) ? $_GET['keyword'] : '' ?>">
            <button type="submit" name="cari" value="1" class="btn btn-primary me-2">
                <i class="bi bi-search"></i>
            </button>
            <a href="ichiban.php" class="btn btn-warning me-2">
                <i class="bi bi-arrow-repeat"></i>
            </a>
        </form>
        <button type="submit" name="export" value="1" class="btn btn-success mb-3">
            Export Excel
        </button>
        <div class="table-responsive">
            <table class="table table-bordered table-striped">
                <thead class="table-dark">
                    <tr>
                        <th>No</th>
                        <th>Tanggal (Order)</th>
                        <th>Kode</th>
                        <th>Nama Toko</th>
                        <th>Alamat</th>
                        <th>Area</th>
                        <th>Nama Sales</th>
                        <th>COP</th>
                        <th>Diskon (%)</th>
                        <th>TOP (day)</th>
                        <th>Order (set)</th>
                        <th>Supply (set)</th>
                        <th>Tanggal Kirim</th>
                        <th>Tanggal Barang Diterima</th>
                        <th>AR Deadline</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if(mysqli_num_rows($result) > 0): ?>
                    <?php $no = $start + 1; while($r = mysqli_fetch_assoc($result)): ?>
                        <tr>
                            <td><?= $no++ ?></td>
                            <td><?= htmlspecialchars($r['tanggal']) ?></td>
                            <td><?= htmlspecialchars($r['kode']) ?></td>
                            <td><?= htmlspecialchars($r['nama_toko']) ?></td>
                            <td><?= htmlspecialchars($r['alamat']) ?></td>
                            <td><?= htmlspecialchars($r['area']) ?></td>
                            <td><?= htmlspecialchars($r['nama_sales']) ?></td>
                            <td><?= htmlspecialchars($r['cop_class']) ?></td>
                            <td><?= htmlspecialchars($r['diskon']) ?>%</td>
                            <td><?= htmlspecialchars($r['top_day']) ?></td>
                            <td><?= htmlspecialchars($r['order_set']) ?></td>
                            <td><?= htmlspecialchars($r['supply_set']) ?></td>
                            <td><?= htmlspecialchars($r['tanggal_kirim']) ?></td>
                            <td><?= htmlspecialchars($r['tanggal_diterima']) ?></td>
                            <td><?= htmlspecialchars($r['ar_deadline']) ?></td>
                        </tr>
                    <?php endwhile; ?>
                    <?php else: ?>
                        <tr><td colspan="15" class="text-center">Tidak ada data</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <nav aria-label="Page navigation">
            <ul class="pagination justify-content-center">
                <!-- Tombol Prev -->
                <li class="page-item <?= ($page <= 1) ? 'disabled' : '' ?>">
                <a class="page-link" href="?page=<?= $page-1 ?>&keyword=<?= $keyword ?>">Previous</a>
                </li>

                <!-- Nomor Halaman -->
                <?php for($i = 1; $i <= $total_pages; $i++): ?>
                <li class="page-item <?= ($page == $i) ? 'active' : '' ?>">
                    <a class="page-link" href="?page=<?= $i ?>&keyword=<?= $keyword ?>">
                    <?= $i ?>
                    </a>
                </li>
                <?php endfor; ?>

                <!-- Tombol Next -->
                <li class="page-item <?= ($page >= $total_pages) ? 'disabled' : '' ?>">
                <a class="page-link" href="?page=<?= $page+1 ?>&keyword=<?= $keyword ?>">Next</a>
                </li>
            </ul>
        </nav>
    </div>
  </div>
</div>

<script>
// Live search Nama Toko
$("#nama_toko").keyup(function(){
    var query = $(this).val();
    if(query != ""){
        $.ajax({
            url:"search_toko.php",
            method:"POST",
            data:{query:query},
            success:function(data){
                $("#tokoList").fadeIn().html(data);
            }
        });
    } else {
        $("#tokoList").fadeOut();
    }
});

// Pilih salah satu hasil
$(document).on("click", ".toko-item", function(e){
    e.preventDefault();
    $("#nama_toko").val($(this).data("nama"));
    $("#alamat").val($(this).data("alamat"));
    $("#area").val($(this).data("area"));
    $("#tokoList").fadeOut();
});
</script>

<script>
// Hitung AR Deadline = Tanggal + TOP(day)
function formatDateToYMD(d) {
  const year = d.getFullYear();
  const month = String(d.getMonth() + 1).padStart(2, '0');
  const day = String(d.getDate()).padStart(2, '0');
  return `${year}-${month}-${day}`;
}

function calcArDeadline() {
  const tgl = document.getElementById('tanggal').value;   // yyyy-mm-dd
  const top = parseInt(document.getElementById('top_day').value || '0', 10);
  const out = document.getElementById('ar_deadline');

  if (tgl && !isNaN(top)) {
    const base = new Date(tgl + 'T00:00:00'); // aman untuk timezone
    base.setDate(base.getDate() + top);
    out.value = formatDateToYMD(base);
  } else {
    out.value = '';
  }
}

document.getElementById('tanggal').addEventListener('change', calcArDeadline);
document.getElementById('top_day').addEventListener('input', calcArDeadline);
</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>