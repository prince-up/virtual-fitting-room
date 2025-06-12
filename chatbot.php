<?php
header('Content-Type: application/json');

// Get the raw POST data
$json = file_get_contents('php://input');
$data = json_decode($json, true);

if (!isset($data['message'])) {
    echo json_encode(['response' => 'Error: No message received']);
    exit;
}

$message = strtolower(trim($data['message']));

// Predefined responses for common queries
$responses = [
    'hello' => 'Hi there! How can I assist you today?',
    'hi' => 'Hello! How may I help you?',
    'hey' => 'Hey! What can I do for you?',
    
    // Shopping related
    'shop' => 'You can browse our collection in the Shop section. We have various categories including t-shirts, jeans, sarees, and more!',
    'products' => 'We offer a wide range of clothing including traditional and modern wear. Check out our Shop page for the complete collection.',
    'price' => 'Our prices vary depending on the product. You can find detailed pricing information on each product page in our shop.',
    
    // Virtual Try-On related
    'virtual try' => 'Our Virtual Try-On feature lets you see how clothes look on you before buying! Just upload your photo and select the items you want to try.',
    'try on' => 'Want to try clothes virtually? Head to our Virtual Try-On section and upload your photo to get started!',
    
    // Account related
    'account' => 'You can manage your account settings in the Profile section. There you can update your details and view your order history.',
    'profile' => 'Visit your Profile page to manage your account settings, update your information, and view your orders.',
    'orders' => 'You can view your order history in your Profile section.',
    
    // Payment related
    'payment' => 'We accept various payment methods including credit/debit cards, UPI, and cash on delivery.',
    'cod' => 'Yes, we offer Cash on Delivery for eligible orders!',
    
    // Size related
    'size' => 'We offer sizes from XS to XXL. Each product page has a detailed size chart to help you choose the right fit.',
    'size chart' => 'You can find size charts on each product page. They provide detailed measurements to help you find your perfect fit.',
    
    // Help related
    'help' => 'I can help you with shopping, virtual try-on, account management, payments, and sizing. What would you like to know more about?',
    'contact' => 'You can reach our customer service team through the Contact Us page or email us at support@virtualfittingroom.com',
    
    // Default response
    'default' => "I'm not sure I understand. Could you please rephrase that? Or you can ask me about our products, virtual try-on feature, sizing, payments, or account management."
];

// Check for keyword matches
$response = $responses['default'];
foreach ($responses as $keyword => $reply) {
    if (strpos($message, $keyword) !== false) {
        $response = $reply;
        break;
    }
}

// Send response
echo json_encode(['response' => $response]);
?> 