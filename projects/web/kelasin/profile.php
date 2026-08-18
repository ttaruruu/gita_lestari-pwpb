<?php
require_once "config/database.php";
require_once "config/auth.php";
require_login();

$stmt = $pdo->prepare("SELECT * FROM users WHERE id=?");
$stmt->execute([$_SESSION['user_id']]);
$user = $stmt->fetch();

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $nama = trim($_POST["nama"] ?? "");
    $email = trim($_POST["email"] ?? "");
    $alamat = trim($_POST["alamat"] ?? "");
    $sekolah = trim($_POST["sekolah"] ?? "");

    $q = $pdo->prepare("UPDATE users SET nama=?, email=?, alamat=?, sekolah=? WHERE id=?");
    $q->execute([$nama,$email,$alamat,$sekolah,$_SESSION['user_id']]);
    $_SESSION['user_name'] = $nama;
    header("Location: profile.php?saved=1");
    exit;
}
?>
<!doctype html>
<html lang="id">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>Profil — KelasIn</title>
<link rel="stylesheet" href="assets/style.css">
</head>
<body>
<?php include "includes/header.php"; ?>
<main class="container narrow">
  <a class="back" href="dashboard.php">← Kembali</a>
  <div class="profile-head"><div class="avatar">●</div><h1>Profil</h1></div>
  <?php if(isset($_GET['saved'])): ?><div class="alert success">Profil berhasil disimpan.</div><?php endif; ?>
  <form method="post" class="profile-form section-card">
    <label>Nama Lengkap<input name="nama" value="<?= htmlspecialchars($user['nama']) ?>" required></label>
    <label>E-mail<input type="email" name="email" value="<?= htmlspecialchars($user['email'] ?? '') ?>"></label>
    <label>Alamat<textarea name="alamat"><?= htmlspecialchars($user['alamat'] ?? '') ?></textarea></label>
    <label>Sekolah<input name="sekolah" value="<?= htmlspecialchars($user['sekolah'] ?? '') ?>"></label>
    <label>NISN<input value="<?= htmlspecialchars($user['nisn']) ?>" disabled></label>
    <button class="primary-btn">Simpan</button>
  </form>
</main>
<?php include "includes/footer.php"; ?>
