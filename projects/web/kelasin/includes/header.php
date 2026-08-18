<?php
require_once __DIR__ . "/../config/auth.php";
require_login();
$current = basename($_SERVER['PHP_SELF']);
?>
<header class="topbar">
  <a class="brand" href="dashboard.php">
    <span class="pencil">✎</span>
    <span>KelasIn</span>
  </a>
  <div class="top-actions">
    <a href="profile.php" class="icon-btn" title="Profil">●</a>
    <a href="logout.php" class="icon-btn" title="Keluar">↪</a>
  </div>
</header>
