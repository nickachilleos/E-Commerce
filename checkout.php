<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_POST['selected_products']) || empty($_POST['selected_products'])) {
        die("Error: No products selected. Please select at least one product.");
    }

    $selected_products = $_POST['selected_products']; // Array of selected product IDs

    // Database connection
    $servername = "localhost";
    $username = "root";
    $password = "";
    $dbname = "marketplace";

    $conn = new mysqli($servername, $username, $password, $dbname);
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Fetch product details from the database
    $placeholders = implode(',', array_fill(0, count($selected_products), '?')); // Create placeholders for the IN clause
    $sql = "SELECT product_id, name, description, photo FROM products_table WHERE product_id IN ($placeholders)";
    $stmt = $conn->prepare($sql);

    if ($stmt === false) {
        die("Error preparing query: " . $conn->error);
    }

    $stmt->bind_param(str_repeat('i', count($selected_products)), ...$selected_products);
    $stmt->execute();
    $result = $stmt->get_result();

    $selected_product_details = [];
    while ($row = $result->fetch_assoc()) {
        $selected_product_details[] = $row;
    }

    $stmt->close();
    $conn->close();
} else {
    header("Location: cart.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            background-color: #c7f9cc;
            height: 100vh;
        }

        .checkout-container {
            width: 100%;
            max-width: 700px;
            background-color: #fff;
            border-radius: 15px;
            padding: 20px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        }

        .checkout-header {
            text-align: center;
            margin-bottom: 20px;
        }

        .checkout-header h1 {
            font-size: 2rem;
            color: #22577a;
        }

        .checkout-header p {
            color: #38a3a5;
            font-size: 1rem;
        }

        .order-summary {
            background-color: #f8f9fa;
            padding: 15px;
            border-radius: 10px;
            margin-bottom: 20px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }

        .order-summary h5 {
            font-size: 1.2rem;
            color: #22577a;
            margin-bottom: 10px;
        }

        .product-item {
            display: flex;
            align-items: center;
            margin-bottom: 10px;
        }

        .product-item img {
            width: 50px;
            height: 50px;
            border-radius: 10px;
            margin-right: 15px;
            object-fit: cover;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }

        .product-item-details {
            flex-grow: 1;
        }

        .product-item-details h6 {
            margin: 0;
            font-size: 1rem;
            color: #22577a;
        }

        .product-item-details p {
            margin: 0;
            font-size: 0.9rem;
            color: #38a3a5;
        }

        .form-label {
            font-weight: bold;
            color: #22577a;
        }

        .form-control {
            border: 1px solid #ced4da;
            border-radius: 10px;
            padding: 10px;
        }

        .form-control:focus {
            border-color: #38a3a5;
            box-shadow: 0 0 5px rgba(56, 163, 165, 0.5);
        }

        .btn-submit {
            background-color: #38a3a5;
            color: #fff;
            font-size: 1.1rem;
            border: none;
            border-radius: 10px;
            padding: 10px 20px;
            transition: background-color 0.3s ease, transform 0.3s ease;
            width: 100%;
        }

        .btn-submit:hover {
            background-color: #57cc99;
            transform: translateY(-2px);
        }
    </style>
</head>

<body>
    <div class="checkout-container">
        <div class="checkout-header">
            <h1>Checkout</h1>
            <p>Complete your order by filling in the details below.</p>
        </div>

        <div class="order-summary">
            <h5>Order Summary</h5>
            <?php foreach ($selected_product_details as $product): ?>
                <div class="product-item">
                    <img src="<?php echo htmlspecialchars($product['photo']); ?>" alt="Product Image">
                    <div class="product-item-details">
                        <h6><?php echo htmlspecialchars($product['name']); ?></h6>
                        <p><?php echo htmlspecialchars($product['description']); ?></p>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <form action="process_order.php" method="POST">
            <input type="hidden" name="selected_products" value="<?php echo implode(',', $selected_products); ?>">

            <div class="mb-3">
                <label for="name" class="form-label">Full Name</label>
                <input type="text" class="form-control" id="name" name="name" placeholder="John Doe" required>
            </div>

            <div class="mb-3">
                <label for="address" class="form-label">Delivery Address</label>
                <textarea class="form-control" id="address" name="address" placeholder="123 Street, City, Country" rows="3" required></textarea>
            </div>

            <div class="mb-3">
                <label for="card-details" class="form-label">Card Details</label>
                <input type="text" class="form-control" id="card-details" name="card_details" placeholder="XXXX-XXXX-XXXX-XXXX" maxlength="19" required>
            </div>

            <button type="submit" class="btn-submit">Place Order</button>
        </form>
    </div>
</body>

</html>
