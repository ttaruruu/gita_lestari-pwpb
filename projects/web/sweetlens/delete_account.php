<?php
session_start();
include "koneksi.php";

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];

$photos = mysqli_query($koneksi, "SELECT photo FROM photos WHERE user_id='$user_id'");
while ($photo = mysqli_fetch_assoc($photos)) {
    if (file_exists($photo['photo'])) {
        unlink($photo['photo']);
    }
}

$results = mysqli_query($koneksi, "SELECT result_image FROM template_results WHERE user_id='$user_id'");
while ($result = mysqli_fetch_assoc($results)) {
    if (file_exists($result['result_image'])) {
        unlink($result['result_image']);
    }
}

mysqli_query($koneksi, "DELETE FROM photos WHERE user_id='$user_id'");
mysqli_query($koneksi, "DELETE FROM template_results WHERE user_id='$user_id'");
mysqli_query($koneksi, "DELETE FROM users WHERE id='$user_id'");

session_destroy();
header("Location: signup.php");
exit;
?>