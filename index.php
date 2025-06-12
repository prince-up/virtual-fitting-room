<?php
session_start();

// Check if user is not logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

// Database connection
$host = 'localhost';
$dbname = 'virtual_fitting_room';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    $error = "Database error: " . $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Virtual Fitting Room</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <!-- AOS Animation -->
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="assets/css/style.css">
    <!-- Three.js and MediaPipe -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/three.js/r128/three.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@mediapipe/camera_utils/camera_utils.js" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/@mediapipe/control_utils/control_utils.js" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/@mediapipe/drawing_utils/drawing_utils.js" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/@mediapipe/pose/pose.js" crossorigin="anonymous"></script>
</head>
<body class="bg-light">
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary sticky-top shadow-sm">
        <div class="container">
            <a class="navbar-brand d-flex align-items-center" href="index.php">
                <i class="fas fa-tshirt me-2"></i>
                <span>Virtual Fitting Room</span>
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link active" href="index.php">
                            <i class="fas fa-home"></i> Home
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="shop.php">
                            <i class="fas fa-shopping-bag"></i> Shop
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="virtual_try.php">
                            <i class="fas fa-tshirt"></i> Try-On
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="cart.php">
                            <i class="fas fa-shopping-cart"></i> Cart
                            <span class="badge bg-danger rounded-pill ms-1" id="cart-count">0</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="about.php">
                            <i class="fas fa-info-circle"></i> About
                        </a>
                    </li>
                </ul>
                <ul class="navbar-nav">
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button" data-bs-toggle="dropdown">
                            <i class="fas fa-user-circle"></i> <?php echo htmlspecialchars($_SESSION['username']); ?>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li><a class="dropdown-item" href="profile.php"><i class="fas fa-user me-2"></i>Profile</a></li>
                            <li><a class="dropdown-item" href="orders.php"><i class="fas fa-box me-2"></i>My Orders</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item" href="logout.php"><i class="fas fa-sign-out-alt me-2"></i>Logout</a></li>
                        </ul>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <div class="container py-5">
        <!-- Hero Section -->
        <div class="row mb-5">
            <div class="col-12">
                <div class="hero-image position-relative rounded-4 overflow-hidden shadow-lg">
                    <img src="https://images.unsplash.com/photo-1441986300917-64674bd600d8?ixlib=rb-4.0.3&auto=format&fit=crop&w=2000&q=80" 
                         alt="Fashion Store" class="img-fluid w-100">
                    <div class="hero-overlay position-absolute top-0 start-0 w-100 h-100 d-flex align-items-center justify-content-center">
                        <div class="text-center text-white">
                            <h1 class="display-4 fw-bold mb-3">Welcome to Virtual Fitting Room</h1>
                            <p class="lead mb-4">Try on clothes virtually before you buy</p>
                            <a href="virtual_try.php" class="btn btn-light btn-lg">Start Fitting Now</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Features Section -->
        <div class="row g-4 mb-5">
            <!-- Shop Section -->
            <div class="col-md-6">
                <div class="card h-100 border-0 shadow-sm">
                    <div class="card-body p-0">
                        <div class="feature-image position-relative">
                            <img src="https://images.unsplash.com/photo-1441986300917-64674bd600d8?ixlib=rb-4.0.3&auto=format&fit=crop&w=1000&q=80" 
                                 alt="Shop Collection" class="img-fluid rounded-top">
                            <div class="feature-content p-4">
                                <i class="fas fa-shopping-bag fa-3x text-primary mb-3"></i>
                                <h3 class="card-title">Shop Now</h3>
                                <p class="card-text">Browse our collection of clothing items and find your perfect style.</p>
                                <a href="shop.php" class="btn btn-primary">Go to Shop</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Virtual Try-On Section -->
            <div class="col-md-6">
                <div class="card h-100 border-0 shadow-sm">
                    <div class="card-body p-0">
                        <div class="feature-image position-relative">
                            <img src="https://images.unsplash.com/photo-1525507119028-ed4c629a60a3?ixlib=rb-4.0.3&auto=format&fit=crop&w=1000&q=80" 
                                 alt="Virtual Try-On" class="img-fluid rounded-top">
                            <div class="feature-content p-4">
                                <i class="fas fa-tshirt fa-3x text-primary mb-3"></i>
                                <h3 class="card-title">Virtual Try-On</h3>
                                <p class="card-text">Try on clothes virtually using our advanced fitting technology.</p>
                                <a href="virtual_try.php" class="btn btn-primary">Try It Now</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Categories Section -->
        <div class="row mb-5">
            <div class="col-12 text-center mb-4">
                <h2 class="display-5">Featured Categories</h2>
                <p class="lead text-muted">Explore our wide range of clothing categories</p>
            </div>
            <!-- Tops Section -->
            <div class="col-md-4 mb-4">
                <div class="card h-100 border-0 shadow-sm">
                    <img src="https://images.unsplash.com/photo-1521572163474-6864f9cf17ab?ixlib=rb-4.0.3&auto=format&fit=crop&w=1000&q=80" 
                         class="card-img-top" alt="Casual T-Shirts">
                    <div class="card-body text-center">
                        <h3 class="card-title">Casual T-Shirts</h3>
                        <a href="shop.php?category=casual-tshirts" class="btn btn-outline-primary">Browse Casual T-Shirts</a>
                    </div>
                </div>
            </div>
            <div class="col-md-4 mb-4">
                <div class="card h-100 border-0 shadow-sm">
                    <img src="https://images.unsplash.com/photo-1523381294911-8d3cead13475?ixlib=rb-4.0.3&auto=format&fit=crop&w=1000&q=80" 
                         class="card-img-top" alt="Graphic T-Shirts">
                    <div class="card-body text-center">
                        <h3 class="card-title">Graphic T-Shirts</h3>
                        <a href="shop.php?category=graphic-tshirts" class="btn btn-outline-primary">Browse Graphic T-Shirts</a>
                    </div>
                </div>
            </div>
            <div class="col-md-4 mb-4">
                <div class="card h-100 border-0 shadow-sm">
                    <img src="https://images.unsplash.com/photo-1529374255404-311a2a4f1fd9?ixlib=rb-4.0.3&auto=format&fit=crop&w=1000&q=80" 
                         class="card-img-top" alt="Polo T-Shirts">
                    <div class="card-body text-center">
                        <h3 class="card-title">Polo T-Shirts</h3>
                        <a href="shop.php?category=polo-tshirts" class="btn btn-outline-primary">Browse Polo T-Shirts</a>
                    </div>
                </div>
            </div>
            <div class="col-md-4 mb-4">
                <div class="card h-100 border-0 shadow-sm">
                    <img src="https://images.unsplash.com/photo-1523381210434-271e8be1f52b?ixlib=rb-4.0.3&auto=format&fit=crop&w=1000&q=80" 
                         class="card-img-top" alt="V-Neck T-Shirts">
                    <div class="card-body text-center">
                        <h3 class="card-title">V-Neck T-Shirts</h3>
                        <a href="shop.php?category=vneck-tshirts" class="btn btn-outline-primary">Browse V-Neck T-Shirts</a>
                    </div>
                </div>
            </div>
            <div class="col-md-4 mb-4">
                <div class="card h-100 border-0 shadow-sm">
                    <img src="https://images.unsplash.com/photo-1529374255404-311a2a4f1fd9?ixlib=rb-4.0.3&auto=format&fit=crop&w=1000&q=80" 
                         class="card-img-top" alt="Long Sleeve T-Shirts">
                    <div class="card-body text-center">
                        <h3 class="card-title">Long Sleeve T-Shirts</h3>
                        <a href="shop.php?category=longsleeve-tshirts" class="btn btn-outline-primary">Browse Long Sleeve T-Shirts</a>
                    </div>
                </div>
            </div>
            <div class="col-md-4 mb-4">
                <div class="card h-100 border-0 shadow-sm">
                    <img src="https://images.unsplash.com/photo-1521572163474-6864f9cf17ab?ixlib=rb-4.0.3&auto=format&fit=crop&w=1000&q=80" 
                         class="card-img-top" alt="Basic T-Shirts">
                    <div class="card-body text-center">
                        <h3 class="card-title">Basic T-Shirts</h3>
                        <a href="shop.php?category=basic-tshirts" class="btn btn-outline-primary">Browse Basic T-Shirts</a>
                    </div>
                </div>
            </div>
            <!-- Bottoms Section -->
            <div class="col-md-4 mb-4">
                <div class="card h-100 border-0 shadow-sm">
                    <img src="https://images.unsplash.com/photo-1542272604-787c3835535d?ixlib=rb-4.0.3&auto=format&fit=crop&w=1000&q=80" 
                         class="card-img-top" alt="Jeans">
                    <div class="card-body text-center">
                        <h3 class="card-title">Jeans</h3>
                        <a href="shop.php?category=jeans" class="btn btn-outline-primary">Browse Jeans</a>
                    </div>
                </div>
            </div>
            <div class="col-md-4 mb-4">
                <div class="card h-100 border-0 shadow-sm">
                    <img src="https://images.unsplash.com/photo-1591195853828-11db59a44f6b?ixlib=rb-4.0.3&auto=format&fit=crop&w=1000&q=80" 
                         class="card-img-top" alt="Pants">
                    <div class="card-body text-center">
                        <h3 class="card-title">Pants</h3>
                        <a href="shop.php?category=pants" class="btn btn-outline-primary">Browse Pants</a>
                    </div>
                </div>
            </div>
            <!-- Dresses Section -->
            <div class="col-md-4 mb-4">
                <div class="card h-100 border-0 shadow-sm">
                    <img src="https://images.unsplash.com/photo-1572804013309-59a88b7e92f1?ixlib=rb-4.0.3&auto=format&fit=crop&w=1000&q=80" 
                         class="card-img-top" alt="Women's Dresses">
                    <div class="card-body text-center">
                        <h3 class="card-title">Women's Dresses</h3>
                        <a href="shop.php?category=womens-dresses" class="btn btn-outline-primary">Browse Dresses</a>
                    </div>
                </div>
            </div>
            <!-- Kids Section -->
            <div class="col-md-4 mb-4">
                <div class="card h-100 border-0 shadow-sm">
                    <img src="https://images.unsplash.com/photo-1596462502278-27bfdc403348?ixlib=rb-4.0.3&auto=format&fit=crop&w=1000&q=80" 
                         class="card-img-top" alt="Kids' Dresses">
                    <div class="card-body text-center">
                        <h3 class="card-title">Kids' Dresses</h3>
                        <a href="shop.php?category=kids-dresses" class="btn btn-outline-primary">Browse Kids' Dresses</a>
                    </div>
                </div>
            </div>
            <div class="col-md-4 mb-4">
                <div class="card h-100 border-0 shadow-sm">
                    <img src="https://images.unsplash.com/photo-1596462502278-27bfdc403348?ixlib=rb-4.0.3&auto=format&fit=crop&w=1000&q=80" 
                         class="card-img-top" alt="Kids' T-Shirts">
                    <div class="card-body text-center">
                        <h3 class="card-title">Kids' T-Shirts</h3>
                        <a href="shop.php?category=kids-tshirts" class="btn btn-outline-primary">Browse Kids' T-Shirts</a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Chatbot Interface -->
    <button id="chatbot-button">
        <div class="button-content">
            <i class="fas fa-comment-dots"></i>
            <span class="notification-dot"></span>
        </div>
    </button>
    
    <div id="chatbot-window" class="modern-chat">
        <div id="chatbot-header">
            <div class="chatbot-header-content">
                <div class="chatbot-avatar">
                    <img src="https://i.imgur.com/7Rh7qwX.png" alt="AI Assistant">
                </div>
                <div class="chatbot-title">
                    <h3>Fashion Assistant</h3>
                    <span class="online-status">Online</span>
                </div>
            </div>
            <button id="minimize-chat" class="minimize-button">
                <i class="fas fa-minus"></i>
            </button>
        </div>
        <div id="chatbot-messages"></div>
        <div id="chatbot-camera" style="display: none;">
            <div class="camera-container">
                <video id="webcam" style="display: none;"></video>
                <canvas id="output-canvas" width="480" height="360"></canvas>
                <div id="measurements" class="measurements-overlay"></div>
            </div>
        </div>
        <div class="typing-indicator">
            <div class="typing-dots">
                <span></span><span></span><span></span>
            </div>
            <span class="typing-text">AI is typing...</span>
        </div>
        <div id="chatbot-suggestions">
            <button class="suggestion-chip" onclick="askQuestion('How do I use virtual try-on?')">Virtual Try-on Help</button>
            <button class="suggestion-chip" onclick="askQuestion('Show me clothing categories')">Browse Categories</button>
            <button class="suggestion-chip" onclick="askQuestion('How to measure size?')">Size Guide</button>
        </div>
        <div id="chatbot-input-container">
            <button id="camera-toggle" class="feature-button" onclick="toggleCamera()">
                <i class="fas fa-camera"></i>
            </button>
            <input type="text" id="chatbot-input" placeholder="Type your message here...">
            <button id="send-message" class="feature-button">
                <i class="fas fa-paper-plane"></i>
            </button>
        </div>
    </div>

    <style>
    /* General Styles */
    body {
        background-color: #f8f9fa;
        background-image: url('https://images.unsplash.com/photo-1441986300917-64674bd600d8?ixlib=rb-4.0.3&auto=format&fit=crop&w=2000&q=80');
        background-size: cover;
        background-position: center;
        background-attachment: fixed;
        background-blend-mode: overlay;
        background-color: rgba(248, 249, 250, 0.9);
    }

    /* Hero Section */
    .hero-image {
        height: 500px;
        border-radius: 1rem;
        overflow: hidden;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
    }
    
    .hero-image img {
        object-fit: cover;
        height: 100%;
        width: 100%;
    }
    
    .hero-overlay {
        background: linear-gradient(to bottom, rgba(0, 0, 0, 0.3), rgba(0, 0, 0, 0.7));
    }

    .hero-overlay h1 {
        text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.5);
        font-weight: 700;
    }

    .hero-overlay .btn-light {
        padding: 0.75rem 2rem;
        font-weight: 600;
        border-radius: 50px;
        transition: all 0.3s ease;
    }

    .hero-overlay .btn-light:hover {
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(255, 255, 255, 0.3);
    }
    
    /* Feature Cards */
    .feature-image {
        height: 300px;
        border-radius: 0.5rem;
        overflow: hidden;
    }
    
    .feature-image img {
        object-fit: cover;
        height: 100%;
        width: 100%;
        transition: transform 0.5s ease;
    }
    
    .feature-content {
        background: white;
        padding: 2rem;
        border-radius: 0.5rem;
    }

    .feature-content i {
        color: #0d6efd;
        margin-bottom: 1.5rem;
    }

    .feature-content h3 {
        font-weight: 600;
        margin-bottom: 1rem;
    }

    .feature-content .btn {
        padding: 0.5rem 1.5rem;
        border-radius: 50px;
        font-weight: 500;
        transition: all 0.3s ease;
    }
    
    /* Category Cards */
    .card {
        border: none;
        border-radius: 1rem;
        overflow: hidden;
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        transition: all 0.3s ease;
        background-color: rgba(255, 255, 255, 0.95);
    }
    
    .card:hover {
        transform: translateY(-10px);
        box-shadow: 0 15px 30px rgba(0, 0, 0, 0.2);
    }

    .card:hover .card-img-top {
        transform: scale(1.05);
    }
    
    .card-img-top {
        height: 250px;
        object-fit: cover;
        transition: transform 0.5s ease;
    }

    .card-body {
        padding: 1.5rem;
        text-align: center;
    }

    .card-title {
        font-weight: 600;
        margin-bottom: 1rem;
    }

    .btn-outline-primary {
        padding: 0.5rem 1.5rem;
        border-radius: 50px;
        font-weight: 500;
        transition: all 0.3s ease;
    }

    .btn-outline-primary:hover {
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(13, 110, 253, 0.2);
    }

    /* Section Headers */
    .display-5 {
        font-weight: 700;
        margin-bottom: 1rem;
        color: #212529;
    }

    .lead.text-muted {
        color: #6c757d !important;
        margin-bottom: 2rem;
    }

    /* Responsive Adjustments */
    @media (max-width: 768px) {
        .hero-image {
            height: 400px;
        }

        .feature-image {
            height: 200px;
        }

        .card-img-top {
            height: 200px;
        }

        .hero-overlay h1 {
            font-size: 2.5rem;
        }
    }

    @media (max-width: 576px) {
        .hero-image {
            height: 300px;
        }

        .hero-overlay h1 {
            font-size: 2rem;
        }

        .feature-content {
            padding: 1.5rem;
        }
    }

    .chatbot-container {
        position: fixed;
        bottom: 20px;
        right: 20px;
        z-index: 1000;
    }

    .chatbot-toggle {
        width: 60px;
        height: 60px;
        border-radius: 50%;
        background-color: #0d6efd;
        color: white;
        border: none;
        cursor: pointer;
        box-shadow: 0 2px 10px rgba(0,0,0,0.2);
        transition: transform 0.3s;
    }

    .chatbot-toggle:hover {
        transform: scale(1.1);
    }

    .chatbot-window {
        position: fixed;
        bottom: 90px;
        right: 20px;
        width: 400px;
        height: 600px;
        background: white;
        border-radius: 15px;
        box-shadow: 0 5px 25px rgba(0,0,0,0.2);
        z-index: 1000;
        display: none;
        flex-direction: column;
        transition: all 0.3s ease;
        opacity: 0;
        transform: translateY(20px);
    }

    .chatbot-window.active {
        display: flex;
        opacity: 1;
        transform: translateY(0);
    }

    .chatbot-header {
        padding: 20px;
        background: linear-gradient(135deg, #0d6efd, #0b5ed7);
        color: white;
        border-radius: 15px 15px 0 0;
        display: flex;
        flex-direction: column;
        align-items: flex-start;
    }

    .chatbot-header .close-btn {
        background: none;
        border: none;
        color: white;
        cursor: pointer;
    }

    .chatbot-messages {
        flex-grow: 1;
        padding: 20px;
        overflow-y: auto;
        background: #f8f9fa;
        display: flex;
        flex-direction: column;
        gap: 15px;
    }

    .message {
        max-width: 85%;
        padding: 12px 18px;
        border-radius: 20px;
        font-size: 14px;
        line-height: 1.4;
        position: relative;
        animation: messagePopIn 0.3s ease-out;
    }

    @keyframes messagePopIn {
        0% {
            opacity: 0;
            transform: translateY(10px);
        }
        100% {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .user {
        background: #0d6efd;
        color: white;
        align-self: flex-end;
        border-bottom-right-radius: 5px;
    }

    .bot {
        background: white;
        color: #212529;
        align-self: flex-start;
        border-bottom-left-radius: 5px;
        box-shadow: 0 2px 5px rgba(0,0,0,0.1);
    }

    #chatbot-input-container {
        padding: 20px;
        background: white;
        border-top: 1px solid #dee2e6;
        border-radius: 0 0 15px 15px;
        display: flex;
        gap: 10px;
        align-items: center;
    }

    #chatbot-input {
        flex-grow: 1;
        padding: 12px 15px;
        border: 1px solid #dee2e6;
        border-radius: 25px;
        outline: none;
        font-size: 14px;
        transition: border-color 0.3s ease;
    }

    #chatbot-input:focus {
        border-color: #0d6efd;
        box-shadow: 0 0 0 3px rgba(13, 110, 253, 0.1);
    }

    #send-message {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        background: #0d6efd;
        color: white;
        border: none;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: all 0.3s ease;
    }

    #send-message:hover {
        background: #0b5ed7;
        transform: scale(1.1);
    }

    .typing-indicator {
        display: none;
        background-color: white;
        padding: 12px 18px;
        border-radius: 20px;
        align-self: flex-start;
        box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        animation: messagePopIn 0.3s ease-out;
    }

    .typing-indicator span {
        display: inline-block;
        width: 8px;
        height: 8px;
        background-color: #0d6efd;
        border-radius: 50%;
        margin-right: 5px;
        animation: typing 1s infinite;
        opacity: 0.4;
    }

    @keyframes typing {
        0%, 100% { transform: translateY(0); }
        50% { transform: translateY(-5px); }
    }

    .typing-indicator span:nth-child(2) { animation-delay: 0.2s; }
    .typing-indicator span:nth-child(3) { animation-delay: 0.4s; }

    #chatbot-camera {
        margin: 10px 20px;
        border-radius: 10px;
        overflow: hidden;
        background: #000;
        position: relative;
    }

    .camera-container {
        position: relative;
        width: 100%;
        height: 0;
        padding-bottom: 75%;
    }

    #output-canvas {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .measurements-overlay {
        position: absolute;
        top: 10px;
        right: 10px;
        background: rgba(0, 0, 0, 0.8);
        color: white;
        padding: 10px 15px;
        border-radius: 10px;
        font-size: 13px;
        z-index: 2;
    }

    #camera-toggle {
        margin: 10px 20px;
        padding: 10px 20px;
        background: #0d6efd;
        color: white;
        border: none;
        border-radius: 25px;
        cursor: pointer;
        font-size: 14px;
        transition: all 0.3s ease;
        width: calc(100% - 40px);
    }

    #camera-toggle:hover {
        background: #0b5ed7;
        transform: translateY(-2px);
    }

    /* Modern Chat Styles */
    .modern-chat {
        position: fixed;
        bottom: 90px;
        right: 20px;
        width: 380px;
        height: 600px;
        background: #ffffff;
        border-radius: 20px;
        box-shadow: 0 8px 40px rgba(0,0,0,0.16);
        z-index: 1000;
        display: none;
        flex-direction: column;
        transition: all 0.3s ease;
        overflow: hidden;
    }

    .modern-chat.active {
        display: flex;
        animation: slideIn 0.3s ease-out;
    }

    @keyframes slideIn {
        from {
            opacity: 0;
            transform: translateY(20px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    #chatbot-header {
        padding: 20px;
        background: linear-gradient(135deg, #2962ff, #1565c0);
        color: white;
        border-radius: 20px 20px 0 0;
    }

    .chatbot-header-content {
        display: flex;
        align-items: center;
        gap: 15px;
    }

    .chatbot-avatar {
        width: 45px;
        height: 45px;
        border-radius: 50%;
        overflow: hidden;
        border: 2px solid rgba(255,255,255,0.2);
    }

    .chatbot-avatar img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .chatbot-title {
        flex-grow: 1;
    }

    .chatbot-title h3 {
        margin: 0;
        font-size: 18px;
        font-weight: 600;
    }

    .online-status {
        font-size: 12px;
        color: #4caf50;
        display: flex;
        align-items: center;
        gap: 5px;
    }

    .online-status::before {
        content: '';
        display: inline-block;
        width: 8px;
        height: 8px;
        background: #4caf50;
        border-radius: 50%;
    }

    .minimize-button {
        background: none;
        border: none;
        color: white;
        cursor: pointer;
        padding: 5px;
        border-radius: 50%;
        transition: background-color 0.3s;
    }

    .minimize-button:hover {
        background: rgba(255,255,255,0.1);
    }

    #chatbot-messages {
        flex-grow: 1;
        padding: 20px;
        overflow-y: auto;
        background: #f8f9fa;
        display: flex;
        flex-direction: column;
        gap: 15px;
    }

    .message {
        max-width: 85%;
        padding: 12px 18px;
        border-radius: 20px;
        font-size: 14px;
        line-height: 1.5;
        position: relative;
        animation: messagePopIn 0.3s ease-out;
    }

    .message.bot {
        background: white;
        color: #212529;
        border-bottom-left-radius: 5px;
        box-shadow: 0 2px 5px rgba(0,0,0,0.05);
        margin-right: auto;
    }

    .message.user {
        background: #2962ff;
        color: white;
        border-bottom-right-radius: 5px;
        margin-left: auto;
    }

    #chatbot-suggestions {
        padding: 10px 20px;
        display: flex;
        gap: 10px;
        overflow-x: auto;
        background: white;
        border-top: 1px solid #eee;
    }

    .suggestion-chip {
        padding: 8px 16px;
        background: #f0f2f5;
        border: none;
        border-radius: 20px;
        white-space: nowrap;
        font-size: 13px;
        color: #2962ff;
        cursor: pointer;
        transition: all 0.3s;
    }

    .suggestion-chip:hover {
        background: #e3f2fd;
        transform: translateY(-1px);
    }

    #chatbot-input-container {
        padding: 15px 20px;
        background: white;
        border-top: 1px solid #eee;
        display: flex;
        gap: 10px;
        align-items: center;
    }

    #chatbot-input {
        flex-grow: 1;
        padding: 12px 15px;
        border: 1px solid #e0e0e0;
        border-radius: 25px;
        outline: none;
        font-size: 14px;
        transition: all 0.3s;
    }

    #chatbot-input:focus {
        border-color: #2962ff;
        box-shadow: 0 0 0 3px rgba(41,98,255,0.1);
    }

    .feature-button {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        border: none;
        background: #f0f2f5;
        color: #2962ff;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        transition: all 0.3s;
    }

    .feature-button:hover {
        background: #e3f2fd;
        transform: scale(1.05);
    }

    #chatbot-button {
        position: fixed;
        bottom: 20px;
        right: 20px;
        width: 60px;
        height: 60px;
        border-radius: 50%;
        background: #2962ff;
        border: none;
        cursor: pointer;
        box-shadow: 0 4px 15px rgba(41,98,255,0.35);
        z-index: 999;
        transition: all 0.3s;
    }

    #chatbot-button:hover {
        transform: scale(1.1);
        box-shadow: 0 6px 20px rgba(41,98,255,0.4);
    }

    .button-content {
        width: 100%;
        height: 100%;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 24px;
        position: relative;
    }

    .notification-dot {
        position: absolute;
        top: 0;
        right: 0;
        width: 12px;
        height: 12px;
        background: #4caf50;
        border-radius: 50%;
        border: 2px solid white;
    }

    .typing-indicator {
        display: none;
        padding: 12px 18px;
        background: white;
        border-radius: 20px;
        align-self: flex-start;
        margin: 10px 20px;
        box-shadow: 0 2px 5px rgba(0,0,0,0.05);
    }

    .typing-dots {
        display: flex;
        gap: 4px;
    }

    .typing-dots span {
        width: 8px;
        height: 8px;
        background: #2962ff;
        border-radius: 50%;
        opacity: 0.4;
        animation: typing 1s infinite;
    }

    .typing-text {
        font-size: 12px;
        color: #666;
        margin-left: 10px;
    }

    @keyframes typing {
        0%, 100% { transform: translateY(0); }
        50% { transform: translateY(-5px); }
    }

    .typing-dots span:nth-child(2) { animation-delay: 0.2s; }
    .typing-dots span:nth-child(3) { animation-delay: 0.4s; }

    /* Scrollbar Styling */
    #chatbot-messages::-webkit-scrollbar {
        width: 6px;
    }

    #chatbot-messages::-webkit-scrollbar-track {
        background: #f1f1f1;
    }

    #chatbot-messages::-webkit-scrollbar-thumb {
        background: #ccc;
        border-radius: 3px;
    }

    #chatbot-messages::-webkit-scrollbar-thumb:hover {
        background: #999;
    }

    /* Camera Styles */
    #chatbot-camera {
        margin: 10px 20px;
        border-radius: 15px;
        overflow: hidden;
        background: #000;
        position: relative;
    }

    .camera-container {
        position: relative;
        width: 100%;
        height: 0;
        padding-bottom: 75%;
    }

    #output-canvas {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .measurements-overlay {
        position: absolute;
        top: 10px;
        right: 10px;
        background: rgba(0,0,0,0.8);
        color: white;
        padding: 12px 18px;
        border-radius: 15px;
        font-size: 13px;
        z-index: 2;
    }
    </style>

    <script>
        // Import required MediaPipe utilities
        const { drawConnectors, drawLandmarks } = window;
        const { POSE_CONNECTIONS } = window;
        
        document.addEventListener('DOMContentLoaded', function() {
            const chatbotButton = document.getElementById('chatbot-button');
            const chatbotWindow = document.getElementById('chatbot-window');
            const chatbotMessages = document.getElementById('chatbot-messages');
            const chatbotInput = document.getElementById('chatbot-input');
            const sendButton = document.getElementById('send-message');
            const typingIndicator = document.querySelector('.typing-indicator');
            const cameraToggle = document.getElementById('camera-toggle');
            const chatbotCamera = document.getElementById('chatbot-camera');
            const webcam = document.getElementById('webcam');
            const outputCanvas = document.getElementById('output-canvas');
            const ctx = outputCanvas.getContext('2d');

            let isProcessing = false;
            let stream = null;
            let pose;
            let camera;
            let isCameraActive = false;

            async function initializePose() {
                pose = new Pose({
                    locateFile: (file) => {
                        return `https://cdn.jsdelivr.net/npm/@mediapipe/pose/${file}`;
                    }
                });

                pose.setOptions({
                    modelComplexity: 1,
                    smoothLandmarks: true,
                    minDetectionConfidence: 0.5,
                    minTrackingConfidence: 0.5
                });

                pose.onResults(onResults);

                camera = new Camera(document.getElementById('webcam'), {
                    onFrame: async () => {
                        if (isCameraActive) {
                            await pose.send({image: document.getElementById('webcam')});
                        }
                    },
                    width: 480,
                    height: 360
                });
            }

            function onResults(results) {
                const canvas = document.getElementById('output-canvas');
                const ctx = canvas.getContext('2d');
                
                ctx.clearRect(0, 0, canvas.width, canvas.height);
                
                if (results.poseLandmarks) {
                    // Draw pose landmarks
                    ctx.fillStyle = '#00FF00';
                    for (const landmark of results.poseLandmarks) {
                        ctx.beginPath();
                        ctx.arc(landmark.x * canvas.width, landmark.y * canvas.height, 5, 0, 2 * Math.PI);
                        ctx.fill();
                    }
                    
                    // Calculate and display measurements
                    const measurements = calculateMeasurements(results.poseLandmarks);
                    displayMeasurements(measurements);
                }
            }

            function calculateMeasurements(landmarks) {
                if (!landmarks) return null;

                // Calculate shoulder width (between left and right shoulders)
                const shoulderWidth = calculateDistance(
                    landmarks[11], // Left shoulder
                    landmarks[12]  // Right shoulder
                );

                // Calculate chest width (between left and right chest points)
                const chestWidth = calculateDistance(
                    landmarks[11], // Left shoulder
                    landmarks[12]  // Right shoulder
                ) * 1.1; // Approximating chest as slightly wider than shoulders

                // Calculate waist width (between left and right hip points)
                const waistWidth = calculateDistance(
                    landmarks[23], // Left hip
                    landmarks[24]  // Right hip
                );

                // Convert to approximate centimeters (this is a rough estimation)
                const pixelToCm = 100; // This value should be calibrated based on real-world testing
                return {
                    shoulderWidth: (shoulderWidth * pixelToCm).toFixed(1),
                    chestWidth: (chestWidth * pixelToCm).toFixed(1),
                    waistWidth: (waistWidth * pixelToCm).toFixed(1)
                };
            }

            function calculateDistance(point1, point2) {
                if (!point1 || !point2) return 0;
                
                // Using 3D coordinates for more accurate measurements
                const dx = point1.x - point2.x;
                const dy = point1.y - point2.y;
                const dz = point1.z - point2.z;
                
                return Math.sqrt(dx * dx + dy * dy + dz * dz);
            }

            function displayMeasurements(measurements) {
                const measurementsDiv = document.getElementById('measurements');
                if (!measurements) {
                    measurementsDiv.innerHTML = 'No measurements available';
                    return;
                }

                measurementsDiv.innerHTML = `
                    <p>Shoulder Width: ${measurements.shoulderWidth} cm</p>
                    <p>Chest Width: ${measurements.chestWidth} cm</p>
                    <p>Waist Width: ${measurements.waistWidth} cm</p>
                `;
            }

            async function toggleCamera() {
                const button = document.getElementById('camera-toggle');
                const chatbotCamera = document.getElementById('chatbot-camera');
                
                if (!isCameraActive) {
                    isCameraActive = true;
                    button.textContent = 'Turn Off Camera';
                    chatbotCamera.style.display = 'block';
                    
                    if (!camera) {
                        await initializePose();
                    }
                    await camera.start();
                } else {
                    isCameraActive = false;
                    button.textContent = 'Turn On Camera';
                    chatbotCamera.style.display = 'none';
                    
                    // Clear measurements when camera is off
                    document.getElementById('measurements').innerHTML = '';
                }
            }

            // Initialize pose detection when the page loads
            document.addEventListener('DOMContentLoaded', initializePose);

            // Toggle chatbot window
            chatbotButton.addEventListener('click', () => {
                chatbotWindow.classList.toggle('active');
                chatbotButton.classList.toggle('active');
                if (chatbotWindow.classList.contains('active')) {
                    chatbotInput.focus();
                }
            });

            // Send message when clicking send button
            sendButton.addEventListener('click', handleMessage);

            // Send message when pressing Enter
            chatbotInput.addEventListener('keypress', (e) => {
                if (e.key === 'Enter' && !e.shiftKey) {
                    e.preventDefault();
                    handleMessage();
                }
            });

            async function handleMessage() {
                if (isProcessing) return;

                const message = chatbotInput.value.trim();
                if (!message) return;

                try {
                    isProcessing = true;
                    
                    // Clear input and add user message
                    chatbotInput.value = '';
                    addMessage('user', message);
                    
                    // Show typing indicator
                    typingIndicator.style.display = 'block';
                    chatbotMessages.scrollTop = chatbotMessages.scrollHeight;

                    // Send message to API
                    const response = await fetch('gemini_chat.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                        },
                        body: JSON.stringify({ message: message })
                    });

                    const data = await response.json();

                    // Hide typing indicator
                    typingIndicator.style.display = 'none';

                    if (data.success) {
                        addMessage('bot', data.response);
                    } else {
                        throw new Error(data.response || 'Failed to get response');
                    }
                } catch (error) {
                    console.error('Error:', error);
                    typingIndicator.style.display = 'none';
                    addMessage('bot', 'I apologize, but I encountered an error. Please try asking your question again.');
                } finally {
                    isProcessing = false;
                    chatbotMessages.scrollTop = chatbotMessages.scrollHeight;
                }
            }

            function addMessage(type, text) {
                const messageDiv = document.createElement('div');
                messageDiv.classList.add('message', type);
                
                // Format links in the text
                const formattedText = text.replace(
                    /(https?:\/\/[^\s]+)/g, 
                    '<a href="$1" target="_blank" rel="noopener noreferrer">$1</a>'
                );
                
                messageDiv.innerHTML = formattedText;
                chatbotMessages.appendChild(messageDiv);
                chatbotMessages.scrollTop = chatbotMessages.scrollHeight;
            }

            // Add initial welcome message
            setTimeout(() => {
                addMessage('bot', "👋 Hi! I'm your AI Fashion Assistant. I can help you with:");
                addMessage('bot', "• Virtual try-ons\n• Size measurements\n• Shopping assistance\n• Style advice\n\nHow can I help you today?");
            }, 1000);

            // Add minimize button functionality
            document.getElementById('minimize-chat').addEventListener('click', function() {
                document.getElementById('chatbot-window').classList.remove('active');
            });
        });

        // Add this to your existing JavaScript
        function askQuestion(question) {
            document.getElementById('chatbot-input').value = question;
            handleMessage();
        }
    </script>

    <!-- Bootstrap JS and other scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    <script>
        AOS.init();
    </script>
</body>
</html> 