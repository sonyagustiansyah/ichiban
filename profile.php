<?php
session_start();
include "config.php";

if (!isset($_SESSION['username'])) {
    header("Location: index.php");
    exit;
}

$username = $_SESSION['username'];
$success = $error = "";

if (isset($_POST['update_password'])) {
    $current_password = $_POST['current_password'];
    $new_password     = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];

    // Ambil user dari database
    $sql = "SELECT * FROM users WHERE username='$username'";
    $result = mysqli_query($conn, $sql);
    $user = mysqli_fetch_assoc($result);

    if ($user && password_verify($current_password, $user['password'])) {
        if ($new_password === $confirm_password) {
            $hashed = password_hash($new_password, PASSWORD_DEFAULT);
            $update = "UPDATE users SET password='$hashed' WHERE username='$username'";
            if (mysqli_query($conn, $update)) {
                $success = "Password berhasil diubah!";
            } else {
                $error = "Terjadi kesalahan: " . mysqli_error($conn);
            }
        } else {
            $error = "Password baru tidak sama dengan konfirmasi!";
        }
    } else {
        $error = "Password lama salah!";
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Profile - Ubah Password</title>
    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

<!-- Navbar -->
<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
  <div class="container-fluid">
    <a class="navbar-brand" href="dashboard.php">Dashboard</a>
    <div class="collapse navbar-collapse" id="navbarNav">
      <ul class="navbar-nav me-auto">
        <li class="nav-item"><a class="nav-link" href="daily_visit.php">Daily Visit</a></li>
        <li class="nav-item"><a class="nav-link" href="timestamp.php">Timestamp</a></li>
        <li class="nav-item"><a class="nav-link" href="ichiban.php">Ichiban</a></li>
        <li class="nav-item"><a class="nav-link" href="stockcard.php">Stock Card</a></li>
      </ul>
      <ul class="navbar-nav">
        <li class="nav-item"><a class="nav-link active" href="profile.php">Profile</a></li>
        <li class="nav-item"><span class="navbar-text text-white me-3"><?= $_SESSION['username']; ?> (<?= $_SESSION['role']; ?>)</span></li>
        <li class="nav-item"><a class="btn btn-danger btn-sm mt-1" href="logout.php">Logout</a></li>
      </ul>
    </div>
  </div>
</nav>

<!-- Konten -->
<div class="container mt-4">
  <div class="row justify-content-center">
    <div class="col-md-6">
      <div class="card shadow-lg">
        <div class="card-body">
          <h3 class="mb-4 text-center">Ubah Password</h3>

          <!-- Pesan -->
          <?php if($success): ?>
            <div class="alert alert-success"><?= $success ?></div>
          <?php endif; ?>
          <?php if($error): ?>
            <div class="alert alert-danger"><?= $error ?></div>
          <?php endif; ?>

          <!-- Form Ubah Password -->
          <form method="post">
            <div class="mb-3">
              <label class="form-label">Password Lama</label>
              <input type="password" name="current_password" class="form-control" required>
            </div>
            <div class="mb-3">
              <label class="form-label">Password Baru</label>
              <input type="password" name="new_password" class="form-control" required>
            </div>
            <div class="mb-3">
              <label class="form-label">Konfirmasi Password Baru</label>
              <input type="password" name="confirm_password" class="form-control" required>
            </div>
            <button type="submit" name="update_password" class="btn btn-primary w-100">Update Password</button>
          </form>
        </div>
      </div>
    </div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>