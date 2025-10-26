<?php
require_once __DIR__ . '/init.php';
if (empty($_SESSION['user'])) { header('Location: login.php'); exit(); }
$lecturer_id = $_SESSION['user']['id'];
$searchQ = trim($_GET['search'] ?? '');
if ($searchQ !== '') {
    $like = "%$searchQ%";
    $stmt = $conn->prepare("
        SELECT g.id, s.name AS student_name, g.course, g.score, g.grade_letter, g.created_at
        FROM grades g
        JOIN students s ON g.student_id = s.id
        WHERE g.lecturer_id = ? AND (s.name LIKE ? OR g.course LIKE ?)
        ORDER BY g.created_at DESC
    ");
    $stmt->bind_param('iss', $lecturer_id, $like, $like);
} else {
    $stmt = $conn->prepare("
        SELECT g.id, s.name AS student_name, g.course, g.score, g.grade_letter, g.created_at
        FROM grades g
        JOIN students s ON g.student_id = s.id
        WHERE g.lecturer_id = ?
        ORDER BY g.created_at DESC
    ");
    $stmt->bind_param('i', $lecturer_id);
}
$stmt->execute();
$rows = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
?>
<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <title>Dashboard — GradeSys</title>
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
      <a href="add_grade.php">Add Grade</a>
      <a href="logout.php" class="primary">Logout</a>
    </nav>
  </div>
</header>

<main class="container">
  <div class="top-actions">
    <h2>Your Grades</h2>
    <div class="right">
      <form method="get" action="profile.php" class="search-form">
        <input type="text" name="search" placeholder="search student or course" value="<?php echo htmlspecialchars($searchQ); ?>">
        <button class="btn btn-outline" type="submit">Search</button>
      </form>
      <a class="btn btn-primary" href="add_grade.php">Add Grade</a>
    </div>
  </div>

  <div class="card table-wrap">
    <table>
      <thead>
        <tr><th>Student</th><th>Course</th><th>Score</th><th>Grade</th><th>Added</th><th>Actions</th></tr>
      </thead>
      <tbody>
        <?php if (empty($rows)): ?>
          <tr><td colspan="6" class="text-muted">No grades found.</td></tr>
        <?php else: foreach ($rows as $r): ?>
          <tr>
            <td><?php echo htmlspecialchars($r['student_name']); ?></td>
            <td><?php echo htmlspecialchars($r['course']); ?></td>
            <td><?php echo htmlspecialchars($r['score']); ?></td>
            <td><?php echo htmlspecialchars($r['grade_letter']); ?></td>
            <td class="small text-muted"><?php echo htmlspecialchars($r['created_at']); ?></td>
            <td class="actions">
              <a href="edit_grade.php?id=<?php echo $r['id']; ?>">Edit</a> |
              <a href="delete_grade.php?id=<?php echo $r['id']; ?>" onclick="return confirm('Delete?')">Delete</a>
            </td>
          </tr>
        <?php endforeach; endif; ?>
      </tbody>
    </table>
  </div>
</main>

<footer class="site-footer">
  <div class="container footer-inner">
    <div class="footer-card small text-muted">&copy; <?php echo date('Y'); ?> GradeSys</div>
  </div>
</footer>
</body>
</html>