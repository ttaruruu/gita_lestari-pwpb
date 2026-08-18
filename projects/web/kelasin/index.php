<?php
require_once "config/database.php";
if (session_status() === PHP_SESSION_NONE) session_start();

if (!empty($_SESSION['user_id'])) {
    header("Location: dashboard.php");
    exit;
}

$error = "";
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $nisn = trim($_POST["nisn"] ?? "");
    $password = $_POST["password"] ?? "";

    $stmt = $pdo->prepare("SELECT * FROM users WHERE nisn = ? LIMIT 1");
    $stmt->execute([$nisn]);
    $user = $stmt->fetch();

    if ($user && password_verify($password, $user["password"])) {
        $_SESSION["user_id"] = $user["id"];
        $_SESSION["user_name"] = $user["nama"];
        header("Location: dashboard.php");
        exit;
    }
    $error = "NISN atau password salah.";
}
?>
<!doctype html>
<html lang="id">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>KelasIn — Masuk</title>
<link rel="stylesheet" href="assets/style.css">
</head>
<body class="login-page">
<div class="login-wrap">
  <div class="login-brand"><span>✎</span><strong>KelasIn</strong></div>
  <h1>MASUK</h1>

  <?php if ($error): ?><div class="alert error"><?= htmlspecialchars($error) ?></div><?php endif; ?>

  <form method="post" class="login-form">
    <label>NISN
      <input name="nisn" autocomplete="username" required>
    </label>
    <label>Password
      <input name="password" type="password" autocomplete="current-password" required>
    </label>
    <button class="white-btn" type="submit">Masuk</button>
  </form>

  <p class="demo-note">Akun demo: <b>1234567890</b> / <b>12345</b></p>
</div>
</body>
</html>
