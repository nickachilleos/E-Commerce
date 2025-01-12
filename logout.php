<?php
session_start();

// Destroy the session
session_unset();
session_destroy();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Logout</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="css/styles.css">
    <style>
        body {
            display: flex;
            justify-content: center;
            align-items: center;
            background: linear-gradient(135deg, #38A3A5, #57CC99);
            height: 100vh;
            margin: 0;
        }

        .logout-card {
            background: #fff;
            border-radius: 15px;
            padding: 40px;
            width: 100%;
            max-width: 400px;
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.2);
            text-align: center;
            animation: fadeIn 0.8s ease-in-out;
        }

        .logout-card h2 {
            font-size: 26px;
            color: #22577A;
            margin-bottom: 15px;
        }

        .logout-card p {
            font-size: 18px;
            color: #38A3A5;
            margin-bottom: 20px;
        }

        .logout-card img {
            width: 80px;
            margin-bottom: 20px;
            border-radius: 50%;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        }

        .logout-card .btn-primary {
            background: linear-gradient(135deg, #57CC99, #38A3A5);
            border: none;
            padding: 10px 20px;
            font-size: 16px;
            border-radius: 8px;
            color: #fff;
            cursor: pointer;
            transition: transform 0.2s, box-shadow 0.2s;
        }

        .logout-card .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 12px rgba(0, 0, 0, 0.2);
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: scale(0.9);
            }
            to {
                opacity: 1;
                transform: scale(1);
            }
        }
    </style>
</head>

<body>
    <div class="logout-card">
        <img src="https://cdn-icons-png.flaticon.com/512/190/190411.png" alt="Thank You">
        <h2>Thank You for Visiting!</h2>
        <p>We hope to see you again soon.</p>
        <button class="btn btn-primary" onclick="redirectToLogin()">Back to Login</button>
    </div>

    <script>
        function redirectToLogin() {
            window.location.href = 'login.php';
        }

        // Automatically redirect after 5 seconds
        // setTimeout(redirectToLogin, 5000);
    </script>
</body>

</html>
