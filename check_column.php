<?php
// Database connection
$host = 'localhost';
$dbname = 'virtual_fitting_room';
$username = 'root';
$password = '';

try {
    // Create connection
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Get table structure
    $stmt = $pdo->query("DESCRIBE users");
    $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "<h2>Users Table Structure:</h2>";
    echo "<table border='1' cellpadding='5'>";
    echo "<tr><th>Field</th><th>Type</th><th>Null</th><th>Key</th><th>Default</th></tr>";
    
    $profile_pic_exists = false;
    
    foreach ($columns as $column) {
        echo "<tr>";
        foreach ($column as $key => $value) {
            echo "<td>" . htmlspecialchars($value ?? 'NULL') . "</td>";
        }
        echo "</tr>";
        
        if ($column['Field'] === 'profile_pic') {
            $profile_pic_exists = true;
        }
    }
    
    echo "</table>";
    
    if ($profile_pic_exists) {
        echo "<p style='color: green; font-weight: bold;'>✓ The profile_pic column exists in the users table!</p>";
    } else {
        echo "<p style='color: red; font-weight: bold;'>✗ The profile_pic column does not exist in the users table.</p>";
        echo "<p>To add the column, run this SQL command:</p>";
        echo "<pre>ALTER TABLE users ADD COLUMN profile_pic VARCHAR(255) DEFAULT NULL;</pre>";
    }
    
} catch(PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
}
?> 