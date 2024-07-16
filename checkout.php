<?php

include 'components/connect.php';

session_start();

if (isset ($_SESSION['user_id'])) {
   $user_id = $_SESSION['user_id'];
} else {
   $user_id = '';
   header('location:user_login.php');
}

if (isset ($_POST['order'])) {
   $name = $_POST['name'];
   $name = filter_var($name, FILTER_SANITIZE_STRING);
   $number = $_POST['number'];
   $number = filter_var($number, FILTER_SANITIZE_STRING);
   $email = $_POST['email'];
   $email = filter_var($email, FILTER_SANITIZE_STRING);
   $method = $_POST['method'];
   $method = filter_var($method, FILTER_SANITIZE_STRING);
   $placed_on = $_POST['placed_on'];
   $address = 'flat no. ' . $_POST['flat'] . ', ' . $_POST['street'] . ', ' . $_POST['city'] . ', ' . $_POST['state'] . ', ' . $_POST['country'] . ' - ' . $_POST['pin_code'];
   $address = filter_var($address, FILTER_SANITIZE_STRING);
   $total_products = $_POST['total_products'];
   $total_price = $_POST['total_price'];
   $shipping_cost = $_POST['shipping_cost'];
   $net_total = $_POST['net_total'];

   $bank_slip = $_FILES['bank_slip']['name'];
   $bank_slip = filter_var($bank_slip, FILTER_SANITIZE_STRING);
   $bank_slip_size_01 = $_FILES['bank_slip']['size'];
   $bank_slip_tmp_name_01 = $_FILES['bank_slip']['tmp_name'];
   $bank_slip_folder_01 = './bankslip/' . $bank_slip;

   $grand_total = 0;

// Update product quantity and sold amount
$check_cart = $conn->prepare("SELECT * FROM `cart` WHERE user_id = ?");
$check_cart->execute([$user_id]);

if ($check_cart->rowCount() > 0) {
    while ($fetch_cart = $check_cart->fetch(PDO::FETCH_ASSOC)) {
        $cart_items[] = $fetch_cart['name'] . ' (' . $fetch_cart['price'] . ' x ' . $fetch_cart['quantity'] . ') - ';
        $total_products = implode($cart_items);
        $grand_total += ($fetch_cart['price'] * $fetch_cart['quantity']);

        // Update product quantity and sold amount
        $update_product = $conn->prepare("UPDATE `product` SET quantity = quantity - ?, sold = sold + ? WHERE id = ?");
        $update_product->execute([$fetch_cart['quantity'], $fetch_cart['quantity'], $fetch_cart['pid']]);
    }

    // Set initial values for shipping_Status and tracker
    $shipping_Status = "To Be Shipped";
    $tracker = 0;

    if ($method === 'online transaction') {
        $insert_order = $conn->prepare("INSERT INTO `orders` (user_id, name, number, email, method, address, total_products, total_price, placed_on, shippingCost, net_total, bank_slip, shipping_Status, tracker) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $insert_order->execute([$user_id, $name, $number, $email, $method, $address, $total_products, $total_price, $placed_on, $shipping_cost, $net_total, $bank_slip, $shipping_Status, $tracker]);

        if ($insert_order) {
            if ($bank_slip_size_01 > 2000000) {
                $message[] = 'image size is too large!';
            } else {
                move_uploaded_file($bank_slip_tmp_name_01, $bank_slip_folder_01);
                $delete_cart = $conn->prepare("DELETE FROM `cart` WHERE user_id = ?");
                $delete_cart->execute([$user_id]);

                $message[] = 'order placed successfully!';
            }
        }
    } else {
        $insert_order = $conn->prepare("INSERT INTO `orders` (user_id, name, number, email, method, address, total_products, total_price, placed_on, shippingCost, net_total, bank_slip, shipping_Status, tracker) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $insert_order->execute([$user_id, $name, $number, $email, $method, $address, $total_products, $total_price, $placed_on, $shipping_cost, $net_total, "", $shipping_Status, $tracker]);

        $delete_cart = $conn->prepare("DELETE FROM `cart` WHERE user_id = ?");
        $delete_cart->execute([$user_id]);

        $message[] = 'order placed successfully!';
    }
} else {
    $message[] = 'your cart is empty';
    $grand_total = 0;
    $shipping_cost = 0;
    $net_total = 0;
}
}

?>


<!DOCTYPE html>
<html lang="en">

<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>checkout</title>

   <!-- font awesome cdn link  -->
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">

   <!-- custom css file link  -->
   <link rel="stylesheet" href="css/style.css">

</head>

<body>

   <?php include 'components/user_header.php'; ?>

   <section class="checkout-orders">

      <form action="" method="POST" enctype="multipart/form-data">


         <h3>your orders</h3>

         <div class="display-orders">
            <?php
            $grand_total = 0;
            $cart_items[] = '';
            $select_cart = $conn->prepare("SELECT * FROM `cart` WHERE user_id = ?");
            $select_cart->execute([$user_id]);
            if ($select_cart->rowCount() > 0) {
               while ($fetch_cart = $select_cart->fetch(PDO::FETCH_ASSOC)) {
                  $cart_items[] = $fetch_cart['name'] . ' (' . $fetch_cart['price'] . ' x ' . $fetch_cart['quantity'] . ') - ';
                  $total_products = implode($cart_items);
                  $grand_total += ($fetch_cart['price'] * $fetch_cart['quantity']);
                  ?>
                  <p>
                     <?= $fetch_cart['name']; ?> <span>(
                        <?= 'Rs : ' . $fetch_cart['price'] . '/- x ' . $fetch_cart['quantity']; ?>)
                     </span>
                  </p>
                  <?php
               }


               $select_shipping = $conn->prepare("SELECT shippingCost FROM `shipping`");
               $select_shipping->execute();
               $shipping_cost = $select_shipping->fetchColumn();

               $grand_total += $shipping_cost;
               $net_total = $grand_total - $shipping_cost;
            } else {
               echo '<p class="empty">your cart is empty!</p>';
            }
            ?>
            <input type="hidden" name="total_products" value="<?= $total_products; ?>">
            <input type="hidden" name="total_price" value="<?= $grand_total; ?>" value="">
            <input type="hidden" name="shipping_cost" value="<?= $shipping_cost; ?>" value="">
            <input type="hidden" name="net_total" value="<?= $net_total; ?>" value=""><br>
            <div class="grand-total">Total cost : <span>Rs :
                  <?= $net_total; ?>/-
               </span></div>
            <div class="grand-total">shipping cost : <span>Rs :
                  <?= $shipping_cost; ?>/-
               </span></div>
            <div class="grand-total">Grand total : <span>Rs :
                  <?= $grand_total; ?>/-
               </span></div>
         </div>

         <h3>place your orders</h3>

         <div class="flex">
            <div class="inputBox">
               <span>your name :</span>
               <input type="text" name="name" placeholder="enter your name" class="box" maxlength="20" required>
            </div>
            <div class="inputBox">
               <span>your number :</span>
               <input type="number" name="number" placeholder="enter your number" class="box" min="0" max="9999999999"
                  onkeypress="if(this.value.length == 10) return false;" required>
            </div>
            <div class="inputBox">
               <span>your email :</span>
               <input type="email" name="email" placeholder="enter your email" class="box" maxlength="50" required>
            </div>
            <div class="inputBox">
               <span>payment method :</span>
               <select name="method" class="box" required>
                  <option value="cash on delivery">cash on delivery</option>
                  <option value="credit card">credit card</option>
                  <option value="online transaction">online transaction</option>
                  <option value="paypal">paypal</option>
               </select>
            </div>
            <div class="inputBox" id="bank-slip-upload" style="display: none;">
               <span>Upload Bank Slip:</span>
               <input type="file" name="bank_slip" class="box">
            </div>
            <div class="inputBox">
               <span>address line 01 :</span>
               <input type="text" name="flat" placeholder="e.g. flat number" class="box" maxlength="50" required>
            </div>
            <div class="inputBox">
               <span>address line 02 :</span>
               <input type="text" name="street" placeholder="e.g. street name" class="box" maxlength="50" required>
            </div>
            <div class="inputBox">
               <span>city :</span>
               <input type="text" name="city" placeholder="e.g. colombo" class="box" maxlength="50" required>
            </div>
            <div class="inputBox">
               <span>state :</span>
               <input type="text" name="state" placeholder="e.g. maharagama" class="box" maxlength="50" required>
            </div>
            <div class="inputBox">
               <span>country :</span>
               <input type="text" name="country" placeholder="e.g. Srilanka" class="box" maxlength="50" required>
            </div>
            <div class="inputBox">
               <span>Postal code :</span>
               <input type="number" min="0" name="pin_code" placeholder="e.g. 123456" min="0" max="999999"
                  onkeypress="if(this.value.length == 6) return false;" class="box" required>
            </div>
         </div>
         <input type="hidden" name="placed_on" value="<?= date('Y-m-d H:i:s'); ?>">
         <input type="submit" name="order" class="btn <?= ($grand_total > 1) ? '' : 'disabled'; ?>" value="place order">

      </form>

   </section>
   <script>
      const paymentMethodSelect = document.querySelector('select[name="method"]');
      const bankSlipUpload = document.getElementById('bank-slip-upload');

      paymentMethodSelect.addEventListener('change', function () {
         const selectedValue = this.value;
         if (selectedValue === 'online transaction') {
            bankSlipUpload.style.display = 'block';
         } else {
            bankSlipUpload.style.display = 'none';
         }
      });
   </script>












   <?php include 'components/footer.php'; ?>

   <script src="js/script.js"></script>

</body>

</html>