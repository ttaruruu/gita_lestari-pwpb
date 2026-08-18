<?php

include "koneksi.php";

$user = $_GET['user'] ?? "";

if ($user == "") {

    header("Location: index.php");
    exit;

}


$cari = $_GET['cari'] ?? "";


if ($cari != "") {

    $cari_db = mysqli_real_escape_string(
        $koneksi,
        $cari
    );

    $query = mysqli_query(
        $koneksi,
        "SELECT * FROM ulasan
         WHERE judul LIKE '%$cari_db%'
         OR penulis LIKE '%$cari_db%'
         ORDER BY id DESC"
    );

} else {

    $query = mysqli_query(
        $koneksi,
        "SELECT * FROM ulasan
         ORDER BY id DESC"
    );

}

?>

<!DOCTYPE html>
<html lang="id">

<head>

    <meta charset="UTF-8">

    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">

    <title>Ulasan - PerpustakaanKu</title>

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
            COMMUNITY REVIEWS
        </p>

        <h1>
            Ulasan Buku
        </h1>

        <p>
            Lihat apa yang pembaca lain pikirkan
            tentang buku favorit mereka.
        </p>

    </div>


    <div class="search-box">

        <form
            method="GET"
            class="search-form"
        >

            <input
                type="hidden"
                name="user"
                value="<?= htmlspecialchars($user) ?>"
            >

            <input
                type="text"
                name="cari"
                value="<?= htmlspecialchars($cari) ?>"
                placeholder="Cari judul buku atau penulis..."
            >

            <button type="submit">
                Cari
            </button>

        </form>

    </div>


    <div class="review-list">

        <?php if (mysqli_num_rows($query) == 0): ?>

            <div class="empty">

                <h3>
                    Belum ada ulasan.
                </h3>

                <p>
                    Jadilah orang pertama yang
                    memberikan ulasan!
                </p>

            </div>

        <?php endif; ?>


        <?php while ($data = mysqli_fetch_assoc($query)): ?>

            <article class="review-card">

                <h3>
                    <?= htmlspecialchars($data['judul']) ?>
                </h3>


                <p class="book-author">

                    oleh
                    <strong>
                        <?= htmlspecialchars($data['penulis']) ?>
                    </strong>

                </p>


                <div class="review-stars">

                    <?php

                    $rating = (int) $data['rating'];

                    echo str_repeat("★", $rating);

                    echo str_repeat("☆", 5 - $rating);

                    ?>

                </div>


                <p class="review-text">

                    <?= nl2br(
                        htmlspecialchars($data['ulasan'])
                    ) ?>

                </p>


                <p class="review-user">

                    Ditulis oleh
                    <strong>
                        @<?= htmlspecialchars($data['username']) ?>
                    </strong>

                </p>

            </article>

        <?php endwhile; ?>

    </div>

</main>


<footer class="footer">
    © 2026 PerpustakaanKu
</footer>


</body>
</html>