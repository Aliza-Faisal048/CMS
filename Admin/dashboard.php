<?php

session_start();


if (!isset($_SESSION["role"]) || $_SESSION["role"] !== "admin") {

    echo "Access denied";
    exit();

}
else {
    echo "Access successful";
}

include "../connection.php";

include "admin_header.php";
include "admin_sidebar.php";


// Total complaints

$query = "SELECT COUNT(*) AS total
          FROM complaints";

$run = mysqli_query($conn, $query);

$row = mysqli_fetch_assoc($run);

$total_complaints = $row["total"];


// Pending complaints

$query = "SELECT COUNT(*) AS pending
          FROM complaints
          WHERE status='Pending'";

$run = mysqli_query($conn, $query);

$row = mysqli_fetch_assoc($run);

$pending_complaints = $row["pending"];

// Resolved complaints

$query = "SELECT COUNT(*) AS resolved
          FROM complaints
          WHERE status='Resolved'";

$run = mysqli_query($conn, $query);

$row = mysqli_fetch_assoc($run);

$resolved_complaints = $row["resolved"];

?>

<div class="main-content">

    <!-- TOP HEADER -->

    <div class="top-header">

        <div>

            <h4 class="mb-1">
                Admin Dashboard
            </h4>

            <small class="text-muted">
                Complaint Management System
            </small>

        </div>

        <div>

            <i class="bi bi-person-circle fs-3"></i>

        </div>

    </div>


    <!-- WELCOME -->

    <div class="content-card">

        <h2>
            Welcome, Admin <i class="bi bi-person-badge fs-3"></i>
        </h2>

        <p>
            Manage and monitor student and teacher complaints.
        </p>

    </div>


    <!-- STATISTICS -->

    <div class="row g-4">

        <!-- Total -->

        <div class="col-md-4">

            <div class="stat-card">

                <div class="d-flex justify-content-between align-items-center">

                    <div>

                        <p class="text-muted mb-1">
                            Total Complaints
                        </p>

                        <h2>
                            <?php echo $total_complaints; ?>
                        </h2>

                    </div>

                    <div class="stat-icon">

                        <i class="bi bi-file-text"></i>

                    </div>

                </div>

            </div>

        </div>


        <!-- Pending -->

        <div class="col-md-4">

            <div class="stat-card">

                <div class="d-flex justify-content-between align-items-center">

                    <div>

                        <p class="text-muted mb-1">
                            Pending
                        </p>

                        <h2>
                            <?php echo $pending_complaints; ?>
                        </h2>

                    </div>

                    <div class="stat-icon">

                        <i class="bi bi-clock"></i>

                    </div>

                </div>

            </div>

        </div>


        <!-- Resolved -->

        <div class="col-md-4">

            <div class="stat-card">

                <div class="d-flex justify-content-between align-items-center">

                    <div>

                        <p class="text-muted mb-1">
                            Resolved
                        </p>

                        <h2>
                            <?php echo $resolved_complaints; ?>
                        </h2>

                    </div>

                    <div class="stat-icon">

                        <i class="bi bi-check-circle"></i>

                    </div>

                </div>

            </div>

        </div>

    </div>

</div>


<?php

include "admin_footer.php";

?>