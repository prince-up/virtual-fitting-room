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

    // Get user details
    $stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
    $stmt->execute([$_SESSION['user_id']]);
    $user = $stmt->fetch();
} catch(PDOException $e) {
    error_log("Database Error: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment - Virtual Fitting Room</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        body {
            background-color: #f8f9fa;
        }
        .payment-card {
            max-width: 800px;
            margin: 0 auto;
            border: none;
            box-shadow: 0 0 20px rgba(0,0,0,0.1) !important;
        }
        .order-summary {
            background-color: #f8f9fa;
            border-radius: 10px;
            padding: 20px;
            border: 1px solid #e9ecef;
        }
        .payment-method {
            border: 2px solid #dee2e6;
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 15px;
            cursor: pointer;
            transition: all 0.3s ease;
            background-color: white;
        }
        .payment-method:hover {
            border-color: #0d6efd;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }
        .payment-method.selected {
            border-color: #0d6efd;
            background-color: #f8f9fa;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }
        .payment-icon {
            font-size: 2.5rem;
            margin-right: 15px;
            color: #0d6efd;
        }
        .qr-code-container {
            display: none;
            text-align: center;
            margin: 20px 0;
            transition: all 0.3s ease;
        }
        .qr-code-container.active {
            display: block;
            animation: fadeIn 0.5s ease;
        }
        .qr-code-image {
            max-width: 300px;
            margin: 0 auto;
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            padding: 10px;
            background-color: white;
        }
        .upi-id {
            margin-top: 15px;
            font-size: 1rem;
            color: #495057;
            background-color: #e9ecef;
            padding: 10px;
            border-radius: 5px;
            display: inline-block;
        }
        .form-control:focus {
            box-shadow: 0 0 0 0.25rem rgba(13,110,253,.15);
            border-color: #0d6efd;
        }
        .btn-primary {
            padding: 12px 25px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .btn-outline-secondary:hover {
            background-color: #6c757d;
            color: white;
        }
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .payment-status {
            display: none;
            margin-top: 15px;
        }
        .payment-status.show {
            display: block;
            animation: fadeIn 0.5s ease;
        }
        .table th {
            background-color: #f8f9fa;
            font-weight: 600;
        }
        .section-title {
            position: relative;
            padding-bottom: 10px;
            margin-bottom: 20px;
            color: #2c3e50;
        }
        .section-title::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            width: 50px;
            height: 3px;
            background-color: #0d6efd;
        }
    </style>
</head>
<body class="bg-light">
    <?php include 'includes/navbar.php'; ?>

    <div class="container py-5">
        <div class="card payment-card shadow-sm">
            <div class="card-body p-4">
                <h2 class="card-title text-center mb-4">Checkout</h2>
                
                <!-- Order Summary -->
                <div class="order-summary mb-4">
                    <h4 class="mb-3">Order Summary</h4>
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Product</th>
                                    <th>Quantity</th>
                                    <th>Price</th>
                                    <th>Total</th>
                                </tr>
                            </thead>
                            <tbody id="cart-items">
                                <!-- Cart items will be loaded here via JavaScript -->
                            </tbody>
                            <tfoot>
                                <tr>
                                    <td colspan="3" class="text-end"><strong>Total:</strong></td>
                                    <td><strong id="total-amount">₹0.00</strong></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>

                <!-- Payment Methods -->
                <form id="payment-form" action="process_payment.php" method="POST">
                    <h4 class="mb-3">Payment Method</h4>
                    <div class="payment-methods mb-4">
                        <div class="payment-method selected" onclick="selectPaymentMethod('card')">
                            <div class="d-flex align-items-center">
                                <input type="radio" name="payment_method" value="card" checked class="me-3">
                                <i class="fas fa-credit-card payment-icon"></i>
                                <div>
                                    <h6 class="mb-0">Credit/Debit Card</h6>
                                    <small class="text-muted">Pay securely with your card</small>
                                </div>
                            </div>
                        </div>
                        <div class="payment-method" onclick="selectPaymentMethod('upi')">
                            <div class="d-flex align-items-center">
                                <input type="radio" name="payment_method" value="upi" class="me-3">
                                <i class="fas fa-mobile-alt payment-icon"></i>
                                <div>
                                    <h6 class="mb-0">UPI</h6>
                                    <small class="text-muted">Pay using UPI</small>
                                </div>
                            </div>
                        </div>
                        <div class="payment-method" onclick="selectPaymentMethod('cod')">
                            <div class="d-flex align-items-center">
                                <input type="radio" name="payment_method" value="cod" class="me-3">
                                <i class="fas fa-money-bill-wave payment-icon"></i>
                                <div>
                                    <h6 class="mb-0">Cash on Delivery</h6>
                                    <small class="text-muted">Pay when you receive</small>
                                </div>
                            </div>
                        </div>

                        <!-- Add QR Code Container with improved styling -->
                        <div id="upi-qr-container" class="qr-code-container">
                            <div class="card border-0 shadow-sm">
                                <div class="card-body p-4">
                                    <h5 class="card-title mb-4">
                                        <i class="fas fa-qrcode me-2 text-primary"></i>
                                        Scan QR Code to Pay
                                    </h5>
                                    <div class="row align-items-center">
                                        <div class="col-md-6">
                                            <img src="assets/images/payment-qr.png" alt="UPI QR Code" class="qr-code-image img-fluid mb-3">
                                            <p class="upi-id mb-2">
                                                <i class="fas fa-id-card me-2"></i>
                                                <span id="upi-id-text">luckyprinceyada1234-1@okhdfcbank</span>
                                                <button class="btn btn-sm btn-outline-primary ms-2" onclick="copyUpiId()">
                                                    <i class="fas fa-copy"></i>
                                                </button>
                                            </p>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="alert alert-info" role="alert">
                                                <h6 class="alert-heading">
                                                    <i class="fas fa-info-circle me-2"></i>
                                                    Payment Steps:
                                                </h6>
                                                <ol class="mb-0 ps-3">
                                                    <li>Open your UPI app</li>
                                                    <li>Scan the QR code</li>
                                                    <li>Enter the exact amount</li>
                                                    <li>Complete the payment</li>
                                                    <li>Click verify below</li>
                                                </ol>
                                            </div>
                                            <div class="d-grid gap-2">
                                                <button type="button" class="btn btn-success" onclick="verifyUpiPayment()">
                                                    <i class="fas fa-check-circle me-2"></i>Verify Payment
                                                </button>
                                            </div>
                                            <div id="payment-status" class="payment-status">
                                                <!-- Payment status will be shown here -->
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Shipping Address -->
                    <h4 class="mb-3">Shipping Address</h4>
                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <label for="name" class="form-label">Full Name</label>
                            <input type="text" class="form-control" id="name" name="name" value="<?php echo htmlspecialchars($user['username'] ?? ''); ?>" required>
                        </div>
                        <div class="col-md-6">
                            <label for="phone" class="form-label">Phone Number</label>
                            <input type="tel" class="form-control" id="phone" name="phone" value="<?php echo htmlspecialchars($user['phone'] ?? ''); ?>" required>
                        </div>
                        <div class="col-12">
                            <label for="address" class="form-label">Address</label>
                            <textarea class="form-control" id="address" name="address" rows="3" required><?php echo htmlspecialchars($user['address'] ?? ''); ?></textarea>
                        </div>
                        <div class="col-md-4">
                            <label for="city" class="form-label">City</label>
                            <input type="text" class="form-control" id="city" name="city" value="<?php echo htmlspecialchars($user['city'] ?? ''); ?>" required>
                        </div>
                        <div class="col-md-4">
                            <label for="state" class="form-label">State</label>
                            <input type="text" class="form-control" id="state" name="state" value="<?php echo htmlspecialchars($user['state'] ?? ''); ?>" required>
                        </div>
                        <div class="col-md-4">
                            <label for="pincode" class="form-label">PIN Code</label>
                            <input type="text" class="form-control" id="pincode" name="pincode" value="<?php echo htmlspecialchars($user['pincode'] ?? ''); ?>" required>
                        </div>
                    </div>

                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-primary btn-lg" id="submit-button">
                            Place Order - Pay <span id="submit-total">₹0.00</span>
                        </button>
                        <a href="<?php echo isset($_GET['source']) && $_GET['source'] === 'buynow' ? 'shop.php' : 'cart.php'; ?>" 
                           class="btn btn-outline-secondary">
                            Back to <?php echo isset($_GET['source']) && $_GET['source'] === 'buynow' ? 'Shop' : 'Cart'; ?>
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Load cart items when page loads
        document.addEventListener('DOMContentLoaded', function() {
            loadCartItems();
        });

        function loadCartItems() {
            // Get cart items based on source
            const isDirectBuy = '<?php echo isset($_GET["source"]) && $_GET["source"] === "buynow"; ?>' === '1';
            const cartItems = isDirectBuy ? 
                JSON.parse(localStorage.getItem('temp_cart') || '[]') : 
                JSON.parse(localStorage.getItem('cart') || '[]');

            const cartContainer = document.getElementById('cart-items');
            const totalElement = document.getElementById('total-amount');
            const submitTotalElement = document.getElementById('submit-total');
            
            if (!cartItems || cartItems.length === 0) {
                // Redirect if cart is empty
                window.location.href = isDirectBuy ? 'shop.php' : 'cart.php';
                return;
            }

            let total = 0;
            cartContainer.innerHTML = '';

            cartItems.forEach(item => {
                const itemTotal = item.price * item.quantity;
                total += itemTotal;

                cartContainer.innerHTML += `
                    <tr>
                        <td>
                            <div class="d-flex align-items-center">
                                <img src="${item.image}" 
                                     alt="${item.name}" 
                                     class="img-thumbnail me-2" style="width: 50px;">
                                <span>${item.name}</span>
                            </div>
                        </td>
                        <td>${item.quantity}</td>
                        <td>₹${item.price.toFixed(2)}</td>
                        <td>₹${itemTotal.toFixed(2)}</td>
                    </tr>
                `;
            });

            totalElement.textContent = `₹${total.toFixed(2)}`;
            submitTotalElement.textContent = `₹${total.toFixed(2)}`;
        }

        function selectPaymentMethod(method) {
            // Remove selected class from all payment methods
            document.querySelectorAll('.payment-method').forEach(el => {
                el.classList.remove('selected');
            });
            
            // Add selected class to clicked method
            const selectedMethod = document.querySelector(`.payment-method[onclick*="${method}"]`);
            if (selectedMethod) {
                selectedMethod.classList.add('selected');
                selectedMethod.querySelector('input[type="radio"]').checked = true;
            }

            // Show/hide UPI QR code container
            const qrContainer = document.getElementById('upi-qr-container');
            if (method === 'upi') {
                qrContainer.classList.add('active');
            } else {
                qrContainer.classList.remove('active');
            }
        }

        function copyUpiId() {
            const upiId = document.getElementById('upi-id-text').textContent;
            navigator.clipboard.writeText(upiId).then(() => {
                alert('UPI ID copied to clipboard!');
            });
        }

        function verifyUpiPayment() {
            const statusDiv = document.getElementById('payment-status');
            statusDiv.innerHTML = `
                <div class="alert alert-success">
                    <i class="fas fa-spinner fa-spin me-2"></i>
                    Verifying your payment...
                </div>
            `;
            statusDiv.classList.add('show');

            // Simulate payment verification (replace with actual verification logic)
            setTimeout(() => {
                statusDiv.innerHTML = `
                    <div class="alert alert-success">
                        <i class="fas fa-check-circle me-2"></i>
                        Payment verified successfully! Processing your order...
                    </div>
                `;
                // Submit the form after successful verification
                setTimeout(() => {
                    document.getElementById('payment-form').submit();
                }, 1500);
            }, 2000);
        }

        // Form submission
        document.getElementById('payment-form').addEventListener('submit', function(e) {
            e.preventDefault();
            
            // Get cart items
            const isDirectBuy = '<?php echo isset($_GET["source"]) && $_GET["source"] === "buynow"; ?>' === '1';
            const cartItems = isDirectBuy ? 
                JSON.parse(localStorage.getItem('temp_cart') || '[]') : 
                JSON.parse(localStorage.getItem('cart') || '[]');
            
            if (!cartItems || cartItems.length === 0) {
                alert('Your cart is empty!');
                window.location.href = isDirectBuy ? 'shop.php' : 'cart.php';
                return;
            }

            const formData = new FormData(this);
            formData.append('cart_items', JSON.stringify(cartItems));
            formData.append('source', isDirectBuy ? 'buynow' : 'cart');
            
            // Submit form
            fetch('process_payment.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Clear the appropriate cart
                    if (isDirectBuy) {
                        localStorage.removeItem('temp_cart');
                    } else {
                        localStorage.removeItem('cart');
                    }
                    
                    // Redirect to success page
                    window.location.href = 'order_success.php?order_id=' + data.order_id;
                } else {
                    alert('Payment failed: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('An error occurred while processing your payment. Please try again.');
            });
        });
    </script>
</body>
</html> 