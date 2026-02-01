<?php
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../core/Database.php';
require_once __DIR__ . '/../core/Auth.php';
require_once __DIR__ . '/../core/Favorite.php';

Auth::requireLogin();

$userId = (int)($_SESSION['user']['id'] ?? 0);
$city = trim($_POST['city_name'] ?? '');

if ($userId <= 0 || $city === '') {
  http_response_code(400);
  echo "bad request";
  exit;
}

Favorite::remove($userId, $city);

header('Location: /cempra/public/favorites.php');
exit;
