<?php
session_start();

// Destroy the session if you don't want to keep the cart/session data after order completion
session_unset();
session_destroy();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Success</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <style>
        body {
            display: flex;
            justify-content: center;
            align-items: center;
            background: linear-gradient(135deg, #38A3A5, #57CC99);
            height: 100vh;
            margin: 0;
        }

        .success-card {
            background: #fff;
            border-radius: 15px;
            padding: 40px;
            width: 100%;
            max-width: 450px;
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.2);
            text-align: center;
            animation: fadeIn 0.8s ease-in-out;
        }

        .success-card img {
            width: 100px;
            margin-bottom: 20px;
            border-radius: 50%;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        }

        .success-card h2 {
            font-size: 28px;
            color: #22577A;
            margin-bottom: 15px;
        }

        .success-card p {
            font-size: 18px;
            color: #38A3A5;
            margin-bottom: 20px;
        }

        .success-card .btn-primary {
            background: linear-gradient(135deg, #57CC99, #38A3A5);
            border: none;
            padding: 10px 20px;
            font-size: 16px;
            border-radius: 8px;
            color: #fff;
            cursor: pointer;
            transition: transform 0.2s, box-shadow 0.2s;
        }

        .success-card .btn-primary:hover {
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
    <div class="success-card">
        <img src="https://cdn-icons-png.flaticon.com/512/190/190411.png" alt="Success">
        <h2>Thank You for Your Order!</h2>
        <p>Your order has been successfully placed. We appreciate your business.</p>
        <button class="btn btn-primary" onclick="redirectToHome()">Back to Home</button>
    </div>

    <script>
        function redirectToHome() {
            window.location.href = 'customer_dashboard.php'; // Change to your home page URL
        }

        // Automatically redirect after 5 seconds (optional)
        setTimeout(redirectToHome, 5000);
    </script>
</body>

</html>
