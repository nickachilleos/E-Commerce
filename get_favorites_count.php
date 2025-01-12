<?php
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'customer') {
    echo json_encode(['count' => 0]);
    exit();
}

// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "marketplace";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    echo json_encode(['count' => 0]);
    exit();
}

$customer_id = $_SESSION['user_id'];

// Get the count of favorites
$sql = "SELECT COUNT(*) AS count FROM favorites_table WHERE customer_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $customer_id);
$stmt->execute();
$result = $stmt->get_result()->fetch_assoc();

echo json_encode(['count' => $result['count']]);

$stmt->close();
$conn->close();
?>
