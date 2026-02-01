<?php
require_once __DIR__ . '/../app/config.php';
?>
<!DOCTYPE html>
<html lang="sq">
<head>
  <meta charset="UTF-8">
  <title>°Cempra - Kyçja / Regjistrimi</title>
  <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
  <header class="header">
    <div class="logo">°C<span>empra</span></div>
    <nav class="nav-links">
      <a href="index.php">Ballina</a>
      <a href="favorites.php">Të preferuarat</a>
      <a href="dashboard.php">Paneli</a>
      <a href="about.php">Rreth nesh</a>
      <?php if (isset($_SESSION['user'])): ?>
        <a href="logout.php">Dil</a>
      <?php else: ?>
        <a href="auth.php">Kyçja</a>
      <?php endif; ?>
    </nav>
  </header>

  <main class="page">
    <h1 class="page-title">Qasja në llogari</h1>
    <p class="page-subtitle">
      Në këtë faqe do të realizohet kyçja dhe regjistrimi i përdoruesve me validim dhe backend.
    </p>

    <div class="two-column">

      <div class="auth-card">
        <h2>Kyçja</h2>

        <form method="POST" action="../app/controllers/auth_login.php">
          <div class="form-group">
            <input class="input" name="email" type="email" placeholder="Email adresa" required>
          </div>

          <div class="form-group">
            <input class="input" name="password" type="password" placeholder="Fjalëkalimi" required>
          </div>

          <button class="btn-primary" type="submit">Kyçu</button>

          <p class="form-footer">
            Lorem ipsum dolor, sit amet consectetur adipisicing elit. Natus, nesciunt?
          </p>
        </form>
      </div>

      <div class="auth-card">
        <h2>Regjistrimi</h2>

        <form method="POST" action="../app/controllers/auth_register.php">
          <div class="form-group">
            <input class="input" name="full_name" type="text" placeholder="Emri dhe mbiemri" required>
          </div>

          <div class="form-group">
            <input class="input" name="email" type="email" placeholder="Email adresa" required>
          </div>

          <div class="form-group">
            <input class="input" name="password" type="password" placeholder="Fjalëkalimi" required>
          </div>

          <div class="form-group">
            <input class="input" name="confirm_password" type="password" placeholder="Përsërit fjalëkalimin" required>
          </div>

          <button class="btn-primary" type="submit">Krijo llogari</button>

          <p class="form-footer">
            Më vonë, këtu do të lidhet databaza për ruajtjen e përdoruesve.
          </p>
        </form>
      </div>

    </div>
  </main>

  <footer class="footer">
    Formularët e kyçjes dhe regjistrimit
  </footer>
</body>
</html>
