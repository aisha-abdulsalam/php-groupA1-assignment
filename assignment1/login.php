<?php
require_once __DIR__ . '/init.php';

// If the user is already logged in, send them to their profile
if (!empty($_SESSION['user'])) {
    header('Location: profile.php');
    exit();
}

require_once __DIR__ . '/inc/header.php';
?>

<div class="card" style="max-width:480px;margin:20px auto">
  <h2>Sign In</h2>

  <form action="authenticate.php" method="POST" class="login-form">
    <label for="email">Email</label>
    <input type="email" id="email" name="email" required>

    <label for="password">Password</label>
    <input type="password" id="password" name="password" required>

    <button type="submit">Login</button>
  </form>

  <?php if (isset($_GET['error'])): ?>
    <p class="error"><?= htmlspecialchars($_GET['error']); ?></p>
  <?php endif; ?>
</div>

<?php require_once __DIR__ . '/inc/footer.php'; ?>