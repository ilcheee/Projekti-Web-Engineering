<?php
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../core/Database.php';
require_once __DIR__ . '/../core/Auth.php';

$fullName = trim($_POST['full_name'] ?? '');
$email    = trim($_POST['email'] ?? '');
$password = $_POST['password'] ?? '';
$confirm  = $_POST['confirm_password'] ?? '';

if ($fullName === '' || $email === '' || $password === '' || $confirm === '') {
  die('Plotëso të gjitha fushat');
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
  die('Email jo valid');
}

if (strlen($password) < 8) {
  die('Password minimum 8 karaktere');
}

if ($password !== $confirm) {
  die('Password-at nuk përputhen');
}

$ok = Auth::register($fullName, $email, $password);

if (!$ok) {
  die('Ky email ekziston');
}

header('Location: ../../public/auth.php');
exit;
