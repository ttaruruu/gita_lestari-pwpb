<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

include "koneksi.php";

$user_id = $_SESSION['user_id'];
$message = '';
$message_type = '';

// Get user data
$query = mysqli_query($koneksi, "SELECT * FROM users WHERE id='$user_id'");
$user = mysqli_fetch_assoc($query);

$edit_mode = isset($_GET['edit']) ? true : false;

// Process save all changes (Done button)
if (isset($_POST['save_all'])) {
    $new_username = trim($_POST['username']);
    $new_email = trim($_POST['email']);
    $old_password = $_POST['old_password'];
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];
    
    $has_error = false;
    
    // Validate username
    if (!empty($new_username) && $new_username != $user['username']) {
        $check = mysqli_query($koneksi, "SELECT id FROM users WHERE username='$new_username' AND id != '$user_id'");
        if (mysqli_num_rows($check) > 0) {
            $message = "Username already taken";
            $message_type = "error";
            $has_error = true;
        } else {
            $new_username = mysqli_real_escape_string($koneksi, $new_username);
            mysqli_query($koneksi, "UPDATE users SET username='$new_username' WHERE id='$user_id'");
            $_SESSION['username'] = $new_username;
        }
    }
    
    // Validate email
    if (!empty($new_email) && $new_email != $user['email']) {
        if (!filter_var($new_email, FILTER_VALIDATE_EMAIL)) {
            $message = "Invalid email format";
            $message_type = "error";
            $has_error = true;
        } else {
            $check = mysqli_query($koneksi, "SELECT id FROM users WHERE email='$new_email' AND id != '$user_id'");
            if (mysqli_num_rows($check) > 0) {
                $message = "Email already taken";
                $message_type = "error";
                $has_error = true;
            } else {
                $new_email = mysqli_real_escape_string($koneksi, $new_email);
                mysqli_query($koneksi, "UPDATE users SET email='$new_email' WHERE id='$user_id'");
            }
        }
    }
    
    // Validate password change
    if (!empty($new_password)) {
        if (empty($old_password)) {
            $message = "Enter current password to change password";
            $message_type = "error";
            $has_error = true;
        } elseif ($new_password !== $confirm_password) {
            $message = "New passwords do not match";
            $message_type = "error";
            $has_error = true;
        } elseif (strlen($new_password) < 4) {
            $message = "Password must be at least 4 characters";
            $message_type = "error";
            $has_error = true;
        } else {
            $user_query = mysqli_query($koneksi, "SELECT password FROM users WHERE id='$user_id'");
            $user_data = mysqli_fetch_assoc($user_query);
            
            if (password_verify($old_password, $user_data['password'])) {
                $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
                mysqli_query($koneksi, "UPDATE users SET password='$hashed_password' WHERE id='$user_id'");
            } else {
                $message = "Current password is incorrect";
                $message_type = "error";
                $has_error = true;
            }
        }
    }
    
    if (!$has_error) {
        $message = "Profile saved successfully!";
        $message_type = "success";
    }
    
    // Refresh user data
    $query = mysqli_query($koneksi, "SELECT * FROM users WHERE id='$user_id'");
    $user = mysqli_fetch_assoc($query);
    $edit_mode = false;
}

// Process upload profile photo
if (isset($_POST['upload_photo']) || isset($_FILES['profile_photo'])) {
    if (isset($_FILES['profile_photo']) && $_FILES['profile_photo']['error'] == UPLOAD_ERR_OK) {
        $file = $_FILES['profile_photo']['name'];
        $tmp = $_FILES['profile_photo']['tmp_name'];
        
        $allowed = ['png', 'jpg', 'jpeg', 'PNG', 'JPG', 'JPEG'];
        $ext = strtolower(pathinfo($file, PATHINFO_EXTENSION));
        
        if (in_array($ext, $allowed)) {
            $profile_dir = "assets/img/profiles/";
            if (!file_exists($profile_dir)) {
                mkdir($profile_dir, 0777, true);
            }
            
            // Delete old photo if exists
            if (!empty($user['profile_photo']) && file_exists($user['profile_photo']) && $user['profile_photo'] != 'assets/img/default-avatar.png') {
                unlink($user['profile_photo']);
            }
            
            $new_filename = "user_" . $user_id . "_" . time() . "." . $ext;
            $path = $profile_dir . $new_filename;
            
            if (move_uploaded_file($tmp, $path)) {
                $path_escaped = mysqli_real_escape_string($koneksi, $path);
                mysqli_query($koneksi, "UPDATE users SET profile_photo='$path_escaped' WHERE id='$user_id'");
                $message = "Profile photo uploaded successfully!";
                $message_type = "success";
                header("Location: profile.php");
                exit;
            } else {
                $message = "Failed to upload photo. Check folder permissions.";
                $message_type = "error";
            }
        } else {
            $message = "Only PNG, JPG, JPEG files are allowed";
            $message_type = "error";
        }
    } elseif (isset($_FILES['profile_photo']) && $_FILES['profile_photo']['error'] != UPLOAD_ERR_NO_FILE) {
        $message = "Upload error. Error code: " . $_FILES['profile_photo']['error'];
        $message_type = "error";
    }
}

// Get profile photo
$profile_photo = "assets/img/default-avatar.png";
if (!empty($user['profile_photo']) && file_exists($user['profile_photo'])) {
    $profile_photo = $user['profile_photo'];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile - SweetLens</title>
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

        .container {
            max-width: 900px;
            margin: 0 auto;
        }

        .header-nav {
            display: flex;
            align-items: center;
            gap: 20px;
            margin-bottom: 20px;
        }

        .back-arrow {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 45px;
            height: 45px;
            background: rgba(255, 255, 255, 0.8);
            border-radius: 50%;
            text-decoration: none;
            color: #b13e6b;
            font-size: 28px;
            font-weight: bold;
            transition: 0.2s;
        }

        .back-arrow:hover {
            background: white;
            transform: scale(1.05);
        }

        .profile-card {
            background: rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(10px);
            border-radius: 32px;
            padding: 30px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
        }

        .profile-header {
            text-align: center;
            margin-bottom: 30px;
        }

        .profile-header h1 {
            color: #b13e6b;
            font-size: 28px;
            margin-bottom: 20px;
        }

        .profile-photo {
            position: relative;
            display: inline-block;
        }

        .profile-photo img {
            width: 150px;
            height: 150px;
            border-radius: 50%;
            object-fit: cover;
            border: 5px solid white;
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.15);
        }

        .camera-icon {
            position: absolute;
            bottom: 10px;
            right: 10px;
            background: #ff4d8c;
            width: 40px;
            height: 40px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            color: white;
            font-size: 20px;
            font-weight: bold;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.2);
            transition: 0.2s;
        }

        .camera-icon:hover {
            transform: scale(1.05);
            background: #ff1f6c;
        }

        .profile-section {
            background: rgba(255, 255, 255, 0.6);
            border-radius: 24px;
            padding: 20px;
            margin-bottom: 20px;
        }

        .profile-section h3 {
            color: #b13e6b;
            font-size: 18px;
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 2px solid rgba(255, 179, 207, 0.5);
        }

        .form-group {
            margin-bottom: 15px;
        }

        .form-group label {
            display: block;
            font-size: 13px;
            font-weight: 600;
            color: #b13e6b;
            margin-bottom: 5px;
        }

        .form-group input {
            width: 100%;
            padding: 12px 15px;
            border: 2px solid #ffb3cf;
            border-radius: 16px;
            font-family: 'Poppins', sans-serif;
            outline: none;
            transition: 0.2s;
            background: white;
        }

        .form-group input:focus {
            border-color: #ff4d8c;
        }

        .btn {
            padding: 12px 30px;
            border-radius: 40px;
            font-weight: 700;
            font-size: 16px;
            cursor: pointer;
            border: none;
            font-family: 'Poppins', sans-serif;
            transition: 0.2s;
        }

        .btn-primary {
            background: linear-gradient(135deg, #ff4d8c, #ff1f6c);
            color: white;
        }

        .btn-secondary {
            background: #ffb3cf;
            color: #b13e6b;
        }

        .btn-primary:hover, .btn-secondary:hover {
            transform: scale(1.02);
        }

        .flex-between {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .message {
            margin-bottom: 20px;
            padding: 12px;
            border-radius: 12px;
            text-align: center;
            font-size: 13px;
        }

        .message.success {
            background: #a5d6a5;
            color: #2e5c2e;
        }

        .message.error {
            background: #ffb0b0;
            color: #8b0000;
        }

        .view-text {
            padding: 10px 0;
            color: #333;
            font-size: 15px;
        }

        .edit-field {
            margin-top: 10px;
        }

        .edit-field input {
            width: 100%;
            padding: 12px;
            border: 2px solid #ffb3cf;
            border-radius: 16px;
            font-family: 'Poppins', sans-serif;
        }

        .password-note {
            font-size: 11px;
            color: #999;
            margin-top: 5px;
        }

        hr {
            margin: 20px 0;
            border: none;
            border-top: 1px solid rgba(255, 179, 207, 0.5);
        }

        .danger-zone {
            border: 1px solid #ffb0b0;
        }

        .danger-zone h3 {
            color: #f44336;
        }

        .danger-text {
            font-size: 13px;
            color: #666;
        }

        .danger-small {
            font-size: 11px;
            color: #999;
        }

        .btn-danger {
            background: #f44336;
            color: white;
        }

        .action-buttons {
            display: flex;
            gap: 15px;
            justify-content: flex-end;
            margin-top: 20px;
            padding-top: 20px;
            border-top: 1px solid rgba(255, 179, 207, 0.5);
        }

        .edit-banner {
            background: #ffe0b5;
            padding: 10px 15px;
            border-radius: 12px;
            margin-bottom: 20px;
            text-align: center;
            font-size: 13px;
            color: #b13e6b;
        }

        .view-mode .edit-field {
            display: none;
        }

        .edit-mode .view-text {
            display: none;
        }
    </style>
</head>
<body class="<?= $edit_mode ? 'edit-mode' : 'view-mode' ?>">
    <div class="container">
        <div class="header-nav">
            <a href="home.php" class="back-arrow">←</a>
        </div>

        <div class="profile-card">
            <div class="profile-header">
                <h1>My Profile</h1>
                
                <div class="profile-photo">
                    <img src="<?= $profile_photo ?>" alt="Profile Photo" id="profileImg">
                    <div class="camera-icon" onclick="document.getElementById('photoInput').click()">+</div>
                </div>
                <form method="post" enctype="multipart/form-data" id="photoForm">
                    <input type="file" name="profile_photo" id="photoInput" accept="image/png, image/jpeg, image/jpg" style="display:none;">
                    <button type="submit" name="upload_photo" style="display:none;">Upload</button>
                </form>
            </div>

            <?php if ($message): ?>
                <div class="message <?= $message_type ?>"><?= $message ?></div>
            <?php endif; ?>

            <?php if ($edit_mode): ?>
                <div class="edit-banner">
                    Edit Mode
                </div>
            <?php endif; ?>

            <form method="post" id="profileForm">
                <div class="profile-section">
                    <h3>Username</h3>
                    <div class="view-text">
                        <?= htmlspecialchars($user['username']) ?>
                    </div>
                    <div class="edit-field">
                        <input type="text" name="username" value="<?= htmlspecialchars($user['username']) ?>">
                    </div>
                </div>

                <!-- Email Section -->
                <div class="profile-section">
                    <h3>Email</h3>
                    <div class="view-text">
                        <?= htmlspecialchars($user['email']) ?>
                    </div>
                    <div class="edit-field">
                        <input type="email" name="email" value="<?= htmlspecialchars($user['email']) ?>">
                    </div>
                </div>

                <!-- Change Password Section -->
                <div class="profile-section">
                    <h3>Change Password</h3>
                    <div class="view-text">
                        ********
                    </div>
                    <div class="edit-field">
                        <div class="form-group">
                            <label>Current Password</label>
                            <input type="password" name="old_password" placeholder="Enter current password">
                        </div>
                        <div class="form-group">
                            <label>New Password</label>
                            <input type="password" name="new_password" placeholder="Min 4 characters">
                        </div>
                        <div class="form-group">
                            <label>Confirm New Password</label>
                            <input type="password" name="confirm_password" placeholder="Re-enter new password">
                        </div>
                        <div class="password-note">Leave empty if you don't want to change password</div>
                    </div>
                </div>

                <hr>

                <!-- Danger Zone -->
                <div class="profile-section danger-zone">
                    <h3>Danger Zone</h3>
                    <div class="flex-between">
                        <div>
                            <p class="danger-text">Permanently delete account</p>
                            <p class="danger-small">All photos will be lost and cannot be recovered</p>
                        </div>
                        <button type="button" class="btn btn-danger" onclick="confirmDeleteAccount()">Delete Account</button>
                    </div>
                </div>

                <?php if ($edit_mode): ?>
                    <div class="action-buttons">
                        <button type="submit" name="save_all" class="btn btn-primary">Done</button>
                        <a href="profile.php" class="btn btn-secondary">Cancel</a>
                    </div>
                <?php else: ?>
                    <div class="action-buttons">
                        <a href="profile.php?edit=1" class="btn btn-primary">Edit Profile</a>
                    </div>
                <?php endif; ?>
            </form>
        </div>
    </div>

    <script>
        function confirmDeleteAccount() {
            if (confirm('Are you sure you want to delete your account? All your photos will be lost and cannot be recovered!')) {
                window.location.href = 'delete_account.php';
            }
        }

        // Auto upload photo when file is selected
        document.getElementById('photoInput')?.addEventListener('change', function() {
            if (this.files.length > 0) {
                this.form.submit();
            }
        });
    </script>
</body>
</html>