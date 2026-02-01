<?php
declare(strict_types=1);

final class Contact {
  public static function save(string $name, string $email, string $message): void {
    $pdo = Database::pdo();
    $stmt = $pdo->prepare("INSERT INTO contact_messages (name, email, message) VALUES (?, ?, ?)");
    $stmt->execute([$name, $email, $message]);
  }

  public static function all(): array {
    $pdo = Database::pdo();
    $stmt = $pdo->query("SELECT id, name, email, message, created_at, is_read FROM contact_messages ORDER BY id DESC");
    return $stmt->fetchAll();
  }

  public static function markRead(int $id): void {
    $pdo = Database::pdo();
    $stmt = $pdo->prepare("UPDATE contact_messages SET is_read = 1 WHERE id = ?");
    $stmt->execute([$id]);
  }
}

?>