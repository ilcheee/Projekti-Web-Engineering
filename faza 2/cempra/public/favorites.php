<?php
require_once __DIR__ . '/../app/config.php';
require_once __DIR__ . '/../app/core/Database.php';
require_once __DIR__ . '/../app/core/Auth.php';
require_once __DIR__ . '/../app/core/Favorite.php';

Auth::requireLogin();

$userId = (int)$_SESSION['user']['id'];
$favs = Favorite::list($userId);
?>
<!DOCTYPE html>
<html lang="sq">
<head>
  <meta charset="UTF-8">
  <title>°Cempra - Të preferuarat</title>

  <link rel="stylesheet" href="assets/css/style.css">
  <link rel="stylesheet" href="assets/css/FavoritesStyle.css">

  <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css">
  <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" defer></script>

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
    <h1 class="page-title">Qytetet e preferuara</h1>

    <?php if (!$favs): ?>
      <p class="page-subtitle">
        Nuk ke asnjë qytet të preferuar ende.
      </p>
    <?php else: ?>
      <div class="card-grid" id="favoritesGrid">
        <?php foreach ($favs as $f): ?>
          <div class="card">
            <div class="card-title"><?= htmlspecialchars($f['city_name']) ?></div>

            <div class="card-sub" style="margin-top:10px;">
              <?= (!empty($f['lat']) && !empty($f['lon']))
                ? 'Koordinata: ' . htmlspecialchars($f['lat']) . ', ' . htmlspecialchars($f['lon'])
                : 'Koordinata: —'
              ?>
            </div>

            <div style="display:flex; gap:10px; margin-top:12px; flex-wrap:wrap;">
              <a class="btn-ghost" href="city.php?city=<?= urlencode($f['city_name']) ?>">Shiko detajet</a>

              <form method="POST" action="../app/controllers/favorite_remove.php" style="margin:0;">
                <input type="hidden" name="city_name" value="<?= htmlspecialchars($f['city_name']) ?>">
                <button class="btn-ghost" type="submit" style="margin-top:0;">Hiq</button>
              </form>
            </div>
          </div>
        <?php endforeach; ?>
      </div>
    <?php endif; ?>

    <div style="margin-top:30px;">
      <button class="btn-primary" onclick="location.href='index.php'">Shto qytet të ri</button>
    </div>
  </main>

  <footer class="footer">
    <div class="footer-map-wrap">
      <div class="footer-map-title">Harta e qyteteve të preferuara</div>
      <div id="favoritesMap" class="footer-map" aria-label="Harta e preferuarave"></div>
    </div>
  </footer>

  <script>
    const favoritesData = <?= json_encode(array_map(function($f){
      return [
        "city_name" => $f["city_name"] ?? "",
        "lat" => isset($f["lat"]) ? (float)$f["lat"] : null,
        "lon" => isset($f["lon"]) ? (float)$f["lon"] : null
      ];
    }, $favs), JSON_UNESCAPED_UNICODE); ?>;

    window.addEventListener("load", () => {
      const mapEl = document.getElementById("favoritesMap");
      if (!mapEl || typeof L === "undefined") return;

      let centerLat = 42.6629;
      let centerLon = 21.1655;
      let zoom = 5;

      const first = favoritesData.find(x => typeof x.lat === "number" && typeof x.lon === "number");
      if (first) {
        centerLat = first.lat;
        centerLon = first.lon;
        zoom = 6;
      }

      const map = L.map("favoritesMap").setView([centerLat, centerLon], zoom);
      L.tileLayer("https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png", {
        attribution: "&copy; OpenStreetMap contributors"
      }).addTo(map);

      const points = [];
      favoritesData.forEach(f => {
        if (typeof f.lat !== "number" || typeof f.lon !== "number") return;
        points.push([f.lat, f.lon]);
        L.marker([f.lat, f.lon]).addTo(map).bindPopup(f.city_name || "Qytet");
      });

      if (points.length > 1) {
        map.fitBounds(points, { padding: [30, 30] });
      }
    });
  </script>
</body>
</html>
