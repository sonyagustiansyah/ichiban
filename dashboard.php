<?php
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: index.php");
    exit;
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Dashboard</title>
    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

<!-- Navbar -->
<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
  <div class="container-fluid">
    <a class="navbar-brand" href="dashboard.php">Dashboard</a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"
      aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>

    <div class="collapse navbar-collapse" id="navbarNav">
      <!-- Menu kiri -->
      <ul class="navbar-nav me-auto mb-2 mb-lg-0">
        <li class="nav-item">
          <a class="nav-link" href="daily_visit.php">Daily Visit</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="timestamp.php">Timestamp</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="ichiban.php">Ichiban</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="stockcard.php">Stock Card</a>
        </li>
      </ul>

      <!-- Menu kanan (user info & logout) -->
      <ul class="navbar-nav">
        <li class="nav-item"><a class="nav-link active" href="profile.php">Profile</a></li>
        <li class="nav-item">
          <span class="navbar-text text-white me-3">
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

<!-- Konten Dashboard -->
<div class="container mt-4">
  <div class="p-4 bg-light rounded shadow-sm">
    <h2>Selamat datang, <?= $_SESSION['username']; ?> 👋</h2>
    <p class="text-muted">Silakan pilih menu di atas untuk mengakses data Daily Visit, Timestamp, Ichiban, atau Stock Card.</p>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>