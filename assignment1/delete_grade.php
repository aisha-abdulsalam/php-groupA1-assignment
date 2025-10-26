<?php
require_once __DIR__ . '/init.php';
if (empty($_SESSION['user'])) { header('Location: login.php'); exit(); }
$lecturer_id = $_SESSION['user']['id'];

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) { header('Location: profile.php'); exit(); }
$id = (int)$_GET['id'];

$stmt = $conn->prepare("DELETE FROM grades WHERE id = ? AND lecturer_id = ?");
$stmt->bind_param('ii', $id, $lecturer_id);
$stmt->execute();

header('Location: profile.php');
exit();