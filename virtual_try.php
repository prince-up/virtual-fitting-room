<?php
session_start();

// Check if user is not logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Virtual Try-On - Fashion Store</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        body {
            background-image: url('https://images.unsplash.com/photo-1441986300917-64674bd600d8?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=1950&q=80');
            background-size: cover;
            background-position: center;
            background-attachment: fixed;
            min-height: 100vh;
            position: relative;
        }

        body::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(255, 255, 255, 0.85);
            z-index: 0;
        }

        .content-wrapper {
            position: relative;
            z-index: 1;
        }

        .navbar {
            background: rgba(33, 37, 41, 0.95) !important;
            backdrop-filter: blur(10px);
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }

        .navbar-brand {
            font-weight: 700;
            color: #fff !important;
            font-size: 1.5rem;
        }

        .nav-link {
            color: rgba(255, 255, 255, 0.85) !important;
            font-weight: 500;
            transition: color 0.3s ease;
        }

        .nav-link:hover {
            color: #fff !important;
        }

        .nav-link.active {
            color: #fff !important;
            border-bottom: 2px solid #fff;
        }

        .cart-icon {
            position: relative;
        }

        .cart-count {
            position: absolute;
            top: -8px;
            right: -8px;
            background-color: #dc3545;
            color: white;
            border-radius: 50%;
            padding: 0.25rem 0.5rem;
            font-size: 0.75rem;
            font-weight: bold;
        }

        .container {
            background: rgba(255, 255, 255, 0.95);
            border-radius: 15px;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.1);
            padding: 2rem;
            margin-top: 2rem;
            margin-bottom: 2rem;
        }

        .btn-primary {
            background-color: #4a90e2;
            border-color: #4a90e2;
            padding: 0.5rem 1.5rem;
            font-weight: 600;
        }

        .btn-primary:hover {
            background-color: #357abd;
            border-color: #357abd;
        }
    </style>
</head>
<body>
    <!-- Modern Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark fixed-top">
        <div class="container-fluid">
            <a class="navbar-brand" href="index.php">
                <i class="fas fa-tshirt me-2"></i>Fashion Store
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="index.php">Home</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="shop.php">Shop</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="virtual_try.php">Virtual Try-On</a>
                    </li>
                </ul>
                <ul class="navbar-nav">
                    <li class="nav-item">
                        <a class="nav-link cart-icon" href="cart.php">
                            <i class="fas fa-shopping-cart"></i>
                            <span class="cart-count" id="cartCount">0</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="profile.php">
                            <i class="fas fa-user me-1"></i>Profile
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="logout.php">
                            <i class="fas fa-sign-out-alt me-1"></i>Logout
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="content-wrapper">
        <div class="container mt-5 pt-5">
            <div class="row">
                <!-- Image Source Selection -->
                <div class="col-md-6 mb-4">
                    <div class="card">
                        <div class="card-header bg-primary text-white">
                            <h5 class="mb-0"><i class="fas fa-image me-2"></i>Choose Image Source</h5>
                        </div>
                        <div class="card-body">
                            <div class="d-grid gap-3">
                                <button id="openCameraBtn" class="btn btn-primary btn-lg">
                                    <i class="fas fa-camera me-2"></i>Open Camera
                                </button>
                                <button id="uploadImageBtn" class="btn btn-secondary btn-lg">
                                    <i class="fas fa-upload me-2"></i>Upload from Gallery
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Camera Section (Initially Hidden) -->
                <div class="col-md-6 mb-4" id="cameraSection" style="display: none;">
                    <div class="card">
                        <div class="card-header bg-primary text-white">
                            <h5 class="mb-0"><i class="fas fa-camera me-2"></i>Take Photo</h5>
                        </div>
                        <div class="card-body">
                            <div class="camera-container mb-3">
                                <video id="video" class="w-100" autoplay playsinline muted></video>
                                <canvas id="canvas" class="d-none"></canvas>
                            </div>
                            <div class="camera-buttons">
                                <button id="capture" class="btn btn-success">
                                    <i class="fas fa-camera-retro me-2"></i>Capture Photo
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Image Upload Section (Initially Hidden) -->
                <div class="col-md-6 mb-4" id="uploadSection" style="display: none;">
                    <div class="card">
                        <div class="card-header bg-primary text-white">
                            <h5 class="mb-0"><i class="fas fa-upload me-2"></i>Upload Photo</h5>
                        </div>
                        <div class="card-body">
                            <form id="uploadForm">
                                <div class="mb-3">
                                    <input type="file" class="form-control" id="imageUpload" accept="image/*" required>
                                </div>
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-upload me-2"></i>Upload
                                </button>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- Preview Section -->
                <div class="col-md-6 mb-4">
                    <div class="card">
                        <div class="card-header bg-primary text-white">
                            <h5 class="mb-0"><i class="fas fa-image me-2"></i>Preview</h5>
                        </div>
                        <div class="card-body">
                            <div id="preview" class="text-center mb-3">
                                <img id="photo" class="img-fluid" style="max-height: 400px; display: none;">
                            </div>
                            <div class="d-flex justify-content-center">
                                <button id="analyze" class="btn btn-info" disabled>
                                    <i class="fas fa-magic me-2"></i>Get Style Suggestions
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Suggestions Section -->
            <div class="row mt-4">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header bg-primary text-white">
                            <h5 class="mb-0"><i class="fas fa-lightbulb me-2"></i>AI Style Suggestions</h5>
                        </div>
                        <div class="card-body">
                            <div id="suggestions" class="row g-4">
                                <!-- Suggestions will be loaded here -->
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // DOM Elements
        const openCameraBtn = document.getElementById('openCameraBtn');
        const uploadImageBtn = document.getElementById('uploadImageBtn');
        const cameraSection = document.getElementById('cameraSection');
        const uploadSection = document.getElementById('uploadSection');
        const video = document.getElementById('video');
        const canvas = document.getElementById('canvas');
        const photo = document.getElementById('photo');
        const captureBtn = document.getElementById('capture');
        const analyzeBtn = document.getElementById('analyze');
        const uploadForm = document.getElementById('uploadForm');
        const imageUpload = document.getElementById('imageUpload');
        const suggestionsDiv = document.getElementById('suggestions');

        let stream = null;

        // Open Camera Button Click
        openCameraBtn.addEventListener('click', async () => {
            try {
                // Hide upload section and show camera section
                uploadSection.style.display = 'none';
                cameraSection.style.display = 'block';

                // Request camera permissions
                stream = await navigator.mediaDevices.getUserMedia({ 
                    video: { 
                        width: { ideal: 1280 },
                        height: { ideal: 720 },
                        facingMode: "user"
                    },
                    audio: false
                });

                video.srcObject = stream;
                await new Promise((resolve) => {
                    video.onloadedmetadata = () => {
                        video.play();
                        resolve();
                    };
                });

                console.log('Camera started successfully');
            } catch (err) {
                console.error("Camera error:", err);
                alert("Error accessing camera. Please make sure you have granted camera permissions.");
            }
        });

        // Upload Image Button Click
        uploadImageBtn.addEventListener('click', () => {
            cameraSection.style.display = 'none';
            uploadSection.style.display = 'block';
        });

        // Capture Photo
        captureBtn.addEventListener('click', () => {
            canvas.width = video.videoWidth;
            canvas.height = video.videoHeight;
            canvas.getContext('2d').drawImage(video, 0, 0);
            photo.src = canvas.toDataURL('image/jpeg');
            photo.style.display = 'block';
            analyzeBtn.disabled = false;
        });

        // Handle Image Upload
        uploadForm.addEventListener('submit', (e) => {
            e.preventDefault();
            const file = imageUpload.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = (e) => {
                    photo.src = e.target.result;
                    photo.style.display = 'block';
                    analyzeBtn.disabled = false;
                };
                reader.readAsDataURL(file);
            }
        });

        // Analyze Image and Get Suggestions
        analyzeBtn.addEventListener('click', async () => {
            try {
                analyzeBtn.disabled = true;
                analyzeBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Analyzing...';
                suggestionsDiv.innerHTML = '<div class="col-12"><div class="alert alert-info">Analyzing image, please wait...</div></div>';

                // Get image data
                const imageData = photo.src.split(',')[1];

                // Prepare the request body
                const requestBody = {
                    contents: [{
                        parts: [{
                            text: "Analyze this person's style and suggest appropriate clothing items (shirts, pants, t-shirts) that would suit them. Consider their body type, skin tone, and overall appearance. Provide specific color and style recommendations. Format the response with clear sections for each type of clothing."
                        }, {
                            inline_data: {
                                mime_type: "image/jpeg",
                                data: imageData
                            }
                        }]
                    }]
                };

                // Call Gemini API with correct endpoint
                const response = await fetch('https://generativelanguage.googleapis.com/v1/models/gemini-1.5-flash:generateContent?key=AIzaSyBA8m9ZxPDLSV6NqYnx48ARntCWfMrE9Sg', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify(requestBody)
                });

                if (!response.ok) {
                    const errorData = await response.json();
                    throw new Error(`API request failed: ${errorData.error?.message || `Status ${response.status}`}`);
                }

                const data = await response.json();
                
                if (!data.candidates || !data.candidates[0] || !data.candidates[0].content || !data.candidates[0].content.parts[0]) {
                    throw new Error('Invalid response format from API');
                }

                const suggestions = data.candidates[0].content.parts[0].text;
                if (!suggestions) {
                    throw new Error('No suggestions found in API response');
                }

                // Fetch matching clothing items from database
                const clothingResponse = await fetch('get_suggested_clothing.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({ suggestions: suggestions })
                });

                const clothingData = await clothingResponse.json();
                displaySuggestions(suggestions, clothingData);
            } catch (error) {
                console.error('Error analyzing image:', error);
                let errorMessage = error.message;
                
                // Handle specific API errors
                if (errorMessage.includes('API key not valid')) {
                    errorMessage = 'API key is invalid. Please check your API key configuration.';
                } else if (errorMessage.includes('quota')) {
                    errorMessage = 'API quota exceeded. Please try again later.';
                } else if (errorMessage.includes('404')) {
                    errorMessage = 'API endpoint not found. Please check the API configuration.';
                }

                suggestionsDiv.innerHTML = `
                    <div class="col-12">
                        <div class="alert alert-danger">
                            <h6>Error getting suggestions</h6>
                            <p>${errorMessage}</p>
                            <p>Please try again or contact support if the problem persists.</p>
                        </div>
                    </div>
                `;
            } finally {
                analyzeBtn.disabled = false;
                analyzeBtn.innerHTML = '<i class="fas fa-magic me-2"></i>Get Style Suggestions';
            }
        });

        function displaySuggestions(suggestions, clothingData) {
            suggestionsDiv.innerHTML = '';
            
            // Split suggestions into sections
            const sections = suggestions.split('\n\n').filter(section => section.trim());
            
            if (sections.length === 0) {
                suggestionsDiv.innerHTML = `
                    <div class="col-12">
                        <div class="alert alert-warning">
                            No specific suggestions were generated. Please try again with a different image.
                        </div>
                    </div>
                `;
                return;
            }
            
            sections.forEach(section => {
                const lines = section.split('\n').filter(line => line.trim());
                if (lines.length > 0) {
                    const category = lines[0].toLowerCase();
                    const matchingItems = clothingData.filter(item => 
                        item.category.toLowerCase().includes(category) || 
                        item.name.toLowerCase().includes(category)
                    );

                    const card = document.createElement('div');
                    card.className = 'col-md-6 col-lg-4 mb-4';
                    card.innerHTML = `
                        <div class="card h-100">
                            <div class="card-body">
                                <h6 class="card-title text-primary">${lines[0]}</h6>
                                <p class="card-text">${lines.slice(1).join('<br>')}</p>
                                
                                ${matchingItems.length > 0 ? `
                                    <div class="mt-3">
                                        <h6 class="text-muted">Suggested Items:</h6>
                                        <div class="row g-2">
                                            ${matchingItems.map(item => `
                                                <div class="col-6">
                                                    <div class="suggested-item">
                                                        <img src="${item.image_url}" alt="${item.name}" class="img-fluid rounded">
                                                        <div class="item-info mt-2">
                                                            <small class="text-muted">${item.name}</small>
                                                            <div class="text-primary">$${item.price}</div>
                                                        </div>
                                                    </div>
                                                </div>
                                            `).join('')}
                                        </div>
                                    </div>
                                ` : ''}
                            </div>
                        </div>
                    `;
                    suggestionsDiv.appendChild(card);
                }
            });
        }

        // Clean up camera stream when leaving page
        window.addEventListener('beforeunload', () => {
            if (stream) {
                stream.getTracks().forEach(track => track.stop());
            }
        });

        // Update cart count from localStorage
        function updateCartCount() {
            const cart = JSON.parse(localStorage.getItem('cart')) || [];
            const cartCount = cart.reduce((total, item) => total + item.quantity, 0);
            document.getElementById('cartCount').textContent = cartCount;
        }
        
        // Call updateCartCount when page loads
        document.addEventListener('DOMContentLoaded', updateCartCount);
    </script>

    <style>
    .camera-container {
        position: relative;
        width: 100%;
        padding-top: 75%; /* 4:3 Aspect Ratio */
        background: #f8f9fa;
        border-radius: 8px;
        overflow: hidden;
        border: 2px solid #ddd;
    }

    .camera-container video,
    .camera-container canvas {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    #preview {
        min-height: 400px;
        background: #f8f9fa;
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        border: 2px solid #ddd;
    }

    .camera-buttons {
        margin-top: 1rem;
        display: flex;
        gap: 1rem;
        justify-content: center;
    }

    .camera-buttons button {
        min-width: 150px;
    }

    .card {
        transition: transform 0.3s ease;
    }

    .card:hover {
        transform: translateY(-5px);
    }

    .suggested-item {
        text-align: center;
        padding: 10px;
        border: 1px solid #eee;
        border-radius: 8px;
        transition: transform 0.3s ease;
    }

    .suggested-item:hover {
        transform: translateY(-5px);
        box-shadow: 0 4px 8px rgba(0,0,0,0.1);
    }

    .suggested-item img {
        max-height: 150px;
        object-fit: cover;
        border-radius: 4px;
    }

    .item-info {
        font-size: 0.9rem;
    }
    </style>
</body>
</html> 