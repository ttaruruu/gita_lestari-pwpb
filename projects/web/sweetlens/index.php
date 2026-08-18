<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sweet Clicks - Photobooth</title>
    <style>
    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
    }

    body {
        width: 100vw;
        height: 100vh;
        background: linear-gradient(145deg, #ffc0d9 0%, #ff9ec2 100%);
        display: flex;
        justify-content: center;
        align-items: center;
        font-family: 'Poppins', sans-serif;
        overflow: hidden;
    }

    @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap');

    .loading-container {
        text-align: center;
        animation: fadeInUp 0.8s ease-out;
    }

    .loading-img {
        width: 280px;
        max-width: 60vw;
        margin-bottom: 30px;
        filter: drop-shadow(0 10px 25px rgba(0, 0, 0, 0.15));
        animation: float 2s ease-in-out infinite;
    }

    .loading-text {
        color: white;
        font-size: 18px;
        font-weight: 500;
        letter-spacing: 2px;
        margin-bottom: 25px;
        text-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
    }

    .loader {
        width: 50px;
        height: 50px;
        margin: 0 auto;
        border: 4px solid rgba(255, 255, 255, 0.3);
        border-radius: 50%;
        border-top-color: white;
        animation: spin 0.8s linear infinite;
    }

    @keyframes spin {
        to {
            transform: rotate(360deg);
        }
    }

    @keyframes float {

        0%,
        100% {
            transform: translateY(0);
        }

        50% {
            transform: translateY(-10px);
        }
    }

    @keyframes fadeInUp {
        from {
            opacity: 0;
            transform: translateY(30px);
        }

        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
    </style>
</head>

<body>
    <div class="loading-container">
        <img src="assets/img/loading.png" class="loading-img" alt="Loading"
            onerror="this.src='https://placehold.co/200x200/ffb3cf/white?text=SWEET'">
        <p class="loading-text">Connecting...</p>
        <div class="loader"></div>
    </div>

    <script>
    setTimeout(function() {
        window.location.href = "signup.php";
    }, 2500);
    </script>
</body>

</html>