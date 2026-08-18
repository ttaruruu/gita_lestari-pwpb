<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}
include "koneksi.php";

$user_id = $_SESSION['user_id'];

// Ambil data user untuk foto profil
$user_query = mysqli_query($koneksi, "SELECT * FROM users WHERE id='$user_id'");
$user_data = mysqli_fetch_assoc($user_query);

$profile_photo = "assets/img/default-avatar.png";
if (!empty($user_data['profile_photo']) && file_exists($user_data['profile_photo'])) {
    $profile_photo = $user_data['profile_photo'];
}

$query = mysqli_query($koneksi, "SELECT * FROM templates ORDER BY id DESC");
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Templates - SweetLens</title>
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
    }

    @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap');

    .side-menu {
        position: fixed;
        top: 0;
        left: -280px;
        width: 280px;
        height: 100vh;
        background: linear-gradient(135deg, #ffb3cf, #ff9ec2);
        padding-top: 90px;
        transition: 0.3s cubic-bezier(0.2, 0.9, 0.4, 1.1);
        z-index: 1000;
        box-shadow: 4px 0 20px rgba(0, 0, 0, 0.2);
        border-radius: 0 24px 24px 0;
    }

    .side-menu.active {
        left: 0;
    }

    .side-menu a {
        display: block;
        padding: 16px 32px;
        color: white;
        text-decoration: none;
        font-weight: 600;
        font-size: 16px;
        transition: 0.2s;
        border-bottom: 1px solid rgba(255, 255, 255, 0.15);
    }

    .side-menu a:hover {
        background: rgba(255, 255, 255, 0.2);
        padding-left: 40px;
    }

    .side-menu a.active {
        background: rgba(255, 255, 255, 0.25);
        border-left: 4px solid white;
    }

    .overlay {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        display: none;
        background: rgba(0, 0, 0, 0.4);
        backdrop-filter: blur(3px);
        z-index: 999;
    }

    .overlay.active {
        display: block;
    }

    .main-content {
        padding: 20px 30px;
    }

    .header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        background: rgba(255, 255, 255, 0.85);
        backdrop-filter: blur(10px);
        padding: 15px 25px;
        border-radius: 20px;
        margin-bottom: 30px;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.08);
    }

    .header-left {
        display: flex;
        align-items: center;
        gap: 15px;
    }

    .header-left h2 {
        color: #b13e6b;
        font-size: 20px;
        font-weight: 600;
    }

    .menu-btn {
        font-size: 26px;
        cursor: pointer;
        background: white;
        width: 42px;
        height: 42px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 50%;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        color: #ff7eb9;
    }

    .header-right {
        display: flex;
        align-items: center;
        gap: 20px;
    }

    .profile-icon {
        width: 42px;
        height: 42px;
        cursor: pointer;
        background: white;
        border-radius: 50%;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        overflow: hidden;
    }

    .profile-icon img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .template-container {
        background: rgba(255, 255, 255, 0.5);
        backdrop-filter: blur(5px);
        border-radius: 24px;
        padding: 25px;
    }

    .template-title {
        color: #b13e6b;
        font-size: 20px;
        font-weight: 600;
        margin-bottom: 20px;
        padding-bottom: 10px;
        border-bottom: 2px solid rgba(255, 255, 255, 0.5);
    }

    .template-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
        gap: 30px;
    }

    .template-card {
        background: white;
        border-radius: 20px;
        overflow: hidden;
        box-shadow: 0 8px 20px rgba(0, 0, 0, 0.1);
        transition: 0.3s;
        text-decoration: none;
        display: block;
    }

    .template-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 15px 30px rgba(0, 0, 0, 0.15);
    }

    .template-card img {
        width: 100%;
        height: 200px;
        object-fit: cover;
    }

    .template-card .info {
        padding: 12px;
        text-align: center;
    }

    .template-card .name {
        color: #b13e6b;
        font-weight: 600;
        font-size: 14px;
    }

    .empty-templates {
        text-align: center;
        padding: 60px;
        color: #b13e6b;
    }

    .profile-sidebar {
        position: fixed;
        top: 0;
        right: -340px;
        width: 320px;
        height: 100vh;
        background: white;
        box-shadow: -4px 0 20px rgba(0, 0, 0, 0.15);
        transition: 0.3s cubic-bezier(0.2, 0.9, 0.4, 1.1);
        z-index: 1001;
        display: flex;
        flex-direction: column;
    }

    .profile-sidebar.active {
        right: 0;
    }

    .profile-header {
        padding: 20px;
        background: linear-gradient(135deg, #ffb3cf, #ff9ec2);
        color: white;
        display: flex;
        align-items: center;
        gap: 15px;
    }

    .profile-header button {
        background: rgba(255, 255, 255, 0.2);
        border: none;
        color: white;
        font-size: 22px;
        cursor: pointer;
        width: 38px;
        height: 38px;
        border-radius: 50%;
    }

    .profile-header h3 {
        font-size: 20px;
    }

    .profile-info {
        text-align: center;
        padding: 30px;
        border-bottom: 1px solid #eee;
    }

    .avatar {
        width: 100px;
        height: 100px;
        margin: 0 auto 12px;
        border-radius: 50%;
        overflow: hidden;
    }

    .avatar img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .profile-info p {
        font-size: 18px;
        color: #333;
        font-weight: 600;
    }

    .profile-info small {
        color: #999;
        font-size: 12px;
    }

    .profile-menu {
        padding: 20px;
    }

    .profile-menu a {
        display: block;
        padding: 12px 18px;
        color: #666;
        text-decoration: none;
        border-radius: 12px;
        transition: 0.2s;
        margin-bottom: 5px;
    }

    .profile-menu a:hover {
        background: #f5f5f5;
        color: #ff4d8c;
    }

    .profile-menu a.danger {
        color: #f44336;
    }

    @media (max-width: 768px) {
        .main-content {
            padding: 15px;
        }

        .header {
            flex-direction: column;
            gap: 15px;
        }

        .template-grid {
            grid-template-columns: repeat(auto-fill, minmax(150px, 1fr));
            gap: 20px;
        }

        .template-card img {
            height: 150px;
        }
    }
    </style>
</head>

<body>

    <div class="side-menu" id="sideMenu">
        <a href="home.php">Gallery</a>
        <a href="photobooth.php">Camera</a>
        <a href="template.php" class="active">Template</a>
    </div>

    <div class="overlay" id="overlay" onclick="toggleMenu()"></div>

    <div class="main-content">
        <div class="header">
            <div class="header-left">
                <div class="menu-btn" onclick="toggleMenu()">☰</div>
                <h2>Choose Template</h2>
            </div>
            <div class="header-right">
                <div class="profile-icon" onclick="toggleProfile()">
                    <img src="<?= $profile_photo ?>" alt="Profile">
                </div>
            </div>
        </div>

        <div class="template-container">
            <div class="template-title">Available Templates</div>
            <div class="template-grid">
                <?php if (mysqli_num_rows($query) > 0): ?>
                <?php while ($template = mysqli_fetch_assoc($query)): ?>
                <a href="use_template.php?id=<?= $template['id'] ?>" class="template-card">
                    <img src="assets/templates/<?= htmlspecialchars($template['image']) ?>"
                        alt="<?= htmlspecialchars($template['name']) ?>">
                    <div class="info">
                        <div class="name"><?= htmlspecialchars($template['name']) ?></div>
                    </div>
                </a>
                <?php endwhile; ?>
                <?php else: ?>
                <div class="empty-templates">
                    <p>No templates available yet.</p>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <div class="profile-sidebar" id="profileSidebar">
        <div class="profile-header">
            <button onclick="toggleProfile()">←</button>
            <h3>My Profile</h3>
        </div>
        <div class="profile-info">
            <div class="avatar">
                <img src="<?= $profile_photo ?>" alt="Profile Photo">
            </div>
            <p><?= htmlspecialchars($_SESSION['username']) ?></p>
            <small><?= htmlspecialchars($user_data['email']) ?></small>
        </div>
        <div class="profile-menu">
            <a href="profile.php">Edit Profile</a>
            <a href="logout.php">Logout</a>
            <a href="#" class="danger" onclick="confirmDelete()">Delete Account</a>
        </div>
    </div>

    <script>
    function toggleMenu() {
        document.getElementById("sideMenu").classList.toggle("active");
        document.getElementById("overlay").classList.toggle("active");
    }

    function toggleProfile() {
        document.getElementById("profileSidebar").classList.toggle("active");
    }

    document.getElementById("overlay").addEventListener("click", function() {
        document.getElementById("sideMenu").classList.remove("active");
        this.classList.remove("active");
    });

    document.addEventListener('click', function(event) {
        const profileSidebar = document.getElementById('profileSidebar');
        const profileIcon = document.querySelector('.profile-icon');
        if (profileSidebar && profileSidebar.classList.contains('active')) {
            if (!profileSidebar.contains(event.target) && !profileIcon?.contains(event.target)) {
                profileSidebar.classList.remove('active');
            }
        }
    });

    function confirmDelete() {
        if (confirm('Are you sure you want to delete your account? This cannot be undone!')) {
            window.location.href = 'delete_account.php';
        }
    }
    </script>
</body>
</html>