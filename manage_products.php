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

// Add a product
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_product'])) {
    $product_name = $conn->real_escape_string($_POST['product_name']);
    $price = $conn->real_escape_string($_POST['price']);
    $product_type = $conn->real_escape_string($_POST['product_type']);
    $description = $conn->real_escape_string($_POST['description']);
    $user_id = $_SESSION['user_id'];

    // Handle photo upload
    $photo_name = null;
    if (isset($_FILES['photo']) && $_FILES['photo']['error'] === UPLOAD_ERR_OK) {
        $upload_dir = 'uploads/';
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0755, true);
        }
        $photo_name = $upload_dir . basename($_FILES['photo']['name']);
        move_uploaded_file($_FILES['photo']['tmp_name'], $photo_name);
    }

    // Insert product into database
    $sql = "INSERT INTO products_table (seller_id, name, description, price, photo, product_type) VALUES (?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("issdss", $user_id, $product_name, $description, $price, $photo_name, $product_type);

    if ($stmt->execute()) {
        $success_message = "Product added successfully!";
    } else {
        $error_message = "Error adding product: " . $stmt->error;
    }
    $stmt->close();
}

// Modify a product
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['modify_product'])) {
    $product_id = $conn->real_escape_string($_POST['product_id']);
    $product_name = $conn->real_escape_string($_POST['product_name']);
    $price = $conn->real_escape_string($_POST['price']);
    $product_type = $conn->real_escape_string($_POST['product_type']);
    $description = $conn->real_escape_string($_POST['description']);
    $user_id = $_SESSION['user_id'];

    // Handle photo upload if a new photo is provided
    $photo_name = null;
    if (isset($_FILES['photo']) && $_FILES['photo']['error'] === UPLOAD_ERR_OK) {
        $upload_dir = 'uploads/';
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0755, true);
        }
        $photo_name = $upload_dir . basename($_FILES['photo']['name']);
        move_uploaded_file($_FILES['photo']['tmp_name'], $photo_name);
    }

    // Update the product in the database
    if ($photo_name) {
        $sql = "UPDATE products_table SET name = ?, price = ?, product_type = ?, description = ?, photo = ? WHERE product_id = ? AND seller_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sdsssii", $product_name, $price, $product_type, $description, $photo_name, $product_id, $user_id);
    } else {
        $sql = "UPDATE products_table SET name = ?, price = ?, product_type = ?, description = ? WHERE product_id = ? AND seller_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sdssii", $product_name, $price, $product_type, $description, $product_id, $user_id);
    }

    if ($stmt->execute()) {
        $success_message = "Product modified successfully!";
    } else {
        $error_message = "Error modifying product: " . $stmt->error;
    }
    $stmt->close();
}


// Delete a product
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['delete_product'])) {
    $product_id = $conn->real_escape_string($_POST['product_id']);
    $user_id = $_SESSION['user_id'];

    // Delete product from database
    $sql = "DELETE FROM products_table WHERE product_id = ? AND seller_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $product_id, $user_id);

    if ($stmt->execute()) {
        $success_message = "Product deleted successfully!";
    } else {
        $error_message = "Error deleting product: " . $stmt->error;
    }
    $stmt->close();
}

// Fetch products
$user_id = $_SESSION['user_id'];
$sql = "SELECT product_id, name, price, product_type, description, photo, upload_timestamp FROM products_table WHERE seller_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$products = $result->fetch_all(MYSQLI_ASSOC);
$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Products</title>
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

        .spacer {
            flex-grow: 1; /* Pushes elements below it to the bottom */
        }

        .logout {
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
            font-size: 2rem;
            color: #22577a;
        }

        .dashboard-header p {
            color: #38a3a5;
            font-size: 1.1rem;
        }

        .product-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
            gap: 20px;
        }

        .product-card {
            background-color: #fff;
            border-radius: 15px;
            padding: 20px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            text-align: center;
            transition: transform 0.3s;
        }

        .product-card:hover {
            transform: translateY(-5px);
        }

        .product-card img {
            max-width: 100px;
            height: auto;
            margin-bottom: 15px;
        }

        .product-card h5 {
            color: #22577a;
            margin-bottom: 10px;
        }

        .add-card {
            display: flex;
            align-items: center;
            justify-content: center;
            background-color: #38a3a5;
            color: #fff;
            font-size: 3rem;
            cursor: pointer;
            border-radius: 15px;
            transition: all 0.3s ease;
        }

        .add-card:hover {
            background-color: #57cc99;
        }

        .product-card .action-buttons {
    margin-top: 10px;
    display: flex;
    justify-content: center;
}

.product-card .delete-icon {
    font-size: 1.5rem;
    color: #ff4d4d;
    cursor: pointer;
    transition: transform 0.3s, color 0.3s;
}

.product-card .delete-icon:hover {
    color: #e60000;
    transform: scale(1.2);
}

.product-card .modify-icon {
    font-size: 1.5rem;
    color: #4caf50;
    cursor: pointer;
    margin-right: 10px;
    transition: transform 0.3s, color 0.3s;
}

.product-card .modify-icon:hover {
    color: #388e3c;
    transform: scale(1.2);
}

    </style>
</head>

<body>
<div class="sidebar">
        <h3>Seller Dashboard</h3>
        <a href="dashboard.php"><i class="bi bi-house-door"></i> Home</a>
        <a href="manage_products.php" class="active"><i class="bi bi-box-seam"></i> Manage Products</a>
        <a href="orders.php"><i class="bi bi-cart"></i> Orders</a>
        <a href="settings.php"><i class="bi bi-gear"></i> Settings</a>
        <div class="spacer"></div>
        <a href="logout.php"><i class="bi bi-box-arrow-left"></i> Logout</a>
        <div class="footer">&copy; 2024 Marketplace</div>
    </div>
    <div class="content">
        <h2>Manage Products</h2>
        <div class="product-grid">
            <?php foreach ($products as $product): ?>
                <div class="product-card">
                    <img src="<?php echo htmlspecialchars($product['photo']); ?>" alt="Product Image">
                    <h5><?php echo htmlspecialchars($product['name']); ?></h5>
                    <p>$<?php echo number_format($product['price'], 2); ?></p>
                    <div class="action-buttons">
                        <i class="bi bi-pencil-square modify-icon" data-bs-toggle="modal" data-bs-target="#modifyProductModal" data-product-id="<?php echo $product['product_id']; ?>" data-name="<?php echo htmlspecialchars($product['name']); ?>" data-price="<?php echo $product['price']; ?>" data-type="<?php echo htmlspecialchars($product['product_type']); ?>" data-description="<?php echo htmlspecialchars($product['description']); ?>"></i>
                        <i class="bi bi-trash delete-icon" data-bs-toggle="modal" data-bs-target="#deleteConfirmModal" data-product-id="<?php echo $product['product_id']; ?>"></i>
                    </div>
                </div>
            <?php endforeach; ?>
            <div class="product-card add-card" data-bs-toggle="modal" data-bs-target="#addProductModal">+</div>
        </div>
    </div>


<!-- Modify Product Modal -->
<div class="modal fade" id="modifyProductModal" tabindex="-1" aria-labelledby="modifyProductModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modifyProductModalLabel">Modify Product</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form method="POST" enctype="multipart/form-data">
                    <input type="hidden" name="product_id" id="modify-product-id">
                    <div class="mb-3">
                        <label for="modify-product-name" class="form-label">Product Name</label>
                        <input type="text" name="product_name" id="modify-product-name" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label for="modify-price" class="form-label">Price</label>
                        <input type="number" step="0.01" name="price" id="modify-price" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label for="modify-product-type" class="form-label">Product Type</label>
                        <select name="product_type" id="modify-product-type" class="form-select" required>
                            <option value="software">Software</option>
                            <option value="phones">Phones</option>
                            <option value="laptops">Laptops</option>
                            <option value="accessories">Accessories</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="modify-description" class="form-label">Description</label>
                        <textarea name="description" id="modify-description" class="form-control" rows="3"></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="modify-photo" class="form-label">Change Product Photo (optional)</label>
                        <input type="file" name="photo" id="modify-photo" class="form-control">
                    </div>
                    <div class="text-center mt-4">
                        <button type="submit" name="modify_product" class="btn btn-primary">Save Changes</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>



<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteConfirmModal" tabindex="-1" aria-labelledby="deleteConfirmModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteConfirmModalLabel">Confirm Deletion</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                Are you sure you want to delete this product?
            </div>
            <div class="modal-footer">
                <form method="POST">
                    <input type="hidden" name="product_id" id="delete-product-id">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" name="delete_product" class="btn btn-danger">Delete</button>
                </form>
            </div>
        </div>
    </div>
</div>


    <!-- Modal -->
    <div class="modal fade" id="addProductModal" tabindex="-1" aria-labelledby="addProductModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addProductModalLabel">Add New Product</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form method="POST" enctype="multipart/form-data">
                        <div class="mb-3">
                            <label for="product_name" class="form-label">Product Name</label>
                            <input type="text" name="product_name" id="product_name" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label for="price" class="form-label">Price</label>
                            <input type="number" step="0.01" name="price" id="price" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label for="product_type" class="form-label">Product Type</label>
                            <select name="product_type" id="product_type" class="form-select" required>
                                <option value="" disabled selected>Select Product Type</option>
                                <option value="software">Software</option>
                                <option value="phones">Phones</option>
                                <option value="laptops">Laptops</option>
                                <option value="accessories">Accessories</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="description" class="form-label">Description</label>
                            <textarea name="description" id="description" class="form-control" rows="3"></textarea>
                        </div>
                        <div class="mb-3">
                            <label for="photo" class="form-label">Product Photo</label>
                            <input type="file" name="photo" id="photo" class="form-control">
                        </div>
                        <div class="text-center mt-4">
                            <button type="submit" name="add_product" class="btn btn-primary">Add Product</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <script>
    const deleteIcons = document.querySelectorAll('.delete-icon');
    const deleteProductIdInput = document.getElementById('delete-product-id');

    deleteIcons.forEach(icon => {
        icon.addEventListener('click', () => {
            const productId = icon.getAttribute('data-product-id');
            deleteProductIdInput.value = productId;
        });
    });
</script>

<script>
    const modifyIcons = document.querySelectorAll('.modify-icon');
    const modifyProductIdInput = document.getElementById('modify-product-id');
    const modifyProductNameInput = document.getElementById('modify-product-name');
    const modifyPriceInput = document.getElementById('modify-price');
    const modifyProductTypeInput = document.getElementById('modify-product-type');
    const modifyDescriptionInput = document.getElementById('modify-description');

    modifyIcons.forEach(icon => {
        icon.addEventListener('click', () => {
            const productId = icon.getAttribute('data-product-id');
            const name = icon.getAttribute('data-name');
            const price = icon.getAttribute('data-price');
            const type = icon.getAttribute('data-type');
            const description = icon.getAttribute('data-description');

            modifyProductIdInput.value = productId;
            modifyProductNameInput.value = name;
            modifyPriceInput.value = price;
            modifyProductTypeInput.value = type;
            modifyDescriptionInput.value = description;
        });
    });
</script>

</body>

</html>