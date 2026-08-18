<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no">
    <title>Photobooth - SweetLens</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            background: #111;
            font-family: 'Poppins', sans-serif;
            overflow-x: hidden;
        }

        @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap');

        .booth-container {
            width: 100%;
            min-height: 100vh;
            background: linear-gradient(145deg, #ffc0d9 0%, #ffe3ee 100%);
            display: flex;
            flex-direction: column;
        }

        .booth-header {
            height: 70px;
            background: rgba(255, 179, 207, 0.9);
            backdrop-filter: blur(4px);
            padding: 0 24px;
            display: flex;
            align-items: center;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
        }

        .menu-btn {
            font-size: 28px;
            cursor: pointer;
            background: white;
            width: 44px;
            height: 44px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 50%;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
            transition: 0.2s;
            color: #ff7eb9;
        }

        .menu-btn:active {
            transform: scale(0.95);
        }

        .header-title {
            margin-left: 20px;
            font-weight: 700;
            font-size: 20px;
            color: white;
            text-shadow: 1px 1px 0 #ff88ae;
        }

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

        .booth-content {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            align-items: flex-start;
            padding: 30px 24px;
            gap: 32px;
        }

        .preview-column {
            display: flex;
            flex-direction: column;
            gap: 16px;
            background: rgba(255, 245, 240, 0.5);
            padding: 16px 12px;
            border-radius: 32px;
            backdrop-filter: blur(4px);
        }

        .preview-box {
            width: 120px;
            height: 90px;
            background: #fef1f5;
            border-radius: 18px;
            object-fit: cover;
            box-shadow: 0 8px 18px rgba(0, 0, 0, 0.1);
            border: 3px solid white;
        }

        .camera-area {
            position: relative;
            width: 480px;
            height: 360px;
            background: #1a1a2e;
            border-radius: 32px;
            overflow: hidden;
            box-shadow: 0 20px 35px rgba(0, 0, 0, 0.25);
            flex-shrink: 0;
            border: 3px solid rgba(255, 255, 240, 0.8);
        }

        #video {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            object-fit: cover;
            transform: scaleX(-1);
            background: #000;
        }

        #canvas {
            display: none;
        }

        .controls {
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 40px;
            padding: 20px 16px 30px;
            background: rgba(255, 230, 240, 0.6);
            margin-top: 10px;
            border-radius: 80px;
            width: fit-content;
            align-self: center;
            backdrop-filter: blur(8px);
        }

        .icon {
            font-size: 24px;
            font-weight: normal;
            background: rgba(255, 255, 255, 0.5);
            width: 55px;
            height: 55px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 50%;
            backdrop-filter: blur(4px);
            cursor: pointer;
            transition: 0.2s;
            color: #b13e6b;
        }

        .icon:hover {
            transform: scale(1.05);
            background: rgba(255, 255, 255, 0.8);
        }

        .icon.active {
            background: #ff4d8c;
            color: white;
        }

        #captureBtn {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            border: 5px solid #ff4d8c;
            background: white;
            cursor: pointer;
            transition: 0.1s linear;
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.2);
        }

        #captureBtn:active {
            transform: scale(0.93);
            background: #ffeef4;
        }

        .counter-wrapper {
            display: flex;
            justify-content: center;
            margin-top: 10px;
        }

        .shot-counter {
            text-align: center;
            font-weight: 600;
            color: #b13e6b;
            background: rgba(255, 255, 255, 0.7);
            padding: 6px 20px;
            border-radius: 40px;
            font-size: 14px;
            backdrop-filter: blur(4px);
        }

        .info-message {
            font-size: 12px;
            color: #b13e6b;
            background: rgba(255, 250, 240, 0.9);
            border-radius: 50px;
            padding: 6px 18px;
            margin-top: 12px;
            text-align: center;
            width: fit-content;
            align-self: center;
            font-weight: 500;
        }

        .action-buttons {
            display: flex;
            justify-content: center;
            gap: 20px;
            margin: 20px 0 25px;
        }

        .btn-next {
            background: linear-gradient(135deg, #ff4d8c, #ff1f6c);
            color: white;
            border: none;
            padding: 12px 35px;
            border-radius: 50px;
            cursor: pointer;
            font-weight: 700;
            font-size: 16px;
            transition: 0.3s;
            box-shadow: 0 5px 15px rgba(255, 77, 140, 0.3);
        }

        .btn-next:hover {
            transform: scale(1.03);
        }

        .btn-reset {
            background: #ffb3cf;
            color: #b13e6b;
            border: none;
            padding: 12px 30px;
            border-radius: 50px;
            cursor: pointer;
            font-weight: 600;
            font-size: 14px;
            transition: 0.2s;
        }

        .btn-reset:hover {
            background: #ff9ec2;
        }

        .hidden {
            display: none;
        }

        footer {
            text-align: center;
            font-size: 11px;
            padding: 16px;
            color: #a7557a;
        }

        /* Timer Modal */
        .modal {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.7);
            display: none;
            align-items: center;
            justify-content: center;
            z-index: 2000;
        }

        .modal.active {
            display: flex;
        }

        .modal-content {
            background: white;
            border-radius: 32px;
            padding: 30px;
            text-align: center;
            width: 300px;
        }

        .modal-content h3 {
            color: #b13e6b;
            margin-bottom: 20px;
        }

        .timer-options {
            display: flex;
            gap: 15px;
            justify-content: center;
            margin-bottom: 20px;
            flex-wrap: wrap;
        }

        .timer-option {
            width: 70px;
            padding: 10px;
            background: #ffb3cf;
            border: none;
            border-radius: 16px;
            cursor: pointer;
            font-weight: 600;
            color: #b13e6b;
            transition: 0.2s;
        }

        .timer-option:hover {
            background: #ff9ec2;
        }

        .timer-option.selected {
            background: #ff4d8c;
            color: white;
        }

        .timer-option.off {
            background: #ddd;
            color: #666;
        }

        .timer-option.off.selected {
            background: #666;
            color: white;
        }

        .modal-close {
            margin-top: 15px;
            background: #ccc;
            border: none;
            padding: 8px 20px;
            border-radius: 20px;
            cursor: pointer;
        }

        /* Countdown Display */
        .countdown-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.8);
            display: none;
            align-items: center;
            justify-content: center;
            z-index: 2001;
        }

        .countdown-overlay.active {
            display: flex;
        }

        .countdown-number {
            font-size: 120px;
            font-weight: bold;
            color: white;
            text-shadow: 0 0 20px #ff4d8c;
            animation: pulse 1s ease-out;
        }

        @keyframes pulse {
            0% { transform: scale(0.5); opacity: 0; }
            100% { transform: scale(1); opacity: 1; }
        }

        /* Filter Modal */
        .filter-modal {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.7);
            display: none;
            align-items: center;
            justify-content: center;
            z-index: 2000;
        }

        .filter-modal.active {
            display: flex;
        }

        .filter-content {
            background: white;
            border-radius: 32px;
            padding: 25px;
            width: 380px;
        }

        .filter-content h3 {
            color: #b13e6b;
            margin-bottom: 20px;
            text-align: center;
        }

        .filter-options {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 12px;
            margin-bottom: 20px;
        }

        .filter-option {
            padding: 10px;
            background: #ffb3cf;
            border: none;
            border-radius: 16px;
            cursor: pointer;
            font-weight: 600;
            color: #b13e6b;
            transition: 0.2s;
            text-align: center;
            font-size: 13px;
        }

        .filter-option:hover {
            background: #ff9ec2;
        }

        .filter-option.selected {
            background: #ff4d8c;
            color: white;
        }

        .filter-preview {
            width: 100%;
            height: 100px;
            background: #f0f0f0;
            border-radius: 16px;
            margin-bottom: 15px;
            overflow: hidden;
        }

        .filter-preview video {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transform: scaleX(-1);
        }

        .filter-close {
            width: 100%;
            background: #ccc;
            border: none;
            padding: 10px;
            border-radius: 30px;
            cursor: pointer;
            font-weight: 600;
        }

        .timer-status {
            margin-left: 10px;
            font-size: 11px;
            background: #ff4d8c;
            color: white;
            padding: 2px 8px;
            border-radius: 20px;
        }

        @media (max-width: 700px) {
            .booth-content {
                flex-direction: column;
                align-items: center;
            }
            .preview-column {
                flex-direction: row;
                order: 2;
                gap: 12px;
                flex-wrap: wrap;
                justify-content: center;
            }
            .preview-box {
                width: 90px;
                height: 68px;
            }
            .camera-area {
                width: 100%;
                max-width: 480px;
                height: auto;
                aspect-ratio: 4 / 3;
            }
            #captureBtn {
                width: 70px;
                height: 70px;
            }
            .filter-options {
                grid-template-columns: repeat(2, 1fr);
            }
        }
    </style>
</head>
<body>
    <div class="booth-container">
        <div class="booth-header">
            <span class="menu-btn" onclick="toggleMenu()">☰</span>
            <div class="header-title">SweetLens</div>
        </div>

        <div class="side-menu" id="sideMenu">
            <a href="home.php">Gallery</a>
            <a href="photobooth.php">Camera</a>
            <a href="template.php">Template</a>
        </div>

        <div class="overlay" id="overlay" onclick="toggleMenu()"></div>

        <div class="booth-content">
            <div class="preview-column">
                <img class="preview-box" id="preview0" alt="preview 1">
                <img class="preview-box" id="preview1" alt="preview 2">
                <img class="preview-box" id="preview2" alt="preview 3">
                <img class="preview-box" id="preview3" alt="preview 4">
            </div>

            <div class="camera-area">
                <video id="video" autoplay playsinline muted></video>
                <canvas id="canvas"></canvas>
            </div>
        </div>

        <div class="controls">
            <div class="icon" id="timerIcon">⏱️</div>
            <button id="captureBtn"></button>
            <div class="icon" id="filterIcon">🎨</div>
        </div>

        <div class="counter-wrapper">
            <div class="shot-counter" id="shotCounter">0 / 4 photos</div>
            <span class="timer-status" id="timerStatus">Timer: Off</span>
        </div>
        <div class="info-message" id="infoMsg">Click the center button to take selfie | max 4 photos</div>

        <div class="action-buttons" id="actionButtons">
            <button class="btn-next hidden" id="nextBtn">Next to Template</button>
            <button class="btn-reset hidden" id="resetBtn">Take Again</button>
        </div>

        <footer>magic mirror — stay in frame</footer>
    </div>

    <!-- Timer Modal -->
    <div class="modal" id="timerModal">
        <div class="modal-content">
            <h3>Set Timer</h3>
            <div class="timer-options">
                <button class="timer-option off" data-timer="0">Off</button>
                <button class="timer-option" data-timer="3">3 detik</button>
                <button class="timer-option" data-timer="5">5 detik</button>
                <button class="timer-option" data-timer="10">10 detik</button>
            </div>
            <button class="modal-close" onclick="closeTimerModal()">Cancel</button>
        </div>
    </div>

    <!-- Countdown Overlay -->
    <div class="countdown-overlay" id="countdownOverlay">
        <div class="countdown-number" id="countdownNumber">3</div>
    </div>

    <!-- Filter Modal -->
    <div class="filter-modal" id="filterModal">
        <div class="filter-content">
            <h3>Choose Filter</h3>
            <div class="filter-preview">
                <video id="filterPreview" autoplay playsinline muted></video>
            </div>
            <div class="filter-options">
                <button class="filter-option" data-filter="none">Normal</button>
                <button class="filter-option" data-filter="grayscale">Grayscale</button>
                <button class="filter-option" data-filter="sepia">Sepia</button>
                <button class="filter-option" data-filter="brightness">Brightness</button>
                <button class="filter-option" data-filter="contrast">Contrast</button>
                <button class="filter-option" data-filter="vintage">Vintage</button>
            </div>
            <button class="filter-close" onclick="closeFilterModal()">Close</button>
        </div>
    </div>

    <script>
    (function() {
        const video = document.getElementById("video");
        const canvas = document.getElementById("canvas");
        const captureBtn = document.getElementById("captureBtn");
        const timerIcon = document.getElementById("timerIcon");
        const filterIcon = document.getElementById("filterIcon");

        const previewBoxes = [
            document.getElementById("preview0"),
            document.getElementById("preview1"),
            document.getElementById("preview2"),
            document.getElementById("preview3")
        ];

        const shotCounterSpan = document.getElementById("shotCounter");
        const infoMsgDiv = document.getElementById("infoMsg");
        const nextBtn = document.getElementById("nextBtn");
        const resetBtn = document.getElementById("resetBtn");
        const timerStatusSpan = document.getElementById("timerStatus");

        let currentShot = 0;
        let mediaStream = null;
        let timerSeconds = 0;
        let countdownInterval = null;
        let currentFilter = 'none';
        let isCountingDown = false;

        const OUTPUT_WIDTH = 960;
        const OUTPUT_HEIGHT = 720;

        const filters = {
            'none': 'none',
            'grayscale': 'grayscale(100%)',
            'sepia': 'sepia(100%)',
            'brightness': 'brightness(1.3)',
            'contrast': 'contrast(1.5)',
            'vintage': 'sepia(50%) contrast(1.2) brightness(0.9)'
        };

        function updateTimerStatus() {
            if (timerSeconds === 0) {
                timerStatusSpan.textContent = 'Timer: Off';
                timerStatusSpan.style.background = '#999';
            } else {
                timerStatusSpan.textContent = 'Timer: ' + timerSeconds + 's';
                timerStatusSpan.style.background = '#ff4d8c';
            }
        }

        function updateCounterUI() {
            shotCounterSpan.innerText = currentShot + " / 4 photos";
            if (currentShot >= 4) {
                infoMsgDiv.innerText = "4 photos taken! Proceed to template";
                nextBtn.classList.remove("hidden");
                resetBtn.classList.remove("hidden");
                captureBtn.style.opacity = "0.6";
                captureBtn.style.cursor = "not-allowed";
            } else {
                nextBtn.classList.add("hidden");
                resetBtn.classList.add("hidden");
                captureBtn.style.opacity = "1";
                captureBtn.style.cursor = "pointer";
            }
        }

        window.toggleMenu = function() {
            document.getElementById("sideMenu").classList.toggle("active");
            document.getElementById("overlay").classList.toggle("active");
        };

        document.getElementById("overlay")?.addEventListener("click", function() {
            document.getElementById("sideMenu")?.classList.remove("active");
            this.classList.remove("active");
        });

        function applyFilterToVideo() {
            if (currentFilter === 'none') {
                video.style.filter = 'none';
            } else {
                video.style.filter = filters[currentFilter];
            }
        }

        function applyFilterToCanvas(ctx, width, height) {
            if (currentFilter === 'none') return;
            
            if (currentFilter === 'grayscale') {
                const imageData = ctx.getImageData(0, 0, width, height);
                const data = imageData.data;
                for (let i = 0; i < data.length; i += 4) {
                    const gray = data[i] * 0.3 + data[i+1] * 0.59 + data[i+2] * 0.11;
                    data[i] = gray;
                    data[i+1] = gray;
                    data[i+2] = gray;
                }
                ctx.putImageData(imageData, 0, 0);
            } else if (currentFilter === 'sepia') {
                const imageData = ctx.getImageData(0, 0, width, height);
                const data = imageData.data;
                for (let i = 0; i < data.length; i += 4) {
                    const r = data[i];
                    const g = data[i+1];
                    const b = data[i+2];
                    data[i] = Math.min(255, r * 0.393 + g * 0.769 + b * 0.189);
                    data[i+1] = Math.min(255, r * 0.349 + g * 0.686 + b * 0.168);
                    data[i+2] = Math.min(255, r * 0.272 + g * 0.534 + b * 0.131);
                }
                ctx.putImageData(imageData, 0, 0);
            } else if (currentFilter === 'brightness') {
                ctx.filter = 'brightness(1.3)';
            } else if (currentFilter === 'contrast') {
                ctx.filter = 'contrast(1.5)';
            } else if (currentFilter === 'vintage') {
                ctx.filter = 'sepia(50%) contrast(1.2) brightness(0.9)';
            }
        }

        async function initCamera() {
            try {
                if (mediaStream) {
                    mediaStream.getTracks().forEach(track => track.stop());
                }

                const stream = await navigator.mediaDevices.getUserMedia({
                    video: { 
                        width: { ideal: 1280 },
                        height: { ideal: 720 },
                        facingMode: "user"
                    }
                });

                mediaStream = stream;
                video.srcObject = stream;
                await video.play();
                
                applyFilterToVideo();

                infoMsgDiv.innerText = "Camera active. Position your face in frame";
                setTimeout(() => {
                    if (infoMsgDiv.innerText.includes("Camera active")) {
                        infoMsgDiv.innerText = "Click the pink button to capture";
                    }
                }, 2000);

            } catch (err) {
                console.error("Camera error:", err);
                alert("Cannot access camera. Please check permission.");
                infoMsgDiv.innerText = "Camera access failed";
            }
        }

        function flashEffect() {
            const flashDiv = document.createElement('div');
            flashDiv.style.position = 'fixed';
            flashDiv.style.top = 0;
            flashDiv.style.left = 0;
            flashDiv.style.width = '100%';
            flashDiv.style.height = '100%';
            flashDiv.style.backgroundColor = 'white';
            flashDiv.style.opacity = '0.6';
            flashDiv.style.pointerEvents = 'none';
            flashDiv.style.zIndex = '9999';
            document.body.appendChild(flashDiv);
            setTimeout(() => flashDiv.remove(), 100);
        }

        async function uploadPhoto(imageData, shotIndex) {
            try {
                const response = await fetch('save_photo.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ image: imageData, shot_index: shotIndex })
                });
                const data = await response.json();
                return data.status === 'success';
            } catch (error) {
                console.error('Upload error:', error);
                return false;
            }
        }

        function startCountdown() {
            if (isCountingDown) return;
            if (currentShot >= 4) {
                infoMsgDiv.innerText = "Maximum 4 photos reached. Proceed to template.";
                return;
            }
            if (timerSeconds === 0) {
                capturePhoto();
                return;
            }
            
            let count = timerSeconds;
            const countdownOverlay = document.getElementById('countdownOverlay');
            const countdownNumber = document.getElementById('countdownNumber');
            
            countdownNumber.textContent = count;
            countdownOverlay.classList.add('active');
            isCountingDown = true;
            
            countdownInterval = setInterval(() => {
                count--;
                if (count > 0) {
                    countdownNumber.textContent = count;
                } else {
                    clearInterval(countdownInterval);
                    countdownOverlay.classList.remove('active');
                    isCountingDown = false;
                    capturePhoto();
                }
            }, 1000);
        }

        function capturePhoto() {
            if (currentShot >= 4) {
                infoMsgDiv.innerText = "Maximum 4 photos reached. Proceed to template.";
                return;
            }

            if (!video.videoWidth || !video.videoHeight) {
                infoMsgDiv.innerText = "Waiting for camera...";
                return;
            }

            flashEffect();

            canvas.width = OUTPUT_WIDTH;
            canvas.height = OUTPUT_HEIGHT;

            const ctx = canvas.getContext("2d");
            
            const videoWidth = video.videoWidth;
            const videoHeight = video.videoHeight;
            
            const targetAspect = OUTPUT_WIDTH / OUTPUT_HEIGHT;
            const videoAspect = videoWidth / videoHeight;
            
            let sx, sy, sw, sh;
            
            if (videoAspect > targetAspect) {
                sh = videoHeight;
                sw = videoHeight * targetAspect;
                sx = (videoWidth - sw) / 2;
                sy = 0;
            } else {
                sw = videoWidth;
                sh = videoWidth / targetAspect;
                sx = 0;
                sy = (videoHeight - sh) / 2;
            }
            
            ctx.save();
            ctx.translate(OUTPUT_WIDTH, 0);
            ctx.scale(-1, 1);
            ctx.drawImage(video, sx, sy, sw, sh, 0, 0, OUTPUT_WIDTH, OUTPUT_HEIGHT);
            ctx.restore();
            
            applyFilterToCanvas(ctx, OUTPUT_WIDTH, OUTPUT_HEIGHT);
            
            const imageData = canvas.toDataURL("image/png");

            if (previewBoxes[currentShot]) {
                previewBoxes[currentShot].src = imageData;
            }

            captureBtn.style.transform = "scale(0.92)";
            setTimeout(() => { captureBtn.style.transform = ""; }, 150);

            infoMsgDiv.innerText = "Uploading photo " + (currentShot + 1) + "...";
            
            uploadPhoto(imageData, currentShot).then((uploadSuccess) => {
                if (uploadSuccess) {
                    infoMsgDiv.innerText = "Photo " + (currentShot + 1) + " saved!";
                } else {
                    infoMsgDiv.innerText = "Failed to save photo " + (currentShot + 1);
                }
            });

            currentShot++;
            updateCounterUI();

            setTimeout(() => {
                if (currentShot < 4) {
                    infoMsgDiv.innerText = currentShot + " photo taken. " + (4 - currentShot) + " remaining";
                }
            }, 1500);
        }

        function openTimerModal() {
            document.getElementById('timerModal').classList.add('active');
        }

        function closeTimerModal() {
            document.getElementById('timerModal').classList.remove('active');
        }

        function openFilterModal() {
            document.getElementById('filterModal').classList.add('active');
            if (mediaStream) {
                const previewVideo = document.getElementById('filterPreview');
                previewVideo.srcObject = mediaStream;
                previewVideo.play();
            }
        }

        function closeFilterModal() {
            document.getElementById('filterModal').classList.remove('active');
        }

        // Timer option click
        document.querySelectorAll('.timer-option').forEach(btn => {
            btn.addEventListener('click', function() {
                document.querySelectorAll('.timer-option').forEach(b => b.classList.remove('selected'));
                this.classList.add('selected');
                timerSeconds = parseInt(this.dataset.timer);
                updateTimerStatus();
                closeTimerModal();
                if (timerSeconds > 0) {
                    infoMsgDiv.innerText = "Timer set to " + timerSeconds + " seconds. Click camera to start countdown";
                } else {
                    infoMsgDiv.innerText = "Timer turned off. Click camera to take photo instantly";
                }
                setTimeout(() => {
                    if (infoMsgDiv.innerText.includes("Timer")) {
                        infoMsgDiv.innerText = "Click the pink button to capture";
                    }
                }, 2000);
            });
        });

        // Filter option click
        document.querySelectorAll('.filter-option').forEach(btn => {
            btn.addEventListener('click', function() {
                document.querySelectorAll('.filter-option').forEach(b => b.classList.remove('selected'));
                this.classList.add('selected');
                currentFilter = this.dataset.filter;
                applyFilterToVideo();
                closeFilterModal();
                let filterName = this.textContent;
                infoMsgDiv.innerText = "Filter: " + filterName + " applied";
                setTimeout(() => {
                    if (infoMsgDiv.innerText.includes("Filter")) {
                        infoMsgDiv.innerText = "Click the pink button to capture";
                    }
                }, 2000);
            });
        });

        timerIcon.addEventListener('click', openTimerModal);
        filterIcon.addEventListener('click', openFilterModal);

        nextBtn.addEventListener("click", () => {
            window.location.href = 'template.php';
        });

        resetBtn.addEventListener("click", () => {
            if (confirm("Reset all photos? This cannot be undone.")) {
                window.location.reload();
            }
        });

        captureBtn.addEventListener("click", () => {
            startCountdown();
        });

        window.addEventListener("keydown", (e) => {
            if (e.code === "Space" || e.code === "KeyC") {
                e.preventDefault();
                startCountdown();
            }
        });

        initCamera();

        window.addEventListener("beforeunload", () => {
            if (mediaStream) {
                mediaStream.getTracks().forEach(track => track.stop());
            }
        });

        updateCounterUI();
        updateTimerStatus();
    })();
    </script>
</body>
</html>