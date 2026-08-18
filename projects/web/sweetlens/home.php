<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}
include "koneksi.php";

$user_id = $_SESSION['user_id'];
$user_query = mysqli_query($koneksi, "SELECT * FROM users WHERE id='$user_id'");
$user_data = mysqli_fetch_assoc($user_query);

$profile_photo = "assets/img/default-avatar.png";
if (!empty($user_data['profile_photo']) && file_exists($user_data['profile_photo'])) {
    $profile_photo = $user_data['profile_photo'];
}

if (isset($_GET['delete_id'])) {
    $delete_id = mysqli_real_escape_string($koneksi, $_GET['delete_id']);
    $query_photo = mysqli_query($koneksi, "SELECT photo FROM photos WHERE id='$delete_id' AND user_id='$user_id'");
    $photo_data = mysqli_fetch_assoc($query_photo);
    
    if ($photo_data) {
        if (file_exists($photo_data['photo'])) {
            unlink($photo_data['photo']);
        }
        mysqli_query($koneksi, "DELETE FROM photos WHERE id='$delete_id' AND user_id='$user_id'");
        header("Location: home.php?deleted=1");
        exit;
    }
}

if (isset($_GET['delete_all'])) {
    $query_photos = mysqli_query($koneksi, "SELECT photo FROM photos WHERE user_id='$user_id'");
    while ($photo = mysqli_fetch_assoc($query_photos)) {
        if (file_exists($photo['photo'])) {
            unlink($photo['photo']);
        }
    }
    mysqli_query($koneksi, "DELETE FROM photos WHERE user_id='$user_id'");
    header("Location: home.php?deleted_all=1");
    exit;
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Home - SweetLens</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(145deg, #ffc0d9 0%, #ffe3ee 100%);
            min-height: 100vh;
            overflow-x: hidden;
        }
        @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap');

        .side-menu {
            position: fixed; top: 0; left: -300px; width: 280px; height: 100vh;
            background: linear-gradient(135deg, #ffb3cf, #ff9ec2); padding-top: 90px;
            transition: 0.3s; z-index: 1000; box-shadow: 4px 0 20px rgba(0,0,0,0.2);
            border-radius: 0 24px 24px 0;
        }
        .side-menu.active { left: 0; }
        .side-menu a {
            display: block; padding: 16px 32px; color: white; text-decoration: none;
            font-weight: 600; font-size: 16px; transition: 0.2s;
            border-bottom: 1px solid rgba(255,255,255,0.15);
        }
        .side-menu a:hover { background: rgba(255,255,255,0.2); padding-left: 40px; }
        .side-menu a.active { background: rgba(255,255,255,0.25); border-left: 4px solid white; }

        .overlay {
            position: fixed; top: 0; left: 0; width: 100%; height: 100%;
            display: none; background: rgba(0,0,0,0.4); backdrop-filter: blur(3px); z-index: 999;
        }
        .overlay.active { display: block; }

        .main-content { margin-left: 0; padding: 20px 30px; transition: 0.3s; }

        .header {
            display: flex; justify-content: space-between; align-items: center;
            background: rgba(255,255,255,0.85); backdrop-filter: blur(10px);
            padding: 15px 25px; border-radius: 20px; margin-bottom: 30px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.08);
        }
        .header-left { display: flex; align-items: center; gap: 15px; }
        .header-left h2 { color: #b13e6b; font-size: 20px; font-weight: 600; }
        .menu-btn {
            font-size: 26px; cursor: pointer; background: white; width: 42px; height: 42px;
            display: flex; align-items: center; justify-content: center; border-radius: 50%;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1); color: #ff7eb9;
        }
        .header-right { display: flex; align-items: center; gap: 20px; }
        .search-box input {
            padding: 10px 18px; border: 2px solid #ffb3cf; border-radius: 30px;
            outline: none; font-size: 14px; width: 250px; background: white;
        }
        .search-box input:focus { border-color: #ff4d8c; }
        .profile-icon {
            width: 42px; height: 42px; cursor: pointer; background: white; border-radius: 50%;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1); overflow: hidden;
        }
        .profile-icon img { width: 100%; height: 100%; object-fit: cover; }

        .gallery-container {
            background: rgba(255,255,255,0.5); backdrop-filter: blur(5px);
            border-radius: 24px; padding: 25px; min-height: 500px;
        }
        .gallery-header {
            display: flex; justify-content: space-between; align-items: center;
            margin-bottom: 20px; padding-bottom: 10px;
            border-bottom: 2px solid rgba(255,255,255,0.5); flex-wrap: wrap; gap: 15px;
        }
        .gallery-title { color: #b13e6b; font-size: 20px; font-weight: 600; }
        .delete-all-btn {
            background: #ff6b6b; color: white; border: none; padding: 8px 20px;
            border-radius: 30px; cursor: pointer; font-weight: 600; font-size: 13px;
        }
        .delete-all-btn:hover { background: #ff4444; }

        .gallery-grid {
            display: grid; grid-template-columns: repeat(auto-fill, minmax(220px, 1fr)); gap: 25px;
        }
        .gallery-item {
            background: white; border-radius: 20px; overflow: hidden;
            box-shadow: 0 8px 20px rgba(0,0,0,0.1); transition: 0.3s;
        }
        .gallery-item:hover { transform: translateY(-5px); }
        .gallery-item img { width: 100%; height: 220px; object-fit: cover; cursor: pointer; }
        .gallery-item .info {
            padding: 12px; background: white; display: flex;
            justify-content: space-between; align-items: center;
        }
        .gallery-item .date { font-size: 11px; color: #999; }
        .delete-btn {
            background: #ff6b6b; color: white; border: none; padding: 5px 12px;
            border-radius: 20px; cursor: pointer; font-size: 11px; font-weight: 600;
        }
        .delete-btn:hover { background: #ff4444; }

        .empty-gallery { text-align: center; padding: 60px; color: #b13e6b; }
        .empty-gallery p { margin-bottom: 20px; }
        .btn-take-photo {
            display: inline-block; padding: 12px 28px; background: #ff4d8c; color: white;
            text-decoration: none; border-radius: 30px; font-weight: 600;
        }
        .btn-take-photo:hover { background: #ff1f6c; }

        .profile-sidebar {
            position: fixed; top: 0; right: -340px; width: 320px; height: 100vh;
            background: white; box-shadow: -4px 0 20px rgba(0,0,0,0.15);
            transition: 0.3s; z-index: 1001; display: flex; flex-direction: column;
        }
        .profile-sidebar.active { right: 0; }
        .profile-header {
            padding: 20px; background: linear-gradient(135deg, #ffb3cf, #ff9ec2);
            color: white; display: flex; align-items: center; gap: 15px;
        }
        .profile-header button {
            background: rgba(255,255,255,0.2); border: none; color: white; font-size: 22px;
            cursor: pointer; width: 38px; height: 38px; border-radius: 50%;
        }
        .profile-header h3 { font-size: 20px; }
        .profile-info { text-align: center; padding: 30px; border-bottom: 1px solid #eee; }
        .avatar { width: 100px; height: 100px; margin: 0 auto 12px; border-radius: 50%; overflow: hidden; }
        .avatar img { width: 100%; height: 100%; object-fit: cover; }
        .profile-info p { font-size: 18px; color: #333; font-weight: 600; }
        .profile-info small { color: #999; font-size: 12px; }
        .profile-menu { padding: 20px; }
        .profile-menu a {
            display: block; padding: 12px 18px; color: #666; text-decoration: none;
            border-radius: 12px; transition: 0.2s; margin-bottom: 5px; font-size: 14px;
        }
        .profile-menu a:hover { background: #f5f5f5; color: #ff4d8c; }
        .profile-menu a.danger { color: #f44336; }
        .profile-menu a.danger:hover { background: #ffebee; }

        .modal {
            position: fixed; top: 0; left: 0; width: 100%; height: 100%;
            background: rgba(0,0,0,0.9); display: none; align-items: center;
            justify-content: center; z-index: 2000; cursor: pointer;
        }
        .modal.active { display: flex; }
        .modal img { max-width: 90%; max-height: 90%; object-fit: contain; }
        .modal-close { position: absolute; top: 20px; right: 30px; color: white; font-size: 32px; cursor: pointer; }

        .notification {
            position: fixed; top: 20px; right: 20px; padding: 12px 24px;
            border-radius: 12px; color: white; font-weight: 600; z-index: 3000;
            animation: slideIn 0.3s ease-out;
        }
        .notification.success { background: #4caf50; }
        @keyframes slideIn { from { transform: translateX(100%); opacity: 0; } to { transform: translateX(0); opacity: 1; } }

        .confirm-modal {
            position: fixed; top: 0; left: 0; width: 100%; height: 100%;
            background: rgba(0,0,0,0.5); display: none; align-items: center;
            justify-content: center; z-index: 3000;
        }
        .confirm-modal.active { display: flex; }
        .confirm-content {
            background: white; padding: 30px; border-radius: 24px; text-align: center; max-width: 350px;
        }
        .confirm-content p { margin-bottom: 25px; color: #333; }
        .confirm-buttons { display: flex; gap: 15px; justify-content: center; }
        .confirm-yes { background: #f44336; color: white; padding: 10px 25px; border-radius: 30px; border: none; cursor: pointer; font-weight: 600; }
        .confirm-no { background: #ccc; color: #333; padding: 10px 25px; border-radius: 30px; border: none; cursor: pointer; font-weight: 600; }

        @media (max-width: 768px) {
            .main-content { padding: 15px; }
            .header { flex-direction: column; gap: 15px; }
            .gallery-grid { grid-template-columns: repeat(auto-fill, minmax(150px, 1fr)); gap: 15px; }
            .gallery-item img { height: 150px; }
            .search-box input { width: 180px; }
        }
    </style>
</head>
<body>

    <div class="side-menu" id="sideMenu">
        <a href="home.php" class="active">Gallery</a>
        <a href="photobooth.php">Camera</a>
        <a href="template.php">Template</a>
    </div>

    <div class="overlay" id="overlay" onclick="toggleMenu()"></div>

    <div class="main-content">
        <div class="header">
            <div class="header-left">
                <div class="menu-btn" onclick="toggleMenu()">☰</div>
                <h2>Welcome, <?= htmlspecialchars($_SESSION['username']) ?></h2>
            </div>
            <div class="header-right">
                <div class="search-box">
                    <input type="text" id="searchInput" placeholder="Search photos...">
                </div>
                <div class="profile-icon" onclick="toggleProfile()">
                    <img src="<?= $profile_photo ?>" alt="Profile">
                </div>
            </div>
        </div>

        <div class="gallery-container">
            <div class="gallery-header">
                <div class="gallery-title">My Photo Gallery</div>
                <?php
                $count_query = mysqli_query($koneksi, "SELECT COUNT(*) as total FROM photos WHERE user_id='$user_id'");
                $count_data = mysqli_fetch_assoc($count_query);
                if ($count_data['total'] > 0):
                ?>
                <button class="delete-all-btn" onclick="showConfirmDeleteAll()">Delete All Photos</button>
                <?php endif; ?>
            </div>
            <div class="gallery-grid" id="galleryGrid">
                <?php
                $query = mysqli_query($koneksi, "SELECT * FROM photos WHERE user_id='$user_id' ORDER BY created_at DESC");
                if (mysqli_num_rows($query) > 0) {
                    while ($photo = mysqli_fetch_assoc($query)) {
                        echo '<div class="gallery-item" data-photo="' . htmlspecialchars($photo['photo']) . '" data-id="' . $photo['id'] . '">';
                        echo '<img src="' . htmlspecialchars($photo['photo']) . '" alt="Photo" onclick="openModal(this.src)">';
                        echo '<div class="info">';
                        echo '<div class="date">' . date('d M Y, H:i', strtotime($photo['created_at'])) . '</div>';
                        echo '<button class="delete-btn" onclick="showConfirmDelete(' . $photo['id'] . ')">Delete</button>';
                        echo '</div></div>';
                    }
                } else {
                    echo '<div class="empty-gallery">';
                    echo '<p>No photos yet. Take your first photo!</p>';
                    echo '<a href="photobooth.php" class="btn-take-photo">Take a Photo</a>';
                    echo '</div>';
                }
                ?>
            </div>
        </div>
    </div>

    <div class="profile-sidebar" id="profileSidebar">
        <div class="profile-header">
            <button onclick="toggleProfile()">←</button>
            <h3>My Profile</h3>
        </div>
        <div class="profile-info">
            <div class="avatar"><img src="<?= $profile_photo ?>" alt="Profile Photo"></div>
            <p><?= htmlspecialchars($_SESSION['username']) ?></p>
            <small><?= htmlspecialchars($user_data['email']) ?></small>
        </div>
        <div class="profile-menu">
            <a href="profile.php">Edit Profile</a>
            <a href="logout.php">Logout</a>
            <a href="#" class="danger" onclick="confirmDeleteAccount()">Delete Account</a>
        </div>
    </div>

    <div class="modal" id="modal">
        <span class="modal-close">&times;</span>
        <img id="modalImg" src="">
    </div>

    <div class="confirm-modal" id="confirmModal">
        <div class="confirm-content">
            <p id="confirmMessage">Are you sure you want to delete this photo?</p>
            <div class="confirm-buttons">
                <button class="confirm-yes" id="confirmYes">Yes, Delete</button>
                <button class="confirm-no" id="confirmNo">Cancel</button>
            </div>
        </div>
    </div>

    <?php if (isset($_GET['deleted'])): ?>
    <div class="notification success" id="notification">Photo deleted successfully!</div>
    <script>setTimeout(() => document.getElementById('notification')?.remove(), 3000);</script>
    <?php endif; ?>

    <?php if (isset($_GET['deleted_all'])): ?>
    <div class="notification success" id="notification">All photos deleted successfully!</div>
    <script>setTimeout(() => document.getElementById('notification')?.remove(), 3000);</script>
    <?php endif; ?>

    <script>
        let pendingDeleteId = null, pendingDeleteAll = false;
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

        const searchInput = document.getElementById('searchInput');
        const galleryItems = document.querySelectorAll('.gallery-item');
        if (searchInput) {
            searchInput.addEventListener('keyup', function() {
                const term = this.value.toLowerCase();
                galleryItems.forEach(item => {
                    const imgSrc = item.getAttribute('data-photo')?.toLowerCase() || '';
                    item.style.display = (imgSrc.includes(term) || term === '') ? 'block' : 'none';
                });
            });
        }

        const modal = document.getElementById('modal');
        const modalImg = document.getElementById('modalImg');
        function openModal(src) { modalImg.src = src; modal.classList.add('active'); }
        document.querySelector('.modal-close').addEventListener('click', () => modal.classList.remove('active'));
        modal.addEventListener('click', (e) => { if (e.target === modal) modal.classList.remove('active'); });

        function showConfirmDelete(photoId) {
            pendingDeleteId = photoId; pendingDeleteAll = false;
            document.getElementById('confirmMessage').innerText = 'Are you sure you want to delete this photo?';
            document.getElementById('confirmModal').classList.add('active');
        }
        function showConfirmDeleteAll() {
            pendingDeleteAll = true; pendingDeleteId = null;
            document.getElementById('confirmMessage').innerText = 'Are you sure you want to delete ALL photos? This cannot be undone!';
            document.getElementById('confirmModal').classList.add('active');
        }
        document.getElementById('confirmYes').addEventListener('click', function() {
            if (pendingDeleteId) window.location.href = 'home.php?delete_id=' + pendingDeleteId;
            else if (pendingDeleteAll) window.location.href = 'home.php?delete_all=1';
            document.getElementById('confirmModal').classList.remove('active');
        });
        document.getElementById('confirmNo').addEventListener('click', function() {
            document.getElementById('confirmModal').classList.remove('active');
            pendingDeleteId = null; pendingDeleteAll = false;
        });
        function confirmDeleteAccount() {
            if (confirm('Are you sure you want to delete your account? This action cannot be undone!')) {
                window.location.href = 'delete_account.php';
            }
        }
        setTimeout(() => { const notif = document.getElementById('notification'); if (notif) setTimeout(() => notif.remove(), 3000); }, 100);
    </script>
</body>
</html>