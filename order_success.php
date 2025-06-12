<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

// Check if order ID is provided
if (!isset($_GET['id'])) {
    header('Location: index.php');
    exit();
}

$order_id = $_GET['id'];

// Database connection
$host = 'localhost';
$dbname = 'virtual_fitting_room';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Get order details
    $stmt = $pdo->prepare("SELECT * FROM orders WHERE id = ? AND user_id = ?");
    $stmt->execute([$order_id, $_SESSION['user_id']]);
    $order = $stmt->fetch();

    if (!$order) {
        header('Location: index.php');
        exit();
    }

    // Get order items
    $stmt = $pdo->prepare("
        SELECT oi.*, ci.name, ci.image 
        FROM order_items oi 
        JOIN clothing_items ci ON oi.item_id = ci.id 
        WHERE oi.order_id = ?
    ");
    $stmt->execute([$order_id]);
    $items = $stmt->fetchAll();

} catch(PDOException $e) {
    $error = 'Database error: ' . $e->getMessage();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Confirmation - Virtual Fitting Room</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body class="bg-light">
    <?php include 'includes/navbar.php'; ?>

    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-body text-center">
                        <div class="mb-4">
                            <i class="fas fa-check-circle text-success" style="font-size: 4rem;"></i>
                        </div>
                        <h1 class="card-title mb-4">Order Confirmed!</h1>
                        <p class="lead mb-4">Thank you for your purchase. Your order has been placed successfully.</p>
                        
                        <div class="alert alert-info mb-4">
                            <h5 class="alert-heading">Order Details</h5>
                            <p class="mb-0">Order ID: #<?php echo str_pad($order['id'], 8, '0', STR_PAD_LEFT); ?></p>
                            <p class="mb-0">Order Date: <?php echo date('F j, Y', strtotime($order['created_at'])); ?></p>
                            <p class="mb-0">Payment Method: <?php echo ucfirst($order['payment_method']); ?></p>
                            <p class="mb-0">Status: <span class="badge bg-warning"><?php echo ucfirst($order['status']); ?></span></p>
                        </div>

                        <div class="table-responsive mb-4">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>Product</th>
                                        <th>Quantity</th>
                                        <th>Price</th>
                                        <th>Total</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($items as $item): ?>
                                        <tr>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <img src="<?php echo htmlspecialchars($item['image']); ?>" 
                                                         alt="<?php echo htmlspecialchars($item['name']); ?>" 
                                                         class="img-thumbnail me-3" style="width: 50px;">
                                                    <div><?php echo htmlspecialchars($item['name']); ?></div>
                                                </div>
                                            </td>
                                            <td><?php echo $item['quantity']; ?></td>
                                            <td>₹<?php echo number_format($item['price'], 2); ?></td>
                                            <td>₹<?php echo number_format($item['price'] * $item['quantity'], 2); ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <td colspan="3" class="text-end"><strong>Total:</strong></td>
                                        <td><strong>₹<?php echo number_format($order['total_amount'], 2); ?></strong></td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>

                        <div class="alert alert-secondary">
                            <h5 class="alert-heading">Delivery Address</h5>
                            <p class="mb-0"><?php echo nl2br(htmlspecialchars($order['delivery_address'])); ?></p>
                        </div>

                        <div class="mt-4">
                            <a href="shop.php" class="btn btn-primary me-2">
                                <i class="fas fa-shopping-bag"></i> Continue Shopping
                            </a>
                            <a href="profile.php" class="btn btn-outline-secondary">
                                <i class="fas fa-user"></i> View Orders
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html> 