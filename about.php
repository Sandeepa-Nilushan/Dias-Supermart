<?php

include 'components/connect.php';

session_start();

if (isset($_SESSION['user_id'])) {
   $user_id = $_SESSION['user_id'];
} else {
   $user_id = '';
}
;

?>

<!DOCTYPE html>
<html lang="en">

<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>about</title>

   <link rel="stylesheet" href="https://unpkg.com/swiper@8/swiper-bundle.min.css" />

   <!-- font awesome cdn link  -->
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">

   <!-- custom css file link  -->
   <link rel="stylesheet" href="css/style.css">
   <link rel="stylesheet" href="css/about.css">


</head>

<body>

   <?php include 'components/user_header.php'; ?>
   <br><br><br><br><br>
   <section class="about">

      <div class="row">

         <div class="image">
            <img src="images/about.jpg" alt="">
         </div>
         <br><br><br><br><br>
         <div class="content">
            <h3>Why choose us?</h3>
            <p>At Supermarket Name, we are dedicated to providing you with the best shopping experience. As a leading
               supermarket in the area, we strive to offer a wide range of high-quality products, exceptional customer
               service, and unbeatable prices</p>

            <h3>Our mission</h3>
            <p>Our mission is to be your one-stop destination for all your grocery needs. We aim to exceed your
               expectations by offering fresh produce, top-notch meats, pantry staples, and specialty items that cater
               to your diverse tastes and preferences.</p>

            <h3>Our Commitment</h3>
            <p>We are committed to sustainability, supporting local farmers, and reducing our environmental footprint.
               By partnering with trusted suppliers and implementing eco-friendly practices, we ensure that our products
               are not only good for you but also for the planet.</p>

            <a href="contact.php" class="btn">contact us</a>
         </div>

      </div>
<br><br><br><br><br>
   </section>

   <section class="team">

      <h1 class="heading">Our Team</h1>

      <div class="swiper team-slider">

         <div class="swiper-wrapper">
            <!-- Team Member 1 -->
            <div class="swiper-slide slide">
               <img src="images/profile/pic-2.png" alt="Team Member 1">
               <h3>Emily Smith</h3>
               <h4>Store Manager 1</h4>
               <p>Emily has been with us for over 5 years and ensures smooth operations and exceptional customer service
                  at our supermarket. With her extensive experience in retail management, she ensures that every
                  customer leaves satisfied. </p>
            </div>

            <!-- Team Member 2 -->
            <div class="swiper-slide slide">
               <img src="images/profile/pic-3.png" alt="Team Member 1">
               <h3>David Johnson</h3>
               <h4>Head of Procurement</h4>
               <p>David is responsible for sourcing high-quality products at the best prices for our supermarket. With
                  his keen eye for quality and strong negotiation skills, he ensures that we offer a diverse range of
                  products while maintaining affordability. </p>
            </div>

            <div class="swiper-slide slide">
               <img src="images/profile/pic-6.png" alt="Team Member 1">
               <h3>Sarah Lee</h3>
               <h4>Customer Relations Officer</h4>
               <p>Sarah is the friendly face you'll often see at our supermarket. With her warm personality and
                  excellent communication skills, she ensures that every customer feels valued and their needs are met.
                  </p>
            </div>

            <!-- Add more team members as needed -->
         </div>

         <!-- Pagination -->
         <div class="swiper-pagination"></div>

      </div>

   </section>



   <?php include 'components/footer.php'; ?>

   <script src="https://unpkg.com/swiper@8/swiper-bundle.min.js"></script>

   <script src="js/script.js"></script>

   <script>

      var swiper = new Swiper(".team-slider", {
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