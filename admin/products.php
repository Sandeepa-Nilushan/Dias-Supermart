<?php

include '../components/connect.php';

session_start();

$admin_id = $_SESSION['admin_id'];

if (!isset ($admin_id)) {
   header('location:admin_login.php');
   exit(); // Add exit after redirection
}

if (isset ($_POST['add_product'])) {

   $name = $_POST['name'];
   $name = filter_var($name, FILTER_SANITIZE_STRING);
   $price = $_POST['price'];
   $price = filter_var($price, FILTER_SANITIZE_STRING);
   $details = $_POST['details'];
   $details = filter_var($details, FILTER_SANITIZE_STRING);
   $base_price = $_POST['base_price']; // Retrieve base price
   $quantity = $_POST['quantity']; // Retrieve quantity

   $image_01 = $_FILES['image_01']['name'];
   $image_01 = filter_var($image_01, FILTER_SANITIZE_STRING);
   $image_size_01 = $_FILES['image_01']['size'];
   $image_tmp_name_01 = $_FILES['image_01']['tmp_name'];
   $image_folder_01 = '../uploaded_img/' . $image_01;

   $image_02 = $_FILES['image_02']['name'];
   $image_02 = filter_var($image_02, FILTER_SANITIZE_STRING);
   $image_size_02 = $_FILES['image_02']['size'];
   $image_tmp_name_02 = $_FILES['image_02']['tmp_name'];
   $image_folder_02 = '../uploaded_img/' . $image_02;

   $image_03 = $_FILES['image_03']['name'];
   $image_03 = filter_var($image_03, FILTER_SANITIZE_STRING);
   $image_size_03 = $_FILES['image_03']['size'];
   $image_tmp_name_03 = $_FILES['image_03']['tmp_name'];
   $image_folder_03 = '../uploaded_img/' . $image_03;
   $cat = $_POST['cat'];

   $select_products = $conn->prepare("SELECT * FROM `products` WHERE category= ? and  name = ?");
   $select_products->execute([$cat, $name]);

   if ($select_products->rowCount() > 0) {
      $message[] = 'product already exists!';
   } else {

      $insert_products = $conn->prepare("INSERT INTO `products`(name, details, price, baseprice, quantity,sold, image_01, image_02, image_03, category) VALUES(?,?,?,?,?,0,?,?,?,?)");
      $insert_products->execute([$name, $details, $price, $base_price, $quantity, $image_01, $image_02, $image_03, $cat]);

      if ($insert_products) {
         if ($image_size_01 > 2000000 or $image_size_02 > 2000000 or $image_size_03 > 2000000) {
            $message[] = 'image size is too large!';
         } else {
            move_uploaded_file($image_tmp_name_01, $image_folder_01);
            move_uploaded_file($image_tmp_name_02, $image_folder_02);
            move_uploaded_file($image_tmp_name_03, $image_folder_03);
            $message[] = 'new product added!';
         }

      }

   }

}

if (isset ($_GET['delete'])) {

   $delete_id = $_GET['delete'];
   $delete_product_image = $conn->prepare("SELECT * FROM `products` WHERE id = ?");
   $delete_product_image->execute([$delete_id]);
   $fetch_delete_image = $delete_product_image->fetch(PDO::FETCH_ASSOC);
   unlink('../uploaded_img/' . $fetch_delete_image['image_01']);
   unlink('../uploaded_img/' . $fetch_delete_image['image_02']);
   unlink('../uploaded_img/' . $fetch_delete_image['image_03']);
   $delete_product = $conn->prepare("DELETE FROM `products` WHERE id = ?");
   $delete_product->execute([$delete_id]);
   $delete_cart = $conn->prepare("DELETE FROM `cart` WHERE pid = ?");
   $delete_cart->execute([$delete_id]);
   $delete_wishlist = $conn->prepare("DELETE FROM `wishlist` WHERE pid = ?");
   $delete_wishlist->execute([$delete_id]);
   header('location:products.php');
   exit(); // Add exit after redirection
}
?>


<!DOCTYPE html>
<html lang="en">

<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>products</title>

   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">

   <link rel="stylesheet" href="../css/admin_style.css">

</head>

<body>

   <?php include '../components/admin_header.php'; ?>

   <section class="add-products">

      <h1 class="heading">add product</h1>

      <form action="" method="post" enctype="multipart/form-data">
         <div class="flex">
            <div class="inputBox">
               <span>product category (required)</span>
               <select required name="cat" id="cat" class="box" placeholder="enter product category">
                  <option value="">..............................................................</option>
                  <option value="beverages">Beverages</option>
                  <option value="cooking_essentials">Cooking Essentials</option>
                  <option value="dairy">Dairy</option>
                  <option value="food_cupboard">Food Cupboard</option>
                  <option value="frozen_food">Frozen Food</option>

               </select>
            </div>
            <div class="inputBox">
               <span>product name (required)</span>
               <input type="text" class="box" required maxlength="100" placeholder="enter product name" name="name">
            </div>
            <div class="inputBox">
               <span>product price (required)</span>
               <input type="number" min="0" class="box" required max="9999999999" placeholder="enter product price"
                  onkeypress="if(this.value.length == 10) return false;" name="price">
            </div>
            <div class="inputBox">
               <span>Product Base Price (required)</span>
               <input type="number" min="0" class="box" required max="9999999999" placeholder="Enter product base price"
                  name="base_price">
            </div>
            <div class="inputBox">
               <span>Product Quantity (required)</span>
               <input type="number" min="1" class="box" required placeholder="Enter product quantity" name="quantity">
            </div>
            <div class="inputBox">
               <span>image 01 (required)</span>
               <input type="file" name="image_01" accept="image/jpg, image/jpeg, image/png, image/webp" class="box"
                  required>
            </div>
            <div class="inputBox">
               <span>image 02 (required)</span>
               <input type="file" name="image_02" accept="image/jpg, image/jpeg, image/png, image/webp" class="box"
                  required>
            </div>
            <div class="inputBox">
               <span>image 03 (required)</span>
               <input type="file" name="image_03" accept="image/jpg, image/jpeg, image/png, image/webp" class="box"
                  required>
            </div>
            <div class="inputBox">
               <span>product details (required)</span>
               <textarea name="details" placeholder="enter product details" class="box" required maxlength="500"
                  cols="30" rows="10"></textarea>
            </div>
         </div>

         <input type="submit" value="add product" class="btn" name="add_product">
      </form>

   </section>

   <section class="show-products">

      <h1 class="heading">products added</h1>


      <?php
      $select_products = $conn->prepare("SELECT * FROM `products`");
      $select_products->execute();
      if ($select_products->rowCount() > 0) {
         echo '<table class="table table-bordered table-striped">';
         echo "<thead>";
         echo "<tr>";
         echo "<th>#Id</th>";
         echo "<th>Image</th>";
         echo "<th>Name</th>";
         echo "<th>Price</th>";
         echo "<th>Details</th>";
         echo "<th>Action</th>";
         echo "</tr>";
         echo "</thead>";
         echo "<tbody>";
         while ($fetch_products = $select_products->fetch(PDO::FETCH_ASSOC)) {
            echo "<tr>";
            echo "<td>" . $fetch_products['id'] . "</td>";
            echo '<td><img src="../uploaded_img/' . $fetch_products['image_01'] . '" alt=""></td>';
            echo "<td>" . $fetch_products['name'] . "</td>";
            echo "<td>Rs : <span>" . $fetch_products['price'] . "</span>/-</td>";
            echo "<td><span>" . $fetch_products['details'] . "</span></td>";
            echo "<td class='flex-btn'>";

            echo '<a href="update_product.php?update=' . $fetch_products['id'] . '" class="option-btn">Update</a>';
            echo '<a href="products.php?delete=' . $fetch_products['id'] . '" class="delete-btn" onclick="return confirm(\'Delete this product?\');">Delete</a>';
            echo "</td>";
            echo "</tr>";
         }
         echo "</tbody>";
         echo "</table>";
      } else {
         echo '<p class="empty">No products added yet!</p>';
      }
      ?>



   </section>








   <script src="../js/admin_script.js"></script>

</body>

</html>