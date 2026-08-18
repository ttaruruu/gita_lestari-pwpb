<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

if (!isset($_GET['id'])) {
    header("Location: template.php");
    exit;
}

include "koneksi.php";
$template_id = $_GET['id'];
$template_query = mysqli_query($koneksi, "SELECT * FROM templates WHERE id='$template_id'");
$template = mysqli_fetch_assoc($template_query);

if (!$template) {
    header("Location: template.php");
    exit;
}

$photos_query = mysqli_query($koneksi, "SELECT * FROM photos WHERE user_id='" . $_SESSION['user_id'] . "' ORDER BY created_at DESC");
$photos = [];
while ($photo = mysqli_fetch_assoc($photos_query)) {
    $photos[] = $photo;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Template - SweetLens</title>
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
            padding: 20px;
        }

        .container {
            max-width: 1400px;
            margin: 0 auto;
        }

        .back-btn {
            display: inline-block;
            margin-bottom: 20px;
            background: rgba(255, 255, 255, 0.8);
            padding: 10px 25px;
            border-radius: 30px;
            text-decoration: none;
            color: #b13e6b;
            font-weight: 600;
        }

        .back-btn:hover {
            background: white;
        }

        .workspace {
            display: flex;
            flex-wrap: wrap;
            gap: 30px;
            justify-content: center;
        }

        .template-area {
            flex: 2;
            min-width: 500px;
            background: rgba(255, 255, 255, 0.4);
            border-radius: 32px;
            padding: 20px;
        }

        .template-preview {
            background: #e0e0e0;
            border-radius: 24px;
            overflow: hidden;
            text-align: center;
            position: relative;
        }

        #resultCanvas {
            max-width: 100%;
            height: auto;
            display: block;
        }

        .photos-area {
            flex: 1;
            min-width: 280px;
            background: rgba(255, 255, 255, 0.5);
            border-radius: 32px;
            padding: 20px;
            display: flex;
            flex-direction: column;
            gap: 20px;
            max-height: 80vh;
            overflow-y: auto;
        }

        .photos-area h3 {
            color: #b13e6b;
            font-size: 18px;
            font-weight: 600;
            padding-bottom: 10px;
            border-bottom: 2px solid rgba(255, 255, 255, 0.5);
        }

        .photos-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(100px, 1fr));
            gap: 15px;
        }

        .photo-card {
            background: white;
            border-radius: 16px;
            overflow: hidden;
            cursor: pointer;
            border: 3px solid transparent;
            transition: all 0.2s;
            position: relative;
        }

        .photo-card.selected {
            border-color: #ff4d8c;
            transform: scale(1.02);
        }

        .photo-card.used {
            opacity: 0.5;
            filter: grayscale(0.5);
            cursor: not-allowed;
        }

        .photo-card.used::after {
            content: "Used";
            position: absolute;
            bottom: 5px;
            left: 0;
            right: 0;
            background: rgba(0,0,0,0.7);
            color: white;
            font-size: 9px;
            text-align: center;
            padding: 2px;
        }

        .photo-card img {
            width: 100%;
            height: 100px;
            object-fit: cover;
        }

        .photo-card .photo-date {
            font-size: 9px;
            color: #999;
            text-align: center;
            padding: 5px;
        }

        .info-panel {
            background: rgba(255, 255, 255, 0.8);
            border-radius: 20px;
            padding: 15px;
        }

        .info-panel p {
            font-size: 13px;
            color: #b13e6b;
            margin-bottom: 10px;
        }

        .photos-list {
            max-height: 200px;
            overflow-y: auto;
        }

        .photo-item {
            display: flex;
            align-items: center;
            justify-content: space-between;
            background: white;
            padding: 8px 12px;
            margin-bottom: 8px;
            border-radius: 12px;
            cursor: pointer;
            transition: 0.2s;
        }

        .photo-item:hover {
            background: #ffb3cf;
        }

        .photo-item.selected-drag {
            background: #ff4d8c;
            color: white;
        }

        .photo-item .photo-name {
            font-size: 12px;
            font-weight: 500;
        }

        .photo-item .photo-controls {
            display: flex;
            gap: 8px;
        }

        .photo-item .photo-controls button {
            background: none;
            border: none;
            cursor: pointer;
            font-size: 14px;
            padding: 2px 5px;
            border-radius: 8px;
        }

        .photo-item .photo-controls button:hover {
            background: rgba(0,0,0,0.1);
        }

        .action-buttons {
            display: flex;
            gap: 15px;
            margin-top: 10px;
        }

        .btn-save, .btn-reset {
            flex: 1;
            padding: 12px;
            border-radius: 40px;
            font-weight: 700;
            font-size: 14px;
            cursor: pointer;
            border: none;
        }

        .btn-save {
            background: linear-gradient(135deg, #ff4d8c, #ff1f6c);
            color: white;
        }

        .btn-reset {
            background: #ffb3cf;
            color: #b13e6b;
        }

        .message {
            margin-top: 15px;
            padding: 10px;
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

        .message.info {
            background: #ffe0b5;
            color: #b13e6b;
        }

        .empty-photos {
            text-align: center;
            padding: 30px;
            color: #b13e6b;
        }

        .empty-photos a {
            display: inline-block;
            margin-top: 10px;
            padding: 8px 20px;
            background: #ff4d8c;
            color: white;
            text-decoration: none;
            border-radius: 30px;
            font-size: 12px;
        }

        .instruction {
            background: #ffe0b5;
            padding: 12px;
            border-radius: 12px;
            font-size: 13px;
            text-align: center;
            margin-bottom: 15px;
        }
    </style>
</head>
<body>
    <div class="container">
        <a href="template.php" class="back-btn">← Back to Templates</a>

        <div class="workspace">
            <div class="template-area">
                <div class="instruction">
                    How to use: 1. Click photo on right  2. Click on template  3. Click photo in list  4. Drag to move, drag corners to resize, drag rotation handle (circle above) to rotate
                </div>
                <div class="template-preview">
                    <canvas id="resultCanvas" width="800" height="600"></canvas>
                </div>
            </div>

            <div class="photos-area">
                <h3>My Photos</h3>
                <div class="photos-grid" id="photosGrid">
                    <?php if (count($photos) > 0): ?>
                        <?php foreach ($photos as $photo): ?>
                            <div class="photo-card" data-photo="<?= htmlspecialchars($photo['photo']) ?>" data-id="<?= $photo['id'] ?>">
                                <img src="<?= htmlspecialchars($photo['photo']) ?>" alt="Photo">
                                <div class="photo-date"><?= date('d M Y', strtotime($photo['created_at'])) ?></div>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="empty-photos">
                            <p>No photos yet.</p>
                            <a href="photobooth.php">Take a Photo First</a>
                        </div>
                    <?php endif; ?>
                </div>

                <div class="info-panel">
                    <p>Photos on Template (click to select):</p>
                    <div class="photos-list" id="photosList"></div>
                </div>

                <div class="action-buttons">
                    <button class="btn-reset" id="resetBtn">Remove All Photos</button>
                    <button class="btn-save" id="saveBtn">Save Result</button>
                </div>

                <div id="message" class="message"></div>
            </div>
        </div>
    </div>

    <script>
        const canvas = document.getElementById('resultCanvas');
        const ctx = canvas.getContext('2d');
        
        let templateImage = new Image();
        let templateLoaded = false;
        let placedPhotos = [];
        let selectedPhotoIndex = null;
        let usedPhotoIds = [];
        
        let isDragging = false;
        let isResizing = false;
        let isRotating = false;
        let resizeDirection = null;
        let dragStartX = 0, dragStartY = 0;
        let photoStartX = 0, photoStartY = 0;
        let photoStartW = 0, photoStartH = 0;
        let photoStartRotation = 0;
        
        let imageCache = {};

        templateImage.src = "assets/templates/<?= $template['image'] ?>";
        
        templateImage.onload = function() {
            canvas.width = templateImage.width;
            canvas.height = templateImage.height;
            drawAll();
            templateLoaded = true;
            updatePhotosList();
            showMessage('Template ready', 'info');
        };

        templateImage.onerror = function() {
            showMessage('Failed to load template', 'error');
        };

        function drawAll() {
            ctx.fillStyle = '#ffffff';
            ctx.fillRect(0, 0, canvas.width, canvas.height);
            
            for (let i = 0; i < placedPhotos.length; i++) {
                drawPhoto(placedPhotos[i]);
            }
            
            ctx.drawImage(templateImage, 0, 0, canvas.width, canvas.height);
            
            if (selectedPhotoIndex !== null) {
                const p = placedPhotos[selectedPhotoIndex];
                if (p) {
                    ctx.strokeStyle = '#ff4d8c';
                    ctx.lineWidth = 3;
                    ctx.strokeRect(p.x, p.y, p.w, p.h);
                    
                    ctx.fillStyle = '#ff4d8c';
                    ctx.fillRect(p.x + p.w - 8, p.y + p.h - 8, 8, 8);
                    ctx.fillRect(p.x + p.w - 8, p.y, 8, 8);
                    ctx.fillRect(p.x, p.y + p.h - 8, 8, 8);
                    ctx.fillRect(p.x, p.y, 8, 8);
                    ctx.fillRect(p.x + p.w/2 - 4, p.y - 4, 8, 8);
                    ctx.fillRect(p.x + p.w/2 - 4, p.y + p.h - 4, 8, 8);
                    ctx.fillRect(p.x - 4, p.y + p.h/2 - 4, 8, 8);
                    ctx.fillRect(p.x + p.w - 4, p.y + p.h/2 - 4, 8, 8);
                    
                    // Rotation handle (circle above the photo)
                    const rotHandleX = p.x + p.w/2;
                    const rotHandleY = p.y - 15;
                    ctx.beginPath();
                    ctx.arc(rotHandleX, rotHandleY, 8, 0, 2 * Math.PI);
                    ctx.fillStyle = '#ff4d8c';
                    ctx.fill();
                    ctx.fillStyle = 'white';
                    ctx.beginPath();
                    ctx.arc(rotHandleX, rotHandleY, 4, 0, 2 * Math.PI);
                    ctx.fill();
                }
            }
        }

        function drawPhoto(photo) {
            if (imageCache[photo.src]) {
                drawRotatedImage(imageCache[photo.src], photo);
            } else {
                const img = new Image();
                img.src = photo.src;
                img.onload = function() {
                    imageCache[photo.src] = img;
                    drawRotatedImage(img, photo);
                    drawAll();
                };
            }
        }

        function drawRotatedImage(img, photo) {
            ctx.save();
            ctx.translate(photo.x + photo.w/2, photo.y + photo.h/2);
            ctx.rotate(photo.rotation * Math.PI / 180);
            
            const imgRatio = img.width / img.height;
            const targetRatio = photo.w / photo.h;
            
            let dw, dh, dx, dy;
            
            if (imgRatio > targetRatio) {
                dh = photo.h;
                dw = img.width * (photo.h / img.height);
                dx = -dw / 2;
                dy = -photo.h / 2;
            } else {
                dw = photo.w;
                dh = img.height * (photo.w / img.width);
                dx = -photo.w / 2;
                dy = -dh / 2;
            }
            
            ctx.drawImage(img, dx, dy, dw, dh);
            ctx.restore();
        }

        function addPhoto(src, photoId, clickX, clickY) {
            if (usedPhotoIds.includes(photoId)) {
                showMessage('This photo is already used', 'error');
                return false;
            }
            
            const img = new Image();
            img.src = src;
            img.onload = function() {
                const baseSize = 160;
                let w, h;
                
                if (img.width > img.height) {
                    w = baseSize;
                    h = (img.height / img.width) * baseSize;
                } else {
                    h = baseSize;
                    w = (img.width / img.height) * baseSize;
                }
                
                let x = clickX - w / 2;
                let y = clickY - h / 2;
                
                x = Math.max(5, Math.min(x, canvas.width - w - 5));
                y = Math.max(5, Math.min(y, canvas.height - h - 5));
                
                placedPhotos.push({
                    src: src,
                    id: photoId,
                    x: x,
                    y: y,
                    w: w,
                    h: h,
                    rotation: 0
                });
                
                usedPhotoIds.push(photoId);
                updateUsedPhotoStatus();
                updatePhotosList();
                drawAll();
                showMessage('Photo added', 'success');
            };
            
            return true;
        }

        function removePhoto(index) {
            const photoId = placedPhotos[index].id;
            const idIndex = usedPhotoIds.indexOf(photoId);
            if (idIndex !== -1) usedPhotoIds.splice(idIndex, 1);
            
            placedPhotos.splice(index, 1);
            if (selectedPhotoIndex === index) selectedPhotoIndex = null;
            else if (selectedPhotoIndex > index) selectedPhotoIndex--;
            
            updateUsedPhotoStatus();
            updatePhotosList();
            drawAll();
            showMessage('Photo removed', 'info');
        }

        function updateUsedPhotoStatus() {
            document.querySelectorAll('.photo-card').forEach(card => {
                const cardId = card.dataset.id;
                if (usedPhotoIds.includes(cardId)) {
                    card.classList.add('used');
                } else {
                    card.classList.remove('used');
                }
            });
        }

        function updatePhotosList() {
            const listDiv = document.getElementById('photosList');
            if (placedPhotos.length === 0) {
                listDiv.innerHTML = '<span style="color:#999; font-size:12px;">No photos placed</span>';
            } else {
                listDiv.innerHTML = placedPhotos.map((photo, idx) => 
                    `<div class="photo-item ${selectedPhotoIndex === idx ? 'selected-drag' : ''}" onclick="selectPhoto(${idx})">
                        <span class="photo-name">Photo ${idx + 1}</span>
                        <div class="photo-controls">
                            <button onclick="event.stopPropagation(); removePhoto(${idx})" style="color:#ff4d8c;">✕</button>
                        </div>
                    </div>`
                ).join('');
            }
        }

        function selectPhoto(index) {
            selectedPhotoIndex = index;
            updatePhotosList();
            drawAll();
            showMessage('Photo ' + (index + 1) + ' selected. Drag to move, resize corners, or rotate using the circle handle', 'info');
        }

        function getMousePos(e) {
            const rect = canvas.getBoundingClientRect();
            const scaleX = canvas.width / rect.width;
            const scaleY = canvas.height / rect.height;
            
            let clientX, clientY;
            if (e.touches) {
                clientX = e.touches[0].clientX;
                clientY = e.touches[0].clientY;
            } else {
                clientX = e.clientX;
                clientY = e.clientY;
            }
            
            return {
                x: (clientX - rect.left) * scaleX,
                y: (clientY - rect.top) * scaleY
            };
        }

        function getResizeDirection(x, y, photo) {
            const edge = 12;
            const left = photo.x;
            const right = photo.x + photo.w;
            const top = photo.y;
            const bottom = photo.y + photo.h;
            
            if (x >= left && x <= left + edge && y >= top && y <= top + edge) return 'top-left';
            if (x >= right - edge && x <= right && y >= top && y <= top + edge) return 'top-right';
            if (x >= left && x <= left + edge && y >= bottom - edge && y <= bottom) return 'bottom-left';
            if (x >= right - edge && x <= right && y >= bottom - edge && y <= bottom) return 'bottom-right';
            if (x >= left && x <= right && y >= top - edge && y <= top + edge) return 'top';
            if (x >= left && x <= right && y >= bottom - edge && y <= bottom + edge) return 'bottom';
            if (x >= left - edge && x <= left + edge && y >= top && y <= bottom) return 'left';
            if (x >= right - edge && x <= right + edge && y >= top && y <= bottom) return 'right';
            return null;
        }

        function isOnRotateHandle(x, y, photo) {
            const handleX = photo.x + photo.w/2;
            const handleY = photo.y - 15;
            const dist = Math.sqrt(Math.pow(x - handleX, 2) + Math.pow(y - handleY, 2));
            return dist <= 12;
        }

        function setCursor(direction) {
            const cursors = {
                'top-left': 'nw-resize',
                'top-right': 'ne-resize',
                'bottom-left': 'sw-resize',
                'bottom-right': 'se-resize',
                'top': 'n-resize',
                'bottom': 's-resize',
                'left': 'w-resize',
                'right': 'e-resize',
                'rotate': 'grabbing'
            };
            canvas.style.cursor = cursors[direction] || 'default';
        }

        canvas.addEventListener('mousemove', function(e) {
            if (selectedPhotoIndex === null) {
                canvas.style.cursor = 'default';
                return;
            }
            
            const photo = placedPhotos[selectedPhotoIndex];
            if (!photo) return;
            
            const pos = getMousePos(e);
            
            if (isOnRotateHandle(pos.x, pos.y, photo)) {
                canvas.style.cursor = 'grabbing';
                return;
            }
            
            const direction = getResizeDirection(pos.x, pos.y, photo);
            if (direction) {
                setCursor(direction);
            } else if (pos.x >= photo.x && pos.x <= photo.x + photo.w && pos.y >= photo.y && pos.y <= photo.y + photo.h) {
                canvas.style.cursor = 'grab';
            } else {
                canvas.style.cursor = 'default';
            }
        });

        canvas.addEventListener('mousedown', function(e) {
            if (selectedPhotoIndex === null) return;
            
            const photo = placedPhotos[selectedPhotoIndex];
            if (!photo) return;
            
            const pos = getMousePos(e);
            
            if (isOnRotateHandle(pos.x, pos.y, photo)) {
                isRotating = true;
                dragStartX = pos.x;
                dragStartY = pos.y;
                photoStartRotation = photo.rotation;
                canvas.style.cursor = 'grabbing';
                e.preventDefault();
                return;
            }
            
            const direction = getResizeDirection(pos.x, pos.y, photo);
            if (direction) {
                isResizing = true;
                resizeDirection = direction;
                dragStartX = pos.x;
                dragStartY = pos.y;
                photoStartX = photo.x;
                photoStartY = photo.y;
                photoStartW = photo.w;
                photoStartH = photo.h;
                e.preventDefault();
                return;
            }
            else if (pos.x >= photo.x && pos.x <= photo.x + photo.w && pos.y >= photo.y && pos.y <= photo.y + photo.h) {
                isDragging = true;
                dragStartX = pos.x;
                dragStartY = pos.y;
                photoStartX = photo.x;
                photoStartY = photo.y;
                canvas.style.cursor = 'grabbing';
                e.preventDefault();
                return;
            }
        });

        window.addEventListener('mousemove', function(e) {
            if (selectedPhotoIndex === null) return;
            
            const photo = placedPhotos[selectedPhotoIndex];
            if (!photo) return;
            
            if (isRotating) {
                const pos = getMousePos(e);
                const centerX = photo.x + photo.w/2;
                const centerY = photo.y + photo.h/2;
                
                const angle1 = Math.atan2(dragStartY - centerY, dragStartX - centerX);
                const angle2 = Math.atan2(pos.y - centerY, pos.x - centerX);
                let deltaAngle = (angle2 - angle1) * 180 / Math.PI;
                
                let newRotation = photoStartRotation + deltaAngle;
                newRotation = ((newRotation % 360) + 360) % 360;
                
                photo.rotation = newRotation;
                drawAll();
            }
            else if (isResizing && resizeDirection) {
                const pos = getMousePos(e);
                let deltaX = pos.x - dragStartX;
                let deltaY = pos.y - dragStartY;
                let newX = photoStartX;
                let newY = photoStartY;
                let newW = photoStartW;
                let newH = photoStartH;
                
                switch(resizeDirection) {
                    case 'right':
                        newW = Math.max(30, photoStartW + deltaX);
                        break;
                    case 'left':
                        newW = Math.max(30, photoStartW - deltaX);
                        newX = photoStartX + (photoStartW - newW);
                        break;
                    case 'bottom':
                        newH = Math.max(30, photoStartH + deltaY);
                        break;
                    case 'top':
                        newH = Math.max(30, photoStartH - deltaY);
                        newY = photoStartY + (photoStartH - newH);
                        break;
                    case 'bottom-right':
                        newW = Math.max(30, photoStartW + deltaX);
                        newH = Math.max(30, photoStartH + deltaY);
                        break;
                    case 'bottom-left':
                        newW = Math.max(30, photoStartW - deltaX);
                        newH = Math.max(30, photoStartH + deltaY);
                        newX = photoStartX + (photoStartW - newW);
                        break;
                    case 'top-right':
                        newW = Math.max(30, photoStartW + deltaX);
                        newH = Math.max(30, photoStartH - deltaY);
                        newY = photoStartY + (photoStartH - newH);
                        break;
                    case 'top-left':
                        newW = Math.max(30, photoStartW - deltaX);
                        newH = Math.max(30, photoStartH - deltaY);
                        newX = photoStartX + (photoStartW - newW);
                        newY = photoStartY + (photoStartH - newH);
                        break;
                }
                
                newX = Math.max(5, Math.min(newX, canvas.width - newW - 5));
                newY = Math.max(5, Math.min(newY, canvas.height - newH - 5));
                
                photo.x = newX;
                photo.y = newY;
                photo.w = newW;
                photo.h = newH;
                
                drawAll();
            }
            else if (isDragging) {
                const pos = getMousePos(e);
                let deltaX = pos.x - dragStartX;
                let deltaY = pos.y - dragStartY;
                let newX = photoStartX + deltaX;
                let newY = photoStartY + deltaY;
                
                newX = Math.max(5, Math.min(newX, canvas.width - photo.w - 5));
                newY = Math.max(5, Math.min(newY, canvas.height - photo.h - 5));
                
                photo.x = newX;
                photo.y = newY;
                
                drawAll();
            }
        });

        window.addEventListener('mouseup', function(e) {
            if (isDragging || isResizing || isRotating) {
                isDragging = false;
                isResizing = false;
                isRotating = false;
                resizeDirection = null;
                canvas.style.cursor = 'default';
                drawAll();
                showMessage('Photo updated', 'info');
            }
        });

        canvas.addEventListener('touchstart', function(e) {
            if (selectedPhotoIndex === null) return;
            e.preventDefault();
            
            const photo = placedPhotos[selectedPhotoIndex];
            if (!photo) return;
            
            const pos = getMousePos(e);
            
            if (isOnRotateHandle(pos.x, pos.y, photo)) {
                isRotating = true;
                dragStartX = pos.x;
                dragStartY = pos.y;
                photoStartRotation = photo.rotation;
                return;
            }
            
            const direction = getResizeDirection(pos.x, pos.y, photo);
            if (direction) {
                isResizing = true;
                resizeDirection = direction;
                dragStartX = pos.x;
                dragStartY = pos.y;
                photoStartX = photo.x;
                photoStartY = photo.y;
                photoStartW = photo.w;
                photoStartH = photo.h;
                return;
            }
            else if (pos.x >= photo.x && pos.x <= photo.x + photo.w && pos.y >= photo.y && pos.y <= photo.y + photo.h) {
                isDragging = true;
                dragStartX = pos.x;
                dragStartY = pos.y;
                photoStartX = photo.x;
                photoStartY = photo.y;
            }
        });

        window.addEventListener('touchmove', function(e) {
            if (!isDragging && !isResizing && !isRotating) return;
            if (selectedPhotoIndex === null) return;
            e.preventDefault();
            
            const photo = placedPhotos[selectedPhotoIndex];
            if (!photo) return;
            
            if (isRotating) {
                const pos = getMousePos(e);
                const centerX = photo.x + photo.w/2;
                const centerY = photo.y + photo.h/2;
                
                const angle1 = Math.atan2(dragStartY - centerY, dragStartX - centerX);
                const angle2 = Math.atan2(pos.y - centerY, pos.x - centerX);
                let deltaAngle = (angle2 - angle1) * 180 / Math.PI;
                
                let newRotation = photoStartRotation + deltaAngle;
                newRotation = ((newRotation % 360) + 360) % 360;
                
                photo.rotation = newRotation;
                drawAll();
            }
            else if (isResizing && resizeDirection) {
                const pos = getMousePos(e);
                let deltaX = pos.x - dragStartX;
                let deltaY = pos.y - dragStartY;
                let newX = photoStartX;
                let newY = photoStartY;
                let newW = photoStartW;
                let newH = photoStartH;
                
                switch(resizeDirection) {
                    case 'right': newW = Math.max(30, photoStartW + deltaX); break;
                    case 'left': newW = Math.max(30, photoStartW - deltaX); newX = photoStartX + (photoStartW - newW); break;
                    case 'bottom': newH = Math.max(30, photoStartH + deltaY); break;
                    case 'top': newH = Math.max(30, photoStartH - deltaY); newY = photoStartY + (photoStartH - newH); break;
                    case 'bottom-right': newW = Math.max(30, photoStartW + deltaX); newH = Math.max(30, photoStartH + deltaY); break;
                    case 'bottom-left': newW = Math.max(30, photoStartW - deltaX); newH = Math.max(30, photoStartH + deltaY); newX = photoStartX + (photoStartW - newW); break;
                    case 'top-right': newW = Math.max(30, photoStartW + deltaX); newH = Math.max(30, photoStartH - deltaY); newY = photoStartY + (photoStartH - newH); break;
                    case 'top-left': newW = Math.max(30, photoStartW - deltaX); newH = Math.max(30, photoStartH - deltaY); newX = photoStartX + (photoStartW - newW); newY = photoStartY + (photoStartH - newH); break;
                }
                
                newX = Math.max(5, Math.min(newX, canvas.width - newW - 5));
                newY = Math.max(5, Math.min(newY, canvas.height - newH - 5));
                
                photo.x = newX;
                photo.y = newY;
                photo.w = newW;
                photo.h = newH;
                drawAll();
            }
            else if (isDragging) {
                const pos = getMousePos(e);
                let deltaX = pos.x - dragStartX;
                let deltaY = pos.y - dragStartY;
                let newX = photoStartX + deltaX;
                let newY = photoStartY + deltaY;
                
                newX = Math.max(5, Math.min(newX, canvas.width - photo.w - 5));
                newY = Math.max(5, Math.min(newY, canvas.height - photo.h - 5));
                
                photo.x = newX;
                photo.y = newY;
                drawAll();
            }
        });

        window.addEventListener('touchend', function(e) {
            if (isDragging || isResizing || isRotating) {
                isDragging = false;
                isResizing = false;
                isRotating = false;
                resizeDirection = null;
                drawAll();
                showMessage('Photo updated', 'info');
            }
        });

        canvas.addEventListener('click', function(e) {
            if (!templateLoaded) {
                showMessage('Template not ready', 'error');
                return;
            }
            
            if (!selectedPhotoSrc) {
                showMessage('Select a photo first from the right panel', 'error');
                return;
            }
            
            const pos = getMousePos(e);
            
            let clickedOnPhoto = false;
            for (let i = 0; i < placedPhotos.length; i++) {
                const p = placedPhotos[i];
                if (pos.x >= p.x && pos.x <= p.x + p.w && pos.y >= p.y && pos.y <= p.y + p.h) {
                    clickedOnPhoto = true;
                    selectPhoto(i);
                    break;
                }
            }
            
            if (!clickedOnPhoto) {
                addPhoto(selectedPhotoSrc, selectedPhotoId, pos.x, pos.y);
            }
        });

        let selectedPhotoSrc = null;
        let selectedPhotoId = null;
        
        document.querySelectorAll('.photo-card').forEach(card => {
            card.addEventListener('click', function() {
                if (this.classList.contains('used')) {
                    showMessage('This photo is already used', 'error');
                    return;
                }
                
                document.querySelectorAll('.photo-card').forEach(c => c.classList.remove('selected'));
                this.classList.add('selected');
                selectedPhotoSrc = this.dataset.photo;
                selectedPhotoId = this.dataset.id;
                showMessage('Photo selected. Click on template to place it', 'success');
            });
        });

        document.getElementById('resetBtn').addEventListener('click', function() {
            if (placedPhotos.length > 0 && confirm('Remove all photos?')) {
                placedPhotos = [];
                usedPhotoIds = [];
                selectedPhotoIndex = null;
                updateUsedPhotoStatus();
                updatePhotosList();
                drawAll();
                showMessage('All photos removed', 'info');
            }
        });

        document.getElementById('saveBtn').addEventListener('click', function() {
            if (placedPhotos.length === 0) {
                showMessage('No photos placed', 'error');
                return;
            }
            
            ctx.drawImage(templateImage, 0, 0, canvas.width, canvas.height);
            const resultImage = canvas.toDataURL('image/png');
            
            showMessage('Saving...', 'info');
            
            fetch('save_template_result.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({
                    result_image: resultImage,
                    template_id: <?= $template_id ?>
                })
            })
            .then(res => res.json())
            .then(data => {
                if (data.status === 'success') {
                    showMessage('Saved successfully!', 'success');
                    setTimeout(() => {
                        window.location.href = 'home.php';
                    }, 1500);
                } else {
                    showMessage('Failed: ' + data.message, 'error');
                }
            })
            .catch(err => {
                console.error(err);
                showMessage('Error saving', 'error');
            });
        });

        function showMessage(msg, type) {
            const msgDiv = document.getElementById('message');
            msgDiv.textContent = msg;
            msgDiv.className = 'message ' + type;
            setTimeout(() => {
                if (document.getElementById('message') === msgDiv) {
                    msgDiv.className = 'message';
                    msgDiv.textContent = '';
                }
            }, 3000);
        }
        
        window.selectPhoto = selectPhoto;
        window.removePhoto = removePhoto;
    </script>
</body>
</html>