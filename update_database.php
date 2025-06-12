<?php
session_start();

// Database connection parameters
$host = 'localhost';
$dbname = 'virtual_fitting_room';
$username = 'root';
$password = '';

try {
    // Create connection
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Add profile_pic column if it doesn't exist
    $pdo->exec("ALTER TABLE users ADD COLUMN IF NOT EXISTS profile_pic VARCHAR(255) DEFAULT NULL");
    
    // Add other missing columns if they don't exist
    $pdo->exec("
        ALTER TABLE users 
        ADD COLUMN IF NOT EXISTS phone VARCHAR(20) DEFAULT NULL,
        ADD COLUMN IF NOT EXISTS address TEXT DEFAULT NULL,
        ADD COLUMN IF NOT EXISTS city VARCHAR(100) DEFAULT NULL,
        ADD COLUMN IF NOT EXISTS state VARCHAR(100) DEFAULT NULL,
        ADD COLUMN IF NOT EXISTS pincode VARCHAR(10) DEFAULT NULL
    ");
    
    // Success message
    $success = "Database updated successfully!";
    
} catch(PDOException $e) {
    $error = "Database error: " . $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Database Update - Virtual Fitting Room</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h4 class="mb-0">Database Update</h4>
                    </div>
                    <div class="card-body">
                        <?php if (isset($error)): ?>
                            <div class="alert alert-danger">
                                <?php echo $error; ?>
                            </div>
                        <?php endif; ?>
                        
                        <?php if (isset($success)): ?>
                            <div class="alert alert-success">
                                <?php echo $success; ?>
                                <p class="mt-3">You can now <a href="profile.php">return to your profile</a>.</p>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html> 