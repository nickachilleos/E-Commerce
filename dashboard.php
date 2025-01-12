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

// Fetch seller's data if needed
$seller_id = $_SESSION['user_id'];
$seller_name = $_SESSION['username'];
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Seller Dashboard</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.10.5/font/bootstrap-icons.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
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
            margin-bottom: 20px;
        }

        .dashboard-header h2 {
            font-size: 2rem;
            color: #22577a;
        }

        .dashboard-header p {
            color: #38a3a5;
        }

        .dashboard-cards {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }

        .card {
            background-color: #fff;
            border: 1px solid #57cc99;
            border-radius: 15px;
            padding: 20px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            text-align: center;
        }

        .card h5 {
            color: #22577a;
        }

        .card p {
            color: #38a3a5;
            font-size: 1.2rem;
        }

        .training-tips, .tasks, .stats {
            margin-bottom: 30px;
            padding: 20px;
            background-color: #fff;
            border: 1px solid #57cc99;
            border-radius: 15px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }

        .training-tips h3, .tasks h3, .stats h3 {
            color: #22577a;
            margin-bottom: 15px;
        }

        .training-tips ul, .tasks ul {
            padding-left: 20px;
            list-style-type: disc;
        }

        .training-tips li, .tasks li {
            color: #38a3a5;
            margin-bottom: 10px;
        }

        .footer {
            margin-top: auto;
            font-size: 0.9rem;
            color: #c7f9cc;
            text-align: center;
        }

        .chart-container {
            position: relative;
            height: 400px;
        }
    </style>
</head>

<body>
<div class="sidebar">
        <h3>Seller Dashboard</h3>
        <a href="dashboard.php" class="active"><i class="bi bi-house-door"></i> Home</a>
        <a href="manage_products.php"><i class="bi bi-box-seam"></i> Manage Products</a>
        <a href="orders.php"><i class="bi bi-cart"></i> Orders</a>
        <a href="settings.php"><i class="bi bi-gear"></i> Settings</a>
        <div class="spacer"></div>
        <a href="logout.php" class="logout"><i class="bi bi-box-arrow-left"></i> Logout </a>
        <div class="footer">&copy; 2024 Marketplace</div>
    </div>


    <div class="content">
        <div class="dashboard-header">
            <h2>Welcome to the Seller Dashboard</h2>
            <p>Manage your products and view your orders here.</p>
        </div>

        <div class="dashboard-cards">
            <div class="card">
                <h5>Total Products</h5>
                <p>24</p>
            </div>
            <div class="card">
                <h5>Total Orders</h5>
                <p>12</p>
            </div>
            <div class="card">
                <h5>Pending Orders</h5>
                <p>3</p>
            </div>
            <div class="card">
                <h5>Revenue</h5>
                <p>$4,567</p>
            </div>
        </div>

        <div class="training-tips">
            <h3>Training Tips</h3>
            <ul>
                <li>Optimize your product descriptions with keywords.</li>
                <li>Respond to customer queries within 24 hours.</li>
                <li>Use high-quality images to attract more customers.</li>
            </ul>
        </div>

        <div class="tasks">
            <h3>Tasks for Today</h3>
            <ul>
                <li>Fulfill 3 pending orders</li>
                <li>Update stock for 5 products</li>
                <li>Review new customer feedback</li>
            </ul>
        </div>

        <div class="stats">
            <h3>Statistics</h3>
            <div class="chart-container">
                <canvas id="salesChart"></canvas>
            </div>
        </div>
    </div>

    <script>
    document.addEventListener("DOMContentLoaded", function () {
        const ctx = document.getElementById('salesChart').getContext('2d');

        // Check if the canvas element exists
        if (!ctx) {
            console.error('Canvas element for chart not found.');
            return;
        }

        // Chart.js data and configuration
        const chartData = {
            labels: ['January', 'February', 'March', 'April', 'May'], // Example data
            datasets: [{
                label: 'Sales Revenue ($)',
                data: [1200, 1900, 3000, 5000, 2200], // Example values
                backgroundColor: 'rgba(87, 204, 153, 0.7)',
                borderColor: '#38a3a5',
                borderWidth: 1
            }]
        };

        const chartOptions = {
            responsive: true,
            plugins: {
                legend: {
                    display: true,
                    position: 'top'
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    title: {
                        display: true,
                        text: 'Revenue ($)'
                    }
                },
                x: {
                    title: {
                        display: true,
                        text: 'Month'
                    }
                }
            }
        };

        // Create the chart
        try {
            const salesChart = new Chart(ctx, {
                type: 'bar',
                data: chartData,
                options: chartOptions
            });
        } catch (error) {
            console.error('Error creating chart:', error);
        }
    });
</script>

</body>

</html>
