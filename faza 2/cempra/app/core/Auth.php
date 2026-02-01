<?php
declare(strict_types=1);

final class Auth {

  public static function register(
    string $fullName,
    string $email,
    string $password
  ): bool {

    $pdo = Database::pdo();

    $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ? LIMIT 1");
    $stmt->execute([$email]);
    if ($stmt->fetch()) {
      return false; // email ekziston
    }

    $hash = password_hash($password, PASSWORD_DEFAULT);

    $stmt = $pdo->prepare(
      "INSERT INTO users (full_name, email, password_hash, role)
       VALUES (?, ?, ?, 'user')"
    );

    $stmt->execute([$fullName, $email, $hash]);
    return true;
  }

  public static function login(string $email, string $password): bool {
    $pdo = Database::pdo();

    $stmt = $pdo->prepare("SELECT id, full_name, email, password_hash, role FROM users WHERE email = ? LIMIT 1");
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    if (!$user) return false;
    if (!password_verify($password, $user['password_hash'])) return false;

    $_SESSION['user'] = [
      'id' => (int)$user['id'],
      'full_name' => $user['full_name'],
      'email' => $user['email'],
      'role' => $user['role'],
      'is_admin' => ($user['role'] === 'admin') ? 1 : 0,
    ];

    return true;
  }

  public static function requireLogin(): void {
    if (!isset($_SESSION['user'])) {
      header('Location: /cempra/public/auth.php');
      exit;
    }
  }

  public static function requireAdmin(): void {
    if (!isset($_SESSION['user'])) {
      header('Location: /cempra/public/auth.php');
      exit;
    }

    if (($_SESSION['user']['role'] ?? '') !== 'admin') {
      header('Location: /cempra/public/dashboard.php');
      exit;
    }
  }
}
