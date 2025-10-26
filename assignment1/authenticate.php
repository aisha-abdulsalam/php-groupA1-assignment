<?php
require_once __DIR__ . '/init.php';
if ($_SERVER['REQUEST_METHOD'] !== 'POST') { header('Location: login.php'); exit(); }
$email = trim($_POST['email'] ?? '');
$password = $_POST['password'] ?? '';
if ($email === '' || $password === '') { header('Location: login.php?error=' . urlencode('Enter email and password.')); exit(); }

$stmt = $conn->prepare("SELECT id, name, email, password FROM lecturers WHERE email = ?");
if (! $stmt) { header('Location: login.php?error=' . urlencode('Server error.')); exit(); }
$stmt->bind_param('s', $email);
$stmt->execute();
$result = $stmt->get_result();
if ($result && $result->num_rows === 1) {
    $lecturer = $result->fetch_assoc();
    if (password_verify($password, $lecturer['password'])) {
        unset($lecturer['password']);
        $_SESSION['user'] = $lecturer;
        header('Location: profile.php');
        exit();
    }
}
header('Location: login.php?error=' . urlencode('Invalid email or password.'));
exit();