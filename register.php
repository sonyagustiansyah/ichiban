<?php
include "config.php";

if (isset($_POST['register'])) {
    $username = trim($_POST['username']);
    $password = $_POST['password'];
    $role = $_POST['role'];

    // Validasi role hanya boleh admin atau user
    if (!in_array($role, ['admin', 'user'])) {
        $error = "Role tidak valid!";
    } else {
        // Cek apakah username sudah ada
        $stmt = $conn->prepare("SELECT id FROM users WHERE username = ?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            $error = "Username sudah digunakan!";
        } else {
            // Hash password
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

            // Simpan user baru
            $stmt = $conn->prepare("INSERT INTO users (username, password, role) VALUES (?, ?, ?)");
            $stmt->bind_param("sss", $username, $hashedPassword, $role);

            if ($stmt->execute()) {
                header("Location: index.php");
                exit;
            } else {
                $error = "Gagal mendaftar, coba lagi!";
            }
        }
        $stmt->close();
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Register</title>
    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container mt-5">
  <div class="row justify-content-center">
    <div class="col-md-5">
      <div class="card shadow-lg">
        <div class="card-body">
          <h3 class="text-center mb-4">Register</h3>
          <?php if(isset($error)) { ?>
            <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
          <?php } ?>
          <form method="post">
            <div class="mb-3">
              <label class="form-label">Username</label>
              <input type="text" name="username" value="<?= isset($username) ? htmlspecialchars($username) : '' ?>" class="form-control" required>
            </div>
            <div class="mb-3">
              <label class="form-label">Password</label>
              <input type="password" name="password" class="form-control" required>
            </div>
            <div class="mb-3">
              <label class="form-label">Role</label>
              <select name="role" class="form-select" required>
                <option value="user" <?= (isset($role) && $role=="user") ? "selected" : "" ?>>User</option>
                <option value="admin" <?= (isset($role) && $role=="admin") ? "selected" : "" ?>>Admin</option>
              </select>
            </div>
            <button type="submit" name="register" class="btn btn-primary w-100">Register</button>
          </form>
          <p class="mt-3 text-center">
            <a href="index.php">Kembali ke Login</a>
          </p>
        </div>
      </div>
    </div>
  </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>