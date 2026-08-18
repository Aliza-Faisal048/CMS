<?php
session_start();
include "../connection.php";
$user_id = $_SESSION["user_id"];

$query = "SELECT profile_picture FROM user_table WHERE id='$user_id'";

$run = mysqli_query($conn, $query);

$user = mysqli_fetch_assoc($run);

$profile_picture = $user["profile_picture"];

?>
<div class="sidebar">

    <div class="sidebar-header">

        <h4>
            Admin Panel
        </h4>

        <p>
            Complaint Management
        </p>

    </div>
 <!-- PROFILE -->

    <div class="sidebar-profile">

        <img
            src="../uploads/profiles/<?php echo $profile_picture; ?>"
            alt="Profile Picture">
        <h6>
            <?php echo $_SESSION["email"]; ?>
        </h6>

        <small>
            <?php echo ucfirst($_SESSION["role"]); ?>
        </small>

    </div>

    <div class="sidebar-menu">

        <a href="dashboard.php">

            <i class="bi bi-speedometer2"></i>

            <span>
                Dashboard
            </span>

        </a>


        <a href="all_complaints.php">

            <i class="bi bi-file-text"></i>

            <span>
                All Complaints
            </span>

        </a>

    </div>

</div>