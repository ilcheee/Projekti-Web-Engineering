<?php
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../core/Database.php';
require_once __DIR__ . '/../core/Auth.php';
require_once __DIR__ . '/../core/Post.php';

Auth::requireAdmin();

$id = (int)($_POST['id'] ?? 0);
if ($id <= 0) die("bad request");

Post::delete($id);

header('Location: /cempra/public/admin/posts.php');
exit;
