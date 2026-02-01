<?php
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../core/Database.php';
require_once __DIR__ . '/../core/Auth.php';
require_once __DIR__ . '/../core/Page.php';

Auth::requireAdmin();

$slug = trim($_POST['slug'] ?? '');
$content = trim($_POST['content'] ?? '');
$userId = (int)($_SESSION['user']['id'] ?? 0);

if ($slug === '' || $content === '' || $userId <= 0) {
  die("bad request");
}

Page::upsert($slug, $content, $userId);

header('Location: /cempra/public/admin/pages.php');
exit;
