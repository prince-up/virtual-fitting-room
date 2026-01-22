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
    <title>Fashion Models & Influencers - Virtual Fitting Room</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        body {
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
            min-height: 100vh;
        }
        
        .hero-section {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 80px 0 60px;
            margin-bottom: 50px;
        }
        
        .hero-section h1 {
            font-size: 3rem;
            font-weight: 700;
            margin-bottom: 20px;
        }
        
        .hero-section p {
            font-size: 1.2rem;
            opacity: 0.9;
        }
        
        .influencer-card {
            background: white;
            border-radius: 20px;
            overflow: hidden;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
            transition: all 0.4s ease;
            margin-bottom: 30px;
            position: relative;
        }
        
        .influencer-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 20px 50px rgba(0,0,0,0.2);
        }
        
        .influencer-image {
            width: 100%;
            height: 400px;
            object-fit: cover;
            transition: transform 0.5s ease;
        }
        
        .influencer-card:hover .influencer-image {
            transform: scale(1.05);
        }
        
        .influencer-content {
            padding: 25px;
        }
        
        .influencer-name {
            font-size: 1.8rem;
            font-weight: 700;
            color: #333;
            margin-bottom: 10px;
        }
        
        .influencer-title {
            color: #667eea;
            font-weight: 600;
            font-size: 1.1rem;
            margin-bottom: 15px;
        }
        
        .influencer-stats {
            display: flex;
            justify-content: space-around;
            padding: 20px 0;
            border-top: 2px solid #f0f0f0;
            border-bottom: 2px solid #f0f0f0;
            margin: 15px 0;
        }
        
        .stat-item {
            text-align: center;
        }
        
        .stat-number {
            font-size: 1.5rem;
            font-weight: 700;
            color: #667eea;
        }
        
        .stat-label {
            font-size: 0.9rem;
            color: #666;
        }
        
        .social-links {
            display: flex;
            gap: 15px;
            justify-content: center;
            margin-top: 20px;
        }
        
        .social-btn {
            width: 45px;
            height: 45px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.2rem;
            transition: all 0.3s ease;
            text-decoration: none;
        }
        
        .social-btn.instagram {
            background: linear-gradient(45deg, #f09433 0%, #e6683c 25%, #dc2743 50%, #cc2366 75%, #bc1888 100%);
            color: white;
        }
        
        .social-btn.twitter {
            background: #1DA1F2;
            color: white;
        }
        
        .social-btn.tiktok {
            background: #000;
            color: white;
        }
        
        .social-btn:hover {
            transform: scale(1.1) rotate(5deg);
        }
        
        .specialty-badge {
            display: inline-block;
            padding: 5px 15px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border-radius: 20px;
            font-size: 0.85rem;
            margin: 5px;
        }
        
        .collection-preview {
            display: flex;
            gap: 10px;
            margin-top: 15px;
            flex-wrap: wrap;
        }
        
        .collection-img {
            width: 80px;
            height: 80px;
            border-radius: 10px;
            object-fit: cover;
            transition: transform 0.3s ease;
            cursor: pointer;
        }
        
        .collection-img:hover {
            transform: scale(1.1);
        }
    </style>
</head>
<body>
    <?php include 'includes/navbar.php'; ?>

    <!-- Hero Section -->
    <div class="hero-section">
        <div class="container text-center">
            <h1>Fashion Models & Influencers</h1>
            <p>Meet our talented fashion ambassadors who bring style to life</p>
        </div>
    </div>

    <!-- Influencers Grid -->
    <div class="container pb-5">
        <div class="row">
            <!-- Influencer 1 -->
            <div class="col-md-6 col-lg-3">
                <div class="influencer-card">
                    <img src="assets/images/IMG_5327.PNG" alt="Fashion Influencer" class="influencer-image">
                    <div class="influencer-content">
                        <h3 class="influencer-name">Beach Style Icon</h3>
                        <p class="influencer-title">Fashion & Lifestyle Influencer</p>
                        
                        <div class="influencer-stats">
                            <div class="stat-item">
                                <div class="stat-number">500K+</div>
                                <div class="stat-label">Followers</div>
                            </div>
                            <div class="stat-item">
                                <div class="stat-number">2.5M</div>
                                <div class="stat-label">Likes</div>
                            </div>
                            <div class="stat-item">
                                <div class="stat-number">850+</div>
                                <div class="stat-label">Posts</div>
                            </div>
                        </div>
                        
                        <div class="mt-3">
                            <span class="specialty-badge">Streetwear</span>
                            <span class="specialty-badge">Casual</span>
                            <span class="specialty-badge">Formal</span>
                        </div>
                        
                        <div class="collection-preview">
                            <img src="assets/images/2.jpg" alt="Collection" class="collection-img">
                            <img src="assets/images/3.jpg" alt="Collection" class="collection-img">
                            <img src="assets/images/toi7.jpg" alt="Collection" class="collection-img">
                        </div>
                        
                        <div class="social-links">
                            <a href="#" class="social-btn instagram"><i class="fab fa-instagram"></i></a>
                            <a href="#" class="social-btn twitter"><i class="fab fa-twitter"></i></a>
                            <a href="#" class="social-btn tiktok"><i class="fab fa-tiktok"></i></a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Influencer 2 -->
            <div class="col-md-6 col-lg-3">
                <div class="influencer-card">
                    <img src="assets/images/IMG_5328.PNG" alt="Fashion Influencer" class="influencer-image">
                    <div class="influencer-content">
                        <h3 class="influencer-name">Garden Elegance</h3>
                        <p class="influencer-title">Luxury Fashion Model</p>
                        
                        <div class="influencer-stats">
                            <div class="stat-item">
                                <div class="stat-number">750K+</div>
                                <div class="stat-label">Followers</div>
                            </div>
                            <div class="stat-item">
                                <div class="stat-number">3.2M</div>
                                <div class="stat-label">Likes</div>
                            </div>
                            <div class="stat-item">
                                <div class="stat-number">1.2K+</div>
                                <div class="stat-label">Posts</div>
                            </div>
                        </div>
                        
                        <div class="mt-3">
                            <span class="specialty-badge">Evening Wear</span>
                            <span class="specialty-badge">Designer</span>
                            <span class="specialty-badge">Luxury</span>
                        </div>
                        
                        <div class="collection-preview">
                            <img src="assets/images/hitesh.jpg" alt="Collection" class="collection-img">
                            <img src="assets/images/harshit.jpg" alt="Collection" class="collection-img">
                            <img src="assets/images/2.jpg" alt="Collection" class="collection-img">
                        </div>
                        
                        <div class="social-links">
                            <a href="#" class="social-btn instagram"><i class="fab fa-instagram"></i></a>
                            <a href="#" class="social-btn twitter"><i class="fab fa-twitter"></i></a>
                            <a href="#" class="social-btn tiktok"><i class="fab fa-tiktok"></i></a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Influencer 3 -->
            <div class="col-md-6 col-lg-3">
                <div class="influencer-card">
                    <img src="assets/images/IMG_5329.PNG" alt="Fashion Influencer" class="influencer-image">
                    <div class="influencer-content">
                        <h3 class="influencer-name">Urban Adventure</h3>
                        <p class="influencer-title">Street Style Icon</p>
                        
                        <div class="influencer-stats">
                            <div class="stat-item">
                                <div class="stat-number">420K+</div>
                                <div class="stat-label">Followers</div>
                            </div>
                            <div class="stat-item">
                                <div class="stat-number">1.8M</div>
                                <div class="stat-label">Likes</div>
                            </div>
                            <div class="stat-item">
                                <div class="stat-number">680+</div>
                                <div class="stat-label">Posts</div>
                            </div>
                        </div>
                        
                        <div class="mt-3">
                            <span class="specialty-badge">Casual</span>
                            <span class="specialty-badge">Athletic</span>
                            <span class="specialty-badge">Street</span>
                        </div>
                        
                        <div class="collection-preview">
                            <img src="assets/images/3.jpg" alt="Collection" class="collection-img">
                            <img src="assets/images/toi7.jpg" alt="Collection" class="collection-img">
                            <img src="assets/images/hitesh.jpg" alt="Collection" class="collection-img">
                        </div>
                        
                        <div class="social-links">
                            <a href="#" class="social-btn instagram"><i class="fab fa-instagram"></i></a>
                            <a href="#" class="social-btn twitter"><i class="fab fa-twitter"></i></a>
                            <a href="#" class="social-btn tiktok"><i class="fab fa-tiktok"></i></a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Influencer 4 -->
            <div class="col-md-6 col-lg-3">
                <div class="influencer-card">
                    <img src="assets/images/IMG_4848.JPG.jpeg" alt="Fashion Influencer" class="influencer-image">
                    <div class="influencer-content">
                        <h3 class="influencer-name">Modern Trends</h3>
                        <p class="influencer-title">Contemporary Fashion Expert</p>
                        
                        <div class="influencer-stats">
                            <div class="stat-item">
                                <div class="stat-number">380K+</div>
                                <div class="stat-label">Followers</div>
                            </div>
                            <div class="stat-item">
                                <div class="stat-number">1.5M</div>
                                <div class="stat-label">Likes</div>
                            </div>
                            <div class="stat-item">
                                <div class="stat-number">540+</div>
                                <div class="stat-label">Posts</div>
                            </div>
                        </div>
                        
                        <div class="mt-3">
                            <span class="specialty-badge">Modern</span>
                            <span class="specialty-badge">Trendy</span>
                            <span class="specialty-badge">Chic</span>
                        </div>
                        
                        <div class="collection-preview">
                            <img src="assets/images/harshit.jpg" alt="Collection" class="collection-img">
                            <img src="assets/images/prince imag.jpg" alt="Collection" class="collection-img">
                            <img src="assets/images/2.jpg" alt="Collection" class="collection-img">
                        </div>
                        
                        <div class="social-links">
                            <a href="#" class="social-btn instagram"><i class="fab fa-instagram"></i></a>
                            <a href="#" class="social-btn twitter"><i class="fab fa-twitter"></i></a>
                            <a href="#" class="social-btn tiktok"><i class="fab fa-tiktok"></i></a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
