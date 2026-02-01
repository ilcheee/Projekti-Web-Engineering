<?php
require_once __DIR__ . '/../app/config.php';

$cityRaw = trim($_GET['city'] ?? '');
if ($cityRaw === '') {
  header('Location: favorites.php');
  exit;
}

$cityOnly = trim(explode(',', $cityRaw)[0]);
?>
<!DOCTYPE html>
<html lang="sq">
<head>
  <meta charset="UTF-8">
  <title>°Cempra - Detajet e qytetit</title>
  <link rel="stylesheet" href="assets/css/style.css">
  <script>
    window.CEMPRA_CITY = <?= json_encode($cityOnly, JSON_UNESCAPED_UNICODE) ?>;
  </script>
  <script src="assets/js/city.js" defer></script>
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
    <h1 class="page-title">Detajet e qytetit</h1>
    <p class="page-subtitle">Qyteti: <strong><?= htmlspecialchars($cityRaw) ?></strong></p>

    <section class="weather-card" id="weatherCard">
      <div class="wx-anim" id="wxAnim" aria-hidden="true">
        <div class="wx-sun"></div>
        <div class="wx-cloud wx-cloud-1"></div>
        <div class="wx-cloud wx-cloud-2"></div>
        <div class="wx-rain"></div>
        <div class="wx-snow"></div>
      </div>

      <div class="weather-main-row">
        <div>
          <div class="weather-city" id="wCity">Duke ngarkuar...</div>
          <div class="weather-desc" id="wDesc">—</div>
        </div>
        <div class="weather-temp" id="wTemp">--°</div>
      </div>

      <div class="weather-extra">
        <div id="wFeels">Ndjehet si: --°</div>
        <div id="wWind">Era: -- km/h</div>
        <div id="wHum">Lagështia: --%</div>
      </div>
    </section>

    <section style="margin-top:22px;">
      <h2 class="page-title" style="font-size:20px;">Parashikimi 3-ditor</h2>
      <div class="card-grid" id="forecastGrid"></div>
    </section>

    <section style="margin-top:22px;">
      <button class="btn-ghost" type="button" onclick="location.href='favorites.php'">
        Kthehu te të preferuarat
      </button>
    </section>
  </main>

  <footer class="footer">Faqja e detajeve</footer>
</body>
</html>
