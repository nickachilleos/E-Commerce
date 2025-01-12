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

$seller_id = $_SESSION['user_id'];
$seller_name = $_SESSION['username'];

// Fetch seller's current settings if needed (mock data for example)
$email_notifications = true;
$push_notifications = false;
$theme = 'light'; // Light theme by default

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Settings</title>
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

        .logout:hover {
            background-color: #80ed99;
            color: #22577a;
        }

        .footer {
            font-size: 0.9rem;
            text-align: center;
            color: #c7f9cc;
        }

        .content {
            flex: 1;
            padding: 20px;
            background-color: #c7f9cc;
            overflow-y: auto;
        }

        .settings-section {
            background-color: #fff;
            border-radius: 15px;
            padding: 20px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            margin-bottom: 20px;
        }

        .settings-section h3 {
            color: #22577a;
            margin-bottom: 15px;
        }

        .form-check-input:checked {
            background-color: #57cc99;
            border-color: #57cc99;
        }

        .btn-save {
            background-color: #57cc99;
            border: none;
            padding: 10px 20px;
            border-radius: 8px;
            color: #fff;
            transition: all 0.3s ease;
        }

        .btn-save:hover {
            background-color: #22577a;
        }
    </style>
</head>

<body>
    <div class="sidebar">
        <h3>Seller Dashboard</h3>
        <a href="dashboard.php"><i class="bi bi-house-door"></i> Home</a>
        <a href="manage_products.php"><i class="bi bi-box-seam"></i> Manage Products</a>
        <a href="orders.php"><i class="bi bi-cart"></i> Orders</a>
        <a href="settings.php" class="active"><i class="bi bi-gear"></i> Settings</a>
        <a href="logout.php" class="logout"><i class="bi bi-box-arrow-left"></i> Logout</a>
        <div class="footer">&copy; 2024 Marketplace</div>
    </div>

    <div class="content">
        <h2>Settings</h2>
        <p>Manage your account settings and preferences below.</p>

        <!-- Profile Settings -->
        <div class="settings-section">
            <h3>Profile Settings</h3>
            <form>
                <div class="mb-3">
                    <label for="name" class="form-label">Username</label>
                    <input type="text" id="name" class="form-control" value="<?= htmlspecialchars($seller_name); ?>">
                </div>
                <div class="mb-3">
                    <label for="email" class="form-label">Email</label>
                    <input type="email" id="email" class="form-control" value="seller@example.com">
                </div>
                <div class="mb-3">
                    <label for="password" class="form-label">Password</label>
                    <input type="password" id="password" class="form-control" placeholder="Change your password">
                </div>
                <button type="button" class="btn-save">Save Changes</button>
            </form>
        </div>

        <!-- Notification Settings -->
        <div class="settings-section">
            <h3>Notification Settings</h3>
            <div class="form-check form-switch">
                <input class="form-check-input" type="checkbox" id="emailNotifications" <?= $email_notifications ? 'checked' : ''; ?>>
                <label class="form-check-label" for="emailNotifications">Email Notifications</label>
            </div>
            <div class="form-check form-switch">
                <input class="form-check-input" type="checkbox" id="pushNotifications" <?= $push_notifications ? 'checked' : ''; ?>>
                <label class="form-check-label" for="pushNotifications">Push Notifications</label>
            </div>
        </div>

        <!-- Privacy Settings -->
        <div class="settings-section">
            <h3>Privacy Settings</h3>
            <button type="button" class="btn btn-danger">Delete Account</button>
        </div>
    </div>
</body>

</html>
