<?php

include "koneksi.php";

$user = $_GET['user'] ?? "";

if ($user == "") {

    header("Location: index.php");
    exit;

}


$id = isset($_GET['id'])
    ? (int) $_GET['id']
    : 0;


/* =====================================================
   AMBIL DATA BUKU
===================================================== */

$query = mysqli_query(
    $koneksi,
    "SELECT *
     FROM buku
     WHERE id = $id"
);


$buku = mysqli_fetch_assoc($query);


if (!$buku) {

    die("Buku tidak ditemukan.");

}


/* =====================================================
   AMBIL ULASAN BUKU
===================================================== */

$judul_db = mysqli_real_escape_string(
    $koneksi,
    $buku['judul']
);

$penulis_db = mysqli_real_escape_string(
    $koneksi,
    $buku['penulis']
);


$query_ulasan = mysqli_query(
    $koneksi,
    "SELECT *
     FROM ulasan

     WHERE judul = '$judul_db'
     AND penulis = '$penulis_db'

     ORDER BY id DESC"
);


/* =====================================================
   HITUNG RATING
===================================================== */

$query_rating = mysqli_query(
    $koneksi,
    "SELECT

        COUNT(*) AS total_ulasan,

        COALESCE(
            AVG(rating),
            0
        ) AS rata_rating

     FROM ulasan

     WHERE judul = '$judul_db'
     AND penulis = '$penulis_db'"
);


$rating_data = mysqli_fetch_assoc(
    $query_rating
);


$total_ulasan = (int)
    $rating_data['total_ulasan'];


$rata_rating = (float)
    $rating_data['rata_rating'];

?>

<!DOCTYPE html>
<html lang="id">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>

        <?= htmlspecialchars($buku['judul']) ?>

        - PerpustakaanKu

    </title>


    <link
        rel="stylesheet"
        href="css/style.css"
    >

</head>


<body>


<!-- =====================================================
     NAVBAR
===================================================== -->

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


<!-- =====================================================
     CONTENT
===================================================== -->

<main class="container">


    <a
        href="buku.php?user=<?= urlencode($user) ?>"
        class="back-link"
    >
        ← Kembali ke katalog
    </a>


    <!-- =================================================
         DETAIL BUKU
    ================================================== -->

    <section class="book-detail">


        <div class="detail-cover">


            <?php if (!empty($buku['cover'])): ?>

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

            <?php else: ?>

                <div class="no-cover">
                    ✦
                </div>

            <?php endif; ?>


        </div>


        <div class="detail-info">


            <p class="section-label">
                BOOK DETAILS ✦
            </p>


            <h1>
                <?= htmlspecialchars($buku['judul']) ?>
            </h1>


            <p class="detail-author">

                <?= htmlspecialchars($buku['penulis']) ?>

            </p>


            <div class="detail-meta">


                <div>

                    <span>
                        PENERBIT
                    </span>

                    <strong>
                        <?= htmlspecialchars($buku['penerbit']) ?>
                    </strong>

                </div>


                <div>

                    <span>
                        TAHUN TERBIT
                    </span>

                    <strong>
                        <?= htmlspecialchars($buku['tahun_terbit']) ?>
                    </strong>

                </div>


            </div>


            <div class="synopsis">


                <h3>
                    Sinopsis
                </h3>


                <p>

                    <?= nl2br(
                        htmlspecialchars(
                            $buku['sinopsis']
                        )
                    ) ?>

                </p>


            </div>


            <a
                href="buat_ulasan.php?user=<?= urlencode($user) ?>&buku_id=<?= $buku['id'] ?>"
                class="hero-button"
            >

                Buat Ulasan ✦

            </a>


        </div>


    </section>


    <!-- =================================================
         RATING SUMMARY
    ================================================== -->

    <section class="rating-section">


        <div class="rating-heading">

            <p class="section-label">
                READER RATING
            </p>

            <h2>
                Apa kata pembaca?
            </h2>

        </div>


        <div class="rating-summary">


            <div class="rating-average">


                <strong>
                    <?= number_format($rata_rating, 1) ?>
                </strong>


                <div class="big-stars">

                    <?php

                    $rating_bulat = round($rata_rating);

                    echo str_repeat(
                        "★",
                        $rating_bulat
                    );

                    echo str_repeat(
                        "☆",
                        5 - $rating_bulat
                    );

                    ?>

                </div>


                <span>

                    <?= $total_ulasan ?>

                    <?= $total_ulasan == 1
                        ? "ulasan"
                        : "ulasan"
                    ?>

                </span>


            </div>


            <div class="rating-description">


                <?php if ($total_ulasan > 0): ?>

                    <p>
                        Rating rata-rata dari
                        pembaca yang sudah memberikan
                        pendapat mereka tentang buku ini.
                    </p>

                <?php else: ?>

                    <p>
                        Belum ada yang memberikan rating
                        untuk buku ini.
                    </p>

                <?php endif; ?>


            </div>


        </div>


    </section>


    <!-- =================================================
         DAFTAR ULASAN
    ================================================== -->

    <section class="book-reviews">


        <div class="reviews-heading">


            <div>

                <p class="section-label">
                    REVIEWS ✦
                </p>

                <h2>
                    Ulasan Pembaca
                </h2>

            </div>


            <a
                href="buat_ulasan.php?user=<?= urlencode($user) ?>&buku_id=<?= $buku['id'] ?>"
                class="small-review-button"
            >
                + Tulis Ulasan
            </a>


        </div>


        <?php if ($total_ulasan == 0): ?>


            <div class="empty-review">

                <div>
                    ✦
                </div>

                <h3>
                    Belum ada ulasan
                </h3>

                <p>
                    Jadilah pembaca pertama
                    yang memberikan pendapat
                    tentang buku ini.
                </p>


                <a
                    href="buat_ulasan.php?user=<?= urlencode($user) ?>&buku_id=<?= $buku['id'] ?>"
                    class="hero-button"
                >
                    Jadilah yang pertama ✦
                </a>

            </div>


        <?php else: ?>


            <div class="detail-review-list">


                <?php while (
                    $ulasan = mysqli_fetch_assoc(
                        $query_ulasan
                    )
                ): ?>


                    <article class="detail-review-card">


                        <div class="review-card-top">


                            <div>


                                <strong>

                                    @<?= htmlspecialchars(
                                        $ulasan['username']
                                    ) ?>

                                </strong>


                                <span>

                                    <?php

                                    $rating_user =
                                        (int)
                                        $ulasan['rating'];

                                    echo str_repeat(
                                        "★",
                                        $rating_user
                                    );

                                    echo str_repeat(
                                        "☆",
                                        5 - $rating_user
                                    );

                                    ?>

                                </span>


                            </div>


                            <small>
                                Pembaca
                            </small>


                        </div>


                        <p>

                            <?= nl2br(
                                htmlspecialchars(
                                    $ulasan['ulasan']
                                )
                            ) ?>

                        </p>


                    </article>


                <?php endwhile; ?>


            </div>


        <?php endif; ?>


    </section>


</main>


<footer class="footer">

    © 2026 PerpustakaanKu ✦

</footer>


</body>
</html>