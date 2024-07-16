<?php
// Include database connection
include '../components/connect.php';

// Check if the user has selected monthly view
$view = isset ($_GET['view']) && $_GET['view'] === 'monthly' ? 'monthly' : 'overall';

// Fetch product details from the database
if ($view === 'monthly') {
    // Retrieve monthly sales data
    $stmt = $conn->prepare("SELECT DATE_FORMAT(placed_on, '%Y-%m') AS order_month, 
                            COUNT(id) AS order_count,
                            SUM(total_price) AS monthly_sales,
                            AVG(total_price) AS avg_order_value
                            FROM orders
                            GROUP BY DATE_FORMAT(placed_on, '%Y-%m')");
    $stmt->execute();
    $monthlyData = $stmt->fetchAll(PDO::FETCH_ASSOC);
} else {
    // Retrieve overall product details
    $stmt = $conn->prepare("SELECT `id`, `name`, `price`, `baseprice`, `quantity`, `sold` FROM `products`");
    $stmt->execute();
    $products = $stmt->fetchAll(PDO::FETCH_ASSOC);
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inventory</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
    <!-- Include Bootstrap CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css">
    <link rel="stylesheet" href="../css/admin_style.css">
</head>

<body>
    <?php include '../components/admin_header.php'; ?>

    <section class="inventory">
        <h1 class="heading">Inventory</h1>
        <div class="box">
            <?php if ($view === 'monthly'): ?>
                <table class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>Order Month</th>
                            <th>Order Count</th>
                            <th>Monthly Sales</th>
                            <th>Avg Order Value</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($monthlyData as $data): ?>
                            <tr>
                                <td>
                                    <?= $data['order_month']; ?>
                                </td>
                                <td>
                                    <?= $data['order_count']; ?>
                                </td>
                                <td>Rs.
                                    <?= $data['monthly_sales']; ?>
                                </td>
                                <td>Rs.
                                    <?= round($data['avg_order_value'], 2); ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <table class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>Product ID</th>
                            <th>Name</th>
                            <th>Quantity</th>
                            <th>Base Price</th>
                            <th>Selling Price</th>
                            <th>Sales</th>
                            <th>Sold Items</th>
                            <th>Profit</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($products as $product): ?>
                            <tr>
                                <td>
                                    <?= $product['id']; ?>
                                </td>
                                <td>
                                    <?= $product['name']; ?>
                                </td>
                                <td>
                                    <?= $product['quantity']; ?>
                                </td>
                                <td>Rs.
                                    <?= $product['baseprice']; ?>
                                </td>
                                <td>Rs.
                                    <?= $product['price']; ?>
                                </td>
                                <td>Rs.
                                    <?= $product['price'] * $product['sold']; ?>
                                </td>
                                <td>
                                    <?= $product['sold']; ?>
                                </td>
                                <td>Rs.
                                    <?= ($product['price'] - $product['baseprice']) * $product['sold']; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </div>
    </section>

    <div class="container mt-3">
        <div class="row">
            <div class="col">
                <a href="?view=overall" class="btn btn-primary">Overall View</a>
            </div>
            <div class="col">
                <a href="?view=monthly" class="btn btn-primary">Monthly View</a>
            </div>
        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
    <script src="../js/admin_script.js"></script>
</body>

</html>