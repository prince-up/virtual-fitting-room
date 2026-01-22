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
        :root {
            --primary-color: #6366f1;
            --secondary-color: #4f46e5;
            --gradient-start: #6366f1;
            --gradient-end: #8b5cf6;
        }

        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            padding: 20px 0;
        }
        
        .navbar {
            margin-bottom: 0;
        }

        .auth-container {
            max-width: 500px;
            margin: 30px auto;
            flex: 1;
            display: flex;
            align-items: center;
        }

        .card {
            border: none;
            border-radius: 20px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
            transform-style: preserve-3d;
            transition: all 0.3s ease;
            background: white;
            width: 100%;
        }

        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 25px 70px rgba(0,0,0,0.35);
        }

        .card-header {
            background: linear-gradient(135deg, var(--gradient-start), var(--gradient-end));
            color: white;
            text-align: center;
            border-radius: 20px 20px 0 0 !important;
            padding: 2rem;
            position: relative;
            overflow: hidden;
        }

        .card-header::before {
            content: '';
            position: absolute;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: linear-gradient(45deg, transparent, rgba(255,255,255,0.1), transparent);
            transform: rotate(45deg);
            animation: shine 3s infinite;
        }
        
        .card-header h4 {
            margin: 0;
            font-weight: 600;
            position: relative;
            z-index: 1;
        }
        
        .card-header p {
            position: relative;
            z-index: 1;
        }

        .form-control {
            border: 2px solid #e2e8f0;
            border-radius: 10px;
            padding: 12px 45px 12px 15px;
            transition: all 0.3s ease;
        }

        .form-control:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.2);
        }

        .password-strength {
            height: 4px;
            margin-top: 5px;
            border-radius: 2px;
            transition: all 0.3s ease;
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

        .btn-primary {
            background: linear-gradient(135deg, var(--gradient-start), var(--gradient-end));
            border: none;
            padding: 12px 24px;
            border-radius: 10px;
            font-weight: 600;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(99, 102, 241, 0.4);
            background: linear-gradient(135deg, var(--gradient-end), var(--gradient-start));
        }

        .btn-primary::after {
            content: '';
            position: absolute;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: linear-gradient(45deg, transparent, rgba(255,255,255,0.1), transparent);
            transform: rotate(45deg);
            animation: shine 3s infinite;
        }

        @keyframes shine {
            0% { transform: translateX(-100%) rotate(45deg); }
            100% { transform: translateX(100%) rotate(45deg); }
        }

        .input-group-custom {
            position: relative;
            margin-bottom: 1rem;
        }

        .input-icon {
            position: absolute;
            right: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: #94a3b8;
            transition: color 0.3s ease;
            pointer-events: none;
            z-index: 5;
        }

        .form-control:focus ~ .input-icon {
            color: var(--primary-color);
        }

        .validation-icon {
            position: absolute;
            right: 40px;
            top: 50%;
            transform: translateY(-50%);
            display: none;
            z-index: 5;
        }
        
        .form-label {
            font-weight: 500;
            margin-bottom: 0.5rem;
            color: #333;
        }
        
        .alert {
            border-radius: 10px;
            border: none;
        }
        
        .login-link {
            margin-top: 1.5rem;
            padding-top: 1.5rem;
            border-top: 1px solid #e2e8f0;
            text-align: center;
        }
        
        .login-link a {
            color: var(--primary-color);
            text-decoration: none;
            font-weight: 600;
            transition: color 0.3s ease;
        }
        
        .login-link a:hover {
            color: var(--gradient-end);
        }
    </style>
</head>
<body>
    <?php include 'includes/navbar.php'; ?>
    
    <div class="container">
        <div class="auth-container">
            <div class="card animate__animated animate__fadeInUp">
                <div class="card-header">
                    <i class="fas fa-user-plus mb-2" style="font-size: 40px;"></i>
                    <h4>Create Your Account</h4>
                    <p class="mb-0">Join Virtual Fitting Room today</p>
                </div>
                <div class="card-body p-4">
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
                        <div class="input-group-custom">
                            <label for="username" class="form-label">
                                <i class="fas fa-user me-2"></i>Username
                            </label>
                            <input type="text" class="form-control" id="username" name="username" placeholder="Choose a username" required>
                            <i class="fas fa-user input-icon"></i>
                        </div>
                        
                        <div class="input-group-custom">
                            <label for="email" class="form-label">
                                <i class="fas fa-envelope me-2"></i>Email Address
                            </label>
                            <input type="email" class="form-control" id="email" name="email" placeholder="Enter your email" required>
                            <i class="fas fa-envelope input-icon"></i>
                        </div>
                        
                        <div class="input-group-custom">
                            <label for="password" class="form-label">
                                <i class="fas fa-lock me-2"></i>Password
                            </label>
                            <input type="password" class="form-control" id="password" name="password" placeholder="Create a password" required>
                            <i class="fas fa-lock input-icon"></i>
                            <div class="password-strength">
                                <div class="strength-bar" id="passwordStrength"></div>
                            </div>
                        </div>
                        
                        <div class="input-group-custom">
                            <label for="confirm_password" class="form-label">
                                <i class="fas fa-lock me-2"></i>Confirm Password
                            </label>
                            <input type="password" class="form-control" id="confirm_password" name="confirm_password" placeholder="Confirm your password" required>
                            <i class="fas fa-lock input-icon"></i>
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
                        
                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-user-plus me-2"></i>
                                <span class="submit-text">Create Account</span>
                                <span class="spinner-border spinner-border-sm d-none ms-2" role="status"></span>
                            </button>
                        </div>
                    </form>
                    
                    <div class="login-link">
                        <p class="mb-0">Already have an account? <a href="login.php">Sign in here</a></p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Enhanced password strength with animation
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
    </script>
</body>
</html>