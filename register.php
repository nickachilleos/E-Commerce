<?php
// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "marketplace";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Create the database structure for users
$sql = "CREATE TABLE IF NOT EXISTS users_table (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role ENUM('seller', 'customer') NOT NULL,
    registration_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)";
$conn->query($sql);
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>
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

        .register-card {
            width: 400px;
            background-color: #ffffff;
            border-radius: 15px;
            padding: 30px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            animation: fadeIn 0.8s ease-in-out;
        }

        .register-header {
            text-align: center;
            margin-bottom: 20px;
        }

        .register-header img {
            width: 80px;
            margin-bottom: 15px;
        }

        .register-header h2 {
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

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: scale(0.95);
            }
            to {
                opacity: 1;
                transform: scale(1);
            }
        }
    </style>
</head>

<body>
    <div class="register-card">
        <div class="register-header">
            <img src="https://cdn-icons-png.flaticon.com/512/3177/3177440.png" alt="User Icon">
            <h2>Register</h2>
        </div>
        <form method="POST" action="register.php">
            <!-- Username Field -->
            <div class="input-container mb-3">
                <i class="input-icon bi bi-person-fill"></i>
                <input type="text" class="form-control" id="username" name="username" placeholder="Enter Your Username..." required>
            </div>

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

            <!-- Role Selection -->
            <div class="input-container mb-3">
                <i class="input-icon bi bi-briefcase-fill"></i>
                <select class="form-control" id="role" name="role" required>
                    <option value="" disabled selected>Select Your Role...</option>
                    <option value="seller">Seller</option>
                    <option value="customer">Customer</option>
                </select>
            </div>

            <!-- Submit Button -->
            <button type="submit" class="btn btn-primary w-100">Register</button>
            <p class="text-center mt-3">
                Already have an account? <a href="login.php" style="color: #22577a;">Login</a>
            </p>
        </form>
    </div>
</body>

</html>

<?php
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $user = $_POST['username'];
    $email = $_POST['email'];
    $pass = password_hash($_POST['password'], PASSWORD_BCRYPT);
    $role = $_POST['role'];

    // Create a database connection
    $conn = new mysqli($servername, $username, $password, $dbname);

    // Check the connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Prepare the SQL query
    $sql = "INSERT INTO users_table (username, email, password, role) VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssss", $user, $email, $pass, $role);

    // Execute and handle the query
    if ($stmt->execute()) {
        echo "<script>alert('Registration successful! You can now log in.');</script>";
        echo "<script>window.location.href = 'login.php';</script>";
    } else {
        echo "<div class='alert alert-danger mt-3'>Error: " . $stmt->error . "</div>";
    }

    // Close the statement and connection
    $stmt->close();
    $conn->close();
}
?>
