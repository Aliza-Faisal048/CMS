<?php

session_start();

include "../connection.php";

include "../includes/header.php";
include "../includes/sidebar.php";

$email = $_SESSION["email"];


/* =========================================
   COMPLAINT STATISTICS
   ========================================= */

// Total

$query = "SELECT COUNT(*) AS total
          FROM complaints
          WHERE email='$email'";

$run = mysqli_query($conn, $query);
$row = mysqli_fetch_assoc($run);

$total_complaints = $row["total"];


// Pending

$query = "SELECT COUNT(*) AS pending
          FROM complaints
          WHERE email='$email'
          AND status='Pending'";

$run = mysqli_query($conn, $query);
$row = mysqli_fetch_assoc($run);

$pending_complaints = $row["pending"];


// In Progress

$query = "SELECT COUNT(*) AS in_progress
          FROM complaints
          WHERE email='$email'
          AND status='In Progress'";

$run = mysqli_query($conn, $query);
$row = mysqli_fetch_assoc($run);

$in_progress_complaints = $row["in_progress"];


// Resolved

$query = "SELECT COUNT(*) AS resolved
          FROM complaints
          WHERE email='$email'
          AND status='Resolved'";

$run = mysqli_query($conn, $query);
$row = mysqli_fetch_assoc($run);

$resolved_complaints = $row["resolved"];


// Unserviceable

$query = "SELECT COUNT(*) AS unserviceable
          FROM complaints
          WHERE email='$email'
          AND status='Unserviceable'";

$run = mysqli_query($conn, $query);
$row = mysqli_fetch_assoc($run);

$unserviceable_complaints = $row["unserviceable"];


/* =========================================
   CATEGORY STATISTICS
   ========================================= */

$hardware = 0;
$software = 0;
$network = 0;

$query = "SELECT c_category, COUNT(*) AS total
          FROM complaints
          WHERE email='$email'
          GROUP BY c_category";

$run = mysqli_query($conn, $query);

while ($row = mysqli_fetch_assoc($run)) {

    if ($row["c_category"] == "Hardware") {

        $hardware = $row["total"];

    }

    elseif ($row["c_category"] == "Software") {

        $software = $row["total"];

    }

    elseif ($row["c_category"] == "Network") {

        $network = $row["total"];

    }

}


/* =========================================
   RECENT COMPLAINTS
   ========================================= */

$query = "
    SELECT
        c.id,
        c.complaint_code,
        c.c_category,
        c.status,

        GROUP_CONCAT(
            cp.problem_detail
            SEPARATOR ', '
        ) AS problems

    FROM complaints c

    LEFT JOIN complaint_problems cp
        ON c.id = cp.complaint_id

    WHERE c.email = '$email'

    GROUP BY c.id

    ORDER BY c.id DESC

    LIMIT 5
";

$run = mysqli_query($conn, $query);

?>


<div class="main-content">


    <!-- TOP HEADER -->

    <div class="top-header">

        <div>

            <h4 class="mb-1">
                Student Dashboard
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

            Welcome, Student
            <i class="bi bi-backpack fs-3"></i>

        </h2>

        <p class="text-muted">

            Track and manage your IT complaints from one place.

        </p>

    </div>



    <!-- STATISTICS -->

    <div class="row g-4">


        <!-- TOTAL -->

        <div class="col-md-3">

            <div class="stat-card">

                <div class="d-flex
                            justify-content-between
                            align-items-center">

                    <div>

                        <a
                            href="all_complaints.php"
                            class="text-decoration-none text-muted mb-1">

                            Total Complaints

                        </a>

                        <h4>
                            <?php echo $total_complaints; ?>
                        </h4>

                    </div>

                    <div class="stat-icon">

                        <i class="bi bi-file-text"></i>

                    </div>

                </div>

            </div>

        </div>



        <!-- PENDING -->

        <div class="col-md-3">

            <div class="stat-card">

                <div class="d-flex
                            justify-content-between
                            align-items-center">

                    <div>

                        <a
                            href="pending_complaint.php"
                            class="text-decoration-none text-muted mb-1">

                            Pending

                        </a>

                        <h4>
                            <?php echo $pending_complaints; ?>
                        </h4>

                    </div>

                    <div class="stat-icon">

                        <i class="bi bi-clock"></i>

                    </div>

                </div>

            </div>

        </div>



        <!-- IN PROGRESS -->

        <div class="col-md-3">

            <div class="stat-card">

                <div class="d-flex
                            justify-content-between
                            align-items-center">

                    <div>

                        <a
                            href="in_progress_complaint.php"
                            class="text-decoration-none text-muted mb-1">

                            In Progress

                        </a>

                        <h4>
                            <?php echo $in_progress_complaints; ?>
                        </h4>

                    </div>

                    <div class="stat-icon">

                        <i class="bi bi-arrow-repeat"></i>

                    </div>

                </div>

            </div>

        </div>



        <!-- RESOLVED -->

        <div class="col-md-3">

            <div class="stat-card">

                <div class="d-flex
                            justify-content-between
                            align-items-center">

                    <div>

                        <a
                            href="resolved_complaints.php"
                            class="text-decoration-none text-muted mb-1">

                            Resolved

                        </a>

                        <h4>
                            <?php echo $resolved_complaints; ?>
                        </h4>

                    </div>

                    <div class="stat-icon">

                        <i class="bi bi-check-circle"></i>

                    </div>

                </div>

            </div>

        </div>



        <!-- UNSERVICEABLE -->

        <div class="col-md-3">

            <div class="stat-card">

                <div class="d-flex
                            justify-content-between
                            align-items-center">

                    <div>

                        <a
                            href="unserviceable_complaints.php"
                            class="text-decoration-none text-muted mb-1">

                            Unserviceable

                        </a>

                        <h4>
                            <?php echo $unserviceable_complaints; ?>
                        </h4>

                    </div>

                    <div class="stat-icon">

                        <i class="bi bi-exclamation-triangle"></i>

                    </div>

                </div>

            </div>

        </div>

    </div>



    <!-- CHARTS -->

    <div class="row g-4 mt-1">


        <!-- STATUS CHART -->

        <div class="col-md-6">

            <div class="content-card">

                <h5 class="mb-3">

                    Complaint Status

                </h5>

                <div class="chart-container" style="height: 300px;">

                    <canvas id="statusChart"></canvas>

                </div>

            </div>

        </div>



        <!-- CATEGORY CHART -->

        <div class="col-md-6">

            <div class="content-card">

                <h5 class="mb-3">

                    Complaint Type

                </h5>

                <div class="chart-container" style="height: 300px;">

                    <canvas id="categoryChart"></canvas>

                </div>

            </div>

        </div>


    </div>



    <!-- RECENT COMPLAINTS -->

    <div class="content-card mt-4">

        <div class="d-flex
                    justify-content-between
                    align-items-center
                    mb-3">

            <h5 class="mb-0">

                Recent Complaints

            </h5>

            <a
                href="all_complaints.php"
                class="btn btn-sm btn-main">

                View All

            </a>

        </div>


        <div class="table-responsive">

            <table class="table align-middle">

                <thead>

                    <tr>

                        <th>
                            Complaint ID
                        </th>

                        <th>
                            Category
                        </th>

                        <th>
                            Problem
                        </th>

                        <th>
                            Status
                        </th>

                    </tr>

                </thead>


                <tbody>

                    <?php

                    if (mysqli_num_rows($run) > 0) {

                        while ($complaint = mysqli_fetch_assoc($run)) {

                    ?>

                        <tr>

                            <td>

                                <?php

                                echo htmlspecialchars(
                                    $complaint["complaint_code"]
                                );

                                ?>

                            </td>


                            <td>

                                <?php

                                echo htmlspecialchars(
                                    $complaint["c_category"]
                                );

                                ?>

                            </td>


                            <td>

                                <?php

                                if (!empty($complaint["problems"])) {

                                    $problem_list = explode(
                                        ", ",
                                        $complaint["problems"]
                                    );

                                    foreach ($problem_list as $problem) {

                                ?>

                                    <div class="mb-1">

                                        <i class="bi bi-dot"></i>

                                        <?php

                                        echo htmlspecialchars(
                                            $problem
                                        );

                                        ?>

                                    </div>

                                <?php

                                    }

                                }

                                else {

                                ?>

                                    <span class="text-muted">

                                        No problem specified

                                    </span>

                                <?php

                                }

                                ?>

                            </td>


                            <td>

                                <?php

                                if (
                                    $complaint["status"]
                                    == "Pending"
                                ) {

                                    echo '<span class="status-badge status-pending">
                                            Pending
                                          </span>';

                                }

                                elseif (
                                    $complaint["status"]
                                    == "In Progress"
                                ) {

                                    echo '<span class="status-badge status-progress">
                                            In Progress
                                          </span>';

                                }

                                elseif (
                                    $complaint["status"]
                                    == "Resolved"
                                ) {

                                    echo '<span class="status-badge status-resolved">
                                            Resolved
                                          </span>';

                                }

                                elseif (
                                    $complaint["status"]
                                    == "Unserviceable"
                                ) {

                                    echo '<span class="status-badge status-unserviceable">
                                            Unserviceable
                                          </span>';

                                }

                                else {
                                    
                                    echo 'Unassigned';
                                }

                                ?>

                            </td>

                        </tr>

                    <?php

                        }

                    }

                    else {

                    ?>

                        <tr>

                            <td
                                colspan="4"
                                class="text-center text-muted py-4">

                                No complaints submitted yet.

                            </td>

                        </tr>

                    <?php

                    }

                    ?>

                </tbody>

            </table>

        </div>

    </div>



</div>



<!-- =========================================
     CHART.JS
     ========================================= -->

<script src="../js/chart.umd.min.js"></script>


<script>

/* =========================================
   STATUS DOUGHNUT CHART
========================================= */

const statusChart =
    document.getElementById("statusChart");


new Chart(statusChart, {

    type: "doughnut",

    data: {

        labels: [

            "Pending",
            "In Progress",
            "Resolved",
            "Unserviceable"

        ],

        datasets: [{

            data: [

                <?php echo $pending_complaints; ?>,

                <?php echo $in_progress_complaints; ?>,

                <?php echo $resolved_complaints; ?>,

                <?php echo $unserviceable_complaints; ?>

            ],

            backgroundColor: [

                "#f59e0b",   // Pending
                "#3b82f6",   // In Progress
                "#22c55e",   // Resolved
                "#ef4444"    // Unserviceable

            ],

            borderWidth: 0

        }]

    },

    options: {

        responsive: true,

        maintainAspectRatio: false,

        plugins: {

            legend: {

                position: "bottom"

            }

        }

    }

});



/* =========================================
   CATEGORY PIE CHART
========================================= */

const categoryChart =
    document.getElementById("categoryChart");


new Chart(categoryChart, {

    type: "pie",

    data: {

        labels: [

            "Hardware",
            "Software",
            "Network"

        ],

        datasets: [{

            data: [

                <?php echo $hardware; ?>,

                <?php echo $software; ?>,

                <?php echo $network; ?>

            ],

            backgroundColor: [

                "#8b5cf6",
                "#ec4899",
                "#06b6d4"

            ],

            borderWidth: 0

        }]

    },

    options: {

        responsive: true,

        maintainAspectRatio: false,

        plugins: {

            legend: {

                position: "bottom"

            }

        }

    }

});

</script>



<?php

include "../includes/footer.php";

?>