<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

include "koneksi.php";
session_start();

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['status' => 'error', 'message' => 'User tidak login']);
    exit;
}

$data = json_decode(file_get_contents("php://input"), true);

if (!isset($data['result_image'])) {
    echo json_encode(['status' => 'error', 'message' => 'Tidak ada gambar']);
    exit;
}

$user_id = $_SESSION['user_id'];
$template_id = isset($data['template_id']) ? $data['template_id'] : 0;
$result_image = $data['result_image'];

// Hapus base64 header
$result_image = preg_replace('/^data:image\/\w+;base64,/', '', $result_image);
$result_image = str_replace(' ', '+', $result_image);
$result_image = base64_decode($result_image);

if ($result_image === false) {
    echo json_encode(['status' => 'error', 'message' => 'Gagal decode gambar']);
    exit;
}

$upload_dir = "assets/img/template_results/";
if (!file_exists($upload_dir)) {
    mkdir($upload_dir, 0777, true);
}

$filename = $upload_dir . time() . "_result.png";

if (file_put_contents($filename, $result_image)) {
    $filename_escaped = mysqli_real_escape_string($koneksi, $filename);
    $user_id_escaped = mysqli_real_escape_string($koneksi, $user_id);
    
    $query = "INSERT INTO photos (user_id, photo, created_at) VALUES ('$user_id_escaped', '$filename_escaped', NOW())";
    
    if (mysqli_query($koneksi, $query)) {
        echo json_encode(['status' => 'success', 'message' => 'Berhasil disimpan']);
    } else {
        unlink($filename);
        echo json_encode(['status' => 'error', 'message' => 'Database error: ' . mysqli_error($koneksi)]);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'Gagal menyimpan file']);
}

mysqli_close($koneksi);
?>