<?php
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'customer') {
    echo json_encode(['error' => 'Unauthorized access.']);
    exit();
}

// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "marketplace";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    echo json_encode(['error' => 'Database connection failed.']);
    exit();
}

// Input validation
$customer_id = $_SESSION['user_id'];
$product_id = $_POST['product_id'] ?? null;
$action = $_POST['action'] ?? '';

if (!$product_id || !in_array($action, ['add', 'increment', 'decrement', 'remove'])) {
    echo json_encode(['error' => 'Invalid input data.']);
    exit();
}

if ($action === 'add') {
    // Add product to cart or increment quantity if it already exists
    $sql = "SELECT * FROM cart_table WHERE customer_id = ? AND product_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $customer_id, $product_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // Product already in cart, increment quantity
        $sql = "UPDATE cart_table SET quantity = quantity + 1 WHERE customer_id = ? AND product_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ii", $customer_id, $product_id);
        $stmt->execute();
    } else {
        // Add new product to cart
        $sql = "INSERT INTO cart_table (customer_id, product_id, quantity) VALUES (?, ?, 1)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ii", $customer_id, $product_id);
        $stmt->execute();
    }

    if ($stmt->affected_rows > 0) {
        echo json_encode(['success' => true, 'message' => 'Product added to the cart!']);
    } else {
        echo json_encode(['error' => 'Failed to remove product from cart.']);
    }
    
} elseif ($action === 'increment') {
    // Increment product quantity in cart
    $sql = "UPDATE cart_table SET quantity = quantity + 1 WHERE customer_id = ? AND product_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $customer_id, $product_id);
    $stmt->execute();

    if ($stmt->affected_rows > 0) {
        // Fetch the updated quantity and price
        $sql = "SELECT c.quantity, p.price 
                FROM cart_table c 
                INNER JOIN products_table p ON c.product_id = p.product_id 
                WHERE c.customer_id = ? AND c.product_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ii", $customer_id, $product_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();

        $total_price = $row['quantity'] * $row['price'];

        echo json_encode(['success' => true, 'quantity' => $row['quantity'], 'total_price' => '€' . number_format($total_price, 2)]);
    } else {
        echo json_encode(['error' => 'Failed to increment product quantity.']);
    }
} elseif ($action === 'decrement') {
    // Decrement product quantity in cart, ensuring it doesn't drop below 1
    $sql = "UPDATE cart_table SET quantity = GREATEST(quantity - 1, 1) WHERE customer_id = ? AND product_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $customer_id, $product_id);
    $stmt->execute();

    if ($stmt->affected_rows > 0) {
        // Fetch the updated quantity and price
        $sql = "SELECT c.quantity, p.price 
                FROM cart_table c 
                INNER JOIN products_table p ON c.product_id = p.product_id 
                WHERE c.customer_id = ? AND c.product_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ii", $customer_id, $product_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();

        $total_price = $row['quantity'] * $row['price'];

        echo json_encode(['success' => true, 'quantity' => $row['quantity'], 'total_price' => $total_price]);
    } else {
        echo json_encode(['error' => 'Failed to decrement product quantity.']);
    }
} elseif ($action === 'remove') {
    // Remove product from cart
    $sql = "DELETE FROM cart_table WHERE customer_id = ? AND product_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $customer_id, $product_id);
    $stmt->execute();

    if ($stmt->affected_rows > 0) {
        echo json_encode(['success' => true, 'message' => 'Product added to the cart!']);
    } else {
        echo json_encode(['error' => 'Failed to remove product from cart.']);
    }
}

$stmt->close();
$conn->close();
?>
