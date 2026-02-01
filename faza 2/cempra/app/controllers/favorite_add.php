<?php
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../core/Database.php';
require_once __DIR__ . '/../core/Auth.php';
require_once __DIR__ . '/../core/Favorite.php';

Auth::requireLogin();

$userId = (int)($_SESSION['user']['id'] ?? 0);
$city = trim($_POST['city_name'] ?? '');
$lat = isset($_POST['lat']) && $_POST['lat'] !== '' ? (float)$_POST['lat'] : null;
$lon = isset($_POST['lon']) && $_POST['lon'] !== '' ? (float)$_POST['lon'] : null;

if ($userId <= 0 || $city === '') {
  http_response_code(400);
  echo "bad request";
  exit;
}

Favorite::add($userId, $city, $lat, $lon);

header('Location: /cempra/public/favorites.php');
exit;
