<?php
// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
<nav class="navbar navbar-expand-lg navbar-dark sticky-top shadow-lg">
    <div class="container">
        <a class="navbar-brand d-flex align-items-center" href="index.php">
            <div class="brand-icon">
                <i class="fas fa-eye"></i>
            </div>
            <span class="brand-text">Lookat me</span>
        </a>
        <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav me-auto ms-lg-4">
                <li class="nav-item">
                    <a class="nav-link" href="index.php">
                        <i class="fas fa-home me-1"></i> Home
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="shop.php">
                        <i class="fas fa-shopping-bag me-1"></i> Shop
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="virtual_try.php">
                        <i class="fas fa-tshirt me-1"></i> Try-On
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="cart.php">
                        <i class="fas fa-shopping-cart me-1"></i> Cart
                        <span class="badge bg-gradient rounded-pill ms-1" id="cart-count">0</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="about.php">
                        <i class="fas fa-info-circle me-1"></i> About
                    </a>
                </li>
            </ul>
            <ul class="navbar-nav">
                <?php if (isset($_SESSION['user_id'])): ?>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle d-flex align-items-center" href="#" id="userDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <div class="user-avatar me-2">
                                <i class="fas fa-user"></i>
                            </div>
                            <span><?php echo htmlspecialchars($_SESSION['username'] ?? 'User'); ?></span>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end shadow-lg" aria-labelledby="userDropdown">
                            <li><a class="dropdown-item" href="profile.php"><i class="fas fa-user me-2"></i>My Profile</a></li>
                            <li><a class="dropdown-item" href="orders.php"><i class="fas fa-box me-2"></i>My Orders</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item text-danger" href="logout.php"><i class="fas fa-sign-out-alt me-2"></i>Logout</a></li>
                        </ul>
                    </li>
                <?php else: ?>
                    <li class="nav-item">
                        <a class="nav-link btn-nav-login" href="login.php">
                            <i class="fas fa-sign-in-alt me-1"></i> Login
                        </a>
                    </li>
                    <li class="nav-item ms-2">
                        <a class="nav-link btn-nav-register" href="register.php">
                            <i class="fas fa-user-plus me-1"></i> Register
                        </a>
                    </li>
                <?php endif; ?>
            </ul>
        </div>
    </div>
</nav>

<script>
// Update cart count
function updateCartCount() {
    const cart = JSON.parse(localStorage.getItem('cart')) || [];
    const cartCount = cart.reduce((total, item) => total + item.quantity, 0);
    const cartCountElement = document.getElementById('cart-count');
    if (cartCountElement) {
        cartCountElement.textContent = cartCount;
    }
}

// Update cart count when page loads
document.addEventListener('DOMContentLoaded', updateCartCount);
</script>

<style>
.navbar {
    background: linear-gradient(135deg, #232f3e 0%, #1a252f 100%) !important;
    padding: 1rem 0;
    transition: all 0.3s ease;
}

.navbar.scrolled {
    padding: 0.5rem 0;
    box-shadow: 0 5px 20px rgba(0,0,0,0.2);
}

.brand-icon {
    width: 45px;
    height: 45px;
    background: linear-gradient(135deg, #ff9900 0%, #ff6600 100%);
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    margin-right: 12px;
    transition: all 0.3s ease;
}

.brand-icon i {
    font-size: 1.3rem;
    color: white;
}

.navbar-brand:hover .brand-icon {
    transform: rotate(10deg) scale(1.1);
}

.brand-text {
    font-size: 1.3rem;
    font-weight: 700;
    background: linear-gradient(135deg, #ffffff 0%, #ff9900 100%);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
}

.nav-link {
    color: #fff !important;
    transition: all 0.3s ease;
    padding: 0.7rem 1rem !important;
    border-radius: 8px;
    margin: 0 2px;
    font-weight: 500;
    position: relative;
}

.nav-link::before {
    content: '';
    position: absolute;
    bottom: 5px;
    left: 50%;
    transform: translateX(-50%);
    width: 0;
    height: 2px;
    background: linear-gradient(90deg, #ff9900, #ff6600);
    transition: width 0.3s ease;
}

.nav-link:hover::before {
    width: 60%;
}

.nav-link:hover {
    color: #ff9900 !important;
    background: rgba(255, 153, 0, 0.1);
}

.navbar-brand {
    color: #fff !important;
}

.badge {
    font-size: 0.75rem;
    padding: 0.25rem 0.5rem;
}

.bg-gradient {
    background: linear-gradient(135deg, #ff9900 0%, #ff6600 100%) !important;
}

.user-avatar {
    width: 35px;
    height: 35px;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1rem;
}

.dropdown-menu {
    border: none;
    border-radius: 12px;
    padding: 0.5rem;
    margin-top: 0.5rem;
}

.dropdown-item {
    border-radius: 8px;
    padding: 0.6rem 1rem;
    transition: all 0.3s ease;
}

.dropdown-item:hover {
    background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
    transform: translateX(5px);
}

.btn-nav-login, .btn-nav-register {
    padding: 0.6rem 1.2rem !important;
    border-radius: 8px;
    font-weight: 600;
}

.btn-nav-login {
    background: rgba(255, 255, 255, 0.1);
    border: 1px solid rgba(255, 255, 255, 0.2);
}

.btn-nav-login:hover {
    background: rgba(255, 255, 255, 0.2);
    border-color: #ff9900;
}

.btn-nav-register {
    background: linear-gradient(135deg, #ff9900 0%, #ff6600 100%);
    border: none;
}

.btn-nav-register:hover {
    transform: translateY(-2px);
    box-shadow: 0 5px 15px rgba(255, 153, 0, 0.3);
}

.navbar-toggler {
    padding: 0.5rem;
    border-radius: 8px;
}

.navbar-toggler:focus {
    box-shadow: 0 0 0 0.2rem rgba(255, 153, 0, 0.25);
}

/* Scroll effect */
window.addEventListener('scroll', function() {
    const navbar = document.querySelector('.navbar');
    if (window.scrollY > 50) {
        navbar.classList.add('scrolled');
    } else {
        navbar.classList.remove('scrolled');
    }
});
</style>

