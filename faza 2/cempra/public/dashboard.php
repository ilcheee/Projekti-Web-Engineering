<?php
require_once __DIR__ . '/../app/config.php';
require_once __DIR__ . '/../app/core/Database.php';
require_once __DIR__ . '/../app/core/Auth.php';
require_once __DIR__ . '/../app/core/Favorite.php';

Auth::requireLogin();

$userId = (int)($_SESSION['user']['id'] ?? 0);
$dbFavs = Favorite::list($userId);

$favCount = count($dbFavs);
$lastFav = $dbFavs[0] ?? null;

$jsFavs = array_map(function ($f) {
    $cityName = $f['city_name'] ?? '';

    $parts = array_map('trim', explode(',', $cityName));
    $name = $parts[0] ?? $cityName;
    $country = $parts[1] ?? '';

    return [
        'city_name' => $cityName,
        'name' => $name,
        'country' => $country,
        'lat' => isset($f['lat']) ? (float)$f['lat'] : null,
        'lon' => isset($f['lon']) ? (float)$f['lon'] : null,
        'created_at' => $f['created_at'] ?? null
    ];
}, $dbFavs);
?>
<!DOCTYPE html>
<html lang="sq">
<head>
  <meta charset="UTF-8">
  <title>°Cempra - Paneli</title>

  <link rel="stylesheet" href="assets/css/style.css">
  <link rel="stylesheet" href="assets/css/DashboardStyle.css">

  <script>
    window.CEMPRA_DB_FAVS = <?= json_encode($jsFavs, JSON_UNESCAPED_UNICODE) ?>;
    window.CEMPRA_FAV_COUNT = <?= (int)$favCount ?>;
  </script>

  <script src="assets/js/dashboard.js" defer></script>
</head>

<body>
  <header class="header">
    <div class="logo">°C<span>empra</span></div>

    <nav class="nav-links">
      <?php
      $isAdmin =
        (($_SESSION['user']['role'] ?? '') === 'admin') ||
        ((int)($_SESSION['user']['is_admin'] ?? 0) === 1);
      ?>
      <?php if ($isAdmin): ?>
        <a href="admin/messages.php">Admin Panel</a>
      <?php endif; ?>

      <a href="index.php">Ballina</a>
      <a href="favorites.php">Të preferuarat</a>
      <a href="dashboard.php">Paneli</a>
      <a href="about.php">Rreth nesh</a>
      <a href="logout.php">Dil</a>
    </nav>
  </header>

  <main class="page">
    <p style="margin: 0 0 10px; text-align:center; font-size:18px; font-style:italic;">
      Mirë se vjen, <?= htmlspecialchars($_SESSION['user']['full_name'] ?? 'Përdorues') ?>!
    </p>

    <h1 class="page-title">Paneli i përdoruesit</h1>

    <p class="page-subtitle">
      Këtu i sheh të dhënat e fundit: qytetin e fundit të preferuar, numrin e preferuarave, kërkimet e sotme dhe njësinë e temperaturës.
    </p>

    <section class="weather-card">
      <div class="weather-main-row">
        <div>
          <div class="weather-city" id="dashFavCity">
            Qyteti i fundit i preferuar:
            <?= $lastFav ? htmlspecialchars($lastFav['city_name'] ?? '') : '—' ?>
          </div>

          <div class="weather-desc" id="dashFavDesc">
            <?= $lastFav ? 'Ky është qyteti yt i fundit i ruajtur në profil.' : 'Shto një qytet te “Të preferuarat” nga Ballina.' ?>
          </div>

          <div class="weather-temp" id="dashFavTemp">—</div>
        </div>
      </div>

      <div class="weather-extra">
        <div id="dashFavCount">Qytete të preferuara: <?= (int)$favCount ?></div>
        <div id="dashSearchesToday">Kërkime sot: 0</div>
        <div id="dashUnit">Njësia: °C</div>
      </div>
    </section>

    <section>
      <h2 class="page-title" style="font-size:20px;">Statistika të shpejta</h2>

      <div class="stats-row">
        <div class="stat-card">
          <div class="stat-label">Qytete të preferuara</div>
          <div class="stat-value" id="statFavCount"><?= (int)$favCount ?></div>
        </div>

        <div class="stat-card">
          <div class="stat-label">Kërkime sot</div>
          <div class="stat-value" id="statSearchesToday">0</div>
        </div>

        <div class="stat-card">
          <div class="stat-label">Njësia e temperaturës</div>
          <div class="stat-value" id="statUnitText">Celsius</div>
        </div>
      </div>
    </section>

    <section style="margin-top:24px;">
      <h2 class="page-title" style="font-size:20px;">Qytetet e mia të preferuara</h2>

      <?php if (!$dbFavs): ?>
        <p class="page-subtitle">Nuk ke asnjë qytet të preferuar ende.</p>
      <?php else: ?>
        <div class="card-grid">
          <?php foreach ($dbFavs as $f): ?>
            <div class="card">
              <div class="card-title"><?= htmlspecialchars($f['city_name'] ?? '') ?></div>

              <div class="card-sub" style="margin-top:10px;">
                <?= (!empty($f['lat']) && !empty($f['lon']))
                  ? 'Koordinata: ' . htmlspecialchars($f['lat']) . ', ' . htmlspecialchars($f['lon'])
                  : 'Koordinata: —'
                ?>
              </div>

              <div style="display:flex; gap:10px; margin-top:12px; flex-wrap:wrap;">
                <a class="btn-ghost" href="city.php?city=<?= urlencode($f['city_name'] ?? '') ?>">Shiko detajet</a>

                <form method="POST" action="../app/controllers/favorite_remove.php" style="margin:0;">
                  <input type="hidden" name="city_name" value="<?= htmlspecialchars($f['city_name'] ?? '') ?>">
                  <button class="btn-ghost" type="submit" style="margin-top:0;">Hiq</button>
                </form>
              </div>
            </div>
          <?php endforeach; ?>
        </div>
      <?php endif; ?>
    </section>

    <section style="margin-top:24px;">
      <h2 class="page-title" style="font-size:20px;">Kërkimet e fundit</h2>
      <ul class="history-list" id="dashHistory">
        <li>Nuk ka kërkime ende.</li>
      </ul>
    </section>

    <section style="margin-top:24px;">
      <button class="btn-primary" id="openSettings" type="button">Hap cilësimet</button>

      <div class="dash-settings" id="settingsPanel" aria-hidden="true">
        <div class="dash-settings-title">Cilësimet</div>

        <div class="dash-row">
          <div>
            <div class="stat-label">Njësia e temperaturës</div>
            <div class="form-note">Zgjidh °C ose °F (ruhet automatikisht).</div>
          </div>

          <div class="unit-toggle">
            <button type="button" class="chip" id="unitC">°C</button>
            <button type="button" class="chip" id="unitF">°F</button>
          </div>
        </div>
      </div>

      <p class="form-note" style="margin-top:10px;">
        Dy njesi te matjes. Celsius (°C) eshte njesia me e perdorur ne bote, ndersa Fahrenheit (°F) eshte me e zakonshme ne Shtetet e Bashkuara.
      </p>
    </section>
  </main>

  <footer class="footer">
    °Cempra · Paneli i përdoruesit
  </footer>
</body>
</html>
