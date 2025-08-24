<?php
session_start();
include 'config.php';

// Cek login
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

$username = $_SESSION['username'];
$role     = $_SESSION['role'];

$err = "";

// ====== CREATE (Simpan data) ======
if (isset($_POST['submit'])) {
    // Ambil & trimming input
    $tanggal    = trim($_POST['tanggal'] ?? '');
    $nama_pos   = trim($_POST['nama_pos'] ?? '');
    $tipe       = trim($_POST['tipe'] ?? '');
    $area       = trim($_POST['area'] ?? '');
    $status     = trim($_POST['status'] ?? '');
    $tujuan     = trim($_POST['tujuan'] ?? '');
    $order_val  = trim($_POST['order_val'] ?? '');
    $qty        = trim($_POST['qty'] ?? '');
    $keterangan = trim($_POST['keterangan'] ?? '');

    // Validasi sederhana
    if ($tanggal === '' || $nama_pos === '' || $tipe === '' || $status === '' || $tujuan === '') {
        $err = "Semua field wajib diisi.";
    } else {
        // === Upload foto aman ===
        $foto = null;
        if (!empty($_FILES['foto']['name'])) {
            $target_dir = __DIR__ . "/uploads/";
            if (!is_dir($target_dir)) {
                mkdir($target_dir, 0777, true);
            }

            $tmpPath = $_FILES["foto"]["tmp_name"];
            $fileSize = $_FILES["foto"]["size"];

            // Validasi MIME asli file
            $mime = function_exists('mime_content_type') ? mime_content_type($tmpPath) : '';
            $allowedMime = ['image/jpeg','image/png','image/gif'];

            // Batas ukuran 2MB
            if (in_array($mime, $allowedMime, true) && $fileSize <= 2*1024*1024) {
                $ext = strtolower(pathinfo($_FILES["foto"]["name"], PATHINFO_EXTENSION));
                // Normalisasi ekstensi terhadap MIME
                if ($mime === 'image/jpeg' && !in_array($ext, ['jpg','jpeg'])) $ext = 'jpg';
                if ($mime === 'image/png'  && $ext !== 'png') $ext = 'png';
                if ($mime === 'image/gif'  && $ext !== 'gif') $ext = 'gif';

                $fileName = uniqid("img_", true) . "." . $ext;
                $destFs = $target_dir . $fileName;        // path di server (filesystem)
                $destUrl = "uploads/" . $fileName;        // path untuk disimpan ke DB (relative URL)

                if (move_uploaded_file($tmpPath, $destFs)) {
                    $foto = $destUrl;
                } else {
                    $err = "Upload foto gagal.";
                }
            } else {
                $err = "File harus JPG/PNG/GIF dan maksimal 2MB.";
            }
        }

        if ($err === "") {
            // Cek duplikat (tanggal + outlet + tujuan) -> sesuaikan jika perlu
            $chk = $conn->prepare("SELECT id FROM `timestamp` WHERE tanggal=? AND nama_pos=? AND tujuan=? LIMIT 1");
            $chk->bind_param("sss", $tanggal, $nama_pos, $tujuan);
            $chk->execute();
            $chk->store_result();

            if ($chk->num_rows > 0) {
                $chk->close();
                $err = "Data dengan kombinasi <b>tanggal</b>, <b>outlet</b>, dan <b>tujuan</b> sudah ada.";
            } else {
                $chk->close();

                // Insert pakai prepared statements
                $stmt = $conn->prepare(
                    "INSERT INTO `timestamp`
                    (tanggal, nama_pos, tipe, area, status, tujuan, `order`, qty, keterangan, foto)
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)"
                );
                // qty bisa numeric, tapi simpan sebagai string agar aman jika kosong
                $stmt->bind_param(
                    "ssssssssss",
                    $tanggal, $nama_pos, $tipe, $area, $status, $tujuan, $order_val, $qty, $keterangan, $foto
                );
                $stmt->execute();
                $stmt->close();

                header("Location: timestamp.php");
                exit();
            }
        }
    }
}

// ====== READ (Pencarian + Pagination) ======
$limit  = 5;
$page   = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
$offset = ($page - 1) * $limit;

$rawKeyword = $_GET['keyword'] ?? '';
$useFilter  = ($rawKeyword !== '');
$kw = "%".$rawKeyword."%";

// Hitung total
if ($useFilter) {
    $countStmt = $conn->prepare(
        "SELECT COUNT(*) AS total FROM `timestamp`
         WHERE tanggal LIKE ? OR nama_pos LIKE ? OR area LIKE ? OR tipe LIKE ? OR status LIKE ? OR tujuan LIKE ? OR `order` LIKE ? OR qty LIKE ? OR keterangan LIKE ?"
    );
    $countStmt->bind_param("sssssssss", $kw,$kw,$kw,$kw,$kw,$kw,$kw,$kw,$kw);
    $countStmt->execute();
    $total_rows = $countStmt->get_result()->fetch_assoc()['total'] ?? 0;
    $countStmt->close();

    $listStmt = $conn->prepare(
        "SELECT * FROM `timestamp`
         WHERE tanggal LIKE ? OR nama_pos LIKE ? OR area LIKE ? OR tipe LIKE ? OR status LIKE ? OR tujuan LIKE ? OR `order` LIKE ? OR qty LIKE ? OR keterangan LIKE ?
         ORDER BY id DESC LIMIT ? OFFSET ?"
    );
    $listStmt->bind_param("sssssssssii", $kw,$kw,$kw,$kw,$kw,$kw,$kw,$kw,$kw,$limit,$offset);
} else {
    $res = $conn->query("SELECT COUNT(*) AS total FROM `timestamp`");
    $total_rows = $res->fetch_assoc()['total'] ?? 0;

    $listStmt = $conn->prepare("SELECT * FROM `timestamp` ORDER BY id DESC LIMIT ? OFFSET ?");
    $listStmt->bind_param("ii", $limit, $offset);
}

$listStmt->execute();
$timestamps = $listStmt->get_result();
$total_pages = (int)ceil($total_rows / $limit);
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Timestamp - Ichiban</title>
  <meta name="viewport" content="width=device-width, initial-scale=1"> <!-- Responsive -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">
  <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
</head>
<body>
<!-- Navbar -->
<nav class="navbar navbar-expand-lg navbar-dark bg-dark mb-4">
  <div class="container-fluid">
    <a class="navbar-brand" href="dashboard.php">SSS</a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarNav">
      <ul class="navbar-nav me-auto">
        <li class="nav-item"><a class="nav-link" href="dashboard.php">Dashboard</a></li>
        <li class="nav-item"><a class="nav-link" href="daily_visit.php">Daily Visit</a></li>
        <li class="nav-item"><a class="nav-link active" href="timestamp.php">Timestamp</a></li>
        <li class="nav-item"><a class="nav-link" href="ichiban.php">SSS</a></li>
        <li class="nav-item"><a class="nav-link" href="stockcard.php">Stock Card</a></li>
      </ul>
      <ul class="navbar-nav">
        <li class="nav-item"><a class="nav-link" href="profile.php">Profile</a></li>
        <li class="nav-item">
          <span class="navbar-text text-white me-3"><?= htmlspecialchars($username) ?> (<?= htmlspecialchars($role) ?>)</span>
        </li>
        <li class="nav-item"><a class="btn btn-danger btn-sm mt-1" href="logout.php">Logout</a></li>
      </ul>
    </div>
  </div>
</nav>

<div class="container">
  <h1 class="mb-3">Timestamp</h1>

  <?php if($err): ?>
    <div class="alert alert-danger"><?= $err ?></div>
  <?php endif; ?>

  <!-- Form Input -->
  <form method="POST" class="row g-3" enctype="multipart/form-data">
    <div class="col-md-3">
      <label class="form-label">Tanggal</label>
      <input type="date" name="tanggal" class="form-control" required>
    </div>
    <div class="col-md-3 position-relative">
      <label class="form-label">Nama Outlet</label>
      <input type="text" name="nama_pos" id="nama_pos" class="form-control" autocomplete="off" required>
      <div id="outletList" class="list-group position-absolute w-100"></div>
    </div>
    <div class="col-md-3">
      <label class="form-label">Area</label>
      <input type="text" name="area" id="area" class="form-control">
    </div>
    <div class="col-md-3">
      <label class="form-label">Tipe</label>
      <select name="tipe" class="form-select" required>
        <option value="NOV">NOV</option>
        <option value="AO">AO</option>
        <option value="NO">NO</option>
        <option value="NOO">NOO</option>
        <option value="POS">POS</option>
      </select>
    </div>
    <div class="col-md-3">
      <label class="form-label">Status</label>
      <select name="status" class="form-select" required>
        <option value="Visit Wajib">Visit Wajib</option>
        <option value="Visit Tambahan">Visit Tambahan</option>
      </select>
    </div>
    <div class="col-md-3">
      <label class="form-label">Tujuan</label>
      <select name="tujuan" class="form-select" required>
        <option value="Visit">Visit</option>
        <option value="Visit Susulan">Visit Susulan</option>
      </select>
    </div>
    <div class="col-md-2">
      <label class="form-label">Order</label>
      <input type="text" name="order_val" class="form-control">
    </div>
    <div class="col-md-2">
      <label class="form-label">Qty</label>
      <input type="number" name="qty" class="form-control" inputmode="numeric">
    </div>
    <div class="col-md-5">
      <label class="form-label">Keterangan</label>
      <input type="text" name="keterangan" class="form-control">
    </div>
    <div class="col-md-3">
      <label class="form-label">Upload Foto</label>
      <input type="file" name="foto" class="form-control" accept="image/*" required>
    </div>
    <div class="col-12">
      <button type="submit" name="submit" class="btn btn-primary">Simpan</button>
    </div>
  </form>

  <hr>
  <h1>Daftar Timestamp</h1>

  <!-- Form Pencarian -->
  <form method="GET" class="d-flex mb-3">
    <input type="text" name="keyword" class="form-control me-2"
           placeholder="Cari nama outlet / area / tipe / tanggal"
           value="<?= htmlspecialchars($rawKeyword) ?>">
    <button type="submit" class="btn btn-primary me-2"><i class="bi bi-search"></i></button>
    <a href="timestamp.php" class="btn btn-warning"><i class="bi bi-arrow-repeat"></i></a>
  </form>

  <!-- Export -->
  <a href="export_timestamp.php<?= $useFilter ? '?keyword='.urlencode($rawKeyword) : '' ?>" class="btn btn-success mb-3">Export Excel</a>

  <!-- Tabel Data -->
  <div class="table-responsive" style="max-height: 500px;">
    <table class="table table-bordered table-striped">
      <thead class="table-dark">
        <tr>
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
        </tr>
      </thead>
      <tbody>
        <?php
        $no = $offset + 1;
        if ($timestamps && $timestamps->num_rows > 0):
          while ($row = $timestamps->fetch_assoc()):
        ?>
        <tr>
          <td><?= $no++ ?></td>
          <td><?= htmlspecialchars($row['tanggal']) ?></td>
          <td><?= htmlspecialchars($row['nama_pos']) ?></td>
          <td><?= htmlspecialchars($row['area']) ?></td>
          <td><?= htmlspecialchars($row['tipe']) ?></td>
          <td><?= htmlspecialchars($row['status']) ?></td>
          <td><?= htmlspecialchars($row['tujuan']) ?></td>
          <td><?= htmlspecialchars($row['order']) ?></td>
          <td><?= htmlspecialchars($row['qty']) ?></td>
          <td><?= htmlspecialchars($row['keterangan']) ?></td>
          <td>
            <?php if (!empty($row['foto'])): ?>
              <img src="<?= htmlspecialchars($row['foto']) ?>" alt="foto" width="80">
            <?php else: ?>
              -
            <?php endif; ?>
          </td>
        </tr>
        <?php
          endwhile;
        else:
          echo "<tr><td colspan='11' class='text-center'>Tidak ada data</td></tr>";
        endif;

        $listStmt->close();
        ?>
      </tbody>
    </table>
  </div>

  <!-- Pagination -->
  <nav>
    <ul class="pagination justify-content-center">
      <li class="page-item <?= ($page <= 1) ? 'disabled' : '' ?>">
        <a class="page-link" href="?page=<?= max(1, $page-1) ?>&keyword=<?= urlencode($rawKeyword) ?>">Previous</a>
      </li>
      <?php for($i=1; $i<=$total_pages; $i++): ?>
        <li class="page-item <?= ($page == $i) ? 'active' : '' ?>">
          <a class="page-link" href="?page=<?= $i ?>&keyword=<?= urlencode($rawKeyword) ?>"><?= $i ?></a>
        </li>
      <?php endfor; ?>
      <li class="page-item <?= ($page >= $total_pages) ? 'disabled' : '' ?>">
        <a class="page-link" href="?page=<?= min($total_pages, $page+1) ?>&keyword=<?= urlencode($rawKeyword) ?>">Next</a>
      </li>
    </ul>
  </nav>
</div>

<script>
$(document).ready(function(){
  $("#nama_pos").on("keyup", function(){
    let query = $(this).val().trim();
    if(query !== ""){
      $.ajax({
        url: "search_outlet.php",
        method: "POST",
        data: {query:query},
        success:function(data){
          $("#outletList").fadeIn().html(data);
        }
      });
    } else {
      $("#outletList").fadeOut().empty();
    }
  });

  // Klik hasil outlet (butuh .outlet-item dari search_outlet.php)
  $(document).on("click", ".outlet-item", function(e){
    e.preventDefault();
    $("#nama_pos").val($(this).data("nama"));
    $("#area").val($(this).data("area"));
    $("#outletList").fadeOut();
  });
});
</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>