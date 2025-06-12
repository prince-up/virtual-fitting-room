-- Insert sample clothing items with new format
INSERT INTO clothing_items (name, description, price, image_url, category, default_position, colors, sizes) VALUES
-- Tops
('Blue T-Shirt', 'Comfortable cotton t-shirt', 19.99, 'assets/images/blue-tshirt.png', 'Tops', 'torso', '["#0000FF", "#000000", "#FFFFFF"]', '["S", "M", "L", "XL"]'),
('White Blouse', 'Formal white blouse', 29.99, 'assets/images/white-blouse.png', 'Tops', 'torso', '["#FFFFFF", "#F5F5F5"]', '["S", "M", "L"]'),
('Striped Polo Shirt', 'Premium cotton blend, breathable fabric', 29.99, 'assets/images/striped-polo.png', 'Tops', 'torso', '["#0000FF", "#FFFFFF", "#000000"]', '["S", "M", "L", "XL"]'),
('V-Neck Sweater', 'Soft wool blend, perfect for layering', 39.99, 'assets/images/vneck-sweater.png', 'Tops', 'torso', '["#800000", "#000000", "#808080"]', '["M", "L", "XL"]'),

-- Bottoms
('Black Jeans', 'Slim fit black jeans', 49.99, 'assets/images/black-jeans.png', 'Bottoms', 'legs', '["#000000", "#333333"]', '["28", "30", "32", "34"]'),
('Blue Denim Jeans', 'Classic blue denim jeans', 44.99, 'assets/images/blue-jeans.png', 'Bottoms', 'legs', '["#0000FF", "#00008B"]', '["28", "30", "32", "34"]'),
('Black Dress Pants', 'Formal dress pants', 39.99, 'assets/images/black-pants.png', 'Bottoms', 'legs', '["#000000", "#333333"]', '["30", "32", "34", "36"]'),

-- Dresses
('Red Dress', 'Elegant evening dress', 79.99, 'assets/images/red-dress.png', 'Dresses', 'full', '["#FF0000", "#880000"]', '["S", "M", "L"]'),
('Floral Summer Dress', 'Lightweight fabric, perfect for summer', 59.99, 'assets/images/floral-dress.png', 'Dresses', 'full', '["#FF69B4", "#FFFFFF", "#000000"]', '["S", "M", "L"]'),
('Little Black Dress', 'Classic style, versatile for any occasion', 69.99, 'assets/images/black-dress.png', 'Dresses', 'full', '["#000000"]', '["S", "M", "L"]'),

-- Jackets
('Leather Biker Jacket', 'Genuine leather, timeless style', 99.99, 'assets/images/leather-jacket.png', 'Jackets', 'torso', '["#000000", "#333333"]', '["S", "M", "L", "XL"]'),
('Denim Jacket', 'Classic design, perfect for layering', 59.99, 'assets/images/denim-jacket.png', 'Jackets', 'torso', '["#0000FF", "#00008B"]', '["S", "M", "L"]'),
('Bomber Jacket', 'Lightweight, casual style', 49.99, 'assets/images/bomber-jacket.png', 'Jackets', 'torso', '["#000000", "#808080", "#800000"]', '["M", "L", "XL"]'),

-- Ethnic
('Silk Kurta', 'Traditional design, premium fabric', 49.99, 'assets/images/silk-kurta.png', 'Ethnic', 'torso', '["#FFD700", "#800000", "#000000"]', '["S", "M", "L"]'),
('Embroidered Anarkali', 'Intricate embroidery, flowing design', 79.99, 'assets/images/anarkali.png', 'Ethnic', 'full', '["#FF69B4", "#FFD700", "#FFFFFF"]', '["S", "M", "L"]'),

-- Artistic
('Abstract Print Shirt', 'Unique design, statement piece', 34.99, 'assets/images/abstract-shirt.png', 'Artistic', 'torso', '["#FF0000", "#00FF00", "#0000FF"]', '["S", "M", "L"]'),
('Geometric Pattern Dress', 'Modern design, eye-catching patterns', 59.99, 'assets/images/geometric-dress.png', 'Artistic', 'full', '["#000000", "#FFFFFF", "#FF0000"]', '["S", "M", "L"]'),
('Tie-Dye Hoodie', 'Hand-dyed, one-of-a-kind design', 44.99, 'assets/images/tie-dye-hoodie.png', 'Artistic', 'torso', '["#FF0000", "#00FF00", "#0000FF", "#FFFF00"]', '["M", "L", "XL"]'); 