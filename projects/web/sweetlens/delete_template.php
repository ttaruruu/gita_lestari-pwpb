<?php
session_start();
include "koneksi.php";

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

if (isset($_GET['id']) && isset($_GET['file'])) {
    $id = mysqli_real_escape_string($koneksi, $_GET['id']);
    $file = $_GET['file'];
    
    // Hapus file dari folder
    $filepath = "assets/templates/" . $file;
    if (file_exists($filepath)) {
        unlink($filepath);
    }
    
    // Hapus dari database
    mysqli_query($koneksi, "DELETE FROM templates WHERE id='$id'");
    
    header("Location: admin_upload_template.php?deleted=1");
    exit;
}

header("Location: admin_upload_template.php");
exit;
?>