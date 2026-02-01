<?php
declare(strict_types=1);

final class Page {

  public static function get(string $slug): ?array {
    $pdo = Database::pdo();
    $stmt = $pdo->prepare("SELECT slug, content, updated_by, updated_at FROM pages WHERE slug = ? LIMIT 1");
    $stmt->execute([$slug]);
    $row = $stmt->fetch();
    return $row ?: null;
  }

  public static function upsert(string $slug, string $content, int $userId): void {
    $pdo = Database::pdo();
    $stmt = $pdo->prepare("
      INSERT INTO pages (slug, content, updated_by)
      VALUES (?, ?, ?)
      ON DUPLICATE KEY UPDATE content = VALUES(content), updated_by = VALUES(updated_by)
    ");
    $stmt->execute([$slug, $content, $userId]);
  }
}
