<?php
include "koneksi.php";

$user = isset($_GET['user']) ? $_GET['user'] : "";

$cari = isset($_GET['cari']) ? $_GET['cari'] : "";

if ($cari != "") {
    $cari_aman = mysqli_real_escape_string($koneksi, $cari);

    $query = mysqli_query(
        $koneksi,
        "SELECT * FROM buku
         WHERE judul LIKE '%$cari_aman%'
         OR penulis LIKE '%$cari_aman%'
         ORDER BY judul ASC"
    );
} else {
    $query = mysqli_query(
        $koneksi,
        "SELECT * FROM buku ORDER BY id DESC"
    );
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">

    <title>Katalog Buku - PerpustakaanKu</title>

    <link rel="stylesheet" href="css/style.css">
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

        <h1>Katalog Buku</h1>

        <p>
            Pilih buku yang ingin kamu baca dan ulas.
        </p>

    </div>


    <div class="search-box">

        <form method="GET" class="search-form">

            <input
                type="hidden"
                name="user"
                value="<?= htmlspecialchars($user) ?>"
            >

            <input
                type="text"
                name="cari"
                placeholder="Cari judul atau penulis..."
                value="<?= htmlspecialchars($cari) ?>"
            >

            <button type="submit">
                CARI
            </button>

        </form>

    </div>


    <div class="book-grid">

        <?php if (mysqli_num_rows($query) > 0): ?>

            <?php while ($buku = mysqli_fetch_assoc($query)): ?>

                <a
                    href="detail_buku.php?id=<?= $buku['id'] ?>&user=<?= urlencode($user) ?>"
                    class="book-card"
                >

                    <div class="book-cover">

                        <?php if (!empty($buku['cover'])): ?>

                            <img
    src="assets/books/<?= htmlspecialchars($buku['cover']) ?>"
    alt="<?= htmlspecialchars($buku['judul']) ?>"
>

                        <?php else: ?>

                            <div class="no-cover">
                                ✦
                            </div>

                        <?php endif; ?>

                    </div>


                    <div class="book-info">

                        <p class="book-label">
                            BOOK
                        </p>

                        <h2>
                            <?= htmlspecialchars($buku['judul']) ?>
                        </h2>

                        <p>
                            <?= htmlspecialchars($buku['penulis']) ?>
                        </p>

                    </div>

                </a>

            <?php endwhile; ?>

        <?php else: ?>

            <div class="empty">

                <h3>Buku tidak ditemukan ✦</h3>

                <p>
                    Coba cari dengan judul atau nama penulis lain.
                </p>

            </div>

        <?php endif; ?>

    </div>

</main>

</body>
</html>