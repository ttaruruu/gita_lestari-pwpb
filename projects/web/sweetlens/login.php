<?php
include "koneksi.php";
session_start();

if (isset($_SESSION['user_id'])) {
    header("Location: home.php");
    exit;
}

$error = "";

if (isset($_POST['login'])) {
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    $query = mysqli_query($koneksi, "SELECT * FROM users WHERE email='$email' OR username='$email'");
    $user = mysqli_fetch_assoc($query);

    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        header("Location: home.php");
        exit;
    } else {
        $error = "Username/Email atau password salah";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - SweetLens</title>
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

        .login-card {
            background: rgba(255, 255, 255, 0.3);
            backdrop-filter: blur(12px);
            border-radius: 32px;
            padding: 40px 35px;
            width: 100%;
            max-width: 400px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.15), 0 0 0 1px rgba(255, 255, 255, 0.2);
            animation: slideUp 0.5s ease-out;
        }

        @keyframes slideUp {
            from { opacity: 0; transform: translateY(30px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .login-card h1 {
            text-align: center;
            color: white;
            font-size: 32px;
            font-weight: 700;
            margin-bottom: 30px;
            letter-spacing: -0.5px;
            text-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }

        .login-card label {
            display: block;
            font-size: 12px;
            font-weight: 600;
            color: white;
            margin-bottom: 5px;
            letter-spacing: 0.5px;
        }

        .login-card input {
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

        .login-card input:focus {
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

        .login-footer {
            display: flex;
            justify-content: flex-end;
            align-items: center;
            margin-top: 10px;
        }

        .login-footer button {
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
        }

        .login-footer button:hover {
            background: #ff4d8c;
            transform: scale(1.02);
        }

        .signup-link {
            text-align: center;
            margin-top: 25px;
            font-size: 13px;
            color: white;
        }

        .signup-link a {
            color: white;
            text-decoration: none;
            font-weight: 600;
            border-bottom: 1px solid rgba(255,255,255,0.5);
        }

        .signup-link a:hover {
            border-bottom-color: white;
        }

        .demo-account {
    margin-top: 25px;
    padding: 15px 18px;
    background: rgba(255, 255, 255, 0.25);
    border: 1px solid rgba(255, 255, 255, 0.35);
    border-radius: 18px;
    color: white;
    font-size: 13px;
}

.demo-account h3 {
    margin-bottom: 8px;
    font-size: 14px;
    font-weight: 600;
}

.demo-account p {
    margin: 4px 0;
}

.demo-account strong {
    font-weight: 600;
}
    </style>
</head>
<body>
    <div class="login-card">
        <h1>Login</h1>

        <?php if ($error): ?>
        <div class="error-message"><?= $error ?></div>
        <?php endif; ?>

        <form method="POST">
            <label>Username or Email</label>
            <input type="text" name="email" required>

            <label>Password</label>
            <input type="password" name="password" required>

            <div class="login-footer">
                <button name="login">Login</button>
            </div>
        </form>

        <p class="signup-link">
            Don't have an account? <a href="signup.php">Sign Up</a>
        </p>
    </div>

    <div class="demo-account">
    <h3>Demo Accunt</h3>
    <p>Username: <strong>demo</strong></p>
    <p>Password: <strong>12345</strong></p>
</div>
</body>
</html>