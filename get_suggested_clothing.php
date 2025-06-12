<?php
// Disable error display and enable error logging
ini_set('display_errors', 0);
ini_set('log_errors', 1);
ini_set('error_log', 'php_errors.log');

// Set headers first
header('Content-Type: application/json');

// Start session after headers
session_start();

// Function to send JSON response
function sendJsonResponse($data, $status = 200) {
    http_response_code($status);
    echo json_encode($data);
    exit();
}

try {
    // Check if user is logged in
    if (!isset($_SESSION['user_id'])) {
        sendJsonResponse(['error' => 'Not authenticated'], 401);
    }

    // Get the raw POST data
    $json = file_get_contents('php://input');
    if ($json === false) {
        sendJsonResponse(['error' => 'Failed to read input data'], 400);
    }

    $data = json_decode($json, true);
    if (json_last_error() !== JSON_ERROR_NONE) {
        sendJsonResponse(['error' => 'Invalid JSON data'], 400);
    }

    if (!isset($data['suggestions'])) {
        sendJsonResponse(['error' => 'No suggestions provided'], 400);
    }

    // Database connection
    $pdo = new PDO('mysql:host=localhost;dbname=virtual_fitting_room', 'root', '');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);

    // Extract keywords from suggestions
    $suggestions = strtolower($data['suggestions']);
    $keywords = array_unique(array_filter(explode(' ', preg_replace('/[^a-z0-9\s]/', ' ', $suggestions))));

    // Build the query
    $query = "SELECT * FROM clothing_items WHERE ";
    $conditions = [];
    $params = [];

    foreach ($keywords as $index => $keyword) {
        if (strlen($keyword) > 2) { // Only use keywords longer than 2 characters
            $conditions[] = "(LOWER(name) LIKE ? OR LOWER(description) LIKE ? OR LOWER(category) LIKE ?)";
            $param = "%$keyword%";
            $params[] = $param;
            $params[] = $param;
            $params[] = $param;
        }
    }

    if (empty($conditions)) {
        // If no valid keywords, get some random items
        $query = "SELECT * FROM clothing_items ORDER BY RAND() LIMIT 6";
        $stmt = $pdo->query($query);
    } else {
        $query .= implode(' OR ', $conditions);
        $query .= " ORDER BY CASE ";
        foreach ($keywords as $index => $keyword) {
            if (strlen($keyword) > 2) {
                $query .= "WHEN LOWER(name) LIKE ? THEN 1 ";
                $query .= "WHEN LOWER(description) LIKE ? THEN 2 ";
                $query .= "WHEN LOWER(category) LIKE ? THEN 3 ";
                $params[] = "%$keyword%";
                $params[] = "%$keyword%";
                $params[] = "%$keyword%";
            }
        }
        $query .= "ELSE 4 END LIMIT 6";

        $stmt = $pdo->prepare($query);
        $stmt->execute($params);
    }

    $items = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Format the response
    $response = array_map(function($item) {
        return [
            'id' => $item['id'],
            'name' => $item['name'],
            'price' => $item['price'],
            'category' => $item['category'],
            'image_url' => $item['image_url'],
            'colors' => json_decode($item['colors'], true) ?: [],
            'sizes' => json_decode($item['sizes'], true) ?: []
        ];
    }, $items);

    sendJsonResponse($response);

} catch (PDOException $e) {
    error_log("Database error: " . $e->getMessage());
    sendJsonResponse(['error' => 'Database error occurred'], 500);
} catch (Exception $e) {
    error_log("General error: " . $e->getMessage());
    sendJsonResponse(['error' => 'An unexpected error occurred'], 500);
}
?> 