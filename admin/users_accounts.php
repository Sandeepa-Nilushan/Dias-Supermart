<?php

include '../components/connect.php';

session_start();

$admin_id = $_SESSION['admin_id'];

if (!isset($admin_id)) {
   header('location:admin_login.php');
}

if (isset($_GET['toggle_block'])) {
   $user_id = $_GET['toggle_block'];
   // Retrieve the current status of the user
   $select_status = $conn->prepare("SELECT status FROM `users` WHERE id = ?");
   $select_status->execute([$user_id]);
   $status = $select_status->fetchColumn();

   // Toggle the status value
   $new_status = ($status == 0) ? 1 : 0;

   // Update the status value in the database
   $update_status = $conn->prepare("UPDATE `users` SET status = ? WHERE id = ?");
   $update_status->execute([$new_status, $user_id]);

   header('location:users_accounts.php');
}

if (isset($_GET['toggle_unblock'])) {
   $user_id = $_GET['toggle_unblock'];
   // Retrieve the current status of the user
   $select_status = $conn->prepare("SELECT status FROM `users` WHERE id = ?");
   $select_status->execute([$user_id]);
   $status = $select_status->fetchColumn();

   // Toggle the status value
   $new_status = ($status == 0) ? 1 : 0;

   // Update the status value in the database
   $update_status = $conn->prepare("UPDATE `users` SET status = ? WHERE id = ?");
   $update_status->execute([$new_status, $user_id]);

   header('location:users_accounts.php');
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Users Accounts</title>

   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
   <link rel="stylesheet" href="../css/admin_style.css">

</head>

<body>

   <?php include '../components/admin_header.php'; ?>

   <section class="accounts">

      <h1 class="heading">User Accounts</h1>

      <div class="unblocked-users">
         <h1>Unblocked Users</h1>
         <div class="box-container">
            <?php
            $select_accounts = $conn->prepare("SELECT * FROM `users` WHERE status = 0");
            $select_accounts->execute();
            if ($select_accounts->rowCount() > 0) {
               while ($fetch_accounts = $select_accounts->fetch(PDO::FETCH_ASSOC)) {
                  ?>
                  <div class="box">
                     <p>User ID: <span>
                           <?= $fetch_accounts['id']; ?>
                        </span></p>
                     <p>Username: <span>
                           <?= $fetch_accounts['name']; ?>
                        </span></p>
                     <p>Email: <span>
                           <?= $fetch_accounts['email']; ?>
                        </span></p>
                     <a href="users_accounts.php?toggle_block=<?= $fetch_accounts['id']; ?>" class="delete-btn">Block</a>
                  </div>
                  <?php
               }
            } else {
               echo '<p class="empty">No unblocked users available!</p>';
            }
            ?>
         </div>
      </div>
<br><br><br><br>
      <div class="blocked-users">
         <h1>Blocked Users</h1>
         <div class="box-container">
            <?php
            $select_accounts = $conn->prepare("SELECT * FROM `users` WHERE status = 1");
            $select_accounts->execute();
            if ($select_accounts->rowCount() > 0) {
               while ($fetch_accounts = $select_accounts->fetch(PDO::FETCH_ASSOC)) {
                  ?>
                  <div class="box">
                     <p>User ID: <span>
                           <?= $fetch_accounts['id']; ?>
                        </span></p>
                     <p>Username: <span>
                           <?= $fetch_accounts['name']; ?>
                        </span></p>
                     <p>Email: <span>
                           <?= $fetch_accounts['email']; ?>
                        </span></p>
                     <a href="users_accounts.php?toggle_unblock=<?= $fetch_accounts['id']; ?>" class="delete-btn">Unblock</a>
                  </div>
                  <?php
               }
            } else {
               echo '<p class="empty">No blocked users available!</p>';
            }
            ?>
         </div>
      </div>


   </section>

   <script src="../js/admin_script.js"></script>

</body>

</html>