<?php
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../core/Database.php';
require_once __DIR__ . '/../core/Auth.php';
require_once __DIR__ . '/../core/Post.php';

Auth::requireAdmin();

function uploadFile(string $field, array $allowedExt, string $destDir): ?string {
  if (!isset($_FILES[$field]) || $_FILES[$field]['error'] !== UPLOAD_ERR_OK) return null;

  $tmp = $_FILES[$field]['tmp_name'];
  $name = $_FILES[$field]['name'];

  $ext = strtolower(pathinfo($name, PATHINFO_EXTENSION));
  if (!in_array($ext, $allowedExt, true)) {
    die("File jo valid për $field");
  }

  if (!is_dir($destDir)) {
    mkdir($destDir, 0777, true);
  }

  $safe = bin2hex(random_bytes(8)) . "." . $ext;
  $target = rtrim($destDir, '/') . '/' . $safe;

  if (!move_uploaded_file($tmp, $target)) {
    die("Upload failed");
  }

  return "uploads/" . $safe;
}

$id = (int)($_POST['id'] ?? 0);
$title = trim($_POST['title'] ?? '');
$body = trim($_POST['body'] ?? '');
$userId = (int)($_SESSION['user']['id'] ?? 0);

if ($title === '' || $body === '' || $userId <= 0) {
  die("Plotëso titullin dhe tekstin.");
}

$uploadDir = __DIR__ . '/../../public/uploads';

$imagePath = uploadFile('image', ['jpg','jpeg','png','webp','gif'], $uploadDir);
$pdfPath   = uploadFile('pdf', ['pdf'], $uploadDir);

$oldImage = trim($_POST['old_image'] ?? '');
$oldPdf   = trim($_POST['old_pdf'] ?? '');

$imageFinal = $imagePath ?? ($oldImage !== '' ? $oldImage : null);
$pdfFinal   = $pdfPath   ?? ($oldPdf !== '' ? $oldPdf : null);

if ($imageFinal === null && $pdfFinal === null) {
  die("Duhet të upload-osh foto ose PDF.");
}

if ($id > 0) {
  Post::update($id, $title, $body, $imageFinal, $pdfFinal, $userId);
} else {
  Post::create($title, $body, $imageFinal, $pdfFinal, $userId);
}

header('Location: /cempra/public/admin/posts.php');
exit;
