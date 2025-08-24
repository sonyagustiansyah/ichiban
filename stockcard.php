<?php
session_start(); // WAJIB ditambahkan di awal

include 'config.php';

// === Export Excel ===
if (isset($_GET['export'])) {
    header("Content-Type: application/vnd.ms-excel");
    header("Content-Disposition: attachment; filename=stockcard_ichiban.xls");

    $sql = "SELECT * FROM stock_card_ichiban";
    $result = $conn->query($sql);

    echo "Kode Ichiban\tNomor OEM\tMerk Mobil\tNama Mobil\tModel Mobil\tPosisi\tQty\tSet\n";
    while ($row = $result->fetch_assoc()) {
        echo $row['kode_ichiban'] . "\t" . $row['nomor_oem'] . "\t" . $row['merk_mobil'] . "\t" .
             $row['nama_mobil'] . "\t" . $row['model_mobil'] . "\t" . $row['posisi'] . "\t" .
             $row['qty'] . "\t" . $row['set'] . "\n";
    }
    exit();
}

// === Insert Data (Prepared Statement) ===
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['submit'])) {
    $stmt = $conn->prepare("INSERT INTO stock_card_ichiban 
        (kode_ichiban, nomor_oem, merk_mobil, nama_mobil, model_mobil, posisi, qty, `set`) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssssssii",
        $_POST['kode_ichiban'],
        $_POST['nomor_oem'],
        $_POST['merk_mobil'],
        $_POST['nama_mobil'],
        $_POST['model_mobil'],
        $_POST['posisi'],
        $_POST['qty'],
        $_POST['set']
    );
    $stmt->execute();
    $stmt->close();
}

// === Pagination & Search ===
$limit = 5;
$page = isset($_GET['page']) ? intval($_GET['page']) : 1;
$start = ($page - 1) * $limit;

$search = isset($_GET['search']) ? trim($_GET['search']) : "";
$where = "";
$params = [];
$types = "";

if ($search !== "") {
    $where = "WHERE kode_ichiban LIKE ? 
              OR nomor_oem LIKE ? 
              OR merk_mobil LIKE ? 
              OR nama_mobil LIKE ? 
              OR model_mobil LIKE ? 
              OR posisi LIKE ?";
    $param = "%$search%";
    $params = [$param, $param, $param, $param, $param, $param];
    $types = "ssssss";
}

// ambil data
if ($where) {
    $stmt = $conn->prepare("SELECT * FROM stock_card_ichiban $where LIMIT ?, ?");
    $types .= "ii";
    $params[] = $start;
    $params[] = $limit;
    $stmt->bind_param($types, ...$params);
} else {
    $stmt = $conn->prepare("SELECT * FROM stock_card_ichiban LIMIT ?, ?");
    $stmt->bind_param("ii", $start, $limit);
}
$stmt->execute();
$result = $stmt->get_result();
$rows = $result->fetch_all(MYSQLI_ASSOC);
$stmt->close();

// hitung total data
if ($where) {
    $stmt = $conn->prepare("SELECT COUNT(*) AS total FROM stock_card_ichiban $where");
    $stmt->bind_param("ssssss", ...array_fill(0, 6, $param));
} else {
    $stmt = $conn->prepare("SELECT COUNT(*) AS total FROM stock_card_ichiban");
}
$stmt->execute();
$total = $stmt->get_result()->fetch_assoc()['total'];
$stmt->close();

$pages = ceil($total / $limit);
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Stock Card Ichiban</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">
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
        <li class="nav-item"><a class="nav-link" href="timestamp.php">Timestamp</a></li>
        <li class="nav-item"><a class="nav-link" href="ichiban.php">SSS</a></li>
        <li class="nav-item"><a class="nav-link active" href="stockcard.php">Stock Card</a></li>
      </ul>
      <ul class="navbar-nav">
        <?php if (isset($_SESSION['username'])): ?>
        <li class="nav-item">
          <a class="nav-link" href="profile.php">
            Profile
          </a>
        </li>
        <li class="nav-item">
          <span class="navbar-text text-white me-3"><?= htmlspecialchars($_SESSION['username']); ?> (<?= htmlspecialchars($_SESSION['role']); ?>)</span>
        </li>
        <li class="nav-item">
          <a class="btn btn-danger btn-sm mt-1" href="logout.php">Logout</a>
        </li>
        <?php else: ?>
        <li class="nav-item">
          <a class="btn btn-success btn-sm mt-1" href="login.php">Login</a>
        </li>
        <?php endif; ?>
      </ul>
    </div>
  </div>
</nav>

<div class="container">
  <h1 class="mb-4">Stock Card</h1>
  <form method="POST" class="row g-3 mb-3">
    <div class="col-md-3"><label class="form-label">Kode SSS</label><input type="text" name="kode_ichiban" class="form-control" required></div>
    <div class="col-md-3"><label class="form-label">Nomor OEM</label><input type="text" name="nomor_oem" class="form-control" required></div>
    <div class="col-md-3"><label class="form-label">Merk Mobil</label><input type="text" name="merk_mobil" class="form-control" required></div>
    <div class="col-md-3"><label class="form-label">Nama Mobil</label><input type="text" name="nama_mobil" class="form-control" required></div>
    <div class="col-md-3"><label class="form-label">Model Mobil</label><input type="text" name="model_mobil" class="form-control" required></div>
    <div class="col-md-3"><label class="form-label">Posisi</label><input type="text" name="posisi" class="form-control" required></div>
    <div class="col-md-3"><label class="form-label">Qty</label><input type="number" name="qty" class="form-control"></div>
    <div class="col-md-3"><label class="form-label">Set</label><input type="number" name="set" class="form-control"></div>
    <div class="col-md-12"><button type="submit" name="submit" class="btn btn-primary">Simpan</button></div>
  </form>

  <hr>
  <h1>Daftar Stock Card</h1>

  <!-- Search -->
  <form method="GET" class="mb-3 d-flex">
    <input type="text" name="search" class="form-control me-2" placeholder="Cari data..." value="<?= htmlspecialchars($search) ?>">
    <button type="submit" class="btn btn-primary me-2"><i class="bi bi-search"></i></button>
    <a href="stockcard.php" class="btn btn-warning me-2"><i class="bi bi-arrow-repeat"></i></a>
  </form>

  <a href="stockcard.php?export=1" class="btn btn-success mb-3">Export Excel</a>

  <!-- Table Responsive -->
  <div class="table-responsive">
    <table class="table table-bordered table-striped">
      <thead class="table-dark">
        <tr>
          <th>No</th>
          <th>Kode SSS</th>
          <th>Nomor OEM</th>
          <th>Merk Mobil</th>
          <th>Nama Mobil</th>
          <th>Model Mobil</th>
          <th>Posisi</th>
          <th>Qty</th>
          <th>Set</th>
        </tr>
      </thead>
      <tbody>
        <?php if ($rows): $no = $start + 1; foreach ($rows as $row): ?>
        <tr>
          <td><?= $no++ ?></td>
          <td><?= htmlspecialchars($row['kode_ichiban']) ?></td>
          <td><?= htmlspecialchars($row['nomor_oem']) ?></td>
          <td><?= htmlspecialchars($row['merk_mobil']) ?></td>
          <td><?= htmlspecialchars($row['nama_mobil']) ?></td>
          <td><?= htmlspecialchars($row['model_mobil']) ?></td>
          <td><?= htmlspecialchars($row['posisi']) ?></td>
          <td><?= htmlspecialchars($row['qty']) ?></td>
          <td><?= htmlspecialchars($row['set']) ?></td>
        </tr>
        <?php endforeach; else: ?>
        <tr><td colspan="9" class="text-center">Tidak ada data</td></tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>

  <!-- Pagination -->
  <nav>
    <ul class="pagination justify-content-center">
      <li class="page-item <?= ($page <= 1) ? 'disabled' : '' ?>">
        <a class="page-link" href="?page=<?= $page-1 ?>&search=<?= urlencode($search) ?>">Prev</a>
      </li>
      <?php for ($i = 1; $i <= $pages; $i++): ?>
        <li class="page-item <?= ($page == $i) ? 'active' : '' ?>">
          <a class="page-link" href="?page=<?= $i ?>&search=<?= urlencode($search) ?>"><?= $i ?></a>
        </li>
      <?php endfor; ?>
      <li class="page-item <?= ($page >= $pages) ? 'disabled' : '' ?>">
        <a class="page-link" href="?page=<?= $page+1 ?>&search=<?= urlencode($search) ?>">Next</a>
      </li>
    </ul>
  </nav>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>