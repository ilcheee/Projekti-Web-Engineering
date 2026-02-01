<?php
require_once __DIR__ . '/../../app/config.php';
require_once __DIR__ . '/../../app/core/Database.php';
require_once __DIR__ . '/../../app/core/Auth.php';
require_once __DIR__ . '/../../app/core/Post.php';

Auth::requireAdmin();

$id = (int)($_GET['id'] ?? 0);
$post = $id > 0 ? Post::find($id) : null;

$title = $post['title'] ?? '';
$body = $post['body'] ?? '';
$oldImage = $post['image_path'] ?? '';
$oldPdf = $post['pdf_path'] ?? '';
?>
<!DOCTYPE html>
<html lang="sq">
<head>
  <meta charset="UTF-8">
  <title>Admin - Post Form</title>
  <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>

<header class="header">
  <div class="logo">°C<span>empra</span></div>
  <nav class="nav-links">
    <a href="posts.php">Back</a>
    <a href="panel.php">Admin Panel</a>
    <a href="../logout.php">Dil</a>
  </nav>
</header>

<main class="page">
  <h1 class="page-title"><?= $id > 0 ? 'Edit Post' : 'Shto Post' ?></h1>

  <form method="POST" action="/cempra/app/controllers/admin_post_save.php" enctype="multipart/form-data" style="max-width:900px;margin:0 auto;">
    <input type="hidden" name="id" value="<?= $id ?>">
    <input type="hidden" name="old_image" value="<?= htmlspecialchars($oldImage) ?>">
    <input type="hidden" name="old_pdf" value="<?= htmlspecialchars($oldPdf) ?>">

    <div class="form-group">
      <input class="input" name="title" type="text" placeholder="Titulli" value="<?= htmlspecialchars($title) ?>" required>
    </div>

    <div class="form-group">
      <textarea class="input" name="body" rows="7" placeholder="Teksti..." required><?= htmlspecialchars($body) ?></textarea>
    </div>

    <div class="form-group">
      <div style="font-size:13px;opacity:0.8;margin-bottom:6px;">Foto (jpg/png/webp/gif) – opsionale</div>
      <input class="input" name="image" type="file" accept=".jpg,.jpeg,.png,.webp,.gif">
      <?php if ($oldImage): ?>
        <div style="margin-top:6px;font-size:13px;">Aktuale: <?= htmlspecialchars($oldImage) ?></div>
      <?php endif; ?>
    </div>

    <div class="form-group">
      <div style="font-size:13px;opacity:0.8;margin-bottom:6px;">PDF – opsionale</div>
      <input class="input" name="pdf" type="file" accept=".pdf">
      <?php if ($oldPdf): ?>
        <div style="margin-top:6px;font-size:13px;">Aktuale: <?= htmlspecialchars($oldPdf) ?></div>
      <?php endif; ?>
    </div>

    <button class="btn-primary" type="submit">Ruaj</button>
  </form>

  <p class="page-subtitle" style="max-width:900px;margin:10px auto 0;">
    Shënim: Duhet të ket së paku 1 (foto ose PDF).
  </p>
</main>

</body>
</html>
