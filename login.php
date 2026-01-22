 2nd  image revoe from all image tags <?php
session_start();

// Check if user is already logged in
if (isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit();
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Database connection
    require_once 'db_config.php';

    try {
        
        $email = $_POST['email'];
        $password = $_POST['password'];
        
        $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch();
        
        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            header('Location: index.php');
            exit();
        } else {
            $error = 'Invalid email or password';
        }
    } catch(PDOException $e) {
        $error = 'Database error: ' . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Virtual Fitting Room</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            overflow-x: hidden;
        }
        
        .login-wrapper {
            display: flex;
            min-height: 100vh;
        }
        
        /* Left side - Login Form */
        .login-side {
            flex: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            padding: 40px 20px;
            position: relative;
        }
        
        .login-container {
            width: 100%;
            max-width: 450px;
            background: white;
            border-radius: 20px;
            padding: 50px 40px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
        }
        
        .login-header {
            text-align: center;
            margin-bottom: 40px;
        }
        
        .login-header i {
            font-size: 60px;
            color: #667eea;
            margin-bottom: 20px;
        }
        
        .login-header h2 {
            color: #333;
            font-weight: 700;
            margin-bottom: 10px;
        }
        
        .login-header p {
            color: #666;
            font-size: 0.95rem;
        }
        
        .form-control {
            border: 2px solid #e0e0e0;
            border-radius: 10px;
            padding: 12px 15px;
            transition: all 0.3s ease;
        }
        
        .form-control:focus {
            border-color: #667eea;
            box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
        }
        
        .form-label {
            font-weight: 600;
            color: #333;
            margin-bottom: 8px;
        }
        
        .btn-login {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            border-radius: 10px;
            padding: 14px;
            font-weight: 600;
            font-size: 16px;
            transition: all 0.3s ease;
            width: 100%;
        }
        
        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(102, 126, 234, 0.3);
        }
        
        .register-link {
            text-align: center;
            margin-top: 25px;
            padding-top: 25px;
            border-top: 1px solid #e0e0e0;
        }
        
        .register-link a {
            color: #667eea;
            text-decoration: none;
            font-weight: 600;
        }
        
        .register-link a:hover {
            color: #764ba2;
        }
        
        /* Right side - Clothing Display */
        .clothing-side {
            flex: 1;
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 40px;
            position: relative;
            overflow: hidden;
        }
        
        .clothing-showcase {
            position: relative;
            width: 100%;
            max-width: 500px;
            height: 600px;
        }
        
        .clothing-box {
            position: absolute;
            width: 100%;
            height: 100%;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            opacity: 0;
            transition: opacity 1s ease-in-out;
        }
        
        .clothing-box.active {
            opacity: 1;
        }
        
        .clothing-image {
            width: 350px;
            height: 450px;
            object-fit: cover;
            border-radius: 20px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
            margin-bottom: 20px;
        }
        
        .clothing-info {
            text-align: center;
            background: white;
            padding: 20px 30px;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
        }
        
        .clothing-name {
            font-size: 1.5rem;
            font-weight: 700;
            color: #333;
            margin-bottom: 5px;
        }
        
        .clothing-price {
            font-size: 1.3rem;
            color: #667eea;
            font-weight: 600;
        }
        
        .dots-indicator {
            position: absolute;
            bottom: 20px;
            left: 50%;
            transform: translateX(-50%);
            display: flex;
            gap: 10px;
        }
        
        .dot {
            width: 12px;
            height: 12px;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.5);
            cursor: pointer;
            transition: all 0.3s ease;
        }
        
        .dot.active {
            background: #667eea;
            width: 30px;
            border-radius: 6px;
        }
        
        .alert {
            border-radius: 10px;
            border: none;
        }
        
        /* Responsive */
        @media (max-width: 992px) {
            .clothing-side {
                display: none;
            }
            
            .login-side {
                flex: 1;
                width: 100%;
            }
        }
    </style>
</head>
<body>
    <?php include 'includes/navbar.php'; ?>

    <div class="login-wrapper">
        <!-- Left Side - Login Form -->
        <div class="login-side">
            <div class="login-container">
                <div class="login-header">
                    <i class="fas fa-user-circle"></i>
                    <h2>Welcome Back!</h2>
                    <p>Login to continue your fashion journey</p>
                </div>
                
                <?php if ($error): ?>
                    <div class="alert alert-danger">
                        <i class="fas fa-exclamation-circle me-2"></i><?php echo $error; ?>
                    </div>
                <?php endif; ?>
                
                <form method="POST" action="">
                    <div class="mb-3">
                        <label for="email" class="form-label">
                            <i class="fas fa-envelope me-2"></i>Email Address
                        </label>
                        <input type="email" class="form-control" id="email" name="email" placeholder="Enter your email" required>
                    </div>
                    <div class="mb-4">
                        <label for="password" class="form-label">
                            <i class="fas fa-lock me-2"></i>Password
                        </label>
                        <input type="password" class="form-control" id="password" name="password" placeholder="Enter your password" required>
                    </div>
                    <button type="submit" class="btn btn-primary btn-login">
                        <i class="fas fa-sign-in-alt me-2"></i>Login
                    </button>
                </form>
                
                <div class="register-link">
                    <p class="mb-0">Don't have an account? <a href="register.php">Create one now</a></p>
                </div>
            </div>
        </div>
        
        <!-- Right Side - Clothing Display -->
        <div class="clothing-side">
            <div class="clothing-showcase">
                <!-- Clothing Item 1 -->
                <div class="clothing-box active" data-index="0">
                    <img src="https://images.unsplash.com/photo-1617137968427-85924c800a22?w=500&h=600&fit=crop" alt="Trendy Outfit" class="clothing-image">
                    <div class="clothing-info">
                        <div class="clothing-name">Classic Summer Look</div>
                        <div class="clothing-price">₹2,499</div>
                    </div>
                </div>
                
                <!-- Clothing Item 2 -->
                <div class="clothing-box" data-index="1">
                    <img src="https://images.unsplash.com/photo-1490114538077-0a7f8cb49891?w=500&h=600&fit=crop" alt="Elegant Wear" class="clothing-image">
                    <div class="clothing-info">
                        <div class="clothing-name">Evening Elegance</div>
                        <div class="clothing-price">₹3,999</div>
                    </div>
                </div>
                
                <!-- Clothing Item 3 -->
                <div class="clothing-box" data-index="2">
                    <img src="https://images.unsplash.com/photo-1506629082955-511b1aa562c8?w=500&h=600&fit=crop" alt="Casual Style" class="clothing-image">
                    <div class="clothing-info">
                        <div class="clothing-name">Casual Comfort</div>
                        <div class="clothing-price">₹1,899</div>
                    </div>
                </div>
                
                <div class="dots-indicator">
                    <span class="dot active" data-index="0"></span>
                    <span class="dot" data-index="1"></span>
                    <span class="dot" data-index="2"></span>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Clothing carousel
        let currentIndex = 0;
        const boxes = document.querySelectorAll('.clothing-box');
        const dots = document.querySelectorAll('.dot');
        
        function showClothing(index) {
            boxes.forEach((box, i) => {
                box.classList.remove('active');
                dots[i].classList.remove('active');
            });
            
            boxes[index].classList.add('active');
            dots[index].classList.add('active');
            currentIndex = index;
        }
        
        function nextClothing() {
            const next = (currentIndex + 1) % boxes.length;
            showClothing(next);
        }
        
        // Auto rotate every 3 seconds
        setInterval(nextClothing, 3000);
        
        // Dot click handlers
        dots.forEach((dot, index) => {
            dot.addEventListener('click', () => showClothing(index));
        });
    </script>
</body>
</html> 