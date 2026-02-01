<?php
declare(strict_types=1);

final class Favorite {
  public static function add(int $userId, string $city, ?float $lat, ?float $lon): void {
    $pdo = Database::pdo();
    $stmt = $pdo->prepare("
      INSERT INTO favorites (user_id, city_name, lat, lon)
      VALUES (?, ?, ?, ?)
      ON DUPLICATE KEY UPDATE lat = VALUES(lat), lon = VALUES(lon)
    ");
    $stmt->execute([$userId, $city, $lat, $lon]);
  }

  public static function remove(int $userId, string $city): void {
    $pdo = Database::pdo();
    $stmt = $pdo->prepare("DELETE FROM favorites WHERE user_id = ? AND city_name = ?");
    $stmt->execute([$userId, $city]);
  }

  public static function list(int $userId): array {
    $pdo = Database::pdo();
    $stmt = $pdo->prepare("SELECT city_name, lat, lon, created_at FROM favorites WHERE user_id = ? ORDER BY id DESC");
    $stmt->execute([$userId]);
    return $stmt->fetchAll();
  }
}
