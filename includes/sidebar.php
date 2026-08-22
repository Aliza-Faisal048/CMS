<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

include "../connection.php";

$user_id = $_SESSION["user_id"];

$query = "SELECT profile_picture, name
          FROM user_table
          WHERE id='$user_id'";

$run = mysqli_query($conn, $query);

$user = mysqli_fetch_assoc($run);

$profile_picture = $user["profile_picture"];
$current_page = basename($_SERVER['PHP_SELF']);
$name = $user["name"];
?>

<aside class="sidebar">

    <!-- SIDEBAR HEADER -->

    <div class="sidebar-header">

        <div class="logo">

            <i class="bi bi-chat-square-text"></i>

            <span>CMS USER</span>

        </div>

    </div>


    <!-- PROFILE -->

    <div class="sidebar-profile">

        <?php if (!empty($profile_picture)) { ?>

            <img
                src="../uploads/profiles/<?php echo htmlspecialchars($profile_picture); ?>"
                alt="Profile Picture">

        <?php } else { ?>

            <div class="profile-placeholder">

                <i class="bi bi-person"></i>

            </div>

        <?php } ?>


        <h6>
            <?php echo htmlspecialchars($name); ?>
        </h6>

        <small>
            <?php echo ucfirst($_SESSION["role"]); ?>
        </small>

    </div>


    <!-- MENU -->

    <div class="sidebar-menu">


        <!-- DASHBOARD -->

        <a
            href="dashboard.php"
            class="<?php
                echo ($current_page == 'dashboard.php')
                    ? 'active'
                    : '';
            ?>"
        >

            <i class="bi bi-speedometer2"></i>

            <span>
                Dashboard
            </span>
        </a>


        <a
            href="submit_complaint.php"
            class="<?php
                echo ($current_page == 'submit_complaint.php')
                    ? 'active'
                    : '';
            ?>"
        >

            <i class="bi bi-plus-circle"></i>

            <span>
                Launch Complaint
            </span>

        </a>


        <a
            href="track_complaints.php"
            class="<?php
                echo ($current_page == 'track_complaints.php')
                    ? 'active'
                    : '';
            ?>"
        >

            <i class="bi bi-clock-history"></i>

            <span>
                Track Complaints
            </span>

        </a>


         <a
            href="all_complaints.php"
            class="<?php
                echo ($current_page == 'all_complaints.php')
                    ? 'active'
                    : '';
            ?>"
        >

            <i class="bi bi-file-text"></i>

            <span>
                All Complaints
            </span>

        </a>

    </div>

</aside>