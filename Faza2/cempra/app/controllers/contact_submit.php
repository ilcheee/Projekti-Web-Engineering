<?php
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../core/Database.php';

$name = trim($_POST['name'] ?? '');
$email = trim($_POST['email'] ?? '');
$message = trim($_POST['message'] ?? '');

if ($name === '' || $email === '' || $message === '') {
  die('Plotëso të gjitha fushat.');
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
  die('Email jo valid.');
}

$pdo = Database::pdo();
$stmt = $pdo->prepare("INSERT INTO contact_messages (name, email, message) VALUES (?, ?, ?)");
$stmt->execute([$name, $email, $message]);

header('Location: /cempra/public/about.php?sent=1');
exit;
