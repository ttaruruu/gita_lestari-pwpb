<?php
session_start();
include "koneksi.php";

// Cek login (opsional, tambahkan jika ingin admin only)
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$message = '';
$message_type = '';

// Buat folder jika belum ada
$template_dir = "assets/templates/";
if (!file_exists($template_dir)) {
    mkdir($template_dir, 0777, true);
}

if (isset($_POST['upload'])) {
    $name = mysqli_real_escape_string($koneksi, trim($_POST['name']));
    $file = $_FILES['template']['name'];
    $tmp = $_FILES['template']['tmp_name'];
    $error = $_FILES['template']['error'];
    
    // Validasi nama template
    if (empty($name)) {
        $message = "Template name is required";
        $message_type = "error";
    }
    // Validasi file
    elseif ($error == UPLOAD_ERR_NO_FILE) {
        $message = "Please select a file to upload";
        $message_type = "error";
    }
    elseif ($error != UPLOAD_ERR_OK) {
        $message = "Upload error occurred. Error code: " . $error;
        $message_type = "error";
    }
    else {
        $allowed = ['png', 'jpg', 'jpeg', 'PNG', 'JPG', 'JPEG'];
        $ext = strtolower(pathinfo($file, PATHINFO_EXTENSION));
        
        if (in_array($ext, $allowed)) {
            // Buat nama file unik
            $new_filename = time() . "_" . preg_replace('/[^a-zA-Z0-9.]/', '_', $file);
            $path = $template_dir . $new_filename;
            
            // Coba upload
            if (move_uploaded_file($tmp, $path)) {
                $query = "INSERT INTO templates (name, image) VALUES ('$name', '$new_filename')";
                if (mysqli_query($koneksi, $query)) {
                    $message = "Template uploaded successfully!";
                    $message_type = "success";
                } else {
                    // Hapus file jika insert gagal
                    if (file_exists($path)) {
                        unlink($path);
                    }
                    $message = "Database error: " . mysqli_error($koneksi);
                    $message_type = "error";
                }
            } else {
                $message = "Failed to upload file. Check folder permissions.";
                $message_type = "error";
            }
        } else {
            $message = "Only PNG, JPG, JPEG files allowed. Your file: ." . $ext;
            $message_type = "error";
        }
    }
}

// Ambil semua template
$templates = mysqli_query($koneksi, "SELECT * FROM templates ORDER BY id DESC");
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - Upload Template</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(145deg, #ffc0d9 0%, #ffe3ee 100%);
            min-height: 100vh;
            padding: 40px 20px;
        }

        @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap');

        .container {
            max-width: 1000px;
            margin: 0 auto;
        }

        .card {
            background: rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(10px);
            border-radius: 32px;
            padding: 30px;
            margin-bottom: 30px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
        }

        h1 {
            color: #b13e6b;
            font-size: 28px;
            margin-bottom: 25px;
            text-align: center;
        }

        h2 {
            color: #b13e6b;
            font-size: 20px;
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 2px solid rgba(255, 179, 207, 0.5);
        }

        label {
            display: block;
            font-weight: 600;
            color: #b13e6b;
            margin-bottom: 8px;
            font-size: 14px;
        }

        input[type="text"], input[type="file"] {
            width: 100%;
            padding: 12px 15px;
            margin-bottom: 20px;
            border: 2px solid #ffb3cf;
            border-radius: 16px;
            font-family: 'Poppins', sans-serif;
            outline: none;
            transition: 0.2s;
            background: white;
        }

        input[type="text"]:focus, input[type="file"]:focus {
            border-color: #ff4d8c;
            box-shadow: 0 0 0 3px rgba(255, 77, 140, 0.1);
        }

        button {
            background: linear-gradient(135deg, #ff4d8c, #ff1f6c);
            color: white;
            border: none;
            padding: 12px 30px;
            border-radius: 40px;
            font-weight: 700;
            font-size: 16px;
            cursor: pointer;
            width: 100%;
            transition: 0.2s;
            font-family: 'Poppins', sans-serif;
        }

        button:hover {
            transform: scale(1.02);
            box-shadow: 0 5px 15px rgba(255, 77, 140, 0.3);
        }

        .success {
            background: #a5d6a5;
            color: #2e5c2e;
            padding: 12px;
            border-radius: 12px;
            margin-bottom: 20px;
            text-align: center;
        }

        .error {
            background: #ffb0b0;
            color: #8b0000;
            padding: 12px;
            border-radius: 12px;
            margin-bottom: 20px;
            text-align: center;
        }

        .template-list {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(180px, 1fr));
            gap: 25px;
        }

        .template-item {
            background: white;
            border-radius: 20px;
            overflow: hidden;
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.1);
            transition: 0.3s;
        }

        .template-item:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 30px rgba(0, 0, 0, 0.15);
        }

        .template-item img {
            width: 100%;
            height: 160px;
            object-fit: cover;
        }

        .template-item .info {
            padding: 12px;
            text-align: center;
        }

        .template-item .name {
            font-weight: 600;
            color: #b13e6b;
            font-size: 13px;
            margin-bottom: 5px;
        }

        .template-item .delete-btn {
            background: #ff6b6b;
            color: white;
            border: none;
            padding: 5px 12px;
            border-radius: 20px;
            cursor: pointer;
            font-size: 11px;
            margin-top: 8px;
            width: auto;
        }

        .template-item .delete-btn:hover {
            background: #ff4444;
            transform: none;
        }

        .back-link {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            margin-bottom: 20px;
            color: #b13e6b;
            text-decoration: none;
            font-weight: 600;
            background: rgba(255, 255, 255, 0.8);
            padding: 8px 20px;
            border-radius: 30px;
            transition: 0.2s;
        }

        .back-link:hover {
            background: white;
            transform: translateX(-5px);
        }

        .empty-templates {
            text-align: center;
            padding: 40px;
            color: #b13e6b;
        }

        .info-text {
            font-size: 12px;
            color: #999;
            margin-top: 10px;
            text-align: center;
        }
    </style>
</head>
<body>
    <div class="container">
        <a href="home.php" class="back-link">← Back to Home</a>
        
        <div class="card">
            <h1>Upload New Template</h1>
            
            <?php if ($message): ?>
                <div class="<?= $message_type ?>"><?= $message ?></div>
            <?php endif; ?>
            
            <form method="post" enctype="multipart/form-data">
                <label>Template Name</label>
                <input type="text" name="name" placeholder="Example: Birthday Frame, Wedding Border" required>
                
                <label>Template Image (PNG/JPG)</label>
                <input type="file" name="template" accept="image/png, image/jpeg, image/jpg" required>
                
                <button type="submit" name="upload">Upload Template</button>
            </form>
            <div class="info-text">Recommended template size: 800x600px or similar ratio. The template should have empty slots/areas for photos.</div>
        </div>

        <div class="card">
            <h2>Existing Templates</h2>
            <?php if (mysqli_num_rows($templates) > 0): ?>
                <div class="template-list">
                    <?php while ($t = mysqli_fetch_assoc($templates)): ?>
                        <div class="template-item">
                            <img src="assets/templates/<?= htmlspecialchars($t['image']) ?>" alt="<?= htmlspecialchars($t['name']) ?>">
                            <div class="info">
                                <div class="name"><?= htmlspecialchars($t['name']) ?></div>
                                <button class="delete-btn" onclick="deleteTemplate(<?= $t['id'] ?>, '<?= htmlspecialchars($t['image']) ?>')">Delete</button>
                            </div>
                        </div>
                    <?php endwhile; ?>
                </div>
            <?php else: ?>
                <div class="empty-templates">
                    <p>No templates yet. Upload your first template above.</p>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <script>
        function deleteTemplate(id, filename) {
            if (confirm('Are you sure you want to delete this template?')) {
                window.location.href = 'delete_template.php?id=' + id + '&file=' + encodeURIComponent(filename);
            }
        }
    </script>
</body>
</html>