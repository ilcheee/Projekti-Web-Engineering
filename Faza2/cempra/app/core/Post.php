<?php
declare(strict_types=1);

final class Post {

  public static function all(): array {
    $pdo = Database::pdo();
    $stmt = $pdo->query("
      SELECT p.id, p.title, p.body, p.image_path, p.pdf_path,
             p.created_by, p.updated_by, p.created_at, p.updated_at,
             u.full_name AS created_by_name
      FROM posts p
      JOIN users u ON u.id = p.created_by
      ORDER BY p.id DESC
    ");
    return $stmt->fetchAll();
  }

  public static function find(int $id): ?array {
    $pdo = Database::pdo();
    $stmt = $pdo->prepare("
      SELECT p.*, u.full_name AS created_by_name
      FROM posts p
      JOIN users u ON u.id = p.created_by
      WHERE p.id = ?
      LIMIT 1
    ");
    $stmt->execute([$id]);
    $row = $stmt->fetch();
    return $row ?: null;
  }

  public static function create(string $title, string $body, ?string $imagePath, ?string $pdfPath, int $userId): void {
    $pdo = Database::pdo();
    $stmt = $pdo->prepare("
      INSERT INTO posts (title, body, image_path, pdf_path, created_by, updated_by)
      VALUES (?, ?, ?, ?, ?, ?)
    ");
    $stmt->execute([$title, $body, $imagePath, $pdfPath, $userId, $userId]);
  }

  public static function update(int $id, string $title, string $body, ?string $imagePath, ?string $pdfPath, int $userId): void {
    $pdo = Database::pdo();
    $stmt = $pdo->prepare("
      UPDATE posts
      SET title = ?, body = ?, image_path = ?, pdf_path = ?, updated_by = ?
      WHERE id = ?
    ");
    $stmt->execute([$title, $body, $imagePath, $pdfPath, $userId, $id]);
  }

  public static function delete(int $id): void {
    $pdo = Database::pdo();
    $stmt = $pdo->prepare("DELETE FROM posts WHERE id = ?");
    $stmt->execute([$id]);
  }
}
