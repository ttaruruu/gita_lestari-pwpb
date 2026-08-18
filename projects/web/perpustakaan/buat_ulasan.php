<?php

include "koneksi.php";

$user = $_GET['user'] ?? "";

if ($user == "") {
    header("Location: index.php");
    exit;
}


/* =====================================================
   PENCARIAN BUKU
   AJAX langsung ke file ini sendiri
===================================================== */

if (isset($_GET['ajax']) && $_GET['ajax'] == "cari") {

    $cari = $_GET['cari'] ?? "";

    $cari = mysqli_real_escape_string(
        $koneksi,
        $cari
    );


    if ($cari == "") {
        exit;
    }


    $query = mysqli_query(
        $koneksi,
        "SELECT *
         FROM buku
         WHERE judul LIKE '%$cari%'
         OR penulis LIKE '%$cari%'
         ORDER BY judul ASC
         LIMIT 6"
    );


    if (!$query) {

        echo '
            <div class="search-empty">
                Error database: ' .
                htmlspecialchars(mysqli_error($koneksi)) .
            '</div>
        ';

        exit;
    }


    if (mysqli_num_rows($query) == 0) {

        echo '
            <div class="search-empty">
                Buku tidak ditemukan ✦
            </div>
        ';

        exit;
    }


    while ($buku_cari = mysqli_fetch_assoc($query)) {

    $id = (int) $buku_cari['id'];

    $judul = htmlspecialchars($buku_cari['judul']);
    $penulis = htmlspecialchars($buku_cari['penulis']);
    $cover = htmlspecialchars($buku_cari['cover']);

    echo '

    <a
        href="buat_ulasan.php?user=' .
        urlencode($user) .
        '&buku_id=' . $id . '"
        class="book-search-item"
    >

        <div class="search-book-cover">

            ' . (
                !empty($cover)
                ? '<img
                    src="assets/books/' . $cover . '"
                    alt="' . $judul . '"
                >'
                : '<div class="no-cover">✦</div>'
            ) . '

        </div>

        <div class="search-book-info">

            <strong>' .
                $judul .
            '</strong>

            <span>' .
                $penulis .
            '</span>

        </div>

    </a>

    ';
}

    exit;
}


/* =====================================================
   BUKU YANG DIPILIH
===================================================== */

$buku_id = isset($_GET['buku_id'])
    ? (int) $_GET['buku_id']
    : 0;


$buku = null;


if ($buku_id > 0) {

    $query_buku = mysqli_query(
        $koneksi,
        "SELECT *
         FROM buku
         WHERE id = $buku_id"
    );


    if ($query_buku) {

        $buku = mysqli_fetch_assoc(
            $query_buku
        );

    }

}


/* =====================================================
   PROSES SUBMIT ULASAN
===================================================== */

$error = "";
$success = "";


if (isset($_POST['submit'])) {

    $buku_id_post = (int) $_POST['buku_id'];

    $ulasan = mysqli_real_escape_string(
        $koneksi,
        $_POST['ulasan']
    );

    $rating = (int) $_POST['rating'];


    $query_buku_post = mysqli_query(
        $koneksi,
        "SELECT *
         FROM buku
         WHERE id = $buku_id_post"
    );


    $buku_post = mysqli_fetch_assoc(
        $query_buku_post
    );


    if (!$buku_post) {

        $error =
            "Buku yang dipilih tidak ditemukan.";

    }

    elseif ($rating < 1 || $rating > 5) {

        $error =
            "Silakan pilih rating 1 sampai 5.";

    }

    elseif ($ulasan == "") {

        $error =
            "Ulasan tidak boleh kosong.";

    }

    else {

        $judul = mysqli_real_escape_string(
            $koneksi,
            $buku_post['judul']
        );


        $penulis = mysqli_real_escape_string(
            $koneksi,
            $buku_post['penulis']
        );


        $insert = mysqli_query(
            $koneksi,
            "INSERT INTO ulasan
            (
                judul,
                penulis,
                ulasan,
                rating,
                username
            )
            VALUES
            (
                '$judul',
                '$penulis',
                '$ulasan',
                '$rating',
                '$user'
            )"
        );


        if ($insert) {

            $success =
                "Ulasan berhasil ditambahkan!";

        }

        else {

            $error =
                "Ulasan gagal ditambahkan: " .
                mysqli_error($koneksi);

        }

    }

}

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
        Buat Ulasan - PerpustakaanKu
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


    <div class="page-title">

        <p class="section-label">
            SHARE YOUR THOUGHTS ✦
        </p>


        <h1>
            Buat Ulasan
        </h1>


        <p>
            Pilih buku yang ingin kamu ceritakan.
        </p>

    </div>



    <div class="review-form">


        <!-- ERROR -->

        <?php if ($error != ""): ?>

            <div class="alert alert-error">

                <?= htmlspecialchars($error) ?>

            </div>

        <?php endif; ?>



        <!-- SUCCESS -->

        <?php if ($success != ""): ?>

            <div class="alert alert-success">

                <?= htmlspecialchars($success) ?>


                <br><br>


                <a
                    href="ulasan.php?user=<?= urlencode($user) ?>"
                >
                    Lihat semua ulasan →
                </a>

            </div>

        <?php endif; ?>



        <?php if ($success == ""): ?>


        <!-- =================================================
             SEARCH BUKU
        ================================================== -->

        <?php if (!$buku): ?>

            <div class="form-group">


                <label>
                    BUKU APA YANG INGIN KAMU ULAS?
                </label>


                <input
                    type="text"
                    id="searchBuku"
                    placeholder="Ketik judul buku..."
                    autocomplete="off"
                >


                <!-- HASIL PENCARIAN -->

                <div
                    id="hasilBuku"
                    class="book-search-results"
                ></div>


            </div>



            <div class="choose-book">


                <div class="choose-book-star">
                    ✦
                </div>


                <h3>
                    Pilih bukumu dulu
                </h3>


                <p>
                    Ketik judul buku di atas untuk
                    menemukan buku yang tersedia.
                </p>


            </div>

        <?php endif; ?>



        <!-- =================================================
             BUKU TERPILIH
        ================================================== -->

        <?php if ($buku): ?>


            <div class="selected-book">


                <div class="selected-book-cover">


                    <?php if (!empty($buku['cover'])): ?>

                        <img src="assets/books/<?= htmlspecialchars($buku['cover']) ?>">

                    <?php endif; ?>


                </div>



                <div class="selected-book-info">


                    <p class="book-label">
                        BUKU YANG DIPILIH ✦
                    </p>


                    <h2>
                        <?= htmlspecialchars($buku['judul']) ?>
                    </h2>


                    <p>
                        <?= htmlspecialchars($buku['penulis']) ?>
                    </p>


                    <a
                        href="buat_ulasan.php?user=<?= urlencode($user) ?>"
                        class="change-book"
                    >
                        ← Ganti buku
                    </a>


                </div>


            </div>



            <!-- =================================================
                 FORM ULASAN
            ================================================== -->

            <form method="POST">


                <input
                    type="hidden"
                    name="buku_id"
                    value="<?= $buku['id'] ?>"
                >



                <div class="form-group">


                    <label>
                        ULASAN
                    </label>


                    <textarea
                        name="ulasan"
                        placeholder="Ceritakan pendapatmu tentang buku ini..."
                        required
                    ></textarea>


                </div>



                <!-- RATING -->

                <div class="rating">


                    <span class="rating-title">
                        RATING
                    </span>


                    <div class="stars">


                        <input
                            type="radio"
                            id="star5"
                            name="rating"
                            value="5"
                        >

                        <label for="star5">
                            ★
                        </label>



                        <input
                            type="radio"
                            id="star4"
                            name="rating"
                            value="4"
                        >

                        <label for="star4">
                            ★
                        </label>



                        <input
                            type="radio"
                            id="star3"
                            name="rating"
                            value="3"
                        >

                        <label for="star3">
                            ★
                        </label>



                        <input
                            type="radio"
                            id="star2"
                            name="rating"
                            value="2"
                        >

                        <label for="star2">
                            ★
                        </label>



                        <input
                            type="radio"
                            id="star1"
                            name="rating"
                            value="1"
                        >

                        <label for="star1">
                            ★
                        </label>


                    </div>


                </div>



                <button
                    type="submit"
                    name="submit"
                    class="btn"
                >
                    KIRIM ULASAN ✦
                </button>


            </form>


        <?php endif; ?>


        <?php endif; ?>


    </div>


</main>



<footer class="footer">

    © 2026 PerpustakaanKu ✦

</footer>



<!-- =====================================================
     SEARCH JAVASCRIPT
===================================================== -->

<script>

const searchInput =
    document.getElementById("searchBuku");


const hasilBuku =
    document.getElementById("hasilBuku");


if (searchInput) {

    searchInput.addEventListener(
        "input",
        function () {


            const keyword =
                this.value.trim();


            if (keyword.length === 0) {

                hasilBuku.innerHTML = "";

                return;

            }


            fetch(
                "buat_ulasan.php?ajax=cari&user=<?= urlencode($user) ?>&cari="
                + encodeURIComponent(keyword)
            )


            .then(function(response) {

                return response.text();

            })


            .then(function(data) {

                hasilBuku.innerHTML = data;

            })


            .catch(function(error) {

                hasilBuku.innerHTML =
                    '<div class="search-empty">' +
                    'Pencarian gagal ✦' +
                    '</div>';

            });


        }
    );

}

</script>


</body>

</html>