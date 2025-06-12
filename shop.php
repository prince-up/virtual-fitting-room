<?php
session_start();

// Check if user is not logged in
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
} catch(PDOException $e) {
    $error = "Database error: " . $e->getMessage();
}

// Get filter parameters
$gender = isset($_GET['gender']) ? $_GET['gender'] : '';
$size = isset($_GET['size']) ? $_GET['size'] : '';
$color = isset($_GET['color']) ? $_GET['color'] : '';
$category = isset($_GET['category']) ? $_GET['category'] : '';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shop - Virtual Fitting Room</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <!-- Custom CSS -->
    <style>
        .filter-section {
            background-color: #f8f9fa;
            padding: 20px;
            border-radius: 10px;
            margin-bottom: 30px;
        }
        .product-card {
            transition: transform 0.3s ease;
            margin-bottom: 20px;
        }
        .product-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0,0,0,0.1);
        }
        .product-image {
            height: 300px;
            object-fit: cover;
        }
        .color-option {
            width: 25px;
            height: 25px;
            border-radius: 50%;
            display: inline-block;
            margin-right: 5px;
            cursor: pointer;
            border: 2px solid #fff;
            box-shadow: 0 0 5px rgba(0,0,0,0.2);
        }
        .color-option.selected {
            border: 2px solid #000;
        }
        .size-option {
            padding: 5px 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
            margin-right: 5px;
            cursor: pointer;
        }
        .size-option.selected {
            background-color: #0d6efd;
            color: white;
            border-color: #0d6efd;
        }
    </style>
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary sticky-top shadow-sm">
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
                        <a class="nav-link active" href="shop.php">
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
    <div class="container py-5">
        <div class="row">
            <!-- Filters Section -->
            <div class="col-md-3">
                <div class="filter-section">
                    <h4 class="mb-4">Filters</h4>
                    
                    <!-- Gender Filter -->
                    <div class="mb-4">
                        <h5>Gender</h5>
                        <div class="btn-group" role="group">
                            <input type="radio" class="btn-check" name="gender" id="gender-all" value="" <?php echo $gender === '' ? 'checked' : ''; ?>>
                            <label class="btn btn-outline-primary" for="gender-all">All</label>
                            
                            <input type="radio" class="btn-check" name="gender" id="gender-male" value="male" <?php echo $gender === 'male' ? 'checked' : ''; ?>>
                            <label class="btn btn-outline-primary" for="gender-male">Male</label>
                            
                            <input type="radio" class="btn-check" name="gender" id="gender-female" value="female" <?php echo $gender === 'female' ? 'checked' : ''; ?>>
                            <label class="btn btn-outline-primary" for="gender-female">Female</label>
                            
                            <input type="radio" class="btn-check" name="gender" id="gender-both" value="both" <?php echo $gender === 'both' ? 'checked' : ''; ?>>
                            <label class="btn btn-outline-primary" for="gender-both">Both</label>
                        </div>
                    </div>

                    <!-- Size Filter -->
                    <div class="mb-4">
                        <h5>Size</h5>
                        <div class="d-flex flex-wrap">
                            <?php
                            $sizes = ['XS', 'S', 'M', 'L', 'XL', 'XXL'];
                            foreach ($sizes as $s) {
                                $checked = $size === $s ? 'checked' : '';
                                echo "<input type='radio' class='btn-check' name='size' id='size-$s' value='$s' $checked>";
                                echo "<label class='btn btn-outline-secondary me-2 mb-2' for='size-$s'>$s</label>";
                            }
                            ?>
                        </div>
                    </div>

                    <!-- Color Filter -->
                    <div class="mb-4">
                        <h5>Color</h5>
                        <div class="d-flex flex-wrap">
                            <?php
                            $colors = [
                                'red' => '#FF0000',
                                'blue' => '#0000FF',
                                'green' => '#008000',
                                'black' => '#000000',
                                'white' => '#FFFFFF',
                                'gray' => '#808080',
                                'yellow' => '#FFFF00',
                                'pink' => '#FFC0CB'
                            ];
                            foreach ($colors as $name => $code) {
                                $checked = $color === $name ? 'selected' : '';
                                echo "<div class='color-option $checked' style='background-color: $code;' data-color='$name'></div>";
                            }
                            ?>
                        </div>
                    </div>

                    <!-- Category Filter -->
                    <div class="mb-4">
                        <h5>Category</h5>
                        <select class="form-select" name="category">
                            <option value="">All Categories</option>
                            <option value="tshirt" <?php echo $category === 'tshirt' ? 'selected' : ''; ?>>T-Shirts</option>
                            <option value="shirt" <?php echo $category === 'shirt' ? 'selected' : ''; ?>>Shirts</option>
                            <option value="jeans" <?php echo $category === 'jeans' ? 'selected' : ''; ?>>Jeans</option>
                            <option value="pants" <?php echo $category === 'pants' ? 'selected' : ''; ?>>Pants</option>
                            <option value="saree" <?php echo $category === 'saree' ? 'selected' : ''; ?>>Sarees</option>
                            <option value="blouse" <?php echo $category === 'blouse' ? 'selected' : ''; ?>>Blouses</option>
                            <option value="dress" <?php echo $category === 'dress' ? 'selected' : ''; ?>>Dresses</option>
                        </select>
                    </div>

                    <button class="btn btn-primary w-100" onclick="applyFilters()">Apply Filters</button>
                </div>
            </div>

            <!-- Products Section -->
            <div class="col-md-9">
                <div class="row" id="products-container">
                    <!-- Products will be loaded here -->
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
    // Define products array globally
    const products = [
        // Jeans (10 items)
        {
            id: 1,
            name: "Blue Denim Jeans",
            price: 49.99,
            image: "https://images.unsplash.com/photo-1541099649105-f69ad21f3246?ixlib=rb-4.0.3&auto=format&fit=crop&w=1000&q=80",
            gender: "both",
            sizes: ["S", "M", "L", "XL"],
            colors: ["blue"],
            category: "jeans"
        },
        {
            id: 2,
            name: "Black Skinny Jeans",
            price: 44.99,
            image: "https://images.unsplash.com/photo-1542272604-787c3835535d?ixlib=rb-4.0.3&auto=format&fit=crop&w=1000&q=80",
            gender: "both",
            sizes: ["S", "M", "L"],
            colors: ["black"],
            category: "jeans"
        },
        {
            id: 3,
            name: "Ripped Jeans",
            price: 54.99,
            image: "https://images.unsplash.com/photo-1541099649105-f69ad21f3246?ixlib=rb-4.0.3&auto=format&fit=crop&w=1000&q=80",
            gender: "both",
            sizes: ["S", "M", "L", "XL"],
            colors: ["blue", "black"],
            category: "jeans"
        },
        {
            id: 4,
            name: "High Waist Jeans",
            price: 59.99,
            image: "https://images.unsplash.com/photo-1542272604-787c3835535d?ixlib=rb-4.0.3&auto=format&fit=crop&w=1000&q=80",
            gender: "female",
            sizes: ["S", "M", "L"],
            colors: ["blue", "black"],
            category: "jeans"
        },
        {
            id: 5,
            name: "Boyfriend Jeans",
            price: 49.99,
            image: "https://images.unsplash.com/photo-1541099649105-f69ad21f3246?ixlib=rb-4.0.3&auto=format&fit=crop&w=1000&q=80",
            gender: "female",
            sizes: ["S", "M", "L"],
            colors: ["blue"],
            category: "jeans"
        },
        {
            id: 6,
            name: "Slim Fit Jeans",
            price: 44.99,
            image: "https://images.unsplash.com/photo-1542272604-787c3835535d?ixlib=rb-4.0.3&auto=format&fit=crop&w=1000&q=80",
            gender: "male",
            sizes: ["S", "M", "L", "XL"],
            colors: ["blue", "black"],
            category: "jeans"
        },
        {
            id: 7,
            name: "Bootcut Jeans",
            price: 49.99,
            image: "https://images.unsplash.com/photo-1541099649105-f69ad21f3246?ixlib=rb-4.0.3&auto=format&fit=crop&w=1000&q=80",
            gender: "both",
            sizes: ["S", "M", "L"],
            colors: ["blue"],
            category: "jeans"
        },
        {
            id: 8,
            name: "Mom Jeans",
            price: 54.99,
            image: "https://images.unsplash.com/photo-1542272604-787c3835535d?ixlib=rb-4.0.3&auto=format&fit=crop&w=1000&q=80",
            gender: "female",
            sizes: ["S", "M", "L"],
            colors: ["blue", "black"],
            category: "jeans"
        },
        {
            id: 9,
            name: "Straight Leg Jeans",
            price: 49.99,
            image: "https://images.unsplash.com/photo-1541099649105-f69ad21f3246?ixlib=rb-4.0.3&auto=format&fit=crop&w=1000&q=80",
            gender: "both",
            sizes: ["S", "M", "L", "XL"],
            colors: ["blue"],
            category: "jeans"
        },
        {
            id: 10,
            name: "Flared Jeans",
            price: 59.99,
            image: "https://images.unsplash.com/photo-1542272604-787c3835535d?ixlib=rb-4.0.3&auto=format&fit=crop&w=1000&q=80",
            gender: "female",
            sizes: ["S", "M", "L"],
            colors: ["blue", "black"],
            category: "jeans"
        },
        // Sarees (10 items)
        {
            id: 31,
            name: "Silk Saree",
            price: 2999,
            image: "https://images.unsplash.com/photo-1611591437281-460bfbe1220a?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=1000&q=80",
            gender: "female",
            sizes: ["Free Size"],
            colors: ["Red", "Gold"],
            category: "sarees"
        },
        {
            id: 32,
            name: "Banarasi Saree",
            price: 4999,
            image: "https://images.unsplash.com/photo-1611591437281-460bfbe1220a?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=1000&q=80",
            gender: "female",
            sizes: ["Free Size"],
            colors: ["Maroon", "Gold"],
            category: "sarees"
        },
        {
            id: 33,
            name: "Cotton Saree",
            price: 1999,
            image: "https://images.unsplash.com/photo-1611591437281-460bfbe1220a?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=1000&q=80",
            gender: "female",
            sizes: ["Free Size"],
            colors: ["Blue", "White"],
            category: "sarees"
        },
        {
            id: 34,
            name: "Designer Saree",
            price: 3999,
            image: "https://images.unsplash.com/photo-1611591437281-460bfbe1220a?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=1000&q=80",
            gender: "female",
            sizes: ["Free Size"],
            colors: ["Pink", "Silver"],
            category: "sarees"
        },
        {
            id: 35,
            name: "Wedding Saree",
            price: 5999,
            image: "https://images.unsplash.com/photo-1611591437281-460bfbe1220a?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=1000&q=80",
            gender: "female",
            sizes: ["Free Size"],
            colors: ["Red", "Gold"],
            category: "sarees"
        },
        {
            id: 36,
            name: "Party Wear Saree",
            price: 3499,
            image: "https://images.unsplash.com/photo-1611591437281-460bfbe1220a?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=1000&q=80",
            gender: "female",
            sizes: ["Free Size"],
            colors: ["Black", "Gold"],
            category: "sarees"
        },
        {
            id: 37,
            name: "Traditional Saree",
            price: 2499,
            image: "https://images.unsplash.com/photo-1611591437281-460bfbe1220a?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=1000&q=80",
            gender: "female",
            sizes: ["Free Size"],
            colors: ["Green", "Gold"],
            category: "sarees"
        },
        {
            id: 38,
            name: "Printed Saree",
            price: 1799,
            image: "https://images.unsplash.com/photo-1611591437281-460bfbe1220a?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=1000&q=80",
            gender: "female",
            sizes: ["Free Size"],
            colors: ["Yellow", "White"],
            category: "sarees"
        },
        {
            id: 39,
            name: "Georgette Saree",
            price: 2799,
            image: "https://images.unsplash.com/photo-1611591437281-460bfbe1220a?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=1000&q=80",
            gender: "female",
            sizes: ["Free Size"],
            colors: ["Purple", "Silver"],
            category: "sarees"
        },
        {
            id: 40,
            name: "Chiffon Saree",
            price: 2299,
            image: "https://images.unsplash.com/photo-1611591437281-460bfbe1220a?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=1000&q=80",
            gender: "female",
            sizes: ["Free Size"],
            colors: ["Peach", "Gold"],
            category: "sarees"
        },
        // Modern Dresses (10 items)
        {
            id: 18,
            name: "Cocktail Dress",
            price: 89.99,
            image: "https://images.unsplash.com/photo-1614179689702-355944cd0918?ixlib=rb-4.0.3&auto=format&fit=crop&w=1000&q=80",
            gender: "female",
            sizes: ["S", "M", "L"],
            colors: ["black", "red", "blue"],
            category: "dress"
        },
        {
            id: 19,
            name: "Maxi Dress",
            price: 79.99,
            image: "https://images.unsplash.com/photo-1614179689702-355944cd0918?ixlib=rb-4.0.3&auto=format&fit=crop&w=1000&q=80",
            gender: "female",
            sizes: ["S", "M", "L"],
            colors: ["blue", "green", "pink"],
            category: "dress"
        },
        {
            id: 20,
            name: "Bodycon Dress",
            price: 69.99,
            image: "https://images.unsplash.com/photo-1614179689702-355944cd0918?ixlib=rb-4.0.3&auto=format&fit=crop&w=1000&q=80",
            gender: "female",
            sizes: ["S", "M", "L"],
            colors: ["black", "red"],
            category: "dress"
        },
        {
            id: 21,
            name: "A-Line Dress",
            price: 74.99,
            image: "https://images.unsplash.com/photo-1614179689702-355944cd0918?ixlib=rb-4.0.3&auto=format&fit=crop&w=1000&q=80",
            gender: "female",
            sizes: ["S", "M", "L"],
            colors: ["blue", "green", "pink"],
            category: "dress"
        },
        {
            id: 22,
            name: "Wrap Dress",
            price: 84.99,
            image: "https://images.unsplash.com/photo-1614179689702-355944cd0918?ixlib=rb-4.0.3&auto=format&fit=crop&w=1000&q=80",
            gender: "female",
            sizes: ["S", "M", "L"],
            colors: ["black", "blue", "red"],
            category: "dress"
        },
        {
            id: 23,
            name: "Shift Dress",
            price: 64.99,
            image: "https://images.unsplash.com/photo-1614179689702-355944cd0918?ixlib=rb-4.0.3&auto=format&fit=crop&w=1000&q=80",
            gender: "female",
            sizes: ["S", "M", "L"],
            colors: ["black", "blue", "green"],
            category: "dress"
        },
        {
            id: 24,
            name: "Midi Dress",
            price: 79.99,
            image: "https://images.unsplash.com/photo-1614179689702-355944cd0918?ixlib=rb-4.0.3&auto=format&fit=crop&w=1000&q=80",
            gender: "female",
            sizes: ["S", "M", "L"],
            colors: ["black", "blue", "pink"],
            category: "dress"
        },
        {
            id: 25,
            name: "Mini Dress",
            price: 69.99,
            image: "https://images.unsplash.com/photo-1614179689702-355944cd0918?ixlib=rb-4.0.3&auto=format&fit=crop&w=1000&q=80",
            gender: "female",
            sizes: ["S", "M", "L"],
            colors: ["black", "red", "blue"],
            category: "dress"
        },
        {
            id: 26,
            name: "Summer Dress",
            price: 59.99,
            image: "https://images.unsplash.com/photo-1614179689702-355944cd0918?ixlib=rb-4.0.3&auto=format&fit=crop&w=1000&q=80",
            gender: "female",
            sizes: ["S", "M", "L"],
            colors: ["blue", "green", "pink"],
            category: "dress"
        },
        {
            id: 27,
            name: "Evening Dress",
            price: 99.99,
            image: "https://images.unsplash.com/photo-1614179689702-355944cd0918?ixlib=rb-4.0.3&auto=format&fit=crop&w=1000&q=80",
            gender: "female",
            sizes: ["S", "M", "L"],
            colors: ["black", "red", "blue"],
            category: "dress"
        }
    ];

    // Function to apply filters
    function applyFilters() {
        const gender = document.querySelector('input[name="gender"]:checked').value;
        const size = document.querySelector('input[name="size"]:checked')?.value || '';
        const color = document.querySelector('.color-option.selected')?.dataset.color || '';
        const category = document.querySelector('select[name="category"]').value;
        
        window.location.href = `shop.php?gender=${gender}&size=${size}&color=${color}&category=${category}`;
    }

    // Color selection
    document.querySelectorAll('.color-option').forEach(option => {
        option.addEventListener('click', function() {
            document.querySelectorAll('.color-option').forEach(opt => opt.classList.remove('selected'));
            this.classList.add('selected');
        });
    });

    // Load products
    document.addEventListener('DOMContentLoaded', function() {
        loadProducts();
    });

    function loadProducts() {
        const container = document.getElementById('products-container');
        if (!container) return;

        container.innerHTML = '';

        // Get current filter values
        const urlParams = new URLSearchParams(window.location.search);
        const gender = urlParams.get('gender') || '';
        const size = urlParams.get('size') || '';
        const color = urlParams.get('color') || '';
        const category = urlParams.get('category') || '';

        // Filter products based on selected filters
        const filteredProducts = products.filter(product => {
            const genderMatch = !gender || product.gender === gender || product.gender === "both";
            const sizeMatch = !size || product.sizes.includes(size);
            const colorMatch = !color || product.colors.includes(color);
            const categoryMatch = !category || product.category === category;
            
            return genderMatch && sizeMatch && colorMatch && categoryMatch;
        });

        // Display filtered products
        if (filteredProducts.length === 0) {
            container.innerHTML = '<div class="col-12 text-center"><p class="text-muted">No products found matching your criteria.</p></div>';
            return;
        }

        filteredProducts.forEach(product => {
            const productCard = `
                <div class="col-md-4 mb-4">
                    <div class="card product-card h-100">
                        <img src="${product.image}" class="card-img-top product-image" alt="${product.name}">
                        <div class="card-body">
                            <h5 class="card-title">${product.name}</h5>
                            <p class="card-text">₹${product.price.toFixed(2)}</p>
                            <div class="mb-2">
                                <small class="text-muted">Available Sizes: ${product.sizes.join(', ')}</small>
                            </div>
                            <div class="mb-3">
                                ${product.colors.map(color => `
                                    <div class="color-option" style="background-color: ${getColorCode(color)};" title="${color}"></div>
                                `).join('')}
                            </div>
                            <div class="d-grid gap-2">
                                <button class="btn btn-primary" onclick="addToCart(${product.id})">
                                    <i class="fas fa-shopping-cart me-2"></i>Add to Cart
                                </button>
                                <button class="btn btn-success" onclick="buyNow(${product.id})">
                                    <i class="fas fa-bolt me-2"></i>Buy Now
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            `;
            container.innerHTML += productCard;
        });
    }

    function getColorCode(color) {
        const colorMap = {
            'red': '#FF0000',
            'blue': '#0000FF',
            'green': '#008000',
            'black': '#000000',
            'white': '#FFFFFF',
            'gray': '#808080',
            'yellow': '#FFFF00',
            'pink': '#FFC0CB'
        };
        return colorMap[color] || '#000000';
    }

    // Function to handle Buy Now
    function buyNow(productId) {
        const product = products.find(p => p.id === productId);
        if (!product) {
            console.error('Product not found:', productId);
            return;
        }

        // Create a temporary cart with just this item
        const singleItemCart = [{
            id: product.id,
            name: product.name,
            price: product.price,
            image: product.image,
            quantity: 1,
            size: product.sizes[0], // Default to first available size
            color: product.colors[0], // Default to first available color
            category: product.category,
            gender: product.gender
        }];

        // Store the single item cart in localStorage
        localStorage.setItem('temp_cart', JSON.stringify(singleItemCart));

        // Redirect to payment page
        window.location.href = 'payment.php?source=buynow';
    }

    // Cart management functions
    function addToCart(productId) {
        const product = products.find(p => p.id === productId);
        if (!product) return;

        // Create modal for size and color selection
        const modalHtml = `
            <div class="modal fade" id="productOptionsModal" tabindex="-1">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">Select Options</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body">
                            <div class="mb-3">
                                <label class="form-label">Size:</label>
                                <div class="btn-group d-flex flex-wrap" role="group">
                                    ${product.sizes.map(size => `
                                        <input type="radio" class="btn-check" name="size" id="size-${size}" value="${size}">
                                        <label class="btn btn-outline-primary me-2 mb-2" for="size-${size}">${size}</label>
                                    `).join('')}
                                </div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Color:</label>
                                <div class="d-flex flex-wrap">
                                    ${product.colors.map(color => `
                                        <div class="color-option me-2 mb-2" 
                                             style="background-color: ${getColorCode(color)};" 
                                             data-color="${color}"
                                             onclick="selectColor(this)"
                                             title="${color}"></div>
                                    `).join('')}
                                </div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Quantity:</label>
                                <div class="input-group" style="width: 150px;">
                                    <button class="btn btn-outline-secondary" type="button" onclick="updateModalQuantity(-1)">-</button>
                                    <input type="text" class="form-control text-center" id="modalQuantity" value="1" readonly>
                                    <button class="btn btn-outline-secondary" type="button" onclick="updateModalQuantity(1)">+</button>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                            <button type="button" class="btn btn-primary" onclick="confirmAddToCart(${productId})">Add to Cart</button>
                        </div>
                    </div>
                </div>
            </div>
        `;

        // Remove existing modal if any
        const existingModal = document.getElementById('productOptionsModal');
        if (existingModal) {
            existingModal.remove();
        }

        // Add modal to body
        document.body.insertAdjacentHTML('beforeend', modalHtml);

        // Show modal
        const modal = new bootstrap.Modal(document.getElementById('productOptionsModal'));
        modal.show();
    }

    function selectColor(element) {
        document.querySelectorAll('.color-option').forEach(opt => opt.style.border = '2px solid #fff');
        element.style.border = '2px solid #000';
    }

    function updateModalQuantity(change) {
        const input = document.getElementById('modalQuantity');
        const currentValue = parseInt(input.value);
        input.value = Math.max(1, currentValue + change);
    }

    function confirmAddToCart(productId) {
        const product = products.find(p => p.id === productId);
        if (!product) return;

        // Get selected options
        const selectedSize = document.querySelector('input[name="size"]:checked')?.value;
        const selectedColor = document.querySelector('.color-option[style*="border: 2px solid rgb(0, 0, 0)"]')?.dataset.color;
        const quantity = parseInt(document.getElementById('modalQuantity').value);

        // Validate selections
        if (!selectedSize || !selectedColor) {
            alert('Please select both size and color');
            return;
        }

        let cart = JSON.parse(localStorage.getItem('cart')) || [];
        const existingItemIndex = cart.findIndex(item => 
            item.id === productId && 
            item.size === selectedSize && 
            item.color === selectedColor
        );

        if (existingItemIndex !== -1) {
            cart[existingItemIndex].quantity += quantity;
        } else {
            cart.push({
                id: product.id,
                name: product.name,
                price: product.price,
                image: product.image,
                quantity: quantity,
                size: selectedSize,
                color: selectedColor,
                category: product.category,
                gender: product.gender
            });
        }

        localStorage.setItem('cart', JSON.stringify(cart));
        updateCartCount();

        // Close modal
        const modal = bootstrap.Modal.getInstance(document.getElementById('productOptionsModal'));
        modal.hide();

        // Show success message
        const toastContainer = document.createElement('div');
        toastContainer.className = 'toast-container position-fixed bottom-0 end-0 p-3';
        toastContainer.innerHTML = `
            <div class="toast align-items-center text-white bg-success border-0" role="alert" aria-live="assertive" aria-atomic="true">
                <div class="d-flex">
                    <div class="toast-body">
                        <i class="fas fa-check-circle me-2"></i>${product.name} added to cart!
                    </div>
                    <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
                </div>
            </div>
        `;
        document.body.appendChild(toastContainer);

        const toast = new bootstrap.Toast(toastContainer.querySelector('.toast'));
        toast.show();

        // Remove toast after it's hidden
        toastContainer.querySelector('.toast').addEventListener('hidden.bs.toast', () => {
            document.body.removeChild(toastContainer);
        });
    }

    function updateCartCount() {
        const cart = JSON.parse(localStorage.getItem('cart')) || [];
        const cartCount = cart.reduce((total, item) => total + item.quantity, 0);
        const cartCountElement = document.getElementById('cart-count');
        if (cartCountElement) {
            cartCountElement.textContent = cartCount;
        }
    }

    // Initialize cart count when page loads
    document.addEventListener('DOMContentLoaded', function() {
        updateCartCount();
        loadProducts();
    });
    </script>
</body>
</html> 