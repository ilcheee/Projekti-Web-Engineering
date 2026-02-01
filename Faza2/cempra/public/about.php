<?php
require_once __DIR__ . '/../app/config.php';
require_once __DIR__ . '/../app/core/Database.php';
require_once __DIR__ . '/../app/core/Auth.php';
require_once __DIR__ . '/../app/core/Page.php';

$page = Page::get('about');
$aboutContent = $page['content'] ?? '';

$isLogged = isset($_SESSION['user']);
$isAdmin = $isLogged && (($_SESSION['user']['role'] ?? '') === 'admin');
?>
<!DOCTYPE html>
<html lang="sq">
<head>
  <meta charset="UTF-8">
  <title>°Cempra - Rreth nesh</title>
  <link rel="stylesheet" href="assets/css/style.css">
  <link rel="stylesheet" href="assets/css/AboutStyle.css">
  <script src="assets/js/about.js" defer></script>
</head>

<body>
  <header class="header">
    <div class="logo">°C<span>empra</span></div>
    <nav class="nav-links">
      <?php if ($isAdmin): ?>
        <a href="admin/panel.php">Admin Panel</a>
      <?php endif; ?>

      <a href="index.php">Ballina</a>
      <a href="favorites.php">Të preferuarat</a>
      <a href="dashboard.php">Paneli</a>
      <a href="posts.php">Posts</a>
      <a href="about.php">Rreth nesh</a>

      <?php if ($isLogged): ?>
        <a href="logout.php">Dil</a>
      <?php else: ?>
        <a href="auth.php">Kyçja</a>
      <?php endif; ?>
    </nav>
  </header>

  <main class="page">
    <?php if (isset($_GET['sent'])): ?>
      <p style="text-align:center; margin-bottom:12px;">Mesazhi u dërgua me sukses ✅</p>
    <?php endif; ?>

    <h1 class="page-title">Rreth °Cempra</h1>

    <?php if (trim($aboutContent) !== ''): ?>
      <p class="page-subtitle">
        <?= nl2br(htmlspecialchars($aboutContent)) ?>
      </p>
    <?php else: ?>
      <p class="page-subtitle">
        °Cempra është një faqe për shfaqjen e motit në kohë reale duke përdorur API të jashtme.
      </p>
    <?php endif; ?>

    <div class="two-column">
      <div class="info-card">
        <h2>Qëllimi i projektit</h2>
        <p style="font-size:13px; color:var(--accent-soft3); margin-top:8px;">
          Ky projekt është zhvilluar për lëndën <strong>Full-Stack Development</strong>.
          Qëllimi është të ndërtojmë një aplikacion modern për motin, me mundësi kërkimi,
          favorites, dashboard personal, kyçje të përdoruesve dhe integrim me API.
        </p>

        <h3 style="margin-top:18px; font-size:15px;">Teknologjitë</h3>
        <ul class="list-tech">
          <li>Frontend: HTML, CSS, JavaScript</li>
          <li>Backend: PHP (OOP)</li>
          <li>Database: MySQL</li>
          <li>API: Open-Meteo (Geocoding + Forecast)</li>
        </ul>

        <h3 style="margin-top:18px; font-size:15px;">Ekipi</h3>
        <p style="font-size:13px; color:var(--accent-soft3); margin-top:6px;">
          Ilioni – Frontend & Backend <br>
          Diella – Frontend & Backend
        </p>
      </div>

      <div class="info-card">
        <h2>Na kontaktoni</h2>
        <p class="form-note">
          Të dhënat ruhen në databazë dhe admini mund t’i lexojë nga Admin Panel.
        </p>

        <form id="contactForm" method="POST" action="../app/controllers/contact_submit.php">
          <div class="form-group" style="margin-top:12px;">
            <input class="input" id="contactName" name="name" type="text" placeholder="Emri juaj" required>
            <div class="error-text" id="errName"></div>
          </div>

          <div class="form-group">
            <input class="input" id="contactEmail" name="email" type="email" placeholder="Email adresa juaj" required>
            <div class="error-text" id="errEmail"></div>
          </div>

          <div class="form-group">
            <textarea class="input" id="contactMsg" name="message" placeholder="Mesazhi juaj" required></textarea>
            <div class="error-text" id="errMsg"></div>
          </div>

          <button class="btn-primary" type="submit">Dërgo mesazhin</button>

          <div class="success-text" id="successMsg" aria-live="polite"></div>
        </form>
      </div>
    </div>
  </main>

  <footer class="footer">
    Faqja "Rreth nesh" & Kontakti
  </footer>
</body>
</html>
