<?php
try {
    $pdo = new PDO('mysql:host=localhost;dbname=project_story', 'root', '');
    echo "Database connected successfully\n";
    
    // Test comments table
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM comments");
    $result = $stmt->fetch();
    echo "Comments count: " . $result['count'] . "\n";
    
    // Test admins table
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM admins");
    $result = $stmt->fetch();
    echo "Admins count: " . $result['count'] . "\n";
    
    // Test stories table
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM stories");
    $result = $stmt->fetch();
    echo "Stories count: " . $result['count'] . "\n";
    
} catch(Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
?>

