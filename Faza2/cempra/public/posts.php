<?php
require_once __DIR__ . '/../app/config.php';
require_once __DIR__ . '/../app/core/Database.php';
require_once __DIR__ . '/../app/core/Post.php';

$posts = Post::all();
?>
<!DOCTYPE html>
<html lang="sq">
<head>
  <meta charset="UTF-8">
  <title>°Cempra - Posts</title>
  <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>

<header class="header">
  <div class="logo">°C<span>empra</span></div>
  <nav class="nav-links">
    <a href="index.php">Ballina</a>
    <a href="posts.php">Posts</a>
    <a href="about.php">Rreth nesh</a>
    <?php if (isset($_SESSION['user'])): ?>
      <a href="dashboard.php">Paneli</a>
      <a href="logout.php">Dil</a>
    <?php else: ?>
      <a href="auth.php">Kyçja</a>
    <?php endif; ?>
  </nav>
</header>

<main class="page">
  <h1 class="page-title">Posts</h1>
  <p class="page-subtitle">Përmbajtje dinamike nga databaza.</p>

  <div style="max-width:1000px;margin:0 auto;">
    <?php if (!$posts): ?>
      <p>Nuk ka post-e ende.</p>
    <?php else: ?>
      <?php foreach ($posts as $p): ?>
        <div style="border:1px solid #ddd;padding:12px;border-radius:10px;margin:10px 0;">
          <div style="font-weight:bold;"><?= htmlspecialchars($p['title']) ?></div>
          <div style="margin-top:6px;font-size:13px;opacity:0.75;">
            <?= htmlspecialchars($p['created_at']) ?> · <?= htmlspecialchars($p['created_by_name']) ?>
          </div>
          <div style="margin-top:10px;">
            <a class="btn-ghost" href="post.php?id=<?= (int)$p['id'] ?>">Lexo</a>
          </div>
        </div>
      <?php endforeach; ?>
    <?php endif; ?>
  </div>
</main>

</body>
</html>
