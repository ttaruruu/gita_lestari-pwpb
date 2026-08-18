<?php

include "koneksi.php";

$error = "";
$success = "";

if (isset($_POST['register'])) {

    $nama = mysqli_real_escape_string(
        $koneksi,
        $_POST['nama']
    );

    $tgl_lahir = mysqli_real_escape_string(
        $koneksi,
        $_POST['tgl_lahir']
    );

    $jk = mysqli_real_escape_string(
        $koneksi,
        $_POST['jk']
    );

    $gmail = mysqli_real_escape_string(
        $koneksi,
        $_POST['gmail']
    );

    $username = mysqli_real_escape_string(
        $koneksi,
        $_POST['username']
    );

    $password = mysqli_real_escape_string(
        $koneksi,
        $_POST['password']
    );

    $konfir = mysqli_real_escape_string(
        $koneksi,
        $_POST['konfir']
    );


    if ($password != $konfir) {

        $error = "Konfirmasi password tidak sama.";

    } else {

        $cek = mysqli_query(
            $koneksi,
            "SELECT * FROM users
             WHERE username='$username'
             OR gmail='$gmail'"
        );


        if (mysqli_num_rows($cek) > 0) {

            $error = "Username atau email sudah digunakan.";

        } else {

            $insert = mysqli_query(
                $koneksi,
                "INSERT INTO users
                (nama, tgl_lahir, jk, gmail, username, password)
                VALUES
                ('$nama', '$tgl_lahir', '$jk', '$gmail',
                 '$username', '$password')"
            );


            if ($insert) {

                $success =
                    "Registrasi berhasil! Silakan login.";

            } else {

                $error =
                    "Registrasi gagal: " .
                    mysqli_error($koneksi);

            }
        }
    }
}

?>

<!DOCTYPE html>
<html lang="id">

<head>

    <meta charset="UTF-8">

    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">

    <title>Register - PerpustakaanKu</title>

    <link rel="stylesheet"
          href="css/style.css">

</head>

<body class="auth-page">

<div class="auth-box register-box">

    <div class="auth-logo">
        PerpustakaanKu
    </div>

    <p class="auth-subtitle">
        Buat akun untuk mulai berbagi ulasan buku.
    </p>


    <?php if ($error != ""): ?>

        <div class="alert alert-error">
            <?= htmlspecialchars($error) ?>
        </div>

    <?php endif; ?>


    <?php if ($success != ""): ?>

        <div class="alert alert-success">

            <?= htmlspecialchars($success) ?>

            <br><br>

            <a href="index.php">
                Login sekarang
            </a>

        </div>

    <?php endif; ?>


    <form method="POST">

        <div class="form-group">

            <label>
                Nama Lengkap
            </label>

            <input
                type="text"
                name="nama"
                placeholder="Nama lengkap"
                required
            >

        </div>


        <div class="form-group">

            <label>
                Tanggal Lahir
            </label>

            <input
                type="date"
                name="tgl_lahir"
                required
            >

        </div>


        <div class="form-group">

            <label>
                Jenis Kelamin
            </label>

            <select name="jk" required>

                <option value="">
                    Pilih jenis kelamin
                </option>

                <option value="Laki-laki">
                    Laki-laki
                </option>

                <option value="Perempuan">
                    Perempuan
                </option>

            </select>

        </div>


        <div class="form-group">

            <label>
                Gmail
            </label>

            <input
                type="email"
                name="gmail"
                placeholder="contoh@gmail.com"
                required
            >

        </div>


        <div class="form-group">

            <label>
                Username
            </label>

            <input
                type="text"
                name="username"
                placeholder="Buat username"
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
                placeholder="Buat password"
                required
            >

        </div>


        <div class="form-group">

            <label>
                Konfirmasi Password
            </label>

            <input
                type="password"
                name="konfir"
                placeholder="Ulangi password"
                required
            >

        </div>


        <button
            type="submit"
            name="register"
            class="btn"
        >
            Daftar
        </button>

    </form>


    <div class="auth-link">

        Sudah punya akun?

        <a href="index.php">
            Login
        </a>

    </div>

</div>

</body>
</html>