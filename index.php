<?php

include 'components/connect.php';

session_start();

if (isset($_SESSION['user_id'])) {
   $user_id = $_SESSION['user_id'];
} else {
   $user_id = '';
}
;

include 'components/wishlist_cart.php';

?>

<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
   if (isset($_POST['add_to_cart'])) {

   } elseif (isset($_POST['direct_checkout'])) {

      $pid = $_POST['pid'];
      $name = $_POST['name'];
      $price = $_POST['price'];
      $qty = $_POST['qty'];

      //   header('Location: directcheckout.php?pid=' . $pid . '&name=' . urlencode($name) . '&price=' . $price . '&qty=' . $qty);
      echo "<script>
              window.location.href = 'directcheckout.php?pid={$pid}&name=' + encodeURIComponent('{$name}') + '&price={$price}&qty={$qty}';
              </script>";

      exit;
   }
}
?>



<!DOCTYPE html>
<html lang="en">

<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>home</title>

   <link rel="stylesheet" href="https://unpkg.com/swiper@8/swiper-bundle.min.css" />
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
   <link rel="stylesheet" href="css/style.css">

</head>

<body>

   <?php include 'components/user_header.php'; ?>

   <div class="home-bg">

      <section class="home">

         <div class="swiper home-slider" style="height: 80vh;">

            <div class="swiper-wrapper">

               <div class="swiper-slide slide">
                  <div class="image">
                     <img src="images/icecream.jpg" alt="">
                  </div>
                  <div class="content">
                     <span>upto 50% off</span>
                     <h3>new Ice Creams </h3>
                     <a href="shop.php" class="btn">shop now</a>
                  </div>
               </div>

               <div class="swiper-slide slide">
                  <div class="image">
                     <img src="images/softdrinks.jpg" alt="">
                  </div>
                  <div class="content">
                     <span>upto 50% off</span>
                     <h3>latest Soft Drinks</h3>
                     <a href="shop.php" class="btn">shop now</a>
                  </div>
               </div>

               <div class="swiper-slide slide">
                  <div class="image">
                     <img src="images/spices.jpg" alt="">
                  </div>
                  <div class="content">
                     <span>upto 50% off</span>
                     <h3>latest Spices</h3>
                     <a href="shop.php" class="btn">shop now</a>
                  </div>
               </div>

            </div>

            <div class="swiper-pagination"></div>

         </div>

      </section>

   </div>

   <section class="category">

      <h1 class="heading">shop by category</h1>

      <div class="swiper category-slider">

         <div class="swiper-wrapper">

            <a href="category.php?category=beverages" class="swiper-slide slide">
               <img src="images/Icon_Beverages" alt="">
               <h3>Beverages <br>. </h3>
            </a>

            <a href="category.php?category=cooking_essentials" class="swiper-slide slide">
               <img src="images/Icon_Cooking-Essentials" alt="">
               <h3>Cooking Essentials</h3>
            </a>

            <a href="category.php?category=dairy" class="swiper-slide slide">
               <img src="images/Icon_Dairy" alt="">
               <h3>Dairy <br>. </h3>
            </a>

            <a href="category.php?category=food_cupboard" class="swiper-slide slide">
               <img src="images/Icon_Food_Cupboard" alt="">
               <h3>Food Cupboard <br>. </h3>
            </a>

            <a href="category.php?category=frozen_food" class="swiper-slide slide">
               <img src="images/Icon_Frozen-Food" alt="">
               <h3>Frozen Food <br>. </h3>
            </a>


         </div>

         <div class="swiper-pagination"></div>

      </div>

   </section>



   <section class="home-products">

      <h1 class="heading">latest products</h1>

      <div class="swiper products-slider">

         <div class="swiper-wrapper">

            <?php
            $select_products = $conn->prepare("SELECT * FROM `products` LIMIT 6");
            $select_products->execute();
            if ($select_products->rowCount() > 0) {
               while ($fetch_product = $select_products->fetch(PDO::FETCH_ASSOC)) {
                  ?>
                  <form action=" " method="post" class="swiper-slide slide">
                     <input type="hidden" name="pid" value="<?= $fetch_product['id']; ?>">
                     <input type="hidden" name="name" value="<?= $fetch_product['name']; ?>">
                     <input type="hidden" name="price" value="<?= $fetch_product['price']; ?>">
                     <input type="hidden" name="image" value="<?= $fetch_product['image_01']; ?>">
                     <button class="fas fa-heart" type="submit" name="add_to_wishlist"></button>
                     <a href="quick_view.php?pid=<?= $fetch_product['id']; ?>" class="fas fa-eye"></a>
                     <img src="uploaded_img/<?= $fetch_product['image_01']; ?>" alt="">
                     <div class="name">
                        <?= $fetch_product['name']; ?>
                     </div>
                     <div class="flex">
                        <div class="price"><span>Rs : </span>
                           <?= $fetch_product['price']; ?><span>/-</span>
                        </div>
                        <input type="number" name="qty" class="qty" min="1" max="99"
                           onkeypress="if(this.value.length == 2) return false;" value="1">
                     </div>
                     <input type="submit" value="add to cart" class="btn" name="add_to_cart">
                     <input type="submit" value="Checkout" class="option-btn" name="direct_checkout">
                  </form>

                  <?php
               }
            } else {
               echo '<p class="empty">no products added yet!</p>';
            }
            ?>

         </div>

         <div class="swiper-pagination"></div>

      </div>

   </section>


   <?php include 'components/footer.php'; ?>

   <script src="https://unpkg.com/swiper@8/swiper-bundle.min.js"></script>

   <script src="js/script.js"></script>

   <script>

      var swiper = new Swiper(".home-slider", {
         loop: true,
         spaceBetween: 20,
         pagination: {
            el: ".swiper-pagination",
            clickable: true,
         },
      });

      var swiper = new Swiper(".category-slider", {
         loop: true,
         spaceBetween: 20,
         pagination: {
            el: ".swiper-pagination",
            clickable: true,
         },
         breakpoints: {
            0: {
               slidesPerView: 2,
            },
            650: {
               slidesPerView: 3,
            },
            768: {
               slidesPerView: 4,
            },
            1024: {
               slidesPerView: 5,
            },
         },
      });

      var swiper = new Swiper(".products-slider", {
         loop: true,
         spaceBetween: 20,
         pagination: {
            el: ".swiper-pagination",
            clickable: true,
         },
         breakpoints: {
            550: {
               slidesPerView: 2,
            },
            768: {
               slidesPerView: 2,
            },
            1024: {
               slidesPerView: 3,
            },
         },
      });

   </script>

</body>

</html>