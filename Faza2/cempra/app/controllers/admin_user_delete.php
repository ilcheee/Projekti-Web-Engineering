<?php
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../core/Database.php';
require_once __DIR__ . '/../core/Auth.php';
require_once __DIR__ . '/../core/User.php';

Auth::requireAdmin();

$id = (int)($_POST['id'] ?? 0);
$me = (int)($_SESSION['user']['id'] ?? 0);

if ($id <= 0) { die("bad request"); }
if ($id === $me) { die("nuk mund ta fshish veten"); }

User::deleteById($id);

header('Location: /cempra/public/admin/users.php');
exit;
