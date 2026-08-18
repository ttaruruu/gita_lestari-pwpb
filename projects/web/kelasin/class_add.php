<?php
require_once "config/database.php";
require_once "config/auth.php";
require_login();

$error = "";
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $nama = trim($_POST["nama_kelas"] ?? "");
    $wali = trim($_POST["wali_kelas"] ?? "");
    if ($nama === "" || $wali === "") {
        $error = "Nama kelas dan wali kelas wajib diisi.";
    } else {
        $stmt = $pdo->prepare("INSERT INTO classes (nama_kelas, wali_kelas) VALUES (?,?)");
        $stmt->execute([$nama,$wali]);
        header("Location: class.php?id=".$pdo->lastInsertId());
        exit;
    }
}
?>
<!doctype html>
<html lang="id">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>Tambah Kelas — KelasIn</title>
<link rel="stylesheet" href="assets/style.css">
</head>
<body class="add-page">
<div class="add-panel">
  <a class="back white-back" href="dashboard.php">← Kembali</a>
  <div class="add-logo">KelasIn</div>
  <h1>Tambah Kelas</h1>
  <?php if($error): ?><div class="alert error"><?= htmlspecialchars($error) ?></div><?php endif; ?>
  <form method="post">
    <label>Kelas<input name="nama_kelas" placeholder="Contoh: XI - RPL 2" required></label>
    <label>Wali Kelas<input name="wali_kelas" placeholder="Nama wali kelas" required></label>
    <button class="white-btn">Tambah</button>
  </form>
</div>
</body>
</html>
