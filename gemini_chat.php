<?php
header('Content-Type: application/json');
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Gemini API configuration
define('GEMINI_API_KEY', 'AIzaSyBA8m9ZxPDLSV6NqYnx48ARntCWfMrE9Sg');
define('GEMINI_API_URL', 'https://generativelanguage.googleapis.com/v1/models/gemini-pro/generateContent');

// Get the message from the POST request
$data = json_decode(file_get_contents('php://input'), true);
$message = $data['message'] ?? '';

// Website-specific information
$website_info = [
    'features' => [
        'virtual_try_on' => 'Our virtual try-on feature lets you see how clothes look on you before buying.',
        'shop' => 'Browse and purchase from our wide collection of clothing items.',
        'cart' => 'Add items to cart and manage your shopping list.',
        'measurements' => 'Get accurate body measurements using our AI-powered camera system.',
        'categories' => 'We offer various categories including t-shirts, jeans, dresses, and more.'
    ],
    'categories' => [
        'casual_tshirts' => 'Comfortable casual t-shirts for everyday wear',
        'graphic_tshirts' => 'T-shirts with unique graphic designs',
        'polo_tshirts' => 'Classic polo t-shirts for a smart casual look',
        'vneck_tshirts' => 'Stylish v-neck t-shirts',
        'longsleeve_tshirts' => 'Long sleeve t-shirts for cooler weather',
        'basic_tshirts' => 'Essential basic t-shirts',
        'jeans' => 'Variety of jeans styles',
        'pants' => 'Different types of pants',
        'womens_dresses' => "Women's dresses for all occasions",
        'kids_dresses' => "Children's dresses",
        'kids_tshirts' => "Children's t-shirts"
    ],
    'pages' => [
        'home' => 'Main page with featured categories and latest collections',
        'shop' => 'Browse and purchase clothes',
        'virtual_try' => 'Try clothes virtually using AI',
        'cart' => 'View and manage your shopping cart',
        'profile' => 'Manage your account and view orders',
        'about' => 'Learn about our team and mission'
    ]
];

function generateResponse($message, $website_info) {
    $message = strtolower($message);
    
    // Check for different types of questions
    if (strpos($message, 'virtual try') !== false || strpos($message, 'try on') !== false) {
        return "Our virtual try-on feature allows you to see how clothes look on you before purchasing. Simply go to the Virtual Try-On page, enable your camera, and follow the instructions. You can also get your measurements using our AI-powered system.";
    }
    
    if (strpos($message, 'category') !== false || strpos($message, 'categories') !== false) {
        return "We offer various clothing categories including: Casual T-shirts, Graphic T-shirts, Polo T-shirts, V-neck T-shirts, Long Sleeve T-shirts, Basic T-shirts, Jeans, Pants, Women's Dresses, and Kids' Clothing. You can find all these in our Shop page.";
    }
    
    if (strpos($message, 'measurement') !== false || strpos($message, 'size') !== false) {
        return "You can get accurate body measurements using our AI-powered camera system. Click the camera button in this chat window, allow camera access, and stand in front of the camera. The system will calculate your shoulder width, chest width, and waist measurements.";
    }
    
    if (strpos($message, 'cart') !== false || strpos($message, 'shopping') !== false) {
        return "To shop on our website: 1. Browse items in the Shop page 2. Click 'Add to Cart' for items you like 3. View your cart by clicking the Cart icon 4. Proceed to checkout when ready. You can also use the 'Buy Now' option for immediate purchase.";
    }
    
    if (strpos($message, 'account') !== false || strpos($message, 'profile') !== false) {
        return "You can manage your account through the Profile page. Here you can update your personal information, view your order history, and manage your saved measurements. Click on your username in the top right corner to access these features.";
    }
    
    if (strpos($message, 'payment') !== false || strpos($message, 'buy') !== false) {
        return "We accept various payment methods including credit/debit cards, UPI, and cash on delivery. Your payment information is securely processed, and you'll receive an order confirmation email after successful purchase.";
    }

    if (strpos($message, 'help') !== false || strpos($message, 'how to') !== false) {
        return "I can help you with: \n1. Virtual Try-On instructions\n2. Finding clothing categories\n3. Getting measurements\n4. Shopping assistance\n5. Account management\n6. Payment information\nWhat would you like to know more about?";
    }
    
    // Default response if no specific match is found
    return "I'm here to help you with virtual try-ons, shopping, measurements, and more. Could you please be more specific about what you'd like to know?";
}

try {
    if (empty($message)) {
        throw new Exception('No message provided');
    }
    
    $response = generateResponse($message, $website_info);
    
    echo json_encode([
        'success' => true,
        'response' => $response
    ]);
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'response' => $e->getMessage()
    ]);
}
?> 