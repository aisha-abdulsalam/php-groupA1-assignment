<?php
include 'db_connect.php';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $name = trim($_POST['name']);
    $course = trim($_POST['course']);
    $score = intval($_POST['score']);

    if (!empty($name) && !empty($course)) {
        $stmt = $conn->prepare("INSERT INTO grades (name, course, score) VALUES (?, ?, ?)");
        $stmt->bind_param("ssi", $name, $course, $score);
        $stmt->execute();
        $stmt->close();
    }
}

$result = $conn->query("SELECT * FROM grades ORDER BY id DESC");

function gradeLetter($score) {
    if ($score >= 70) return "A";
    elseif ($score >= 60) return "B";
    elseif ($score >= 50) return "C";
    elseif ($score >= 45) return "D";
    elseif ($score >= 40) return "E";
    else return "F";
}

$stats_query = "SELECT COUNT(*) AS total, AVG(score) AS avg_score, MIN(score) AS min_score, MAX(score) AS max_score FROM grades";
$stats = $conn->query($stats_query)->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>View Student Grades</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; background: #f7f7f7; }
        h1, h2 { color: #333; }
        table { border-collapse: collapse; width: 100%; background: #fff; }
        th, td { border: 1px solid #ccc; padding: 8px; text-align: center; }
        th { background-color: #f2f2f2; }
        .stats { background: #fff; padding: 10px; margin-top: 20px; width: 50%; border-radius: 8px; }
        a { text-decoration: none; color: blue; }
    </style>
</head>
<body>

<h1>All Recorded Student Grades</h1>

<a href="index.html">← Go back to Add Grade</a>

<table>
    <tr>
        <th>ID</th>
        <th>Name</th>
        <th>Course</th>
        <th>Score</th>
        <th>Grade</th>
    </tr>

    <?php if ($result->num_rows > 0): ?>
        <?php while ($row = $result->fetch_assoc()): ?>
            <tr>
                <td><?= $row['id'] ?></td>
                <td><?= htmlspecialchars($row['name']) ?></td>
                <td><?= htmlspecialchars($row['course']) ?></td>
                <td><?= $row['score'] ?></td>
                <td><?= gradeLetter($row['score']) ?></td>
            </tr>
        <?php endwhile; ?>
    <?php else: ?>
        <tr><td colspan="5">No grades found</td></tr>
    <?php endif; ?>
</table>

<div class="stats">
    <h2>Statistics</h2>
    <p><strong>Total Students:</strong> <?= $stats['total'] ?></p>
    <p><strong>Average Score:</strong> <?= round($stats['avg_score'], 2) ?></p>
    <p><strong>Highest Score:</strong> <?= $stats['max_score'] ?></p>
    <p><strong>Lowest Score:</strong> <?= $stats['min_score'] ?></p>
</div>

</body>
</html>
