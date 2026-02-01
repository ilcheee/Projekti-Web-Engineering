<?php
require_once __DIR__ . '/../app/config.php';
require_once __DIR__ . '/../app/core/Database.php';
require_once __DIR__ . '/../app/core/Post.php';

$id = (int)($_GET['id'] ?? 0);
$post = $id > 0 ? Post::find($id) : null;

if (!$post) {
  http_response_code(404);
  echo "Post not found";
  exit;
}
?>
<!DOCTYPE html>
<html lang="sq">
<head>
  <meta charset="UTF-8">
  <title>°Cempra - <?= htmlspecialchars($post['title']) ?></title>
  <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>

<header class="header">
  <div class="logo">°C<span>empra</span></div>
  <nav class="nav-links">
    <a href="posts.php">Back</a>
    <a href="index.php">Ballina</a>
    <a href="about.php">Rreth nesh</a>
  </nav>
</header>

<main class="page" style="max-width:1000px;margin:0 auto;">
  <h1 class="page-title"><?= htmlspecialchars($post['title']) ?></h1>
  <p class="page-subtitle">Nga <?= htmlspecialchars($post['created_by_name']) ?> · <?= htmlspecialchars($post['created_at']) ?></p>

  <?php if (!empty($post['image_path'])): ?>
    <img src="<?= htmlspecialchars($post['image_path']) ?>" alt="image" style="width:100%;max-height:420px;object-fit:cover;border-radius:14px;margin:12px 0;">
  <?php endif; ?>

  <div style="white-space:pre-wrap;line-height:1.6;">
    <?= htmlspecialchars($post['body']) ?>
  </div>

  <?php if (!empty($post['pdf_path'])): ?>
    <div style="margin-top:16px;">
      <a class="btn-primary" href="<?= htmlspecialchars($post['pdf_path']) ?>" target="_blank" rel="noopener">Hap PDF</a>
    </div>
  <?php endif; ?>
</main>

</body>
</html>
