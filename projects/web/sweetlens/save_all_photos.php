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

if (!isset($data['photos']) || empty($data['photos'])) {
    echo json_encode(['status' => 'error', 'message' => 'Tidak ada foto']);
    exit;
}

$user_id = $_SESSION['user_id'];
$photos = $data['photos'];
$success_count = 0;
$saved_files = [];

$upload_dir = "assets/img/uploads/";
if (!file_exists($upload_dir)) {
    mkdir($upload_dir, 0777, true);
}

foreach ($photos as $photo) {
    $image = $photo['data'];
    $index = $photo['index'];
    
    // Hapus prefix base64
    $image = str_replace('data:image/png;base64,', '', $image);
    $image = str_replace(' ', '+', $image);
    $image = base64_decode($image);
    
    if ($image) {
        $filename = $upload_dir . time() . "_" . $index . ".png";
        
        if (file_put_contents($filename, $image)) {
            $filename_escaped = mysqli_real_escape_string($koneksi, $filename);
            $user_id_escaped = mysqli_real_escape_string($koneksi, $user_id);
            
            // Query dengan kolom yang benar: photo
            $query = "INSERT INTO photos (user_id, photo, created_at) VALUES ('$user_id_escaped', '$filename_escaped', NOW())";
            
            if (mysqli_query($koneksi, $query)) {
                $success_count++;
                $saved_files[] = $filename;
            } else {
                // Hapus file jika insert gagal
                unlink($filename);
            }
        }
    }
}

echo json_encode([
    'status' => 'success',
    'message' => $success_count . ' dari ' . count($photos) . ' foto berhasil disimpan',
    'saved_files' => $saved_files
]);

mysqli_close($koneksi);
?>