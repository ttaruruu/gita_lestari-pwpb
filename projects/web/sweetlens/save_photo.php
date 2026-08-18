<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

include "koneksi.php";
session_start();

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['status' => 'error', 'message' => 'User not logged in']);
    exit;
}

$data = json_decode(file_get_contents("php://input"), true);

if (!isset($data['image']) || empty($data['image'])) {
    echo json_encode(['status' => 'error', 'message' => 'No image data']);
    exit;
}

$image = $data['image'];
$shot_index = isset($data['shot_index']) ? $data['shot_index'] : 0;

if (strpos($image, 'data:image/png;base64,') === 0) {
    $image = str_replace('data:image/png;base64,', '', $image);
} elseif (strpos($image, 'data:image/jpeg;base64,') === 0) {
    $image = str_replace('data:image/jpeg;base64,', '', $image);
}

$image = str_replace(' ', '+', $image);
$image = base64_decode($image);

if ($image === false) {
    echo json_encode(['status' => 'error', 'message' => 'Failed to decode image']);
    exit;
}

$upload_dir = "assets/img/uploads/";
if (!file_exists($upload_dir)) {
    mkdir($upload_dir, 0777, true);
}

$filename = $upload_dir . time() . "_" . $shot_index . ".png";

if (file_put_contents($filename, $image)) {
    $user_id = $_SESSION['user_id'];
    $filename_escaped = mysqli_real_escape_string($koneksi, $filename);
    $user_id_escaped = mysqli_real_escape_string($koneksi, $user_id);
    
    $query = "INSERT INTO photos (user_id, photo, created_at) VALUES ('$user_id_escaped', '$filename_escaped', NOW())";
    
    if (mysqli_query($koneksi, $query)) {
        echo json_encode(['status' => 'success', 'message' => 'Photo saved', 'filename' => $filename]);
    } else {
        unlink($filename);
        echo json_encode(['status' => 'error', 'message' => 'Database error: ' . mysqli_error($koneksi)]);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'Failed to save file']);
}

mysqli_close($koneksi);
?>