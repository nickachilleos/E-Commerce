<?php
session_start();

// Display a thank-you message after logout
if (isset($_GET['message'])) {
    $message = htmlspecialchars($_GET['message']);
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

// Handle login submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $conn->real_escape_string($_POST['email']);
    $password = $_POST['password'];

    // Validate inputs
    if (empty($email) || empty($password)) {
        $error_message = "Email and password are required.";
    } else {
        // Check user credentials
        $query = "SELECT * FROM users_table WHERE email = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $user = $result->fetch_assoc();

            if (password_verify($password, $user['password'])) {
                // Successful login
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $user['username'];
                $_SESSION['role'] = $user['role'];

                // Redirect based on user role
                if ($user['role'] === 'customer') {
                    header("Location: customer_dashboard.php"); // Redirect to customer dashboard
                } elseif ($user['role'] === 'seller') {
                    header("Location: dashboard.php"); // Redirect to seller dashboard
                } else {
                    $error_message = "Invalid user role.";
                }
                exit();
            } else {
                $error_message = "Invalid password.";
            }
        } else {
            $error_message = "User not found.";
        }
        $stmt->close();
    }
}
$conn->close();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.10.5/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="css/styles.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4fdfb;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }
        .login-card {
            width: 400px;
            background-color: #ffffff;
            border-radius: 15px;
            padding: 30px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        }
        .login-header {
            text-align: center;
            margin-bottom: 20px;
        }
        .login-header img {
            width: 80px;
            margin-bottom: 15px;
        }
        .login-header h2 {
            color: #22577a;
        }
        .input-container {
            position: relative;
        }
        .input-icon {
            position: absolute;
            top: 50%;
            left: 15px;
            transform: translateY(-50%);
            color: #57cc99;
        }
        .form-control {
            padding-left: 40px;
        }
        .btn-primary {
            background-color: #57cc99;
            border: none;
            border-radius: 10px;
        }
        .btn-primary:hover {
            background-color: #22577a;
        }
    </style>
</head>

<body>
    <div class="login-card">
        <div class="login-header">
            <img src="https://cdn-icons-png.flaticon.com/512/3177/3177440.png" alt="User Icon">
            <h2>Login</h2>
        </div>
        <form method="POST" action="login.php">
            <!-- Display Error Messages -->
            <?php if (isset($error_message)): ?>
                <div class="alert alert-danger"><?php echo $error_message; ?></div>
            <?php endif; ?>

            <!-- Email Field -->
            <div class="input-container mb-3">
                <i class="input-icon bi bi-envelope-fill"></i>
                <input type="email" class="form-control" id="email" name="email" placeholder="Enter Your Email..." required>
            </div>

            <!-- Password Field -->
            <div class="input-container mb-3">
                <i class="input-icon bi bi-lock-fill"></i>
                <input type="password" class="form-control" id="password" name="password" placeholder="Enter Your Password..." required>
            </div>

            <!-- Submit Button -->
            <button type="submit" class="btn btn-primary w-100">Login</button>

            <!-- Additional Links -->
            <p class="text-center mt-3">
                <a href="forgot_password.php" style="color: #22577a; text-decoration: none;">Forgot Password?</a>
            </p>
            <p class="text-center mt-2">
                Don't have an account? <a href="register.php" style="color: #22577a;">Register</a>
            </p>
        </form>
    </div>
</body>

</html>
