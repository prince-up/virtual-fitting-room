<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

// Initialize cart if not exists
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

// Handle remove item
if (isset($_GET['remove']) && isset($_SESSION['cart'][$_GET['remove']])) {
    unset($_SESSION['cart'][$_GET['remove']]);
    header('Location: cart.php');
    exit();
}

// Handle quantity update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_cart'])) {
    foreach ($_POST['quantity'] as $item_id => $quantity) {
        if (isset($_SESSION['cart'][$item_id])) {
            $_SESSION['cart'][$item_id]['quantity'] = max(1, intval($quantity));
        }
    }
    header('Location: cart.php');
    exit();
}

// Calculate total
$total = 0;
foreach ($_SESSION['cart'] as $item) {
    $total += $item['price'] * $item['quantity'];
}

// Database connection
$host = 'localhost';
$dbname = 'virtual_fitting_room';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    error_log("Database Error: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shopping Cart - Virtual Fitting Room</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <!-- Custom CSS -->
    <style>
        .cart-item {
            transition: transform 0.3s ease;
            margin-bottom: 20px;
        }
        .cart-item:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0,0,0,0.1);
        }
        .cart-item-image {
            width: 100px;
            height: 100px;
            object-fit: cover;
            border-radius: 5px;
        }
        .quantity-controls {
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .quantity-controls button {
            width: 30px;
            height: 30px;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 0;
        }
        .empty-cart {
            text-align: center;
            padding: 50px 0;
        }
        .empty-cart i {
            font-size: 48px;
            color: #ddd;
            margin-bottom: 20px;
        }
        .cart-item img {
            max-width: 100px;
            height: auto;
        }
        .quantity-control {
            width: 120px;
        }
    </style>
</head>
<body class="bg-light">
    <?php include 'includes/navbar.php'; ?>

    <div class="container py-5">
        <div class="row">
            <div class="col-lg-8">
                <div class="card shadow-sm">
                    <div class="card-body">
                        <h2 class="card-title mb-4">Shopping Cart</h2>
                        <div id="cart-items">
                            <!-- Cart items will be loaded here via JavaScript -->
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-4">
                <div class="card shadow-sm">
                    <div class="card-body">
                        <h4 class="card-title mb-3">Order Summary</h4>
                        <div class="d-flex justify-content-between mb-2">
                            <span>Subtotal:</span>
                            <span id="subtotal">₹0.00</span>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span>Shipping:</span>
                            <span>Free</span>
                        </div>
                        <hr>
                        <div class="d-flex justify-content-between mb-3">
                            <strong>Total:</strong>
                            <strong id="total">₹0.00</strong>
                        </div>
                        <button id="checkout-button" class="btn btn-primary w-100" onclick="proceedToCheckout()">
                            Proceed to Checkout
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            loadCartItems();
        });

        function loadCartItems() {
            const cartItems = JSON.parse(localStorage.getItem('cart') || '[]');
            const cartContainer = document.getElementById('cart-items');
            const subtotalElement = document.getElementById('subtotal');
            const totalElement = document.getElementById('total');
            const checkoutButton = document.getElementById('checkout-button');

            if (cartItems.length === 0) {
                cartContainer.innerHTML = `
                    <div class="text-center py-4">
                        <i class="fas fa-shopping-cart fa-3x text-muted mb-3"></i>
                        <h5>Your cart is empty</h5>
                        <a href="shop.php" class="btn btn-primary mt-3">Continue Shopping</a>
                    </div>
                `;
                checkoutButton.disabled = true;
                return;
            }

            let total = 0;
            cartContainer.innerHTML = '';

            cartItems.forEach((item, index) => {
                const itemTotal = item.price * item.quantity;
                total += itemTotal;

                cartContainer.innerHTML += `
                    <div class="cart-item mb-3 border-bottom pb-3">
                        <div class="row align-items-center">
                            <div class="col-md-2">
                                <img src="${item.image}" alt="${item.name}" class="img-fluid">
                            </div>
                            <div class="col-md-4">
                                <h5 class="mb-1">${item.name}</h5>
                                <p class="text-muted mb-0">Size: ${item.size || 'N/A'}</p>
                                <p class="text-muted mb-0">Color: ${item.color || 'N/A'}</p>
                            </div>
                            <div class="col-md-3">
                                <div class="quantity-control input-group">
                                    <button class="btn btn-outline-secondary" onclick="updateQuantity(${index}, -1)">-</button>
                                    <input type="text" class="form-control text-center" value="${item.quantity}" readonly>
                                    <button class="btn btn-outline-secondary" onclick="updateQuantity(${index}, 1)">+</button>
                                </div>
                            </div>
                            <div class="col-md-2 text-end">
                                <p class="mb-0">₹${itemTotal.toFixed(2)}</p>
                            </div>
                            <div class="col-md-1 text-end">
                                <button class="btn btn-link text-danger" onclick="removeItem(${index})">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                `;
            });

            subtotalElement.textContent = `₹${total.toFixed(2)}`;
            totalElement.textContent = `₹${total.toFixed(2)}`;
        }

        function updateQuantity(index, change) {
            const cartItems = JSON.parse(localStorage.getItem('cart') || '[]');
            if (cartItems[index]) {
                cartItems[index].quantity = Math.max(1, cartItems[index].quantity + change);
                localStorage.setItem('cart', JSON.stringify(cartItems));
                loadCartItems();
                updateCartCount();
            }
        }

        function removeItem(index) {
            const cartItems = JSON.parse(localStorage.getItem('cart') || '[]');
            cartItems.splice(index, 1);
            localStorage.setItem('cart', JSON.stringify(cartItems));
            loadCartItems();
            updateCartCount();
        }

        function updateCartCount() {
            const cartItems = JSON.parse(localStorage.getItem('cart') || '[]');
            const cartCount = cartItems.reduce((total, item) => total + item.quantity, 0);
            const cartCountElement = document.getElementById('cart-count');
            if (cartCountElement) {
                cartCountElement.textContent = cartCount;
            }
        }

        function proceedToCheckout() {
            window.location.href = 'payment.php';
        }
    </script>
</body>
</html> 
</html> 