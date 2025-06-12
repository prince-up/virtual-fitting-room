<?php
session_start();

// Check if user is logged in
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

    // Check if required columns exist
    $stmt = $pdo->query("SHOW COLUMNS FROM users");
    $columns = $stmt->fetchAll(PDO::FETCH_COLUMN);
    
    $missing_columns = array_diff(
        ['phone', 'address', 'city', 'state', 'pincode'],
        $columns
    );
    
    if (!empty($missing_columns)) {
        header('Location: update_database.php');
        exit();
    }

    // Get user details
    $stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
    $stmt->execute([$_SESSION['user_id']]);
    $user = $stmt->fetch();

    // Get user's orders
    $stmt = $pdo->prepare("
        SELECT o.*, COUNT(oi.id) as item_count 
        FROM orders o 
        LEFT JOIN order_items oi ON o.id = oi.order_id 
        WHERE o.user_id = ? 
        GROUP BY o.id 
        ORDER BY o.created_at DESC
    ");
    $stmt->execute([$_SESSION['user_id']]);
    $orders = $stmt->fetchAll();

    // Handle profile update
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_profile'])) {
        $new_username = $_POST['username'];
        $new_email = $_POST['email'];
        $new_phone = $_POST['phone'];
        $new_address = $_POST['address'];
        $new_city = $_POST['city'];
        $new_state = $_POST['state'];
        $new_pincode = $_POST['pincode'];

        // Check if email is already taken by another user
        $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ? AND id != ?");
        $stmt->execute([$new_email, $_SESSION['user_id']]);
        if ($stmt->fetch()) {
            $error = 'Email already taken by another user';
        } else {
            // Update user details
            $stmt = $pdo->prepare("
                UPDATE users 
                SET username = ?, email = ?, phone = ?, address = ?, city = ?, state = ?, pincode = ? 
                WHERE id = ?
            ");
            $stmt->execute([
                $new_username, $new_email, $new_phone, $new_address, 
                $new_city, $new_state, $new_pincode, $_SESSION['user_id']
            ]);
            
            // Update session
            $_SESSION['username'] = $new_username;
            $success = 'Profile updated successfully';
            
            // Refresh user data
            $stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
            $stmt->execute([$_SESSION['user_id']]);
            $user = $stmt->fetch();
        }
    }

} catch(PDOException $e) {
    $error = 'Database error: ' . $e->getMessage();
}

// Handle profile picture upload
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Check if file was uploaded
    if (isset($_FILES['profile_pic']) && $_FILES['profile_pic']['error'] === UPLOAD_ERR_OK) {
        $target_dir = "uploads/profile_pics/";
        
        // Create directory if it doesn't exist
        if (!file_exists($target_dir)) {
            mkdir($target_dir, 0777, true);
        }
        
        // Get file extension and generate new filename
        $file_extension = strtolower(pathinfo($_FILES["profile_pic"]["name"], PATHINFO_EXTENSION));
        $new_filename = $_SESSION['user_id'] . '_' . time() . '.' . $file_extension;
        $target_file = $target_dir . $new_filename;
        
        // Validate file type
        $allowed_types = ['jpg', 'jpeg', 'png', 'gif'];
        if (!in_array($file_extension, $allowed_types)) {
            $error = "Sorry, only JPG, JPEG, PNG & GIF files are allowed.";
        } else {
            // Check if file is an actual image
            $check = @getimagesize($_FILES["profile_pic"]["tmp_name"]);
            if ($check !== false) {
                // Try to move uploaded file
                if (move_uploaded_file($_FILES["profile_pic"]["tmp_name"], $target_file)) {
                    try {
                        // Update profile picture in database
                        $stmt = $pdo->prepare("UPDATE users SET profile_pic = ? WHERE id = ?");
                        $stmt->execute([$target_file, $_SESSION['user_id']]);
                        $success = "Profile picture updated successfully!";
                        
                        // Update user data
                        $user['profile_pic'] = $target_file;
                    } catch(PDOException $e) {
                        $error = "Database error: " . $e->getMessage();
                    }
                } else {
                    $error = "Sorry, there was an error uploading your file.";
                }
            } else {
                $error = "File is not an image.";
            }
        }
    } else if ($_FILES['profile_pic']['error'] !== UPLOAD_ERR_NO_FILE) {
        // Handle upload errors
        switch ($_FILES['profile_pic']['error']) {
            case UPLOAD_ERR_INI_SIZE:
                $error = "The uploaded file exceeds the upload_max_filesize directive in php.ini.";
                break;
            case UPLOAD_ERR_FORM_SIZE:
                $error = "The uploaded file exceeds the MAX_FILE_SIZE directive in the HTML form.";
                break;
            case UPLOAD_ERR_PARTIAL:
                $error = "The uploaded file was only partially uploaded.";
                break;
            default:
                $error = "An error occurred while uploading the file.";
                break;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Profile - Virtual Fitting Room</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <!-- Custom CSS -->
    <style>
        body {
            background-color: #f3f3f3;
            min-height: 100vh;
        }
        .navbar {
            background-color: #232f3e !important;
        }
        .profile-container {
            background-color: white;
            border-radius: 4px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
            margin-top: 20px;
            margin-bottom: 20px;
        }
        .profile-header {
            background-color: #232f3e;
            padding: 20px;
            color: white;
            border-radius: 4px 4px 0 0;
        }
        .profile-picture {
            width: 120px;
            height: 120px;
            border-radius: 50%;
            border: 3px solid white;
            object-fit: cover;
            margin: -60px auto 20px;
            display: block;
            background-color: #f3f3f3;
        }
        .profile-info {
            padding: 20px;
        }
        .info-item {
            margin-bottom: 15px;
            padding-bottom: 15px;
            border-bottom: 1px solid #e7e7e7;
        }
        .info-item:last-child {
            border-bottom: none;
        }
        .upload-btn {
            position: relative;
            overflow: hidden;
            display: inline-block;
            background-color: #ff9900;
            border: none;
        }
        .upload-btn:hover {
            background-color: #e88a00;
        }
        .upload-btn input[type=file] {
            position: absolute;
            left: 0;
            top: 0;
            opacity: 0;
            cursor: pointer;
            width: 100%;
            height: 100%;
        }
        .btn-success {
            background-color: #ff9900;
            border: none;
        }
        .btn-success:hover {
            background-color: #e88a00;
        }
        .nav-link {
            color: #fff !important;
        }
        .nav-link:hover {
            color: #ff9900 !important;
        }
        .info-item h5 {
            color: #232f3e;
            font-size: 16px;
            font-weight: 600;
        }
        .info-item p {
            color: #555;
            font-size: 14px;
        }
    </style>
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-dark sticky-top shadow-sm">
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
                        <a class="nav-link" href="index.php">
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
                </ul>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="profile-container">
                    <div class="profile-header">
                        <h2>My Profile</h2>
                    </div>
                    
                    <?php if (isset($error)): ?>
                        <div class="alert alert-danger m-3"><?php echo $error; ?></div>
                    <?php endif; ?>
                    
                    <?php if (isset($success)): ?>
                        <div class="alert alert-success m-3"><?php echo $success; ?></div>
                    <?php endif; ?>

                    <img src="<?php echo isset($user['profile_pic']) && !empty($user['profile_pic']) ? $user['profile_pic'] : 'assets/images/default-profile.png'; ?>" 
                         alt="Profile Picture" 
                         class="profile-picture"
                         id="profile-preview">

                    <div class="profile-info">
                        <form action="" method="POST" enctype="multipart/form-data">
                            <div class="mb-4 text-center">
                                <label class="upload-btn btn btn-primary">
                                    <i class="fas fa-camera me-2"></i>Change Profile Picture
                                    <input type="file" name="profile_pic" accept="image/*" onchange="previewImage(this)">
                                </label>
                            </div>
                            <button type="submit" class="btn btn-success w-100">
                                <i class="fas fa-save me-2"></i>Save Changes
                            </button>
                        </form>

                        <div class="info-item">
                            <h5><i class="fas fa-user me-2"></i>Username</h5>
                            <p class="mb-0"><?php echo htmlspecialchars($user['username']); ?></p>
                        </div>

                        <div class="info-item">
                            <h5><i class="fas fa-envelope me-2"></i>Email</h5>
                            <p class="mb-0"><?php echo htmlspecialchars($user['email']); ?></p>
                        </div>

                        <div class="info-item">
                            <h5><i class="fas fa-calendar me-2"></i>Member Since</h5>
                            <p class="mb-0"><?php echo date('F j, Y', strtotime($user['created_at'])); ?></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function previewImage(input) {
            if (input.files && input.files[0]) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    document.getElementById('profile-preview').src = e.target.result;
                }
                reader.readAsDataURL(input.files[0]);
            }
        }
    </script>
</body>
</html> 