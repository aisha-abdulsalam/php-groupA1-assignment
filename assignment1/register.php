<?php
require_once __DIR__ . '/init.php';
if (!empty($_SESSION['user'])) { header('Location: profile.php'); exit(); }
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password_raw = $_POST['password'] ?? '';
    if ($name === '' || $email === '' || $password_raw === '') {
        $error = 'All fields required.';
    } else {
        $hash = password_hash($password_raw, PASSWORD_BCRYPT);
        $check = $conn->prepare("SELECT id FROM lecturers WHERE email = ?");
        $check->bind_param("s", $email);
        $check->execute();
        if ($check->get_result()->num_rows > 0) {
            $error = 'Email already registered.';
        } else {
            $stmt = $conn->prepare("INSERT INTO lecturers (name,email,password) VALUES (?,?,?)");
            $stmt->bind_param("sss", $name, $email, $hash);
            if ($stmt->execute()) {
                header('Location: login.php');
                exit();
            } else {
                $error = 'DB error: ' . $stmt->error;
            }
        }
    }
}
?>
<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <title>Register — GradeSys</title>
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <link rel="stylesheet" href="css/style.css">
</head>
<body>
<header class="site-header">
  <div class="container header-inner">
    <a class="brand" href="index.php">
      <img src="asset/logo.png" alt="logo" class="logo">
      <div class="title">GradeSys</div>
    </a>
    <nav class="nav">
      <a href="index.php">Home</a>
      <a href="login.php">Login</a>
    </nav>
  </div>
</header>

<main class="container">
  <div class="auth-card card">
    <h2>Register as Lecturer</h2>
    <?php if ($error): ?><p class="text-muted" style="color:var(--brand);"><?php echo htmlspecialchars($error); ?></p><?php endif; ?>
    <form method="post" novalidate>
      <div class="row">
        <label>Name</label>
        <input type="text" name="name" required>
      </div>
      <div class="row">
        <label>Email</label>
        <input type="email" name="email" required>
      </div>
      <div class="row">
        <label>Password</label>
        <input type="password" name="password" required>
      </div>
      <div class="form-actions" style="display:flex;gap:10px;justify-content:flex-end">
        <a class="btn btn-outline" href="login.php">Already registered?</a>
        <button class="btn btn-primary" type="submit">Register</button>
      </div>
    </form>
  </div>
</main>

<footer class="site-footer">
  <div class="container footer-inner">
    <div class="footer-card small text-muted">&copy; <?php echo date('Y'); ?> GradeSys</div>
  </div>
</footer>
</body>
</html>