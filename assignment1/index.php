<?php
require_once __DIR__ . '/init.php';
?>
<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <title>GradeSys — Home</title>
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <link rel="stylesheet" href="css/style.css">
</head>
<body>
<header class="site-header">
  <div class="container header-inner">
    <a class="brand" href="index.php">
      <img src="asset/logo.png.jpeg" alt="logo" class="logo">
      <div class="title">GradeSys</div>
    </a>
    <nav class="nav">
      <?php if (!empty($_SESSION['user'])): ?>
        <a href="profile.php">Dashboard</a>
        <a href="add_grade.php">Add Grade</a>
        <a href="logout.php" class="primary">Logout</a>
      <?php else: ?>
        <a href="index.php">Home</a>
        <a href="register.php">Register</a>
        <a href="login.php" class="primary">Sign in</a>
      <?php endif; ?>
    </nav>
  </div>
</header>

<main class="container">
  <section class="hero">
    <div>
      <h1>Welcome to GradeSys</h1>
      <p class="text-muted">Manage student grades quickly — record, search, edit and track who entered each grade.</p>
      <div class="cta">
        <a class="btn btn-primary" href="register.php">Sign up (Lecturer)</a>
        <a class="btn btn-outline" href="login.php">Sign in</a>
      </div>
    </div>

    <div class="card">
      <img src="asset/logo.png" alt="logo" class="logo" style="display:block;margin:12px auto;max-width:220px;">
      <p class="small text-muted" style="text-align:center">Your private grading dashboard.</p>
    </div>
  </section>
</main>

<footer class="site-footer">
  <div class="container footer-inner">
    <div class="footer-card small text-muted">&copy; <?php echo date('Y'); ?> GradeSys</div>
  </div>
</footer>
</body>
</html>