<?php
require_once "config/database.php";
require_once "config/auth.php";
require_login();

$classId = (int)($_GET["class_id"] ?? 0);
$year = (int)($_GET["year"] ?? date("Y"));

if ($classId) {
    $stmt = $pdo->prepare("SELECT * FROM classes WHERE id=?");
    $stmt->execute([$classId]);
    $class = $stmt->fetch();
    if (!$class) { header("Location: recap.php"); exit; }

    $stmt = $pdo->prepare("
        SELECT s.id, s.nama, s.nis,
        SUM(a.status='Hadir') hadir,
        SUM(a.status='Izin') izin,
        SUM(a.status='Sakit') sakit,
        SUM(a.status='Alpa') alpa
        FROM students s
        LEFT JOIN attendance a ON a.student_id=s.id AND YEAR(a.tanggal)=?
        WHERE s.class_id=?
        GROUP BY s.id
        ORDER BY s.nama
    ");
    $stmt->execute([$year, $classId]);
    $rows = $stmt->fetchAll();
} else {
    $class = null;
    $classes = $pdo->query("SELECT * FROM classes ORDER BY nama_kelas")->fetchAll();
}
?>
<!doctype html>
<html lang="id">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>Rekapitulasi — KelasIn</title>
<link rel="stylesheet" href="assets/style.css">
</head>
<body>
<?php include "includes/header.php"; ?>
<main class="container">
  <div class="page-title">
    <div><p class="eyebrow">REKAPITULASI</p><h1><?= $class ? htmlspecialchars($class['nama_kelas']) : "Rekapitulasi" ?></h1></div>
    <?php if ($class): ?><a class="small-btn" href="attendance.php?class_id=<?= $classId ?>">Isi Absen</a><?php endif; ?>
  </div>

  <?php if (!$class): ?>
    <div class="year-picker">
      <h2><?= $year ?></h2>
      <div class="class-list">
        <?php foreach ($classes as $c): ?>
          <a class="class-item" href="recap.php?class_id=<?= $c['id'] ?>&year=<?= $year ?>"><span><?= htmlspecialchars($c['nama_kelas']) ?></span><b>›</b></a>
        <?php endforeach; ?>
      </div>
    </div>
  <?php else: ?>
    <form class="year-form">
      <input type="hidden" name="class_id" value="<?= $classId ?>">
      <label>Tahun
        <select name="year" onchange="this.form.submit()">
          <?php for($y=date("Y");$y>=2024;$y--): ?><option <?= $y===$year?"selected":"" ?>><?= $y ?></option><?php endfor; ?>
        </select>
      </label>
    </form>
    <section class="section-card">
      <div class="table-wrap">
        <table class="data-table">
          <thead><tr><th>No</th><th>Nama</th><th>Hadir</th><th>Izin</th><th>Sakit</th><th>Alpa</th></tr></thead>
          <tbody>
          <?php if (!$rows): ?><tr><td colspan="6" class="empty">Belum ada siswa.</td></tr>
          <?php else: foreach($rows as $i=>$r): ?>
            <tr><td><?= $i+1 ?></td><td><?= htmlspecialchars($r['nama']) ?></td><td><?= (int)$r['hadir'] ?></td><td><?= (int)$r['izin'] ?></td><td><?= (int)$r['sakit'] ?></td><td><?= (int)$r['alpa'] ?></td></tr>
          <?php endforeach; endif; ?>
          </tbody>
        </table>
      </div>
    </section>
  <?php endif; ?>
</main>
<?php include "includes/footer.php"; ?>
