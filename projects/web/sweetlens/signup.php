<?php
include "koneksi.php";
session_start();

$error = "";

if (isset($_POST['signup'])) {
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $confirm = $_POST['confirm'];

    if ($password !== $confirm) {
$error = "Passwords do not match";
} else {
$check = mysqli_query($koneksi, "SELECT * FROM users WHERE email='$email' OR username='$username'");
if (mysqli_num_rows($check) > 0) {
$error = "Username or email already registered";
} else {
$hashed_password = password_hash($password, PASSWORD_DEFAULT);
$query = "INSERT INTO users (username, email, password) VALUES ('$username', '$email', '$hashed_password')";

if (mysqli_query($koneksi, $query)) {
// Retrieve the newly created user's data
$user_id = mysqli_insert_id($koneksi);

// Set session for auto-login
$_SESSION['user_id'] = $user_id;
$_SESSION['username'] = $username;

// Directly redirect to home page
header("Location: home.php");
exit;
} else {
$error = "Registration failed, try again";
}
}
}
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign Up - SweetLens</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            min-height: 100vh;
            background: linear-gradient(145deg, #ffc0d9 0%, #ff9ec2 100%);
            display: flex;
            justify-content: center;
            align-items: center;
            font-family: 'Poppins', sans-serif;
            padding: 20px;
        }

        @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap');

        .signup-card {
            background: rgba(255, 255, 255, 0.3);
            backdrop-filter: blur(12px);
            border-radius: 32px;
            padding: 40px 35px;
            width: 100%;
            max-width: 420px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.15), 0 0 0 1px rgba(255, 255, 255, 0.2);
            animation: slideUp 0.5s ease-out;
        }

        @keyframes slideUp {
            from { opacity: 0; transform: translateY(30px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .signup-card h1 {
            text-align: center;
            color: white;
            font-size: 32px;
            font-weight: 700;
            margin-bottom: 30px;
            letter-spacing: -0.5px;
            text-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }

        .signup-card label {
            display: block;
            font-size: 12px;
            font-weight: 600;
            color: white;
            margin-bottom: 5px;
            letter-spacing: 0.5px;
        }

        .signup-card input {
            width: 100%;
            padding: 12px 15px;
            margin-bottom: 20px;
            border: none;
            border-radius: 16px;
            background: rgba(255, 255, 255, 0.9);
            font-size: 14px;
            font-family: 'Poppins', sans-serif;
            transition: all 0.2s;
            outline: none;
        }

        .signup-card input:focus {
            background: white;
            box-shadow: 0 0 0 3px rgba(255, 255, 255, 0.3);
        }

        .error-message {
            background: rgba(255, 100, 100, 0.9);
            color: white;
            padding: 10px 15px;
            border-radius: 12px;
            font-size: 13px;
            margin-bottom: 20px;
            text-align: center;
        }

        .signup-footer {
            display: flex;
            justify-content: flex-end;
            margin-top: 10px;
        }

        .signup-footer button {
            background: #ff6b9d;
            border: none;
            padding: 12px 28px;
            border-radius: 30px;
            color: white;
            font-weight: 600;
            font-size: 14px;
            cursor: pointer;
            transition: all 0.2s;
            font-family: 'Poppins', sans-serif;
            width: 100%;
        }

        .signup-footer button:hover {
            background: #ff4d8c;
            transform: scale(1.02);
        }

        .login-link {
            text-align: center;
            margin-top: 25px;
            font-size: 13px;
            color: white;
        }

        .login-link a {
            color: white;
            text-decoration: none;
            font-weight: 600;
            border-bottom: 1px solid rgba(255,255,255,0.5);
        }

        .login-link a:hover {
            border-bottom-color: white;
        }
    </style>
</head>
<body>
    <div class="signup-card">
        <h1>Sign Up</h1>

        <?php if ($error): ?>
        <div class="error-message"><?= $error ?></div>
        <?php endif; ?>

        <form method="POST">
            <label>Username</label>
            <input type="text" name="username" required>

            <label>Email</label>
            <input type="email" name="email" required>

            <label>Password</label>
            <input type="password" name="password" required>

            <label>Confirm Password</label>
            <input type="password" name="confirm" required>

            <div class="signup-footer">
                <button type="submit" name="signup">Create Account</button>
            </div>
        </form>

        <p class="login-link">
            Already have an account? <a href="login.php">Login</a>
        </p>
    </div>
</body>
</html>