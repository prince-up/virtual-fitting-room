<?php
session_start();

// Check if user is already logged in
if (isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

$error = '';
$success = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get form data
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    $phone = trim($_POST['phone'] ?? '');
    $address = trim($_POST['address'] ?? '');
    $city = trim($_POST['city'] ?? '');
    $state = trim($_POST['state'] ?? '');
    $pincode = trim($_POST['pincode'] ?? '');

    // Validate input
    if (empty($username) || empty($email) || empty($password) || empty($confirm_password)) {
        $error = "All fields are required";
    } elseif ($password !== $confirm_password) {
        $error = "Passwords do not match";
    } elseif (strlen($password) < 6) {
        $error = "Password must be at least 6 characters long";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Invalid email format";
    } else {
        try {
            // Connect to database
            require_once 'db_config.php';

            // Check if email already exists
            $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
            $stmt->execute([$email]);
            
            if ($stmt->rowCount() > 0) {
                $error = "Email already exists";
            } else {
                // Hash password
                $hashed_password = password_hash($password, PASSWORD_DEFAULT);

                // Prepare SQL statement
                $sql = "INSERT INTO users (username, email, password, phone, address, city, state, pincode) 
                        VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
                
                // Execute with all parameters
                $stmt = $pdo->prepare($sql);
                $result = $stmt->execute([
                    $username,
                    $email,
                    $hashed_password,
                    $phone,
                    $address,
                    $city,
                    $state,
                    $pincode
                ]);

                if ($result) {
                    $success = "Registration successful! You can now login.";
                    // Redirect to login page after 2 seconds
                    header("refresh:2;url=login.php");
                } else {
                    $error = "Registration failed. Please try again.";
                }
            }
        } catch(PDOException $e) {
            $error = "Registration failed: " . $e->getMessage();
            // Log the error for debugging
            error_log("Registration error: " . $e->getMessage());
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - Virtual Fitting Room</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <!-- Animate.css -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css">
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
        
        .register-wrapper {
            display: flex;
            min-height: 100vh;
        }
        
        /* Left side - Register Form */
        .register-side {
            flex: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            padding: 40px 20px;
            position: relative;
            overflow-y: auto;
        }
        
        .register-container {
            width: 100%;
            max-width: 500px;
            background: white;
            border-radius: 20px;
            padding: 40px 35px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
            margin: 20px 0;
        }
        
        .register-header {
            text-align: center;
            margin-bottom: 30px;
        }
        
        .register-header i {
            font-size: 50px;
            color: #667eea;
            margin-bottom: 15px;
        }
        
        .register-header h2 {
            color: #333;
            font-weight: 700;
            margin-bottom: 8px;
        }
        
        .register-header p {
            color: #666;
            font-size: 0.95rem;
        }
        
        .form-control, .form-select {
            border: 2px solid #e0e0e0;
            border-radius: 10px;
            padding: 11px 15px;
            transition: all 0.3s ease;
        }
        
        .form-control:focus, .form-select:focus {
            border-color: #667eea;
            box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
        }
        
        .form-label {
            font-weight: 600;
            color: #333;
            margin-bottom: 6px;
            font-size: 0.9rem;
        }
        
        .password-strength {
            height: 4px;
            margin-top: 5px;
            border-radius: 2px;
            background-color: #e2e8f0;
        }
        
        .strength-bar {
            height: 100%;
            border-radius: 2px;
            transition: all 0.3s ease;
        }
        
        .strength-0 { width: 20%; background-color: #ef4444; }
        .strength-1 { width: 40%; background-color: #f59e0b; }
        .strength-2 { width: 60%; background-color: #f59e0b; }
        .strength-3 { width: 80%; background-color: #10b981; }
        .strength-4 { width: 100%; background-color: #10b981; }
        
        .btn-register {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            border-radius: 10px;
            padding: 13px;
            font-weight: 600;
            font-size: 16px;
            transition: all 0.3s ease;
            width: 100%;
        }
        
        .btn-register:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(102, 126, 234, 0.3);
        }
        
        .login-link {
            text-align: center;
            margin-top: 20px;
            padding-top: 20px;
            border-top: 1px solid #e0e0e0;
        }
        
        .login-link a {
            color: #667eea;
            text-decoration: none;
            font-weight: 600;
        }
        
        .login-link a:hover {
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
            margin-bottom: 20px;
        }
        
        .validation-icon {
            position: absolute;
            right: 15px;
            top: 38px;
            display: none;
        }
        
        .input-wrapper {
            position: relative;
        }
        
        /* Responsive */
        @media (max-width: 992px) {
            .clothing-side {
                display: none;
            }
            
            .register-side {
                flex: 1;
                width: 100%;
            }
        }
    </style>
</head>
<body>
    <?php include 'includes/navbar.php'; ?>
    
    <div class="register-wrapper">
        <!-- Left Side - Register Form -->
        <div class="register-side">
            <div class="register-container">
                <div class="register-header">
                    <i class="fas fa-user-plus"></i>
                    <h2>Join Us Today!</h2>
                    <p>Create your Virtual Fitting Room account</p>
                </div>
                
                <?php if ($error): ?>
                    <div class="alert alert-danger animate__animated animate__shakeX">
                        <i class="fas fa-exclamation-circle me-2"></i><?php echo $error; ?>
                    </div>
                <?php endif; ?>
                <?php if ($success): ?>
                    <div class="alert alert-success animate__animated animate__bounceIn">
                        <i class="fas fa-check-circle me-2"></i><?php echo $success; ?>
                    </div>
                <?php endif; ?>
                
                <form method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" id="signupForm">
                    <div class="mb-3">
                        <label for="username" class="form-label">
                            <i class="fas fa-user me-2"></i>Username
                        </label>
                        <input type="text" class="form-control" id="username" name="username" placeholder="Choose a username" required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="email" class="form-label">
                            <i class="fas fa-envelope me-2"></i>Email Address
                        </label>
                        <input type="email" class="form-control" id="email" name="email" placeholder="Enter your email" required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="password" class="form-label">
                            <i class="fas fa-lock me-2"></i>Password
                        </label>
                        <input type="password" class="form-control" id="password" name="password" placeholder="Create a password" required>
                        <div class="password-strength">
                            <div class="strength-bar" id="passwordStrength"></div>
                        </div>
                    </div>
                    
                    <div class="mb-3 input-wrapper">
                        <label for="confirm_password" class="form-label">
                            <i class="fas fa-lock me-2"></i>Confirm Password
                        </label>
                        <input type="password" class="form-control" id="confirm_password" name="confirm_password" placeholder="Confirm your password" required>
                        <span class="validation-icon" id="confirmCheck"></span>
                    </div>
                    
                    <div class="mb-3">
                        <label for="phone" class="form-label">
                            <i class="fas fa-phone me-2"></i>Phone Number
                        </label>
                        <input type="tel" class="form-control" id="phone" name="phone" placeholder="Your phone number">
                    </div>
                    
                    <div class="mb-3">
                        <label for="address" class="form-label">
                            <i class="fas fa-map-marker-alt me-2"></i>Address
                        </label>
                        <textarea class="form-control" id="address" name="address" rows="2" placeholder="Your address"></textarea>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="city" class="form-label">City</label>
                            <input type="text" class="form-control" id="city" name="city" placeholder="City">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="state" class="form-label">State</label>
                            <input type="text" class="form-control" id="state" name="state" placeholder="State">
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="pincode" class="form-label">Pincode</label>
                        <input type="text" class="form-control" id="pincode" name="pincode" placeholder="Pincode">
                    </div>
                    
                    <button type="submit" class="btn btn-primary btn-register">
                        <i class="fas fa-user-plus me-2"></i>
                        <span class="submit-text">Create Account</span>
                        <span class="spinner-border spinner-border-sm d-none ms-2" role="status"></span>
                    </button>
                </form>
                
                <div class="login-link">
                    <p class="mb-0">Already have an account? <a href="login.php">Sign in here</a></p>
                </div>
            </div>
        </div>
        
        <!-- Right Side - Clothing Display -->
        <div class="clothing-side">
            <div class="clothing-showcase">
                <!-- Clothing Item 1 -->
                <div class="clothing-box active" data-index="0">
                    <img src="assets/images/harshit.jpg" alt="Stylish Collection" class="clothing-image">
                    <div class="clothing-info">
                        <div class="clothing-name">Modern Formal Wear</div>
                        <div class="clothing-price">₹3,499</div>
                    </div>
                </div>
                
                <!-- Clothing Item 2 -->
                <div class="clothing-box" data-index="1">
                    <img src="assets/images/toi7.jpg" alt="Trending Fashion" class="clothing-image">
                    <div class="clothing-info">
                        <div class="clothing-name">Athletic Style</div>
                        <div class="clothing-price">₹2,799</div>
                    </div>
                </div>
                
                <!-- Clothing Item 3 -->
                <div class="clothing-box" data-index="2">
                    <img src="assets/images/hitesh.jpg" alt="Latest Trends" class="clothing-image">
                    <div class="clothing-info">
                        <div class="clothing-name">Smart Casual</div>
                        <div class="clothing-price">₹2,299</div>
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
        // Password strength indicator
        document.getElementById('password').addEventListener('input', function(e) {
            const password = e.target.value;
            const strength = calculatePasswordStrength(password);
            const strengthBar = document.getElementById('passwordStrength');
            strengthBar.className = `strength-bar strength-${strength}`;
        });

        // Password confirmation check
        document.getElementById('confirm_password').addEventListener('input', function(e) {
            const confirm = e.target;
            const password = document.getElementById('password');
            const confirmCheck = document.getElementById('confirmCheck');
            
            if(confirm.value === password.value && password.value !== '') {
                confirmCheck.innerHTML = '<i class="fas fa-check-circle text-success"></i>';
                confirmCheck.style.display = 'block';
                confirm.classList.add('is-valid');
                confirm.classList.remove('is-invalid');
            } else if(confirm.value !== '') {
                confirmCheck.innerHTML = '<i class="fas fa-times-circle text-danger"></i>';
                confirmCheck.style.display = 'block';
                confirm.classList.add('is-invalid');
                confirm.classList.remove('is-valid');
            }
        });

        function calculatePasswordStrength(password) {
            let strength = 0;
            if (password.length >= 8) strength++;
            if (password.match(/[a-z]/) && password.match(/[A-Z]/)) strength++;
            if (password.match(/\d/)) strength++;
            if (password.match(/[^a-zA-Z\d]/)) strength++;
            return Math.min(strength, 4);
        }

        // Form submission animation
        document.getElementById('signupForm').addEventListener('submit', function(e) {
            const btn = document.querySelector('button[type="submit"]');
            const submitText = btn.querySelector('.submit-text');
            const spinner = btn.querySelector('.spinner-border');
            if (submitText && spinner) {
                submitText.classList.add('d-none');
                spinner.classList.remove('d-none');
                btn.disabled = true;
            }
        });
        
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