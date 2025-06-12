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
            $host = 'localhost';
            $dbname = 'virtual_fitting_room';
            $db_username = 'root';
            $db_password = '';

            $pdo = new PDO("mysql:host=$host;dbname=$dbname", $db_username, $db_password);
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

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
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
            min-height: 100vh;
        }

        .auth-container {
            max-width: 440px;
            margin: 50px auto;
            transform-style: preserve-3d;
        }

        .card {
            border: none;
            border-radius: 20px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.15);
            transform: translateZ(20px);
            transition: all 0.3s ease;
            background: rgba(255,255,255,0.95);
            backdrop-filter: blur(10px);
        }

        .card:hover {
            transform: translateZ(30px) translateY(-5px);
            box-shadow: 0 15px 40px rgba(0,0,0,0.2);
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

        .form-control {
            border: 2px solid #e2e8f0;
            border-radius: 10px;
            padding: 12px 20px;
            transition: all 0.3s ease;
        }

        .form-control:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.2);
        }

        .password-strength {
            height: 6px;
            margin-top: 8px;
            border-radius: 3px;
            transition: all 0.3s ease;
        }

        .strength-0 { background-color: #ef4444; width: 20%; }
        .strength-1 { background-color: #f59e0b; width: 40%; }
        .strength-2 { background-color: #f59e0b; width: 60%; }
        .strength-3 { background-color: #10b981; width: 80%; }
        .strength-4 { background-color: #10b981; width: 100%; }

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

        .input-group {
            position: relative;
            margin-bottom: 1.5rem;
        }

        .input-icon {
            position: absolute;
            right: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: #94a3b8;
            transition: color 0.3s ease;
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
        }

        .animate__delay-1 { animation-delay: 0.2s; }
        .animate__delay-2 { animation-delay: 0.4s; }
        .animate__delay-3 { animation-delay: 0.6s; }
    </style>
</head>
<body>
    <div class="container">
        <div class="auth-container animate__animated animate__fadeInUp">
            <div class="card">
                <div class="card-header">
                    <h4 class="mb-0 animate__animated animate__fadeInDown">Welcome to Virtual Fitting Room</h4>
                    <p class="mt-2 mb-0 animate__animated animate__fadeIn animate__delay-1">Create your account</p>
                </div>
                <div class="card-body p-4">
                    <?php if ($error): ?>
                        <div class="alert alert-danger animate__animated animate__shakeX"><?php echo $error; ?></div>
                    <?php endif; ?>
                    <?php if ($success): ?>
                        <div class="alert alert-success animate__animated animate__shakeX"><?php echo $success; ?></div>
                    <?php endif; ?>
                    <form method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
                        <div class="mb-4 position-relative animate__animated animate__fadeIn animate__delay-2">
                            <label for="username" class="form-label">Username</label>
                            <input type="text" class="form-control" id="username" name="username" required>
                            <i class="fas fa-user input-icon"></i>
                        </div>
                        <div class="mb-4 position-relative animate__animated animate__fadeIn animate__delay-3">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" class="form-control" id="email" name="email" required>
                            <i class="fas fa-envelope input-icon"></i>
                        </div>
                        <div class="mb-4 position-relative animate__animated animate__fadeIn animate__delay-1">
                            <label for="password" class="form-label">Password</label>
                            <input type="password" class="form-control" id="password" name="password" required>
                            <i class="fas fa-lock input-icon"></i>
                            <div class="password-strength" id="passwordStrength"></div>
                        </div>
                        <div class="mb-4 position-relative animate__animated animate__fadeIn animate__delay-2">
                            <label for="confirm_password" class="form-label">Confirm Password</label>
                            <input type="password" class="form-control" id="confirm_password" name="confirm_password" required>
                            <i class="fas fa-lock input-icon"></i>
                            <span class="validation-icon" id="confirmCheck"></span>
                        </div>
                        <div class="mb-3">
                            <label for="phone" class="form-label">Phone Number</label>
                            <input type="tel" class="form-control" id="phone" name="phone">
                        </div>
                        <div class="mb-3">
                            <label for="address" class="form-label">Address</label>
                            <textarea class="form-control" id="address" name="address" rows="2"></textarea>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="city" class="form-label">City</label>
                                <input type="text" class="form-control" id="city" name="city">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="state" class="form-label">State</label>
                                <input type="text" class="form-control" id="state" name="state">
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="pincode" class="form-label">Pincode</label>
                            <input type="text" class="form-control" id="pincode" name="pincode">
                        </div>
                        <div class="d-grid gap-2 animate__animated animate__fadeIn animate__delay-3">
                            <button type="submit" class="btn btn-primary">
                                <span class="submit-text">Register</span>
                                <span class="spinner-border spinner-border-sm d-none" role="status"></span>
                            </button>
                        </div>
                    </form>
                    <div class="text-center mt-4 animate__animated animate__fadeIn animate__delay-1">
                        <p class="mb-0">Already have an account? 
                            <a href="login.php" class="text-decoration-none fw-bold" style="color: var(--primary-color);">Sign In</a>
                        </p>
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
            strengthBar.style.transform = 'scaleX(1)';
            strengthBar.className = `password-strength strength-${strength} animate__animated animate__fadeIn`;
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
            } else {
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
            btn.querySelector('.submit-text').classList.add('d-none');
            btn.querySelector('.spinner-border').classList.remove('d-none');
            btn.disabled = true;
        });
    </script>
</body>
</html>