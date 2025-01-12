<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_POST['selected_products']) || empty($_POST['selected_products'])) {
        die("Error: No products selected. Please select at least one product.");
    }

    $name = filter_input(INPUT_POST, 'name', FILTER_SANITIZE_STRING);
    $address = filter_input(INPUT_POST, 'address', FILTER_SANITIZE_STRING);
    $card_details = filter_input(INPUT_POST, 'card_details', FILTER_SANITIZE_STRING);
    $selected_products = explode(',', $_POST['selected_products']); // Convert the hidden input back to an array

    if (empty($name) || empty($address) || empty($card_details)) {
        die("Error: All fields are required.");
    }

    // Database connection
    $servername = "localhost";
    $username = "root";
    $password = "";
    $dbname = "marketplace";

    $conn = new mysqli($servername, $username, $password, $dbname);
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Insert order details
    $order_query = $conn->prepare("
        INSERT INTO orders_table (customer_id, customer_name, address, card_details, product_id, quantity, status)
        VALUES (?, ?, ?, ?, ?, ?, ?)
    ");

    if (!$order_query) {
        die("Error preparing statement: " . $conn->error);
    }

    $customer_id = $_SESSION['user_id'];
    $status = 'pending';

    // Insert each product into the orders_table
    foreach ($selected_products as $product_id) {
        $quantity = 1; // Default quantity for now (can be adjusted based on cart data)
        $order_query->bind_param("issssis", $customer_id, $name, $address, $card_details, $product_id, $quantity, $status);

        if (!$order_query->execute()) {
            die("Error placing order: " . $conn->error);
        }
    }

    $order_query->close();
    $conn->close();

    header("Location: order_success.php");
    exit();
} else {
    header("Location: cart.php");
    exit();
}
?>
