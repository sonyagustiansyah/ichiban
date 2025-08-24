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

    // Validasi panjang password minimal 6 karakter
    if (strlen($new_password) < 6) {
        $error = "Password baru minimal 6 karakter!";
    } else {
        // Ambil user dari database (prepared statement)
        $stmt = $conn->prepare("SELECT password FROM users WHERE username = ?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();
        $user = $result->fetch_assoc();

        if ($user && password_verify($current_password, $user['password'])) {
            if ($new_password === $confirm_password) {
                $hashed = password_hash($new_password, PASSWORD_DEFAULT);

                $stmt = $conn->prepare("UPDATE users SET password = ? WHERE username = ?");
                $stmt->bind_param("ss", $hashed, $username);

                if ($stmt->execute()) {
                    $success = "Password berhasil diubah!";
                } else {
                    $error = "Terjadi kesalahan. Coba lagi!";
                }
            } else {
                $error = "Password baru tidak sama dengan konfirmasi!";
            }
        } else {
            $error = "Password lama salah!";
        }
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
    <a class="navbar-brand" href="dashboard.php">SSS</a>
    <div class="collapse navbar-collapse" id="navbarNav">
      <ul class="navbar-nav me-auto">
        <li class="nav-item"><a class="nav-link" href="dashboard.php">Dashboard</a></li>
        <li class="nav-item"><a class="nav-link" href="daily_visit.php">Daily Visit</a></li>
        <li class="nav-item"><a class="nav-link" href="timestamp.php">Timestamp</a></li>
        <li class="nav-item"><a class="nav-link" href="ichiban.php">SSS</a></li>
        <li class="nav-item"><a class="nav-link" href="stockcard.php">Stock Card</a></li>
      </ul>
      <ul class="navbar-nav">
        <li class="nav-item"><a class="nav-link active" href="profile.php">Profile</a></li>
        <li class="nav-item"><span class="navbar-text text-white me-3"><?= htmlspecialchars($_SESSION['username']); ?> (<?= htmlspecialchars($_SESSION['role']); ?>)</span></li>
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
            <div class="alert alert-success"><?= htmlspecialchars($success) ?></div>
          <?php endif; ?>
          <?php if($error): ?>
            <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
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