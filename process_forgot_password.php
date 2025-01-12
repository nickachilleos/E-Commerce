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

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = isset($_POST['email']) ? $_POST['email'] : null;
    $new_password = isset($_POST['new_password']) ? $_POST['new_password'] : null;
    $confirm_password = isset($_POST['confirm_password']) ? $_POST['confirm_password'] : null;

    // Validate inputs
    if (!$email || !$new_password || !$confirm_password) {
        die("All fields are required. <a href='forgot_password.php'>Try again</a>");
    }

    // Check if passwords match
    if ($new_password !== $confirm_password) {
        die("Passwords do not match. <a href='forgot_password.php'>Try again</a>");
    }

    // Check if the email exists
    $sql = "SELECT id FROM users_table WHERE email = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 0) {
        die("No account found with this email. <a href='forgot_password.php'>Try again</a>");
    }

    // Update the password
    $hashed_password = password_hash($new_password, PASSWORD_BCRYPT);
    $sql = "UPDATE users_table SET password = ? WHERE email = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $hashed_password, $email);

    if ($stmt->execute()) {
        echo "Password successfully updated. <a href='login.php'>Login</a>";
    } else {
        echo "Error updating password: " . $stmt->error;
    }

    $stmt->close();
}
$conn->close();
?>
