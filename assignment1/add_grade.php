<?php
require_once __DIR__ . '/init.php';
if (empty($_SESSION['user'])) { header('Location: login.php'); exit(); }
$lecturer_id = $_SESSION['user']['id'];
$error = $success = '';

function calculate_grade($s) {
    if ($s >= 70) return 'A';
    if ($s >= 60) return 'B';
    if ($s >= 50) return 'C';
    if ($s >= 45) return 'D';
    return 'F';
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $student_name = trim($_POST['student_name'] ?? '');
    $course = trim($_POST['course'] ?? '');
    $score = (int)($_POST['score'] ?? -1);

    if ($student_name === '' || $course === '' || $score < 0 || $score > 100) {
        $error = 'Please enter valid values.';
    } else {
        $stmt = $conn->prepare("SELECT id FROM students WHERE name = ?");
        $stmt->bind_param('s', $student_name);
        $stmt->execute();
        $res = $stmt->get_result();
        if ($res && $res->num_rows > 0) {
            $student_id = $res->fetch_assoc()['id'];
        } else {
            $insert = $conn->prepare("INSERT INTO students (name) VALUES (?)");
            $insert->bind_param('s', $student_name);
            $insert->execute();
            $student_id = $insert->insert_id;
        }
        $letter = calculate_grade($score);
        $ins = $conn->prepare("INSERT INTO grades (student_id, course, score, grade_letter, lecturer_id) VALUES (?, ?, ?, ?, ?)");
        $ins->bind_param('isisi', $student_id, $course, $score, $letter, $lecturer_id);
        if ($ins->execute()) {
            $success = "Added grade ($letter) for " . htmlspecialchars($student_name);
        } else {
            $error = "DB error: " . $ins->error;
        }
    }
}
?>
<!doctype html>
<html><head><meta charset="utf-8"><title>Add Grade</title>
<meta name="viewport" content="width=device-width,initial-scale=1">
<link rel="stylesheet" href="css/style.css">
</head>
<body>
<header class="site-header">
  <div class="container header-inner">
    <a class="brand" href="index.php"><img src="asset/logo.png.jpeg" alt="logo" class="logo"><div class="title">GradeSys</div></a>
    <nav class="nav"><a href="profile.php">Dashboard</a><a href="logout.php" class="primary">Logout</a></nav>
  </div>
</header>

<main class="container">
  <div class="card card-centered">
    <h2>Add Grade</h2>
    <?php if ($error): ?><p class="text-muted" style="color:crimson"><?php echo htmlspecialchars($error); ?></p><?php endif; ?>
    <?php if ($success): ?><p style="color:green"><?php echo $success; ?></p><?php endif; ?>
    <form method="post" action="add_grade.php">
      <div class="row"><label>Student Name</label><input type="text" name="student_name" required></div>
      <div class="row"><label>Course</label><input type="text" name="course" required></div>
      <div class="row"><label>Score (0-100)</label><input type="number" name="score" min="0" max="100" required></div>
      <div style="display:flex;gap:10px;justify-content:flex-end">
        <a class="btn btn-outline" href="profile.php">Back</a>
        <button class="btn btn-primary" type="submit">Add Grade</button>
      </div>
    </form>
  </div>
</main>

<footer class="site-footer">
  <div class="container footer-inner"><div class="footer-card small text-muted">&copy; <?php echo date('Y'); ?> GradeSys</div></div>
</footer>
</body></html>