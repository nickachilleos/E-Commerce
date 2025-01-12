<?php
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'seller') {
    header("Location: login.php");
    exit();
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

$seller_id = $_SESSION['user_id']; // Current seller ID

// Fetch orders for the current seller
$sql = "
    SELECT o.order_id, o.quantity, o.order_date, 
           p.name AS product_name, p.price, 
           u.username AS customer_name, u.email AS customer_email
    FROM orders_table o
    INNER JOIN products_table p ON o.product_id = p.product_id
    INNER JOIN users_table u ON o.customer_id = u.id
    WHERE p.seller_id = ?
    ORDER BY o.order_date DESC";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $seller_id);
$stmt->execute();
$result = $stmt->get_result();
$orders = $result->fetch_all(MYSQLI_ASSOC);

$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Orders</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.10.5/font/bootstrap-icons.min.css">
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            margin: 0;
            padding: 0;
            display: flex;
            background-color: #c7f9cc;
            height: 100vh;
        }

        .sidebar {
            width: 230px;
            background-color: #22577a;
            color: #fff;
            display: flex;
            flex-direction: column;
            padding: 20px;
            position: relative;
            border-radius: 0 30px 30px 0;
            height: 100%;
            box-shadow: 2px 0 5px rgba(0, 0, 0, 0.1);
        }

        .sidebar h3 {
            margin-bottom: 20px;
            font-size: 1.5rem;
            color: #80ed99;
            text-align: center;
        }

        .sidebar a {
            text-decoration: none;
            color: #c7f9cc;
            padding: 10px;
            border-radius: 10px;
            margin-bottom: 10px;
            display: flex;
            align-items: center;
            position: relative;
            transition: all 0.3s ease;
        }

        .sidebar a i {
            margin-right: 10px;
            font-size: 1.2rem;
        }

        .sidebar a .badge {
            background-color: #80ed99; /* Soft green tone */
            color: #22577a; /* Dark text for contrast */
            font-size: 0.8rem;
            position: absolute;
            top: -5px;
            right: 10px;
            border-radius: 50%;
            padding: 6px 10px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.15); /* Subtle shadow */
            font-weight: bold;
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }

        .sidebar a:hover .badge {
            transform: scale(1.1); /* Slightly enlarge on hover */
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2); /* Stronger shadow on hover */
        }

        .sidebar a:hover {
            background-color: #80ed99;
            color: #22577a;
        }

        .sidebar a.active {
            background-color: #80ed99;
            color: #22577a;
        }

        .logout {
            margin-top: auto; /* Pushes logout to the bottom */
            text-decoration: none;
            color: #c7f9cc;
            padding: 10px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            position: relative;
            transition: all 0.3s ease;
        }

        .logout i {
            margin-right: 10px;
            font-size: 1.2rem;
        }

        .logout:hover {
            background-color: #80ed99;
            color: #22577a;
        }

        .content {
            flex: 1;
            padding: 20px;
            background-color: #c7f9cc;
            overflow-y: auto;
        }

        .dashboard-header {
            text-align: center;
            margin-bottom: 40px;
        }

        .dashboard-header h2 {
            font-size: 2.2rem;
            color: #22577a;
            margin-bottom: 10px;
        }

        .dashboard-header p {
            color: #38a3a5;
            font-size: 1.2rem;
        }

        /* Orders Table Styles */
        .orders-table {
            background-color: #fff;
            border-radius: 15px;
            padding: 20px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        }

        .orders-table table {
            width: 100%;
            border-collapse: collapse;
        }

        .orders-table th, .orders-table td {
            padding: 15px;
            text-align: left;
            border-bottom: 1px solid #ddd;
            font-size: 1rem;
        }

        .orders-table th {
            background-color: #22577a;
            color: #fff;
            font-weight: 600;
        }

        .orders-table tr:hover {
            background-color: #f0fcf8;
        }

        .orders-table td {
            vertical-align: middle;
        }

        .orders-table .price, .orders-table .total {
            color: #38a3a5;
            font-weight: bold;
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .sidebar {
                width: 70px;
                padding: 10px;
            }

            .sidebar h3 {
                font-size: 1.2rem;
            }

            .sidebar a {
                justify-content: center;
                padding: 10px;
            }

            .sidebar a span {
                display: none;
            }

            .content {
                margin-left: 80px;
                padding: 15px;
            }

            .orders-table th, .orders-table td {
                padding: 10px;
                font-size: 0.9rem;
            }
        }
    </style>
</head>

<body>
    <div class="sidebar">
        <h3>Seller Dashboard</h3>
        <a href="dashboard.php"><i class="bi bi-house-door"></i> Home</a>
        <a href="manage_products.php"><i class="bi bi-box-seam"></i> Manage Products</a>
        <a href="orders.php" class="active"><i class="bi bi-cart"></i> Orders</a>
        <a href="settings.php"><i class="bi bi-gear"></i> Settings</a>
        <a href="logout.php" class="logout"><i class="bi bi-box-arrow-left"></i> Logout</a>
        <div class="footer">&copy; 2024 Marketplace</div>
    </div>

    <div class="content">
        <div class="dashboard-header">
            <h2>Manage Orders</h2>
            <p>View and manage your orders below.</p>
        </div>

        <div class="orders-table">
            <?php if (empty($orders)): ?>
                <p class="text-center text-muted">No orders found for your products.</p>
            <?php else: ?>
                <table class="table">
                    <thead>
                        <tr>
                            <th>Order ID</th>
                            <th>Product</th>
                            <th>Customer</th>
                            <th>Quantity</th>
                            <th>Price</th>
                            <th>Total</th>
                            <th>Order Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($orders as $order): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($order['order_id']); ?></td>
                                <td><?php echo htmlspecialchars($order['product_name']); ?></td>
                                <td>
                                    <?php echo htmlspecialchars($order['customer_name']); ?><br>
                                    <small class="text-muted"><?php echo htmlspecialchars($order['customer_email']); ?></small>
                                </td>
                                <td><?php echo htmlspecialchars($order['quantity']); ?></td>
                                <td class="price">$<?php echo number_format($order['price'], 2); ?></td>
                                <td class="total">$<?php echo number_format($order['price'] * $order['quantity'], 2); ?></td>
                                <td><?php echo htmlspecialchars(date('Y-m-d', strtotime($order['order_date']))); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </div>
    </div>
</body>

</html>

