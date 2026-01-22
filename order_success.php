<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

// Check if order ID is provided
if (!isset($_GET['order_id']) && !isset($_GET['id'])) {
    header('Location: index.php');
    exit();
}

$order_id = isset($_GET['order_id']) ? $_GET['order_id'] : $_GET['id'];

// Database connection
require_once 'db_config.php';

try {

    // Get order details
    $stmt = $pdo->prepare("SELECT * FROM orders WHERE id = ? AND user_id = ?");
    $stmt->execute([$order_id, $_SESSION['user_id']]);
    $order = $stmt->fetch();

    if (!$order) {
        header('Location: index.php');
        exit();
    }

    // Get order items
    $stmt = $pdo->prepare("SELECT * FROM order_items WHERE order_id = ?");
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
                            <i class="fas fa-check-circle text-success" style="font-size: 5rem;"></i>
                        </div>
                        <h1 class="card-title mb-3" style="color: #28a745; font-weight: 700;">✓ Order Confirmed!</h1>
                        <p class="lead mb-4">Thank you for your purchase. Your order has been placed successfully!</p>
                        
                        <div class="alert alert-success mb-4">
                            <h5 class="alert-heading"><i class="fas fa-info-circle me-2"></i>Order Details</h5>
                            <p class="mb-1"><strong>Order ID:</strong> #<?php echo str_pad($order['id'], 8, '0', STR_PAD_LEFT); ?></p>
                            <p class="mb-1"><strong>Order Date:</strong> <?php echo date('F j, Y g:i A', strtotime($order['order_date'])); ?></p>
                            <p class="mb-1"><strong>Payment Method:</strong> <?php echo ucfirst($order['payment_method']); ?></p>
                            <p class="mb-0"><strong>Status:</strong> <span class="badge bg-info">Processing</span></p>
                        </div>

                        <div class="table-responsive mb-4">
                            <table class="table table-bordered">
                                <thead class="table-light">
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
                                            <td><?php echo htmlspecialchars($item['product_name']); ?></td>
                                            <td><?php echo $item['quantity']; ?></td>
                                            <td>₹<?php echo number_format($item['price'], 2); ?></td>
                                            <td>₹<?php echo number_format($item['price'] * $item['quantity'], 2); ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <td colspan="3" class="text-end"><strong>Total Amount:</strong></td>
                                        <td><strong>₹<?php echo number_format($order['total_amount'], 2); ?></strong></td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>

                        <div class="alert alert-secondary">
                            <h5 class="alert-heading"><i class="fas fa-shipping-fast me-2"></i>Shipping Address</h5>
                            <p class="mb-1"><strong><?php echo htmlspecialchars($order['shipping_name']); ?></strong></p>
                            <p class="mb-1"><?php echo htmlspecialchars($order['shipping_address']); ?></p>
                            <p class="mb-0"><?php echo htmlspecialchars($order['shipping_city']); ?>, <?php echo htmlspecialchars($order['shipping_state']); ?> - <?php echo htmlspecialchars($order['shipping_pincode']); ?></p>
                            <p class="mb-0"><i class="fas fa-phone me-1"></i><?php echo htmlspecialchars($order['shipping_phone']); ?></p>
                        </div>

                        <div class="mt-4 d-flex gap-2 justify-content-center flex-wrap">
                            <button onclick="downloadReceipt()" class="btn btn-success btn-lg">
                                <i class="fas fa-download me-2"></i>Download Receipt
                            </button>
                            <a href="shop.php" class="btn btn-primary btn-lg">
                                <i class="fas fa-shopping-bag me-2"></i>Continue Shopping
                            </a>
                            <a href="index.php" class="btn btn-outline-secondary btn-lg">
                                <i class="fas fa-home me-2"></i>Go to Home
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function downloadReceipt() {
            const orderData = {
                orderId: '<?php echo str_pad($order["id"], 8, "0", STR_PAD_LEFT); ?>',
                orderDate: '<?php echo date("F j, Y g:i A", strtotime($order["order_date"])); ?>',
                paymentMethod: '<?php echo ucfirst($order["payment_method"]); ?>',
                customerName: '<?php echo htmlspecialchars($order["shipping_name"]); ?>',
                shippingAddress: '<?php echo htmlspecialchars($order["shipping_address"]); ?>',
                shippingCity: '<?php echo htmlspecialchars($order["shipping_city"]); ?>',
                shippingState: '<?php echo htmlspecialchars($order["shipping_state"]); ?>',
                shippingPincode: '<?php echo htmlspecialchars($order["shipping_pincode"]); ?>',
                shippingPhone: '<?php echo htmlspecialchars($order["shipping_phone"]); ?>',
                items: <?php echo json_encode($items); ?>,
                totalAmount: '<?php echo number_format($order["total_amount"], 2); ?>'
            };

            // Create receipt content
            let receiptContent = `
==============================================
           LOOKAT ME - ORDER RECEIPT
==============================================

Order ID: #${orderData.orderId}
Order Date: ${orderData.orderDate}
Payment Method: ${orderData.paymentMethod}

----------------------------------------------
CUSTOMER DETAILS
----------------------------------------------
Name: ${orderData.customerName}
Address: ${orderData.shippingAddress}
         ${orderData.shippingCity}, ${orderData.shippingState} - ${orderData.shippingPincode}
Phone: ${orderData.shippingPhone}

----------------------------------------------
ORDER ITEMS
----------------------------------------------
`;

            orderData.items.forEach((item, index) => {
                const itemTotal = (item.price * item.quantity).toFixed(2);
                receiptContent += `
${index + 1}. ${item.product_name}
   Qty: ${item.quantity} x ₹${item.price}
   Subtotal: ₹${itemTotal}
`;
            });

            receiptContent += `
----------------------------------------------
TOTAL AMOUNT: ₹${orderData.totalAmount}
----------------------------------------------

Thank you for shopping with Lookat me!
For support, contact: +91 7986614646
==============================================
`;

            // Create download
            const blob = new Blob([receiptContent], { type: 'text/plain' });
            const url = window.URL.createObjectURL(blob);
            const a = document.createElement('a');
            a.href = url;
            a.download = `Lookat_Me_Receipt_${orderData.orderId}.txt`;
            document.body.appendChild(a);
            a.click();
            document.body.removeChild(a);
            window.URL.revokeObjectURL(url);
        }
    </script>
</body>
</html> 