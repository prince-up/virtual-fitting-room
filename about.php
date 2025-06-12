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
    <title>About Us - Virtual Fitting Room</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <!-- Custom CSS -->
    <style>
        .about-section {
            padding: 80px 0;
            background-color: #f8f9fa;
        }
        .feature-card {
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 0 15px rgba(0,0,0,0.1);
            height: 100%;
            transition: transform 0.3s ease;
        }
        .feature-card:hover {
            transform: translateY(-5px);
        }
        .feature-icon {
            font-size: 2.5rem;
            color: #ff9900;
            margin-bottom: 20px;
        }
        .team-section {
            padding: 80px 0;
            background-color: #fff;
        }
        .team-card {
            text-align: center;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 0 15px rgba(0,0,0,0.1);
            margin-bottom: 30px;
            background-color: #fff;
            transition: transform 0.3s ease;
        }
        .team-card:hover {
            transform: translateY(-5px);
        }
        .team-image {
            width: 200px;
            height: 200px;
            border-radius: 50%;
            margin: 0 auto 20px;
            object-fit: cover;
            border: 5px solid #ff9900;
        }
        .team-name {
            color: #232f3e;
            font-size: 1.5rem;
            margin-bottom: 10px;
        }
        .team-role {
            color: #ff9900;
            font-weight: 600;
            margin-bottom: 15px;
        }
        .team-description {
            color: #666;
            font-size: 0.9rem;
        }
        .model-card {
            position: relative;
            overflow: hidden;
            border-radius: 15px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.2);
            margin-bottom: 30px;
            height: 400px;
        }
        .model-image {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.5s ease;
        }
        .model-card:hover .model-image {
            transform: scale(1.1);
        }
        .model-overlay {
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            background: linear-gradient(transparent, rgba(0,0,0,0.7));
            padding: 20px;
            color: white;
            text-align: center;
        }
        .model-overlay h4 {
            margin: 0;
            font-size: 1.5rem;
            font-weight: 600;
        }
    </style>
</head>
<body>
    <?php include 'includes/navbar.php'; ?>

    <!-- Hero Section -->
    <div class="about-section">
        <div class="container">
            <div class="row align-items-center mb-5">
                <div class="col-md-6">
                    <h1 class="display-4 mb-4">About Virtual Fitting Room</h1>
                    <p class="lead mb-4">Welcome to the future of online shopping! Our virtual fitting room combines cutting-edge technology with fashion to provide you with a unique and convenient shopping experience.</p>
                    <p class="mb-4">Try on clothes virtually, get personalized style recommendations, and shop with confidence from the comfort of your home.</p>
                </div>
                <div class="col-md-6">
                    <img src="assets/images/ut.jpg" alt="Virtual Fitting Room" class="img-fluid rounded shadow">
                </div>
            </div>

            <div class="row align-items-center">
                <div class="col-md-6">
                    <img src="assets/images/pri.jpg" alt="Virtual Fitting Room" class="img-fluid rounded shadow">
                </div>
                <div class="col-md-6">
                    <div class="ps-md-4">
                        <h2 class="mb-4">Experience Fashion Like Never Before!</h2>
                        <p class="lead mb-4">Say goodbye to fitting room hassles! Our Virtual Fitting Room lets you try clothes digitally before buying. Simply upload a photo or use your camera to see how outfits look on you in real-time.</p>
                        
                        <div class="mb-4">
                            <h4 class="mb-3">✨ Benefits:</h4>
                            <ul class="list-unstyled">
                                <li><i class="fas fa-check-circle text-success me-2"></i>Saves time – No more long trial room queues</li>
                                <li><i class="fas fa-check-circle text-success me-2"></i>Perfect fit – AI-powered size recommendations</li>
                                <li><i class="fas fa-check-circle text-success me-2"></i>Mix & match – Experiment with styles effortlessly</li>
                            </ul>
                        </div>
                        
                        <p class="mb-3">🛍️ Shop smarter, try virtually!</p>
                        <div class="hashtags text-muted">
                            <small>#VirtualFittingRoom #FutureOfFashion #SmartShopping</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Team Section -->
    <div class="team-section">
        <div class="container">
            <h2 class="text-center mb-5">Meet Our Team</h2>
            <div class="row justify-content-center">
                <div class="col-md-3">
                    <div class="team-card">
                        <img src="assets/images/PRIN PHHOTO.jpg" alt="Prince Yadav" class="team-image">
                        <h3 class="team-name">PRINCE YADAV</h3>
                        <div class="team-role">Founder & CEO</div>
                        <p class="team-description">Visionary leader with expertise in e-commerce and fashion technology. Driving innovation in virtual shopping experiences.</p>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="team-card">
                        <img src="assets/images/hitesh.jpg" alt="Harshit" class="team-image">
                        <h3 class="team-name">HARSHIT</h3>
                        <div class="team-role">CTO</div>
                        <p class="team-description">Technical expert specializing in computer vision and augmented reality. Leading our technology development.</p>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="team-card">
                        <img src="assets/images/harshit.jpg" alt="Hitesh" class="team-image">
                        <h3 class="team-name">HITESH</h3>
                        <div class="team-role">Head of Design</div>
                        <p class="team-description">Creative director focused on user experience and interface design. Making virtual try-ons intuitive and enjoyable.</p>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="team-card">
                        <img src="assets/images/mang.jpg" alt="Shreyash" class="team-image">
                        <h3 class="team-name">SHREYASH</h3>
                        <div class="team-role">Lead Developer</div>
                        <p class="team-description">Full-stack developer with expertise in AR/VR technologies. Building seamless and innovative virtual fitting experiences.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Fashion Models Section -->
    <div class="container-fluid py-5 bg-light">
        <div class="container">
            <h2 class="text-center mb-5">Our Fashion Models</h2>
            <div class="row g-4">
                <div class="col-md-6 col-lg-4">
                    <div class="model-card">
                        <img src="assets/images/ut4.jpg" alt="Fashion Model" class="img-fluid model-image">
                        <div class="model-overlay">
                            <h4>Summer Collection</h4>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 col-lg-4">
                    <div class="model-card">
                        <img src="assets/images/ut2.jpg" alt="Fashion Model" class="img-fluid model-image">
                        <div class="model-overlay">
                            <h4>Evening Wear</h4>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 col-lg-4">
                    <div class="model-card">
                        <img src="assets/images/utu3.jpg" alt="Fashion Model" class="img-fluid model-image">
                        <div class="model-overlay">
                            <h4>Casual Collection</h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Features Section -->
    <div class="container py-5">
        <h2 class="text-center mb-5">Our Features</h2>
        <div class="row g-4">
            <div class="col-md-4">
                <div class="feature-card bg-white">
                    <i class="fas fa-tshirt feature-icon"></i>
                    <h4>Virtual Try-On</h4>
                    <p>Experience clothes virtually before buying. Our advanced technology lets you see how outfits look on you in real-time.</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="feature-card bg-white">
                    <i class="fas fa-magic feature-icon"></i>
                    <h4>Style Recommendations</h4>
                    <p>Get personalized style suggestions based on your preferences, body type, and previous purchases.</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="feature-card bg-white">
                    <i class="fas fa-shopping-bag feature-icon"></i>
                    <h4>Easy Shopping</h4>
                    <p>Browse through our extensive collection of clothes and accessories with an intuitive shopping interface.</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Contact Section -->
    <div class="bg-light py-5">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-md-8 text-center">
                    <h2 class="mb-4">Get In Touch</h2>
                    <p class="mb-4">Have questions or feedback? We'd love to hear from you!</p>
                    <div class="row g-3 justify-content-center">
                        <div class="col-md-4">
                            <div class="d-flex flex-column align-items-center">
                                <i class="fas fa-envelope mb-2 feature-icon"></i>
                                <h5>Email</h5>
                                <p>contact@virtualfittingroom.com</p>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="d-flex flex-column align-items-center">
                                <i class="fas fa-phone mb-2 feature-icon"></i>
                                <h5>Phone</h5>
                                <p>+91 7986614646</p>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="d-flex flex-column align-items-center">
                                <i class="fas fa-map-marker-alt mb-2 feature-icon"></i>
                                <h5>Location</h5>
                                <p>123 Fashion Street, Style City  </p>
                                <P> INDIA</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>