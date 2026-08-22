<?php

session_start();

include "../connection.php";


/* =========================================
   ADMIN ACCESS CONTROL
   ========================================= */

if (
    !isset($_SESSION["user_id"]) ||
    !isset($_SESSION["role"]) ||
    $_SESSION["role"] !== "hr_admin"
) {

    header("Location: ../login.php");
    exit();

}


/* =========================================
   TOTAL COMPLAINTS
   ========================================= */

$query = "
    SELECT COUNT(*) AS total
    FROM complaints
";

$run = mysqli_query($conn, $query);

$row = mysqli_fetch_assoc($run);

$total_complaints = $row["total"];


/* =========================================
   PENDING COMPLAINTS
   ========================================= */

$query = "
    SELECT COUNT(*) AS total
    FROM complaints
    WHERE status = 'Pending'
";

$run = mysqli_query($conn, $query);

$row = mysqli_fetch_assoc($run);

$pending_complaints = $row["total"];


/* =========================================
   IN PROGRESS COMPLAINTS
   ========================================= */

$query = "
    SELECT COUNT(*) AS total
    FROM complaints
    WHERE status = 'In Progress'
";

$run = mysqli_query($conn, $query);

$row = mysqli_fetch_assoc($run);

$in_progress_complaints = $row["total"];


/* =========================================
   RESOLVED COMPLAINTS
   ========================================= */

$query = "
    SELECT COUNT(*) AS total
    FROM complaints
    WHERE status = 'Resolved'
";

$run = mysqli_query($conn, $query);

$row = mysqli_fetch_assoc($run);

$resolved_complaints = $row["total"];


/* =========================================
   UNSERVICEABLE COMPLAINTS
   ========================================= */

$query = "
    SELECT COUNT(*) AS total
    FROM complaints
    WHERE status = 'Unserviceable'
";

$run = mysqli_query($conn, $query);

$row = mysqli_fetch_assoc($run);

$unserviceable_complaints = $row["total"];



/* =========================================
   CHART DATA
   ========================================= */


/* ---------- ROLE ---------- */

$role_query = "
    SELECT role, COUNT(*) AS total
    FROM complaints
    GROUP BY role
";

$role_run = mysqli_query($conn, $role_query);

$role_labels = [];
$role_data = [];

while ($row = mysqli_fetch_assoc($role_run)) {

    $role_labels[] = ucfirst($row["role"]);
    $role_data[] = $row["total"];

}


/* ---------- CATEGORY ---------- */

$category_query = "
    SELECT c_category, COUNT(*) AS total
    FROM complaints
    GROUP BY c_category
";

$category_run = mysqli_query($conn, $category_query);

$category_labels = [];
$category_data = [];

while ($row = mysqli_fetch_assoc($category_run)) {

    $category_labels[] = $row["c_category"];
    $category_data[] = $row["total"];

}


/* ---------- STATUS ---------- */

$status_query = "
    SELECT status, COUNT(*) AS total
    FROM complaints
    WHERE status IS NOT NULL
    AND status != ''
    GROUP BY status
";

$status_run = mysqli_query($conn, $status_query);

$status_labels = [];
$status_data = [];

while ($row = mysqli_fetch_assoc($status_run)) {

    $status_labels[] = $row["status"];
    $status_data[] = $row["total"];

}



/* =========================================
   RECENT COMPLAINTS
   ========================================= */

$recent_query = "
    SELECT

        c.id,
        c.complaint_code,
        c.email,
        c.role,
        c.c_category,
        c.asset_id,
        c.status,
        c.assigned_to,

        GROUP_CONCAT(
            cp.problem_detail
            SEPARATOR '|||'
        ) AS problems

    FROM complaints c

    LEFT JOIN complaint_problems cp
        ON c.id = cp.complaint_id

    GROUP BY c.id

    ORDER BY c.id DESC

    LIMIT 5
";

$recent_run = mysqli_query(
    $conn,
    $recent_query
);


/* =========================================
   HEADER + SIDEBAR
   ========================================= */

include "admin_header.php";
include "admin_sidebar.php";

?>


<!-- =========================================
     CHART STYLING
========================================== -->

<style>

.chart-card {

    background: #ffffff;

    border-radius: 15px;

    padding: 20px;

    height: 400px;

}

.chart-container {

    position: relative;

    height: 300px;

    width: 100%;

    margin-top: 15px;

}

</style>



<div class="main-content">


    <!-- =====================================
         TOP HEADER
    ====================================== -->

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



    <!-- =====================================
         WELCOME
    ====================================== -->

    <div class="content-card mb-4">

        <h2>

            Welcome, <?php echo htmlspecialchars($name); ?>

            <i class="bi bi-person-badge fs-3"></i>

        </h2>

        <p class="mb-0">

            Manage and monitor student and teacher
            complaints from one place.

        </p>

    </div>



<!-- STATISTICS -->

    <div class="row g-4">


            <!-- TOTAL -->

        <div class="col-md-3">

            <a
                href="all_complaints.php"
                class="text-decoration-none text-dark"
            >

                <div class="stat-card">

                    <div class="d-flex
                                justify-content-between
                                align-items-center">

                        <div>

                            <span class="text-muted mb-1 d-block">
                                Total Complaints
                            </span>

                            <h4>
                                <?php echo $total_complaints; ?>
                            </h4>

                        </div>

                        <div class="stat-icon">

                            <i class="bi bi-file-text"></i>

                        </div>

                    </div>

                </div>

            </a>

        </div>
        <!-- PENDING -->

        <div class="col-md-3">

            <a
                href="pending_complaint.php"
                class="text-decoration-none text-dark"
            >

                <div class="stat-card">

                    <div class="d-flex
                                justify-content-between
                                align-items-center">

                        <div>

                            <span class="text-muted mb-1 d-block">
                                Pending
                            </span>

                            <h4>
                                <?php echo $pending_complaints; ?>
                            </h4>

                        </div>

                        <div class="stat-icon">

                            <i class="bi bi-clock"></i>

                        </div>

                    </div>

                </div>

            </a>

        </div>
        <!-- IN PROGRESS -->

        <div class="col-md-3">

            <a
                href="in_progress_complaint.php"
                class="text-decoration-none text-dark"
            >

                <div class="stat-card">

                    <div class="d-flex
                                justify-content-between
                                align-items-center">

                        <div>

                            <span class="text-muted mb-1 d-block">
                                In Progress
                            </span>

                            <h4>
                                <?php echo $in_progress_complaints; ?>
                            </h4>

                        </div>

                        <div class="stat-icon">

                            <i class="bi bi-arrow-repeat"></i>

                        </div>

                    </div>

                </div>

            </a>

        </div>
        <!-- RESOLVED -->

        <div class="col-md-3">

            <a
                href="resolved_complaints.php"
                class="text-decoration-none text-dark"
            >

                <div class="stat-card">

                    <div class="d-flex
                                justify-content-between
                                align-items-center">

                        <div>

                            <span class="text-muted mb-1 d-block">
                                Resolved
                            </span>

                            <h4>
                                <?php echo $resolved_complaints; ?>
                            </h4>

                        </div>

                        <div class="stat-icon">

                            <i class="bi bi-check-circle"></i>

                        </div>

                    </div>

                </div>

            </a>

        </div>
        <!-- UNSERVICEABLE -->

        <div class="col-md-3">

            <a
                href="unserviceable_complaints.php"
                class="text-decoration-none text-dark"
            >

                <div class="stat-card">

                    <div class="d-flex
                                justify-content-between
                                align-items-center">

                        <div>

                            <span class="text-muted mb-1 d-block">
                                Unserviceable
                            </span>

                            <h4>
                                <?php echo $unserviceable_complaints; ?>
                            </h4>

                        </div>

                        <div class="stat-icon">

                            <i class="bi bi-exclamation-triangle"></i>

                        </div>

                    </div>

                </div>

            </a>

        </div>

    <!-- =====================================
         CHARTS
    ====================================== -->

    <div class="row g-4 mt-1">


        <!-- ROLE CHART -->

        <div class="col-md-4">

            <div class="chart-card">

                <h5 class="mb-1">

                    Complaints by Role

                </h5>

                <small class="text-muted">

                    Students vs Teachers

                </small>


                <div class="chart-container">

                    <canvas id="roleChart"></canvas>

                </div>

            </div>

        </div>



        <!-- CATEGORY CHART -->

        <div class="col-md-4">

            <div class="chart-card">

                <h5 class="mb-1">

                    Complaints by Category

                </h5>

                <small class="text-muted">

                    Hardware, Software and Network

                </small>


                <div class="chart-container">

                    <canvas id="categoryChart"></canvas>

                </div>

            </div>

        </div>



        <!-- STATUS CHART -->

        <div class="col-md-4">

            <div class="chart-card">

                <h5 class="mb-1">

                    Complaints by Status

                </h5>

                <small class="text-muted">

                    Current complaint status

                </small>


                <div class="chart-container">

                    <canvas id="statusChart"></canvas>

                </div>

            </div>

        </div>


    </div>



    <!-- =====================================
         RECENT COMPLAINTS
    ====================================== -->

    <div class="content-card mt-4">


        <div
            class="
                d-flex
                justify-content-between
                align-items-center
                mb-3
            "
        >

            <div>

                <h5 class="mb-1">

                    Recent Complaints

                </h5>

                <small class="text-muted">

                    Latest complaints submitted by users

                </small>

            </div>


            <a
                href="all_complaints.php"
                class="btn btn-primary btn-sm"
            >

                View All

                <i class="bi bi-arrow-right ms-1"></i>

            </a>

        </div>



        <div class="table-responsive">

            <table
                class="
                    table
                    table-bordered
                    table-hover
                    align-middle
                "
            >

                <thead>

                    <tr>

                        <th>
                            Complaint ID
                        </th>

                        <th>
                            Submitted By
                        </th>

                        <th>
                            Category
                        </th>

                        <th>
                            Problem
                        </th>

                        <th>
                            Assigned To
                        </th>

                        <th>
                            Status
                        </th>

                    </tr>

                </thead>


                <tbody>


                <?php

                if (
                    $recent_run &&
                    mysqli_num_rows($recent_run) > 0
                ) {

                    while (
                        $row =
                        mysqli_fetch_assoc($recent_run)
                    ) {

                        $status =
                            $row["status"];


                        /* =========================
                           STATUS CLASS
                        ========================== */

                        if (
                            $status === "Pending"
                        ) {

                            $status_class =
                                "status-pending";

                        }

                        elseif (
                            $status === "In Progress"
                        ) {

                            $status_class =
                                "status-progress";

                        }

                        elseif (
                            $status === "Resolved"
                        ) {

                            $status_class =
                                "status-resolved";

                        }

                        elseif (
                            $status === "Unserviceable"
                        ) {

                            $status_class =
                                "status-unserviceable";

                        }

                        else {
                            $status = "Unassigned";
                            $status_class = "text-muted";
                        }

                ?>


                    <tr>


                        <!-- COMPLAINT ID -->

                        <td>

                            <strong>

                                <?php

                                echo htmlspecialchars(
                                    $row["complaint_code"]
                                    ?: $row["id"]
                                );

                                ?>

                            </strong>

                        </td>



                        <!-- SUBMITTED BY -->

                        <td>

                            <?php

                            echo htmlspecialchars(
                                $row["email"]
                            );

                            ?>

                            <br>

                            <small class="text-muted">

                                <?php

                                echo htmlspecialchars(
                                    ucfirst(
                                        $row["role"]
                                    )
                                );

                                ?>

                            </small>

                        </td>



                        <!-- CATEGORY -->

                        <td>

                            <?php

                            echo htmlspecialchars(
                                $row["c_category"]
                            );

                            ?>

                        </td>



                        <!-- PROBLEM -->

                        <td>

                            <?php

                            if (
                                !empty(
                                    $row["problems"]
                                )
                            ) {

                                $problem_list =
                                    explode(
                                        "|||",
                                        $row["problems"]
                                    );


                                foreach (
                                    $problem_list
                                    as $problem
                                ) {

                            ?>

                                <div class="mb-1">

                                    <i
                                        class="
                                            bi
                                            bi-dot
                                        "
                                    ></i>

                                    <?php

                                    echo htmlspecialchars(
                                        trim($problem)
                                    );

                                    ?>

                                </div>

                            <?php

                                }

                            }

                            else {

                            ?>

                                <span
                                    class="text-muted"
                                >

                                    No problem specified

                                </span>

                            <?php

                            }

                            ?>

                        </td>



                        <!-- ASSIGNED TO -->

                        <td>

                            <?php

                            if (
                                !empty(
                                    $row["assigned_to"]
                                )
                            ) {

                                echo htmlspecialchars(
                                    $row["assigned_to"]
                                );

                            }

                            else {

                            ?>

                                <span
                                    class="text-muted"
                                >

                                    Unassigned

                                </span>

                            <?php

                            }

                            ?>

                        </td>



                        <!-- STATUS -->

                        <td>

                            <span
                                class="
                                    status-badge
                                    <?php
                                    echo $status_class;
                                    ?>
                                "
                            >

                                <?php

                                echo htmlspecialchars(
                                    $status
                                );

                                ?>

                            </span>

                        </td>


                    </tr>


                <?php

                    }

                }

                else {

                ?>

                    <tr>

                        <td
                            colspan="6"
                            class="text-center py-5"
                        >

                            <i
                                class="
                                    bi
                                    bi-inbox
                                    fs-1
                                    text-muted
                                "
                            ></i>

                            <p class="mt-3 mb-0">

                                No complaints found.

                            </p>

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
========================================== -->

<script src="../js/chart.umd.min.js"></script>



<script>

/* =========================================
   ROLE CHART
========================================= */

new Chart(

    document.getElementById("roleChart"),

    {

        type: "bar",

        data: {

            labels:
                <?php
                echo json_encode($role_labels);
                ?>,

            datasets: [

                {

                    label: "Complaints",

                    data:
                        <?php
                        echo json_encode($role_data);
                        ?>,

                    backgroundColor: [

                        "#6f42c1",
                        "#0d6efd"

                    ],

                    borderRadius: 8,

                    borderWidth: 0

                }

            ]

        },

        options: {

            responsive: true,

            maintainAspectRatio: false,

            scales: {

                y: {

                    beginAtZero: true,

                    ticks: {

                        precision: 0

                    },

                    title: {

                        display: true,

                        text: "Number of Complaints"

                    }

                },

                x: {

                    title: {

                        display: true,

                        text: "Role"

                    }

                }

            },

            plugins: {

                legend: {

                    display: false

                }

            }

        }

    }

);

/* =========================================
   CATEGORY CHART
========================================= */

new Chart(

    document.getElementById("categoryChart"),

    {

        type: "pie",

        data: {

            labels:
                <?php
                echo json_encode($category_labels);
                ?>,

            datasets: [

                {

                    data:
                        <?php
                        echo json_encode($category_data);
                        ?>,

                    backgroundColor: [

                        "#dc3545",
                        "#ffc107",
                        "#198754"

                    ],

                    borderWidth: 0

                }

            ]

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

    }

);



/* =========================================
   STATUS CHART
========================================= */

new Chart(

    document.getElementById("statusChart"),

    {

        type: "doughnut",

        data: {

            labels:
                <?php
                echo json_encode($status_labels);
                ?>,

            datasets: [

                {

                    data:
                        <?php
                        echo json_encode($status_data);
                        ?>,

                    backgroundColor: [

                        "#ffc107",
                        "#0d6efd",
                        "#198754",
                        "#dc3545",
                        "#6c757d"

                    ],

                    borderWidth: 0

                }

            ]

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

    }

);

</script>



<?php

include "admin_footer.php";

?>