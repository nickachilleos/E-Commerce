<?php
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'customer') {
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

// Fetch customer data
$customer_id = $_SESSION['user_id'];
$customer_name = $_SESSION['username'];

// Fetch products
$sql = "SELECT * FROM products_table";
$result = $conn->query($sql);
$products = $result->fetch_all(MYSQLI_ASSOC);

// Close the connection
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Customer Dashboard</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.10.5/font/bootstrap-icons.min.css">
    <style>
        body {
            font-family: Arial, sans-serif;
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
            margin-bottom: 20px;
        }

        .dashboard-header h2 {
            font-size: 2rem;
            color: #22577a;
        }

        .dashboard-header p {
            color: #38a3a5;
        }

        .product-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
            gap: 20px;
        }

        .product-card {
            background-color: #fff;
            border-radius: 15px;
            padding: 15px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.15);
            text-align: center;
            display: flex;
            flex-direction: column;
            height: 320px;
            justify-content: space-between;
            transition: transform 0.3s, box-shadow 0.3s;
        }

        .product-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 6px 15px rgba(0, 0, 0, 0.2);
        }

        .product-card img {
            width: 100%;
            height: 150px;
            object-fit: contain;
            border-radius: 10px;
        }

        .product-card h5 {
            color: #22577a;
            margin-bottom: 5px;
            font-size: 1rem;
            font-weight: bold;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .product-card p {
            color: #38a3a5;
            font-size: 0.9rem;
            margin-bottom: 10px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .actions button {
            background-color: #57cc99;
            border: none;
            padding: 5px 10px;
            color: #fff;
            border-radius: 10px;
            font-size: 0.9rem;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .actions button:hover {
            background-color: #22577a;
        }

        .alert {
            background-color: #80ed99;
            color: #22577a;
            padding: 10px;
            margin: 15px auto;
            width: 80%;
            border-radius: 5px;
            font-size: 1rem;
            font-weight: bold;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.2);
        }
    </style>
</head>

<body>
    <div class="sidebar">
        <h3>Customer Dashboard</h3>
        <a href="customer_dashboard.php" class="active"><i class="bi bi-house-door"></i> Home</a>
        <a href="favorites.php"><i class="bi bi-heart"></i> Favorites <span class="badge favorites-count">0</span></a>
        <a href="cart.php"><i class="bi bi-cart"></i> Cart <span class="badge cart-count">0</span></a>
        <a href="logout.php" class="logout"><i class="bi bi-box-arrow-left"></i> Logout</a>
    </div>

    <div class="content">
        <div class="dashboard-header">
            <h2>Welcome, <?php echo htmlspecialchars($customer_name); ?>!</h2>
            <p>Explore and shop the best products from various sellers.</p>
        </div>

        <div class="product-grid">
    <?php foreach ($products as $product): ?>
        <div class="product-card" id="product-<?php echo $product['product_id']; ?>">
            <img src="<?php echo htmlspecialchars($product['photo']); ?>" alt="Product Image">
            <h5><?php echo htmlspecialchars($product['name']); ?></h5>
            <p><?php echo htmlspecialchars($product['description']); ?></p>
            <p>Price: €<?php echo number_format($product['price'], 2); ?></p> <!-- Display price -->
            <div class="actions">
                <button onclick="addToFavorites(<?php echo $product['product_id']; ?>)">❤ Favorite</button>
                <button onclick="addToCart(<?php echo $product['product_id']; ?>)">Add to Cart</button>
            </div>
        </div>
    <?php endforeach; ?>
</div>


    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        function addToFavorites(productId) {
            $.post('favorites_handler.php', { product_id: productId, action: 'add' }, function (response) {
                const data = JSON.parse(response);
                if (data.message) {
                    alert(data.message);
                    refreshFavoritesCount();
                } else {
                    alert(data.error || 'Something went wrong!');
                }
            });
        }

        function addToCart(productId) {
    $.post('cart_handler.php', { product_id: productId, action: 'add' }, function (response) {
        const data = JSON.parse(response);
        if (data.success) {
            alert(data.message); // Success message
            refreshCartCount(); // Update cart count
        } else {
            alert(data.error || 'Something went wrong!'); // Error message
        }
    }).fail(function () {
        alert('Failed to communicate with the server. Please try again.');
    });
}


        function refreshFavoritesCount() {
            $.get('get_favorites_count.php', function (response) {
                const data = JSON.parse(response);
                $('.favorites-count').text(data.count || 0);
            });
        }

        function refreshCartCount() {
            $.get('get_cart_count.php', function (response) {
                const data = JSON.parse(response);
                $('.cart-count').text(data.count || 0);
            });
        }

        $(document).ready(function () {
            refreshFavoritesCount();
            refreshCartCount();
        });
    </script>
</body>

</html>
