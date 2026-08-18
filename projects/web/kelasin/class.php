<?php
require_once "config/database.php";
require_once "config/auth.php";
require_login();

$id = (int)($_GET["id"] ?? 0);
$stmt = $pdo->prepare("SELECT * FROM classes WHERE id = ?");
$stmt->execute([$id]);
$class = $stmt->fetch();
if (!$class) { header("Location: dashboard.php"); exit; }

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["add_student"])) {
    $nama = trim($_POST["nama"] ?? "");
    $nis = trim($_POST["nis"] ?? "");
    if ($nama !== "") {
        $stmt = $pdo->prepare("INSERT INTO students (class_id, nis, nama) VALUES (?, ?, ?)");
        $stmt->execute([$id, $nis ?: null, $nama]);
    }
    header("Location: class.php?id=".$id);
    exit;
}

if (isset($_GET["delete_student"])) {
    $studentId = (int)$_GET["delete_student"];
    $stmt = $pdo->prepare("DELETE FROM students WHERE id = ? AND class_id = ?");
    $stmt->execute([$studentId, $id]);
    header("Location: class.php?id=".$id);
    exit;
}

$studentsStmt = $pdo->prepare("SELECT * FROM students WHERE class_id = ? ORDER BY nama");
$studentsStmt->execute([$id]);
$students = $studentsStmt->fetchAll();
?>
<!doctype html>
<html lang="id">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title><?= htmlspecialchars($class['nama_kelas']) ?> — KelasIn</title>
<link rel="stylesheet" href="assets/style.css">
</head>
<body>
<?php include "includes/header.php"; ?>
<main class="container">
  <a class="back" href="dashboard.php">← Kembali</a>

  <div class="page-title">
    <div>
      <p class="eyebrow">KELAS</p>
      <h1><?= htmlspecialchars($class['nama_kelas']) ?></h1>
      <p class="muted-text">Wali kelas: <?= htmlspecialchars($class['wali_kelas']) ?></p>
    </div>
    <a class="small-btn" href="attendance.php?class_id=<?= $id ?>">Isi Absen</a>
  </div>

  <section class="section-card">
    <div class="section-head">
      <div><span class="muted">DATA SISWA</span><h2>Daftar siswa</h2></div>
      <button class="small-btn" onclick="document.getElementById('studentForm').classList.toggle('hidden')">+ Tambah</button>
    </div>

    <form method="post" id="studentForm" class="inline-form hidden">
      <input type="text" name="nis" placeholder="NIS">
      <input type="text" name="nama" placeholder="Nama lengkap" required>
      <button name="add_student" class="small-btn">Simpan</button>
    </form>

    <?php if (!$students): ?>
      <div class="empty">Belum ada siswa di kelas ini.</div>
    <?php else: ?>
      <div class="student-list">
        <?php foreach ($students as $i => $student): ?>
          <div class="student-row">
            <span><b><?= $i+1 ?>.</b> <?= htmlspecialchars($student['nama']) ?></span>
            <span class="row-actions">
              <small><?= htmlspecialchars($student['nis'] ?? '-') ?></small>
              <a href="?id=<?= $id ?>&delete_student=<?= $student['id'] ?>" onclick="return confirm('Hapus siswa ini?')">×</a>
            </span>
          </div>
        <?php endforeach; ?>
      </div>
    <?php endif; ?>
  </section>

  <section class="quick-grid">
    <a href="attendance.php?class_id=<?= $id ?>"><span>✓</span><strong>Absen Hari Ini</strong><small>Catat kehadiran siswa</small></a>
    <a href="recap.php?class_id=<?= $id ?>"><span>▤</span><strong>Rekapitulasi</strong><small>Lihat riwayat absensi</small></a>
  </section>
</main>
<?php include "includes/footer.php"; ?>
