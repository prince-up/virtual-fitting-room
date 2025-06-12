CREATE DATABASE IF NOT EXISTS virtual_fitting_room;
USE virtual_fitting_room;

CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    phone VARCHAR(20),
    address TEXT,
    city VARCHAR(100),
    state VARCHAR(100),
    pincode VARCHAR(10),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS clothing_items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    description TEXT,
    price DECIMAL(10,2) NOT NULL,
    category VARCHAR(50) NOT NULL,
    image VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS user_outfits (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    name VARCHAR(255) NOT NULL,
    items JSON NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id)
);

CREATE TABLE IF NOT EXISTS orders (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    total_amount DECIMAL(10,2) NOT NULL,
    delivery_address TEXT NOT NULL,
    payment_method ENUM('cod', 'card', 'paytm') NOT NULL,
    status ENUM('pending', 'processing', 'shipped', 'delivered', 'cancelled') DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id)
);

CREATE TABLE IF NOT EXISTS order_items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    order_id INT NOT NULL,
    item_id INT NOT NULL,
    quantity INT NOT NULL,
    price DECIMAL(10,2) NOT NULL,
    FOREIGN KEY (order_id) REFERENCES orders(id),
    FOREIGN KEY (item_id) REFERENCES clothing_items(id)
);

-- Sample data
INSERT INTO clothing_items (name, description, price, image_url, category, default_position, colors, sizes) VALUES
('Blue T-Shirt', 'Comfortable cotton t-shirt', 19.99, 'assets/images/blue-tshirt.png', 'Tops', 'torso', '["#0000FF", "#000000", "#FFFFFF"]', '["S", "M", "L", "XL"]'),
('Black Jeans', 'Slim fit black jeans', 49.99, 'assets/images/black-jeans.png', 'Bottoms', 'legs', '["#000000", "#333333"]', '["28", "30", "32", "34"]'),
('Red Dress', 'Elegant evening dress', 79.99, 'assets/images/red-dress.png', 'Dresses', 'full', '["#FF0000", "#880000"]', '["S", "M", "L"]'),
('White Blouse', 'Formal white blouse', 29.99, 'assets/images/white-blouse.png', 'Tops', 'torso', '["#FFFFFF", "#F5F5F5"]', '["S", "M", "L"]');

INSERT INTO clothing_items (name, description, price, category, image_url) VALUES
('Men\'s T-Shirt', 'Comfortable cotton t-shirt', 499.00, 'men', 'assets/images/clothing/men-tshirt-1.jpg'),
('Women\'s Dress', 'Elegant summer dress', 1299.00, 'women', 'assets/images/clothing/women-dress-1.jpg'),
('Men\'s Jeans', 'Classic blue jeans', 999.00, 'men', 'assets/images/clothing/men-jeans-1.jpg'),
('Women\'s Top', 'Casual women\'s top', 699.00, 'women', 'assets/images/clothing/women-top-1.jpg');

-- Sample clothing items
INSERT INTO clothing_items (name, description, price, category, image_url) VALUES
('Blue Denim Jeans', 'Classic blue denim jeans', 1499.00, 'jeans', 'bluejeans.jpg'),
('Black Skinny Jeans', 'Slim fit black jeans', 1599.00, 'jeans', 'black_jeans.jpg'),
('Light Blue Jeans', 'Casual light blue jeans', 1399.00, 'jeans', 'light_blue_jeans.jpg'),
('Ripped Black Jeans', 'Trendy ripped black jeans', 1699.00, 'jeans', 'ripped_black_jeans.jpg'),
('High Waist Blue Jeans', 'Comfortable high waist jeans', 1799.00, 'jeans', 'high_waist_jeans.jpg'),
('Formal Shirt', 'Classic white formal shirt', 1299.00, 'shirts', 'shirt1.jpg'),
('Casual T-Shirt', 'Comfortable cotton t-shirt', 599.00, 'shirts', 'shirt2.jpg'),
('Summer Dress', 'Light and airy summer dress', 1999.00, 'dresses', 'women1.jpg'),
('Leather Jacket', 'Premium leather jacket', 4999.00, 'jackets', 'leather_jacaket.jpg'),
('Wool Blazer', 'Formal wool blazer', 3999.00, 'jackets', 'wool_blazzer.jpg'),
('Trench Coat', 'Classic trench coat', 4499.00, 'jackets', 'trench_coat.jpg'),
('Little Black Dress', 'Elegant black dress', 2999.00, 'dresses', 'liiitle_black.webp'),
('Denim Shorts', 'Casual denim shorts', 999.00, 'shorts', 'denim_shorts.jpg'),
('Black Skirt', 'Versatile black skirt', 1299.00, 'skirts', 'blackskirt.jpg'),
('Red Silk Saree', 'Traditional red silk saree', 5999.00, 'ethnic', 'red_silk_saree.jpg'),
('Formal Evening Gown', 'Elegant evening gown', 6999.00, 'dresses', 'formral_evening.jpg'),
('Summer Formal Shirt', 'Light formal shirt', 1499.00, 'shirts', 'sum_er_formal.jpg'),
('Black Crop Top', 'Trendy crop top', 799.00, 'tops', 'black_crop_trap.jpg'),
('Striped Blouse', 'Casual striped blouse', 899.00, 'tops', 'stripped_blowse.webp'),
('Designer T-Shirt', 'Unique printed t-shirt', 1299.00, 'shirts', 'tio90.jpg'),
('Artistic Top', 'Hand-painted artistic top', 1599.00, 'tops', 'paint 2.avif'); 