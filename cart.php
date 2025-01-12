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
$customer_name = htmlspecialchars($_SESSION['username']); // Escape for safety

// Fetch cart items
$sql = "
    SELECT c.product_id, c.quantity, p.name, p.description, p.photo, p.price 
    FROM cart_table c
    INNER JOIN products_table p ON c.product_id = p.product_id
    WHERE c.customer_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $customer_id);
$stmt->execute();
$result = $stmt->get_result();
$cart_items = $result->fetch_all(MYSQLI_ASSOC);

$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cart</title>
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
            background-color: #80ed99;
            color: #22577a;
            font-size: 0.8rem;
            position: absolute;
            top: -5px;
            right: 10px;
            border-radius: 50%;
            padding: 6px 10px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.15);
            font-weight: bold;
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }

        .sidebar a:hover .badge {
            transform: scale(1.1);
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
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
            margin-top: auto;
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

        .product-card .actions {
            display: flex;
            justify-content: space-between;
            gap: 5px;
        }

        .product-card .actions button {
            background-color: #57cc99;
            border: none;
            padding: 5px 10px;
            color: #fff;
            border-radius: 10px;
            font-size: 0.9rem;
            cursor: pointer;
            transition: all 0.3s ease;
            flex-grow: 1;
        }

        .product-card .actions button:hover {
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

        .quantity-controls {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
        }
        .quantity-controls button {
            background-color: #57cc99;
            border: none;
            height: 30px; /* Adjust the size as needed */
            width: 30px; /* Equal width and height for a circular shape */
            color: #fff;
            border-radius: 50%; /* Fully circular shape */
            font-size: 1.2rem;
            cursor: pointer;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            justify-content: center;

        }


        .quantity-controls button:hover {
            background-color: #22577a;
        }

        .quantity-controls span {
            font-size: 1rem;
            font-weight: bold;
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
            flex-grow: 1;
        }

        .actions button:hover {
            background-color: #22577a;
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
            flex-grow: 1;
        }

        .actions button:hover {
            background-color: #22577a;
        }


        #select-all-container {
    display: flex;
    align-items: center;
    margin-bottom: 20px;
    font-size: 1.2rem;
    color: #22577a; /* Dark text color */
    font-weight: bold;
}

#select-all {
    appearance: none; /* Remove default checkbox styling */
    width: 25px;
    height: 25px;
    border: 2px solid #22577a; /* Border color */
    border-radius: 5px; /* Slightly rounded corners */
    background-color: #c7f9cc; /* Background matching the page */
    cursor: pointer;
    transition: all 0.3s ease;
    margin-right: 10px; /* Space between checkbox and label */
}

#select-all:checked {
    background-color: #80ed99; /* Checked background color */
    border-color: #22577a;
    box-shadow: 0 0 5px rgba(0, 0, 0, 0.3); /* Slight shadow for emphasis */
}

#select-all:hover {
    transform: scale(1.1); /* Slightly enlarge on hover */
}

.product-checkbox {
    appearance: none; /* Remove default styling */
    width: 20px;
    height: 20px;
    background-color: #c7f9cc; /* Background color */
    border: 2px solid #22577a; /* Border color */
    border-radius: 5px; /* Slightly rounded corners */
    cursor: pointer;
    transition: all 0.3s ease;
    margin-bottom: 10px; /* Space between checkbox and other elements */
}

.product-checkbox:checked {
    background-color: #80ed99; /* Green background when checked */
    border-color: #22577a; /* Keep border consistent */
    box-shadow: 0 0 5px rgba(0, 0, 0, 0.2); /* Add subtle shadow */
}

.product-checkbox:hover {
    transform: scale(1.1); /* Slightly enlarge on hover */
}

</style>
</head>

<body>
    <div class="sidebar">
        <h3>Customer Dashboard</h3>
        <a href="customer_dashboard.php"><i class="bi bi-house-door"></i> Home</a>
        <a href="favorites.php"><i class="bi bi-heart"></i> Favorites <span class="badge favorites-count">0</span></a>
        <a href="cart.php" class="active"><i class="bi bi-cart"></i> Cart <span class="badge cart-count">0</span></a>
        <a href="logout.php" class="logout"><i class="bi bi-box-arrow-left"></i> Logout</a>
    </div>

    <div class="content">
        <div class="dashboard-header">
            <h2>Your Cart</h2>
            <p>View and manage your cart items.</p>
        </div>

        <form id="cart-form" action="checkout.php" method="POST">
            <div id="select-all-container">
                <input type="checkbox" id="select-all">
                <label for="select-all">Select All</label>
            </div>
            <div class="product-grid">
                <?php if (empty($cart_items)): ?>
                    <p class="text-center text-muted">Your cart is empty.</p>
                <?php else: ?>
                    <?php foreach ($cart_items as $item): ?>
                        <div class="product-card">
                            <input type="checkbox" name="selected_products[]" value="<?php echo $item['product_id']; ?>" class="product-checkbox">
                            <img src="<?php echo htmlspecialchars($item['photo']); ?>" alt="Product Image">
                            <h5><?php echo htmlspecialchars($item['name']); ?></h5>
                            <p>Price: €<span id="price-<?php echo $item['product_id']; ?>"><?php echo number_format($item['price'] * $item['quantity'], 2); ?></span></p>
                            <div class="quantity-controls">
                                <button type="button" class="decrement-btn" data-product-id="<?php echo $item['product_id']; ?>">-</button>
                                <span id="quantity-<?php echo $item['product_id']; ?>"><?php echo $item['quantity']; ?></span>
                                <button type="button" class="increment-btn" data-product-id="<?php echo $item['product_id']; ?>">+</button>
                            </div>
                            <div class="actions">
                                <button type="button" class="remove-cart-btn" data-product-id="<?php echo $item['product_id']; ?>">Remove</button>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
            <button type="submit" class="btn btn-primary mt-3">Order Selected</button>
        </form>
    </div>


    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        $(document).ready(function () {
            $('#select-all').on('change', function () {
                $('.product-checkbox').prop('checked', $(this).prop('checked'));
            });

            $('.product-grid').on('change', '.product-checkbox', function () {
                const allChecked = $('.product-checkbox').length === $('.product-checkbox:checked').length;
                $('#select-all').prop('checked', allChecked);
            });
        });
        $(document).ready(function () {
            function refreshCartCount() {
                $.get('get_cart_count.php', function (response) {
                    const data = JSON.parse(response);
                    $('.cart-count').text(data.count || 0);
                });
            }

            function refreshFavoritesCount() {
                $.get('get_favorites_count.php', function (response) {
                    const data = JSON.parse(response);
                    $('.favorites-count').text(data.count || 0);
                });
            }

            refreshCartCount();
            refreshFavoritesCount();
        });
    
      function updateQuantity(productId, action) {
    const quantitySpan = $(`#quantity-${productId}`);
    const priceSpan = $(`#price-${productId}`);

    $.ajax({
        url: 'cart_handler.php',
        type: 'POST',
        data: { product_id: productId, action: action },
        success: function (response) {
            const data = JSON.parse(response);
            if (data.success) {
                quantitySpan.text(data.quantity); // Update the quantity
                priceSpan.text(`€${data.total_price.toFixed(2)}`); // Update the price
            } else {
                alert(data.error);
            }
        },
        error: function () {
            alert('Error updating quantity. Please try again.');
        }
    });
   
}       
$(document).on('click', '.increment-btn', function () {
            const productId = $(this).data('product-id');
            updateQuantity(productId, 'increment');
        });

        $(document).on('click', '.decrement-btn', function () {
            const productId = $(this).data('product-id');
            updateQuantity(productId, 'decrement');
        });
         // Handle item removal
$(document).on('click', '.remove-cart-btn', function () {
    const productId = $(this).data('product-id');
    $.ajax({
        url: 'cart_handler.php',
        type: 'POST',
        data: { product_id: productId, action: 'remove' },
        success: function (response) {
            console.log('Response from server:', response); // Log the server response
            try {
                const data = JSON.parse(response); // Attempt to parse JSON
                if (data.success) {
                    $(`#product-${productId}`).fadeOut(300, function () {
                        $(this).remove(); // Remove the product card after fade-out
                    });

                    // Optional: Update cart count dynamically
                    refreshCartCount();
                } else {
                    alert(data.error || 'Failed to remove item.');
                }
            } catch (error) {
                console.error('Error parsing response:', response);
                alert('The Product has been removed succesfully!');
            }
        },
        error: function (xhr, status, error) {
            console.error('AJAX Error:', status, error);
            alert('Error removing item. Please try again.');
        }
    });
});

    </script>

</body>

</html>