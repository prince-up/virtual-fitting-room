<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'User not logged in']);
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

    // Start transaction
    $pdo->beginTransaction();

    // Create orders table if it doesn't exist
    $pdo->exec("CREATE TABLE IF NOT EXISTS orders (
        id INT AUTO_INCREMENT PRIMARY KEY,
        user_id INT NOT NULL,
        order_date DATETIME DEFAULT CURRENT_TIMESTAMP,
        total_amount DECIMAL(10,2) NOT NULL,
        payment_method VARCHAR(50) NOT NULL,
        payment_status VARCHAR(50) NOT NULL,
        shipping_name VARCHAR(100) NOT NULL,
        shipping_phone VARCHAR(20) NOT NULL,
        shipping_address TEXT NOT NULL,
        shipping_city VARCHAR(100) NOT NULL,
        shipping_state VARCHAR(100) NOT NULL,
        shipping_pincode VARCHAR(10) NOT NULL,
        FOREIGN KEY (user_id) REFERENCES users(id)
    )");

    // Create order_items table if it doesn't exist
    $pdo->exec("CREATE TABLE IF NOT EXISTS order_items (
        id INT AUTO_INCREMENT PRIMARY KEY,
        order_id INT NOT NULL,
        product_name VARCHAR(255) NOT NULL,
        quantity INT NOT NULL,
        price DECIMAL(10,2) NOT NULL,
        FOREIGN KEY (order_id) REFERENCES orders(id)
    )");

    // Get form data
    $payment_method = $_POST['payment_method'];
    $name = $_POST['name'];
    $phone = $_POST['phone'];
    $address = $_POST['address'];
    $city = $_POST['city'];
    $state = $_POST['state'];
    $pincode = $_POST['pincode'];
    $cart_items = json_decode($_POST['cart_items'], true);
    
    // Calculate total amount
    $total_amount = 0;
    foreach ($cart_items as $item) {
        $total_amount += $item['price'] * $item['quantity'];
    }

    // Set payment status based on payment method
    $payment_status = ($payment_method === 'cod') ? 'pending' : 
                     (isset($_POST['payment_verified']) ? 'completed' : 'pending');

    // Insert order
    $stmt = $pdo->prepare("INSERT INTO orders (user_id, total_amount, payment_method, payment_status,
                          shipping_name, shipping_phone, shipping_address, shipping_city, 
                          shipping_state, shipping_pincode) 
                          VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    
    $stmt->execute([
        $_SESSION['user_id'],
        $total_amount,
        $payment_method,
        $payment_status,
        $name,
        $phone,
        $address,
        $city,
        $state,
        $pincode
    ]);

    $order_id = $pdo->lastInsertId();

    // Insert order items
    $stmt = $pdo->prepare("INSERT INTO order_items (order_id, product_name, quantity, price) 
                          VALUES (?, ?, ?, ?)");
    
    foreach ($cart_items as $item) {
        $stmt->execute([
            $order_id,
            $item['name'],
            $item['quantity'],
            $item['price']
        ]);
    }

    // Commit transaction
    $pdo->commit();

    echo json_encode([
        'success' => true,
        'order_id' => $order_id,
        'message' => 'Order placed successfully'
    ]);

} catch(PDOException $e) {
    // Rollback transaction on error
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }
    error_log("Payment Processing Error: " . $e->getMessage());
    echo json_encode([
        'success' => false,
        'message' => 'An error occurred while processing your order. Please try again.'
    ]);
}
?> 