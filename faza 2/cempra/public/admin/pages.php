<?php
require_once __DIR__ . '/../../app/config.php';
require_once __DIR__ . '/../../app/core/Database.php';
require_once __DIR__ . '/../../app/core/Auth.php';
require_once __DIR__ . '/../../app/core/Page.php';

Auth::requireAdmin();

$home = Page::get('home');
$about = Page::get('about');
?>
<!DOCTYPE html>
<html lang="sq">
<head>
  <meta charset="UTF-8">
  <title>Admin - Pages</title>
  <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>

<header class="header">
  <div class="logo">°C<span>empra</span></div>
  <nav class="nav-links">
    <a href="panel.php">Admin Panel</a>
    <a href="../index.php">Home</a>
    <a href="../about.php">About</a>
    <a href="../logout.php">Dil</a>
  </nav>
</header>

<main class="page">
  <h1 class="page-title">Pages</h1>
  <p class="page-subtitle">Ndrysho Home/About content nga databaza.</p>

  <div style="max-width:1000px;margin:0 auto;">
    <h2 style="margin:18px 0 8px;">Home</h2>
    <form method="POST" action="/cempra/app/controllers/admin_page_save.php">
      <input type="hidden" name="slug" value="home">
      <textarea class="input" name="content" rows="7" required><?= htmlspecialchars($home['content'] ?? 'Mirësevini në °Cempra!') ?></textarea>
      <button class="btn-primary" type="submit" style="margin-top:10px;">Ruaj Home</button>
    </form>

    <h2 style="margin:22px 0 8px;">About</h2>
    <form method="POST" action="/cempra/app/controllers/admin_page_save.php">
      <input type="hidden" name="slug" value="about">
      <textarea class="input" name="content" rows="7" required><?= htmlspecialchars($about['content'] ?? 'Rreth nesh...') ?></textarea>
      <button class="btn-primary" type="submit" style="margin-top:10px;">Ruaj About</button>
    </form>
  </div>
</main>

</body>
</html>
