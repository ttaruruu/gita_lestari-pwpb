<?php

include "koneksi.php";

$user = $_GET['user'] ?? "";

if ($user == "") {

    header("Location: index.php");
    exit;

}


$query = mysqli_query(
    $koneksi,
    "SELECT * FROM users
     WHERE username='$user'"
);


$data = mysqli_fetch_assoc($query);


if (!$data) {

    die("Data akun tidak ditemukan.");

}

?>

<!DOCTYPE html>
<html lang="id">

<head>

    <meta charset="UTF-8">

    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">

    <title>Akun - PerpustakaanKu</title>

    <link rel="stylesheet"
          href="css/style.css">

</head>

<body>


<nav class="navbar">

    <div class="nav-inner">

        <a
            href="beranda.php?user=<?= urlencode($user) ?>"
            class="logo"
        >
            <span class="logo-star">✦</span>
            <span>PerpustakaanKu</span>
        </a>

        <div class="nav-menu">

            <a
                href="beranda.php?user=<?= urlencode($user) ?>"
                class="<?= basename($_SERVER['PHP_SELF']) == 'beranda.php' ? 'active' : '' ?>"
            >
                Beranda
            </a>

            <a
                href="buku.php?user=<?= urlencode($user) ?>"
                class="<?= basename($_SERVER['PHP_SELF']) == 'buku.php' || basename($_SERVER['PHP_SELF']) == 'detail_buku.php' ? 'active' : '' ?>"
            >
                Buku
            </a>

            <a
                href="ulasan.php?user=<?= urlencode($user) ?>"
                class="<?= basename($_SERVER['PHP_SELF']) == 'ulasan.php' ? 'active' : '' ?>"
            >
                Ulasan
            </a>

            <a
                href="buat_ulasan.php?user=<?= urlencode($user) ?>"
                class="<?= basename($_SERVER['PHP_SELF']) == 'buat_ulasan.php' ? 'active' : '' ?>"
            >
                Buat Ulasan
            </a>

            <a
                href="akun.php?user=<?= urlencode($user) ?>"
                class="<?= basename($_SERVER['PHP_SELF']) == 'akun.php' ? 'active' : '' ?>"
            >
                Akun
            </a>

        </div>

    </div>

</nav>


<main class="container">

    <div class="page-title">

        <p class="section-label">
            PROFIL
        </p>

        <h1>
            Informasi Akun
        </h1>

        <p>
            Informasi mengenai akun PerpustakaanKu kamu.
        </p>

    </div>


    <div class="account-card">

        <div class="account-header">

            <div class="account-avatar">

                <?= strtoupper(
                    substr($data['nama'], 0, 1)
                ) ?>

            </div>


            <div>

                <h2>
                    <?= htmlspecialchars($data['nama']) ?>
                </h2>

                <p>
                    @<?= htmlspecialchars($data['username']) ?>
                </p>

            </div>

        </div>


        <div class="info-row">

            <span class="info-label">
                Nama Lengkap
            </span>

            <span class="info-value">
                <?= htmlspecialchars($data['nama']) ?>
            </span>

        </div>


        <div class="info-row">

            <span class="info-label">
                Tanggal Lahir
            </span>

            <span class="info-value">
                <?= htmlspecialchars($data['tgl_lahir']) ?>
            </span>

        </div>


        <div class="info-row">

            <span class="info-label">
                Jenis Kelamin
            </span>

            <span class="info-value">
                <?= htmlspecialchars($data['jk']) ?>
            </span>

        </div>


        <div class="info-row">

            <span class="info-label">
                Gmail
            </span>

            <span class="info-value">
                <?= htmlspecialchars($data['gmail']) ?>
            </span>

        </div>


        <div class="info-row">

            <span class="info-label">
                Username
            </span>

            <span class="info-value">
                <?= htmlspecialchars($data['username']) ?>
            </span>

        </div>

    </div>

</main>


<footer class="footer">
    © 2026 PerpustakaanKu
</footer>


</body>
</html>