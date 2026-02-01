<?php
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../core/Database.php';
require_once __DIR__ . '/../core/Auth.php';

$email = trim($_POST['email'] ?? '');
$password = $_POST['password'] ?? '';

if ($email === '' || $password === '') {
  die('Plotëso email dhe password.');
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
  die('Email jo valid.');
}

$ok = Auth::login($email, $password);

if (!$ok) {
  die('Email ose password gabim.');
}

// redirect pas login
header('Location: ../../public/dashboard.php');
exit;
