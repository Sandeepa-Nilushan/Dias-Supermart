<?php

include '../components/connect.php';

session_start();

$admin_id = $_SESSION['admin_id'];

if(!isset($admin_id)){
   header('location:admin_login.php');
}

if(isset($_POST['update_payment'])){
   $order_id = $_POST['order_id'];
   $payment_status = $_POST['payment_status'];
   $payment_status = filter_var($payment_status, FILTER_SANITIZE_STRING);
   $update_payment = $conn->prepare("UPDATE `orders` SET payment_status = ? WHERE id = ?");
   $update_payment->execute([$payment_status, $order_id]);
   $message[] = 'payment status updated!';
}

if(isset($_GET['delete'])){
   $delete_id = $_GET['delete'];
   $delete_order = $conn->prepare("DELETE FROM `orders` WHERE id = ?");
   $delete_order->execute([$delete_id]);
   header('location:placed_orders.php');
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>placed orders</title>


      <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
   <!-- Include Bootstrap CSS -->
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css">


   <link rel="stylesheet" href="../css/admin_style.css">

</head>
<body>
<?php include '../components/admin_header.php'; ?>

<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if(isset($_POST['viewSlip'])) {

        $id = $_POST['order_id'];
      //   header('Location: show_bank_slip.php?id='.$id );
                 echo "<script>
              window.location.href = 'show_bank_slip.php?id={$id}';
              </script>";
        exit;
    }
}
?>

<section class="orders">

<h1 class="heading">placed orders</h1>

<div class="box">
   <table class="table table-bordered table-striped">
      <thead>
         <tr>
            <th>#Id</th>
            <th>Placed On</th>
            <th>Name</th>
            <th>Number</th>
            <th>Address</th>
            <th>Total Products</th>
            <th>Total Price</th>
            <th>Shipping Cost</th>
            <th>Grand Total</th>
            <th>Payment Method</th>
            <th>Payment Status</th>
            <th>Action</th>
         </tr>
      </thead>
      <tbody>
         <?php
            $select_orders = $conn->prepare("SELECT * FROM `orders`");
            $select_orders->execute();
            if ($select_orders->rowCount() > 0) {
               while ($fetch_orders = $select_orders->fetch(PDO::FETCH_ASSOC)) {
         ?>
         <tr>
            <td><?= $fetch_orders['id']; ?></td>
            <td><?= $fetch_orders['placed_on']; ?></td>
            <td><?= $fetch_orders['name']; ?></td>
            <td><?= $fetch_orders['number']; ?></td>
            <td><?= $fetch_orders['address']; ?></td>
            <td><?= $fetch_orders['total_products']; ?></td>
            <td>Rs : <?= $fetch_orders['net_total']; ?>/-</td>
            <td>Rs : <?= $fetch_orders['shippingCost']; ?>/-</td>
            <td>Rs : <?= $fetch_orders['total_price']; ?>/-</td>
            <td><?= $fetch_orders['method']; ?></td>
            <td>
               <form action="" method="post">
                  <input type="hidden" name="order_id" value="<?= $fetch_orders['id']; ?>">
                  <select name="payment_status" class="form-select">
                     <option  <?= ($fetch_orders['payment_status'] == 'pending') ? 'selected' : ''; ?> value="pending">pending</option>
                     <option <?= ($fetch_orders['payment_status'] == 'completed') ? 'selected' : ''; ?> value="completed">completed</option>
                  </select>
               </td>
               <td class="flex-btn">
                  <input type="submit" value="update" class="btn btn-primary" name="update_payment">
                  <a href="placed_orders.php?delete=<?= $fetch_orders['id']; ?>" class="delete-btn" onclick="return confirm('delete this order?');">delete</a>
                   <?php if ($fetch_orders['bank_slip']): ?>
                           <input type="submit" value="View Slip" class="option-btn" name="viewSlip">
                     <?php endif; ?>
               </td>
               </form>
         </tr>
         <?php
               }
            } else {
               echo '<tr><td colspan="12" class="empty">No orders placed yet!</td></tr>';
            }
         ?>
      </tbody>
   </table>
</div>

</section>

<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>

<script src="../js/admin_script.js"></script>
   <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>

</body>
</html>