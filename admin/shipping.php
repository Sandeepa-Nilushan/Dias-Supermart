<?php
include '../components/connect.php';
session_start();

$admin_id = $_SESSION['admin_id'];

if (!isset($admin_id)) {
    header('location:admin_login.php');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_shipping'])) {

   
    // Process form data
    $order_id = $_POST['id'];
    $shipping_status = $_POST['shipping_Status'];
    $tracker = isset($_POST['tracker']) ? $_POST['tracker'] : null;

    // Update shipping status and tracker in the database
    $update_shipping = $conn->prepare("UPDATE `orders` SET shipping_Status = ?, tracker = ? WHERE id = ?");
    if (!$update_shipping) {
        echo "Prepare failed: (" . $conn->errorCode() . ") " . $conn->errorInfo()[2] . "<br>";
    } else {
        
    }

    if (!$update_shipping->execute([$shipping_status, $tracker, $order_id])) {
        echo "Execute failed: (" . $update_shipping->errorCode() . ") " . $update_shipping->errorInfo()[2] . "<br>";
    } else {

    }
}

// Check if a search query is submitted
$search_query = isset($_GET['q']) ? $_GET['q'] : '';

// Prepare the SQL query to fetch orders based on order ID
$select_orders = $conn->prepare("SELECT `id`, `name`, `address`, `shipping_status`, `tracker` FROM `orders` WHERE `id` LIKE ?");
$search_param = "%{$search_query}%";
$select_orders->execute([$search_param]);
$orders = $select_orders->fetchAll(PDO::FETCH_ASSOC);

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shipping Orders</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
    <!-- Include Bootstrap CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css">
    <link rel="stylesheet" href="../css/admin_style.css">
    <style>
        /* Custom CSS for Shipping Page */
        .orders {
            margin-top: 50px;
        }

        .search-container {
            margin-bottom: 20px;
        }

        .search-input {
            border-radius: 0;
            font-size: 1.6rem;
            height: 40px;
        }

        .search-btn {
            border-radius: 0;
            font-size: 1.6rem;
            height: 40px;
        }

        .clear-btn {
            border-radius: 0;
            font-size: 1.6rem;
            height: 40px;
        }

        .status-select {
            width: 150px;
            font-size: 1.6rem;
        }

        .update-btn {
            margin-right: 10px;
        }

        .delete-btn {
            color: red;
        }

        .form-select {
            border-radius: 0;
            font-size: 1.6rem;
            height: 40px;

        }

        .option-btn {
            margin-left: 10px;
        }
    </style>
</head>

<body>
    <?php include '../components/admin_header.php'; ?>

    <section class="orders">
        <h1 class="heading">Shipping Orders</h1>
        <div class="box">
            <!-- Search form -->
            <div class="search-container">
                <form method="GET" class="mb-3">
                    <div class="input-group">
                        <input type="text" class="form-control search-input" name="q"
                            placeholder="Search by Order ID..." value="<?= $search_query ?>">

                        <div class="container mt-3">
                            <div class="row">
                                <div class="col">
                                    <button class="btn btn-outline-primary search-btn" type="submit">Search</button>
                                </div>
                                <div class="col">
                                    <button class="btn btn-outline-secondary clear-btn" type="button"
                                        onclick="window.location.href='shipping.php'">Clear</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>

            <br><br><br>

            <table class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>Order ID</th>
                        <th>Customer Name</th>
                        <th>Address</th>
                        <th>Status</th>
                        <th>Tracking Number</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($orders as $order): ?>
                        <tr>
                            <td>
                                <?= $order['id']; ?>
                            </td>
                            <td>
                                <?= $order['name']; ?>
                            </td>
                            <td>
                                <?= $order['address']; ?>
                            </td>
                            <td>
                                <form action="" method="post">
                                    <select name="shipping_Status" class="form-select status-select">
                                        <option value="To Be Shipped" <?= ($order['shipping_status'] === 'To Be Shipped') ? 'selected' : ''; ?>>To be shipped</option>
                                        <option value="Shipped" <?= ($order['shipping_status'] === 'Shipped') ? 'selected' : ''; ?>>Shipped</option>
                                        <option value="Completed" <?= ($order['shipping_status'] === 'Completed') ? 'selected' : ''; ?>>Completed</option>
                                    </select>
                            </td>
                            <td>
                                <input type="text" name="tracker" class="form-control" value="<?= $order['tracker']; ?>">
                            </td>
                            <td>
                                <input type="hidden" name="id" value="<?= $order['id']; ?>">
                                <button type="submit" class="btn btn-primary update-btn"
                                    name="update_shipping">Update</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>

        </div>
    </section>

    <!-- Bootstrap JS -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
</body>

</html>