<?php
require_once 'includes/functions.php';

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

if (is_user_loggedin()) {
    $redirect_url = isset($_SESSION['redirect_url']) ? $_SESSION['redirect_url'] : 'index.php';
    unset($_SESSION['redirect_url']);
    header('Location: ' . $redirect_url);
    exit();
}

$error = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username_email = trim($_POST['username_email']);
    $password = trim($_POST['password']);

    if (empty($username_email) || empty($password)) {
        $error = "Username/Email dan password harus diisi.";
    } else {
        global $db;
        $sql = "SELECT user_id, username, password, is_admin FROM users WHERE username = ? OR email = ?";
        if ($stmt = $db->prepare($sql)) {
            $stmt->bind_param("ss", $param_username_email, $param_username_email);
            $param_username_email = $username_email;

            if ($stmt->execute()) {
                $stmt->store_result();
                if ($stmt->num_rows == 1) {
                    $stmt->bind_result($user_id, $username, $hashed_password, $is_admin);
                    if ($stmt->fetch()) {
                        if (password_verify($password, $hashed_password)) {
                            session_regenerate_id(true);
                            $_SESSION['user_id'] = $user_id;
                            $_SESSION['username'] = $username;
                            $_SESSION['is_admin'] = (bool)$is_admin;

                            $update_login_sql = "UPDATE users SET last_login = NOW() WHERE user_id = ?";
                            if ($update_login_stmt = $db->prepare($update_login_sql)) {
                                $update_login_stmt->bind_param("i", $user_id);
                                $update_login_stmt->execute();
                                $update_login_stmt->close();
                            }

                            $redirect_url = $_SESSION['is_admin'] ? 'admin/index.php' : (isset($_SESSION['redirect_url']) ? $_SESSION['redirect_url'] : 'index.php');
                            unset($_SESSION['redirect_url']);
                            header('Location: ' . $redirect_url);
                            exit();
                        } else {
                            $error = "Username/Email atau password salah.";
                        }
                    }
                } else {
                    $error = "Username/Email atau password salah.";
                }
            } else {
                $error = "Oops! Terjadi kesalahan. Silakan coba lagi nanti.";
            }
            $stmt->close();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Glow Beauty - Login</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

<!-- Navbar -->
<nav class="navbar navbar-expand-lg navbar-light bg-light shadow-sm">
  <div class="container">
    <a class="navbar-brand fw-bold text-primary" href="index.php">Glow Beauty</a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse justify-content-end" id="navbarNav">
      <ul class="navbar-nav">
        <li class="nav-item"><a class="nav-link" href="index.php">Beranda</a></li>
        <?php if (is_user_loggedin()): ?>
          <li class="nav-item"><a class="nav-link" href="profile.php">Profil</a></li>
          <li class="nav-item"><a class="nav-link" href="logout.php">Logout</a></li>
        <?php else: ?>
          <li class="nav-item"><a class="nav-link" href="login.php">Login</a></li>
          <li class="nav-item"><a class="nav-link" href="register.php">Daftar</a></li>
        <?php endif; ?>
        <?php if (is_admin()): ?>
          <li class="nav-item"><a class="nav-link" href="admin/index.php">Admin</a></li>
        <?php endif; ?>
      </ul>
    </div>
  </div>
</nav>

<!-- Login Form -->
<main class="container my-5">
  <div class="row justify-content-center">
    <div class="col-md-6">
      <div class="card shadow-sm">
        <div class="card-header text-center bg-primary text-white">
          <h4>Login User atau Admin</h4>
        </div>
        <div class="card-body">
          <?php if (!empty($error)): ?>
            <div class="alert alert-danger"><?php echo $error; ?></div>
          <?php endif; ?>
          <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
            <div class="mb-3">
              <label for="username_email" class="form-label">Username atau Email</label>
              <input type="text" class="form-control" id="username_email" name="username_email" required value="<?php echo isset($username_email) ? htmlspecialchars($username_email) : ''; ?>">
            </div>
            <div class="mb-3">
              <label for="password" class="form-label">Password</label>
              <input type="password" class="form-control" id="password" name="password" required>
            </div>
            <div class="d-grid">
              <button type="submit" class="btn btn-primary">Login</button>
            </div>
          </form>
          <div class="mt-3 text-center">
            <p>Belum punya akun? <a href="register.php">Daftar di sini</a>.</p>
          </div>
        </div>
      </div>
    </div>
  </div>
</main>

<!-- Footer -->
<footer class="bg-light text-center py-3 border-top mt-5">
  <div class="container">
    <p class="mb-0">&copy; <?php echo date("Y"); ?> Glow Beauty. All rights reserved.</p>
  </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
