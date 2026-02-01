<?php
require_once __DIR__ . '/../../app/config.php';
require_once __DIR__ . '/../../app/core/Database.php';
require_once __DIR__ . '/../../app/core/Auth.php';
require_once __DIR__ . '/../../app/core/Post.php';

Auth::requireAdmin();

$posts = Post::all();
?>
<!DOCTYPE html>
<html lang="sq">
<head>
  <meta charset="UTF-8">
  <title>Admin - Posts</title>
  <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>

<header class="header">
  <div class="logo">°C<span>empra</span></div>
  <nav class="nav-links">
    <a href="panel.php">Admin Panel</a>
    <a href="../posts.php">Posts (Public)</a>
    <a href="../logout.php">Dil</a>
  </nav>
</header>

<main class="page">
  <h1 class="page-title">Posts</h1>
  <p class="page-subtitle">CRUD: tekst + foto/PDF (ruhet created_by).</p>

  <a class="btn-primary" href="post_form.php">Shto Post</a>

  <div style="max-width:1000px;margin:16px auto 0;">
    <?php if (!$posts): ?>
      <p>Nuk ka post-e ende.</p>
    <?php else: ?>
      <?php foreach ($posts as $p): ?>
        <div style="border:1px solid #ddd;padding:12px;border-radius:10px;margin:10px 0;">
          <div style="font-weight:bold;"><?= htmlspecialchars($p['title']) ?></div>
          <div style="margin-top:6px;font-size:13px;opacity:0.8;">
            Created by: <?= htmlspecialchars($p['created_by_name']) ?> | <?= htmlspecialchars($p['created_at']) ?>
          </div>

          <div style="margin-top:10px; display:flex; gap:10px; flex-wrap:wrap;">
            <a class="btn-ghost" href="post_form.php?id=<?= (int)$p['id'] ?>">Edit</a>

            <form method="POST" action="/cempra/app/controllers/admin_post_delete.php" style="margin:0;" onsubmit="return confirm('Me fshi post-in?');">
              <input type="hidden" name="id" value="<?= (int)$p['id'] ?>">
              <button class="btn-ghost" type="submit">Delete</button>
            </form>

            <a class="btn-ghost" href="../post.php?id=<?= (int)$p['id'] ?>">Shiko</a>
          </div>
        </div>
      <?php endforeach; ?>
    <?php endif; ?>
  </div>
</main>

</body>
</html>
