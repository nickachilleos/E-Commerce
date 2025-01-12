<?php
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'customer') {
    echo json_encode(['error' => 'Unauthorized access.']);
    exit();
}

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "marketplace";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    echo json_encode(['error' => 'Database connection failed.']);
    exit();
}

$customer_id = $_SESSION['user_id'];
$product_id = $_POST['product_id'] ?? null;
$action = $_POST['action'] ?? '';

if (!$product_id) {
    echo json_encode(['error' => 'Invalid product ID.']);
    exit();
}

if ($action === 'add') {
    $sql = "INSERT INTO favorites_table (customer_id, product_id) VALUES (?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $customer_id, $product_id);
    if ($stmt->execute()) {
        echo json_encode(['message' => 'Product added to favorites.']);
    } else {
        echo json_encode(['error' => 'Failed to add product to favorites.']);
    }
    $stmt->close();
} elseif ($action === 'remove') {
    $sql = "DELETE FROM favorites_table WHERE customer_id = ? AND product_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $customer_id, $product_id);
    if ($stmt->execute()) {
        echo json_encode(['message' => 'Product removed from favorites.']);
    } else {
        echo json_encode(['error' => 'Failed to remove product from favorites.']);
    }
    $stmt->close();
} else {
    echo json_encode(['error' => 'Invalid action.']);
}

$conn->close();
?>
