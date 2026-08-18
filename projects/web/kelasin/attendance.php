<?php
require_once "config/database.php";
require_once "config/auth.php";
require_login();

$classId = (int)($_GET["class_id"] ?? $_POST["class_id"] ?? 0);
$stmt = $pdo->prepare("SELECT * FROM classes WHERE id=?");
$stmt->execute([$classId]);
$class = $stmt->fetch();
if (!$class) { header("Location: dashboard.php"); exit; }

$date = $_GET["date"] ?? $_POST["tanggal"] ?? date("Y-m-d");

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["save_attendance"])) {
    $date = $_POST["tanggal"];
    $statuses = $_POST["status"] ?? [];
    $notes = $_POST["catatan"] ?? [];

    $pdo->beginTransaction();
    try {
        foreach ($statuses as $studentId => $status) {
            $studentId = (int)$studentId;
            $check = $pdo->prepare("SELECT id FROM attendance WHERE student_id=? AND tanggal=?");
            $check->execute([$studentId, $date]);
            $existing = $check->fetchColumn();

            if ($existing) {
                $q = $pdo->prepare("UPDATE attendance SET status=?, catatan=? WHERE id=?");
                $q->execute([$status, trim($notes[$studentId] ?? ""), $existing]);
            } else {
                $q = $pdo->prepare("INSERT INTO attendance (student_id, tanggal, status, catatan) VALUES (?,?,?,?)");
                $q->execute([$studentId, $date, $status, trim($notes[$studentId] ?? "")]);
            }
        }
        $pdo->commit();
        header("Location: attendance.php?class_id=".$classId."&date=".urlencode($date)."&saved=1");
        exit;
    } catch (Throwable $e) {
        $pdo->rollBack();
        die("Gagal menyimpan absensi.");
    }
}

$stmt = $pdo->prepare("
    SELECT s.*,
           COALESCE(a.status, 'Hadir') status,
           COALESCE(a.catatan, '') catatan
    FROM students s
    LEFT JOIN attendance a ON a.student_id=s.id AND a.tanggal=?
    WHERE s.class_id=?
    ORDER BY s.nama
");
$stmt->execute([$date, $classId]);
$students = $stmt->fetchAll();
?>
<!doctype html>
<html lang="id">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>Absen <?= htmlspecialchars($class['nama_kelas']) ?></title>
<link rel="stylesheet" href="assets/style.css">
</head>
<body>
<?php include "includes/header.php"; ?>
<main class="container">
  <a class="back" href="class.php?id=<?= $classId ?>">← Kembali</a>
  <div class="page-title">
    <div><p class="eyebrow">ABSEN</p><h1><?= htmlspecialchars($class['nama_kelas']) ?></h1></div>
    <form><input class="date-input" type="date" name="date" value="<?= htmlspecialchars($date) ?>"><input type="hidden" name="class_id" value="<?= $classId ?>"><button class="small-btn">Tampilkan</button></form>
  </div>

  <?php if (isset($_GET["saved"])): ?><div class="alert success">Absensi berhasil disimpan.</div><?php endif; ?>

  <form method="post">
    <input type="hidden" name="class_id" value="<?= $classId ?>">
    <input type="hidden" name="tanggal" value="<?= htmlspecialchars($date) ?>">
    <section class="section-card attendance-card">
      <?php if (!$students): ?>
        <div class="empty">Belum ada siswa. Tambahkan siswa dari halaman kelas.</div>
      <?php else: ?>
        <div class="attendance-head"><span>No</span><span>Nama</span><span>Status</span><span>Catatan</span></div>
        <?php foreach ($students as $i=>$student): ?>
          <div class="attendance-row">
            <span><?= $i+1 ?></span>
            <strong><?= htmlspecialchars($student['nama']) ?></strong>
            <select name="status[<?= $student['id'] ?>]">
              <?php foreach (["Hadir","Izin","Sakit","Alpa"] as $status): ?>
                <option <?= $student['status']===$status ? "selected":"" ?>><?= $status ?></option>
              <?php endforeach; ?>
            </select>
            <input name="catatan[<?= $student['id'] ?>]" value="<?= htmlspecialchars($student['catatan']) ?>" placeholder="-">
          </div>
        <?php endforeach; ?>
        <button name="save_attendance" class="primary-btn">Simpan Absensi</button>
      <?php endif; ?>
    </section>
  </form>
</main>
<?php include "includes/footer.php"; ?>
