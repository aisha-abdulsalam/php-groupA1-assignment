<?php
require_once _DIR_ . '/init.php';
if (empty($_SESSION['user'])) { header('Location: login.php'); exit(); }
$lecturer_id = $_SESSION['user']['id'];

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) { die('Invalid ID'); }
$id = (int)$_GET['id'];

$stmt = $conn->prepare("SELECT g.id, g.score, g.course, s.name AS student_name FROM grades g JOIN students s ON g.student_id = s.id WHERE g.id = ? AND g.lecturer_id = ?");
$stmt->bind_param('ii', $id, $lecturer_id);
$stmt->execute();
$res = $stmt->get_result();
if (!$res || $res->num_rows === 0) { die('Not found or no permission'); }
$grade = $res->fetch_assoc();

$error = $success = '';
function calculate_grade($s) {
    if ($s >= 70) return 'A';
    if ($s >= 60) return 'B';
    if ($s >= 50) return 'C';
    if ($s >= 45) return 'D';
    return 'F';
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $course = trim($_POST['course'] ?? '');
    $score = (int)($_POST['score'] ?? -1);
    if ($course === '' || $score < 0 || $score > 100) {
        $error = 'Invalid input';
    } else {
        $letter = calculate_grade($score);
        $u = $conn->prepare("UPDATE grades SET course = ?, score = ?, grade_letter = ? WHERE id = ? AND lecturer_id = ?");
        $u->bind_param('siiii', $course, $score, $letter, $id, $lecturer_id);
        if ($u->execute()) {
            $success = 'Updated';
            $grade['course'] = $course; $grade['score'] = $score;
        } else { $error = 'DB error: '.$u->error; }
    }
}
?>
<!doctype html>
<html><head><meta charset="utf-8"><title>Edit Grade</title>
<meta name="viewport" content="width=device-width,initial-scale=1">
<link rel="stylesheet" href="css/style.css">
</head>
<body>
<header class="site-header"><div class="container header-inner"><a class="brand" href="index.php"><img src="asset/logo.png" alt="logo" class="logo"><div class="title">GradeSys</div></a><nav class="nav"><a href="profile.php">Dashboard</a><a href="logout.php" class="primary">Logout</a></nav></div></header>

<main class="container">
  <div class="card card-centered">
    <h2>Edit Grade for <?php echo htmlspecialchars($grade['student_name']); ?></h2>
    <?php if ($error): ?><p class="text-muted" style="color:crimson"><?php echo htmlspecialchars($error); ?></p><?php endif; ?>
    <?php if ($success): ?><p style="color:green"><?php echo htmlspecialchars($success); ?></p><?php endif; ?>
    <form method="post" action="edit_grade.php?id=<?php echo $id; ?>">
      <div class="row"><label>Course</label><input type="text" name="course" value="<?php echo htmlspecialchars($grade['course']); ?>" required></div>
      <div class="row"><label>Score</label><input type="number" name="score" min="0" max="100" value="<?php echo htmlspecialchars($grade['score']); ?>" required></div>
      <div style="display:flex;gap:10px;justify-content:flex-end">
        <a class="btn btn-outline" href="profile.php">Back</a>
        <button class="btn btn-primary" type="submit">Update</button>
      </div>
    </form>
  </div>
</main>

<footer class="site-footer"><div class="container footer-inner"><div class="footer-card small text-muted">&copy; <?php echo date('Y'); ?> GradeSys</div></div></footer>
</body></html>