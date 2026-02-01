<?php
declare(strict_types=1);

final class User {

  public static function all(): array {
    $pdo = Database::pdo();
    $stmt = $pdo->query("SELECT id, full_name, email, role, created_at FROM users ORDER BY id DESC");
    return $stmt->fetchAll();
  }

  public static function setRole(int $id, string $role): void {
    $role = ($role === 'admin') ? 'admin' : 'user';
    $pdo = Database::pdo();
    $stmt = $pdo->prepare("UPDATE users SET role = ? WHERE id = ?");
    $stmt->execute([$role, $id]);
  }

  public static function deleteById(int $id): void {
    $pdo = Database::pdo();
    $stmt = $pdo->prepare("DELETE FROM users WHERE id = ?");
    $stmt->execute([$id]);
  }
}
