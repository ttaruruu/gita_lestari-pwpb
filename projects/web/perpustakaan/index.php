<?php

include "koneksi.php";

$error = "";

if (isset($_POST['login'])) {

    $username = mysqli_real_escape_string(
        $koneksi,
        $_POST['username']
    );

    $password = mysqli_real_escape_string(
        $koneksi,
        $_POST['password']
    );

    $query = mysqli_query(
        $koneksi,
        "SELECT * FROM users
         WHERE username='$username'
         AND password='$password'"
    );

    if (mysqli_num_rows($query) > 0) {

        header(
            "Location: beranda.php?user=" .
            urlencode($username)
        );

        exit;

    } else {

        $error = "Username atau password salah.";

    }
}

?>

<!DOCTYPE html>
<html lang="id">

<head>

    <meta charset="UTF-8">

    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">

    <title>Login - PerpustakaanKu</title>

    <link rel="stylesheet"
          href="css/style.css">

</head>

<body class="auth-page">

<div class="auth-box">

    <div class="auth-logo">
        PerpustakaanKu
    </div>

    <p class="auth-subtitle">
        Selamat datang kembali, pembaca.
    </p>


    <?php if ($error != ""): ?>

        <div class="alert alert-error">
            <?= htmlspecialchars($error) ?>
        </div>

    <?php endif; ?>


    <form method="POST">

        <div class="form-group">

            <label>
                Username
            </label>

            <input
                type="text"
                name="username"
                placeholder="Masukkan username"
                required
            >

        </div>


        <div class="form-group">

            <label>
                Password
            </label>

            <input
                type="password"
                name="password"
                placeholder="Masukkan password"
                required
            >

        </div>


        <button
            type="submit"
            name="login"
            class="btn"
        >
            Login
        </button>

    </form>


    <div class="auth-link">

        Belum punya akun?

        <a href="register.php">
            Daftar sekarang
        </a>

    </div>

</div>

</body>
</html>