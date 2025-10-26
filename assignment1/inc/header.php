<?php
// Assumes init.php (which starts the session and loads $conn) is already included by the page
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>GradeSys</title>
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <link rel="stylesheet" href="css/style.css">
</head>
<body>
<header class="site-header">
  <div class="container header-inner">
    <a class="brand" href="index.php">
      <img src="asset/logo.png" alt="GradeSys logo" class="logo">
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