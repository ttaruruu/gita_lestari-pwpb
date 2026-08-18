<?php
require_once "config/database.php";
require_once "config/auth.php";
require_login();

$classes = $pdo->query("
    SELECT c.*,
           (SELECT COUNT(*) FROM students s WHERE s.class_id = c.id) AS total_students
    FROM classes c
    ORDER BY c.created_at DESC, c.id DESC
")->fetchAll();

$years = $pdo->query("SELECT DISTINCT YEAR(tanggal) tahun FROM attendance ORDER BY tahun DESC")->fetchAll();
?>
<!doctype html>
<html lang="id">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>KelasIn — Dashboard</title>
<link rel="stylesheet" href="assets/style.css">
</head>
<body>
<?php include "includes/header.php"; ?>

<main class="container">
  <div class="welcome">
    <div>
      <p class="eyebrow">SELAMAT DATANG</p>
      <h1><?= htmlspecialchars($_SESSION["user_name"]) ?></h1>
    </div>
    <a class="round-add" href="class_add.php">+</a>
  </div>

  <div class="tabs">
    <a class="active" href="dashboard.php">Absen</a>
    <a href="recap.php">Rekapitulasi</a>
  </div>

  <section class="section-card">
    <div class="section-head">
      <div>
        <span class="muted">DAFTAR KELAS</span>
        <h2>Kelas yang dikelola</h2>
      </div>
      <a class="small-btn" href="class_add.php">+ Tambah Kelas</a>
    </div>

    <?php if (!$classes): ?>
      <div class="empty">Belum ada kelas. Tambahkan kelas pertama kamu.</div>
    <?php else: ?>
      <div class="class-list">
        <?php foreach ($classes as $i => $class): ?>
          <a class="class-item" href="class.php?id=<?= $class['id'] ?>">
            <span><?= $i + 1 ?>. <?= htmlspecialchars($class['nama_kelas']) ?></span>
            <small><?= (int)$class['total_students'] ?> siswa · <?= htmlspecialchars($class['wali_kelas']) ?></small>
            <b>›</b>
          </a>
        <?php endforeach; ?>
      </div>
    <?php endif; ?>
  </section>

  <section class="section-card">
    <div class="section-head">
      <div><span class="muted">REKAP</span><h2>Tahun tersedia</h2></div>
    </div>
    <div class="year-list">
      <?php if (!$years): ?>
        <div class="empty">Belum ada data absensi.</div>
      <?php else: ?>
        <?php foreach ($years as $year): ?>
          <a href="recap.php?year=<?= $year['tahun'] ?>"><?= $year['tahun'] ?> <span>›</span></a>
        <?php endforeach; ?>
      <?php endif; ?>
    </div>
  </section>
</main>
<?php include "includes/footer.php"; ?>
