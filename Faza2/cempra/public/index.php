<?php
require_once __DIR__ . '/../app/config.php';
require_once __DIR__ . '/../app/core/Database.php';
require_once __DIR__ . '/../app/core/Auth.php';
require_once __DIR__ . '/../app/core/Favorite.php';
require_once __DIR__ . '/../app/core/Page.php';

$page = Page::get('home');
$homeContent = $page['content'] ?? '';

$isLogged = isset($_SESSION['user']);
$isAdmin = $isLogged && (($_SESSION['user']['role'] ?? '') === 'admin');

$userId = $isLogged ? (int)$_SESSION['user']['id'] : 0;
$favs = $isLogged ? Favorite::list($userId) : [];
?>
<!DOCTYPE html>
<html lang="sq">
<head>
  <meta charset="UTF-8">
  <title>°Cempra - Ballina</title>
  <link rel="stylesheet" href="assets/css/style.css">
  <link rel="stylesheet" href="assets/css/BallinaStyle.css">
  <script src="assets/js/index.js" defer></script>
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
    <h1 class="page-title">Kërko motin</h1>

    <?php if (trim($homeContent) !== ''): ?>
      <p class="page-subtitle">
        <?= nl2br(htmlspecialchars($homeContent)) ?>
      </p>
    <?php else: ?>
      <p class="page-subtitle">
        Shkruaje emrin e një qyteti për të parë paraqitjen e motit.
      </p>
    <?php endif; ?>

    <div class="search-box">
      <input id="cityInput" type="text" placeholder="Kërko qytetin (p.sh. Prishtina)">
      <button id="searchBtn" class="btn-primary" type="button">Kërko</button>
    </div>

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
          <div class="weather-city" id="wCity">Kerko qytetin, shtetin</div>
          <div class="weather-desc" id="wDesc"></div>
        </div>
        <div class="weather-temp" id="wTemp">?</div>
      </div>

      <div class="weather-extra">
        <div id="wWind">Era: ?</div>
        <div id="wHum">Lagështia: ?</div>
        <div id="wFeels">Ndjehet si: ?</div>
      </div>

      <div class="chips">
        <button class="chip" id="addFavoriteBtn" type="button">Shto tek të preferuarat</button>

        <form id="favoriteAddForm" method="POST" action="../app/controllers/favorite_add.php" style="display:none;">
          <input type="hidden" name="city_name" id="favCityName">
          <input type="hidden" name="lat" id="favLat">
          <input type="hidden" name="lon" id="favLon">
        </form>

        <button class="chip" id="toggleForecast" type="button">Shiko parashikimin 3-ditor</button>
      </div>

      <div class="forecast-panel" id="forecastPanel" aria-hidden="true">
        <div class="forecast-title">Parashikimi për 3 ditët e ardhshme.</div>
        <div class="forecast-grid" id="forecastGrid"></div>
      </div>
    </section>

    <h2 class="page-title" style="font-size:20px; margin-top:26px;">Qytete të njohura</h2>
    <p class="page-subtitle">Moti</p>

    <div class="card-grid">
      <div class="popular-slider" aria-label="Qytete të njohura slider">
        <div class="popular-track">
          <div class="card">
            <div class="card-title">Londër</div>
            <div class="card-temp">17°</div>
            <div class="card-sub">Me vranësira</div>
          </div>
          <div class="card">
            <div class="card-title">New York</div>
            <div class="card-temp">21°</div>
            <div class="card-sub">Me diell</div>
          </div>
          <div class="card">
            <div class="card-title">Tokyo</div>
            <div class="card-temp">19°</div>
            <div class="card-sub">Shi i lehtë</div>
          </div>
          <div class="card">
            <div class="card-title">Prishtina</div>
            <div class="card-temp">23°</div>
            <div class="card-sub">Pjesërisht me vranësira</div>
          </div>

          <div class="card">
            <div class="card-title">Londër</div>
            <div class="card-temp">17°</div>
            <div class="card-sub">Me vranësira</div>
          </div>
          <div class="card">
            <div class="card-title">New York</div>
            <div class="card-temp">21°</div>
            <div class="card-sub">Me diell</div>
          </div>
          <div class="card">
            <div class="card-title">Tokyo</div>
            <div class="card-temp">19°</div>
            <div class="card-sub">Shi i lehtë</div>
          </div>
          <div class="card">
            <div class="card-title">Prishtina</div>
            <div class="card-temp">23°</div>
            <div class="card-sub">Pjesërisht me vranësira</div>
          </div>
        </div>
      </div>
    </div>
  </main>

  <footer class="footer">
    °Cempra
  </footer>
</body>
</html>
