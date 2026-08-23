<?php
session_start();
include "../connection.php";

// Current page name
$current_page = basename($_SERVER['PHP_SELF']);
$user_id = $_SESSION["user_id"];

$query = "SELECT profile_picture, name FROM user_table WHERE id='$user_id'";

$run = mysqli_query($conn, $query);

$user = mysqli_fetch_assoc($run);

$profile_picture = $user["profile_picture"];
$name = $user["name"];
?>

<div class="sidebar">

    <!-- =========================
         ADMIN PROFILE
         ========================= -->

    <div class="sidebar-profile">

        <?php if (!empty($_SESSION["profile_picture"])) { ?>

            <img
                src="../uploads/<?php echo htmlspecialchars($_SESSION["profile_picture"]); ?>"
                alt="Profile Picture">

        <?php } else { ?>

            <div class="profile-placeholder">
                <i class="bi bi-person-fill"></i>
            </div>

        <?php } ?>


        <!-- NAME -->

        <h6>
            <?php echo htmlspecialchars($name); ?>
        </h6>


        <!-- ROLE -->

        <small>
            <?php
            echo htmlspecialchars(
                $_SESSION["role"] ?? "Admin"
            );
            ?>
        </small>

    </div>


    <!-- =========================
         SIDEBAR HEADER
         ========================= -->

    <div class="sidebar-header">

        <h4>
            Admin Panel
        </h4>

        <p>
            Complaint Management
        </p>

    </div>


    <!-- =========================
         SIDEBAR MENU
         ========================= -->

    <div class="sidebar-menu">


        <!-- DASHBOARD -->

        <a
            href="dashboard.php"
            class="<?php echo ($current_page == 'dashboard.php') ? 'active' : ''; ?>">

            <i class="bi bi-speedometer2"></i>

            <span>
                Dashboard
            </span>

        </a>


        <!-- ALL COMPLAINTS -->

        <a
            href="all_complaints.php"
            class="<?php echo ($current_page == 'all_complaints.php') ? 'active' : ''; ?>">

            <i class="bi bi-file-text"></i>

            <span>
                All Complaints
            </span>

        </a>

        <!-- PENDING COMPLAINTS -->

        <a
            href="pending_complaint.php"
            class="<?php echo ($current_page == 'pending_complaint.php') ? 'active' : ''; ?>">

            <i class="bi bi-clock"></i>

            <span>
                Pending Complaints
            </span>

        </a>

        <!-- IN PROGRESS COMPLAINTS -->

        <a
            href="in_progress_complaint.php"
            class="<?php echo ($current_page == 'in_progress_complaint.php') ? 'active' : ''; ?>">

            <i class="bi bi-gear"></i>

            <span>
                In Progress Complaints
            </span>

        </a>

        <!-- RESOLVED COMPLAINTS -->

        <a
            href="resolved_complaints.php"
            class="<?php echo ($current_page == 'resolved_complaints.php') ? 'active' : ''; ?>">

            <i class="bi bi-file-check"></i>

            <span>
                Resolved Complaints
            </span>

        </a>

        <!-- UNSERVICEABLE COMPLAINTS -->

        <a
            href="unserviceable_complaints.php"
            class="<?php echo ($current_page == 'unserviceable_complaints.php') ? 'active' : ''; ?>">

            <i class="bi bi-x-circle"></i>

            <span>
                Unserviceable Complaints
            </span>

        </a>


        <!-- LOGOUT -->

        <a href="../logout.php">

            <i class="bi bi-box-arrow-right"></i>

            <span>
                Logout
            </span>

        </a>


    </div>

</div>

<div class="sidebar-overlay" id="sidebarOverlay"></div>