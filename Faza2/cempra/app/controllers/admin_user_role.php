<?php
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../core/Database.php';
require_once __DIR__ . '/../core/Auth.php';
require_once __DIR__ . '/../core/User.php';

Auth::requireAdmin();

$id = (int)($_POST['id'] ?? 0);
$role = trim($_POST['role'] ?? 'user');

if ($id <= 0) { die("bad request"); }

User::setRole($id, $role);

header('Location: /cempra/public/admin/users.php');
exit;
