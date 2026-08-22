<?php

session_start();

include "../connection.php";


/* =========================================
   IT STAFF ACCESS CONTROL
========================================= */

if (
    !isset($_SESSION["user_id"]) ||
    !isset($_SESSION["role"]) ||
    $_SESSION["role"] !== "it_staff"
) {

    header("Location: ../login.php");
    exit();

}


/* =========================================
   GET LOGGED-IN IT STAFF
========================================= */

$user_id = intval($_SESSION["user_id"]);


$staff_query = "
    SELECT
        id,
        name,
        email
    FROM user_table
    WHERE id = '$user_id'
    AND role = 'it_staff'
    LIMIT 1
";


$staff_run = mysqli_query(
    $conn,
    $staff_query
);


if (
    !$staff_run ||
    mysqli_num_rows($staff_run) == 0
) {

    session_destroy();

    header("Location: ../login.php");
    exit();

}


$staff = mysqli_fetch_assoc(
    $staff_run
);


$name = $staff["name"];
$email = $staff["email"];


/* =========================================
   SAFE STAFF NAME
========================================= */

$staff_name_safe =
    mysqli_real_escape_string(
        $conn,
        $name
    );


/* =========================================
   TOTAL ASSIGNED COMPLAINTS
========================================= */

$query = "
    SELECT COUNT(*) AS total
    FROM complaints
    WHERE assigned_to = '$staff_name_safe'
";


$run = mysqli_query(
    $conn,
    $query
);


$row = mysqli_fetch_assoc($run);


$total_complaints =
    $row["total"];


/* =========================================
   PENDING ASSIGNED COMPLAINTS
========================================= */

$query = "
    SELECT COUNT(*) AS total
    FROM complaints
    WHERE assigned_to = '$staff_name_safe'
    AND status = 'Pending'
";


$run = mysqli_query(
    $conn,
    $query
);


$row = mysqli_fetch_assoc($run);


$pending_complaints =
    $row["total"];


/* =========================================
   IN PROGRESS ASSIGNED COMPLAINTS
========================================= */

$query = "
    SELECT COUNT(*) AS total
    FROM complaints
    WHERE assigned_to = '$staff_name_safe'
    AND status = 'In Progress'
";


$run = mysqli_query(
    $conn,
    $query
);


$row = mysqli_fetch_assoc($run);


$in_progress_complaints =
    $row["total"];


/* =========================================
   RESOLVED ASSIGNED COMPLAINTS
========================================= */

$query = "
    SELECT COUNT(*) AS total
    FROM complaints
    WHERE assigned_to = '$staff_name_safe'
    AND status = 'Resolved'
";


$run = mysqli_query(
    $conn,
    $query
);


$row = mysqli_fetch_assoc($run);


$resolved_complaints =
    $row["total"];


/* =========================================
   UNSERVICEABLE ASSIGNED COMPLAINTS
========================================= */

$query = "
    SELECT COUNT(*) AS total
    FROM complaints
    WHERE assigned_to = '$staff_name_safe'
    AND status = 'Unserviceable'
";


$run = mysqli_query(
    $conn,
    $query
);


$row = mysqli_fetch_assoc($run);


$unserviceable_complaints =
    $row["total"];


/* =========================================
   STATUS CHART DATA
========================================= */

$status_query = "
    SELECT
        status,
        COUNT(*) AS total
    FROM complaints
    WHERE assigned_to = '$staff_name_safe'
    AND status IS NOT NULL
    AND status != ''
    GROUP BY status
";


$status_run = mysqli_query(
    $conn,
    $status_query
);


$status_labels = [];
$status_data = [];


while (
    $row =
    mysqli_fetch_assoc($status_run)
) {

    $status_labels[] =
        $row["status"];

    $status_data[] =
        $row["total"];

}


/* =========================================
   RECENT ASSIGNED COMPLAINTS
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

    WHERE c.assigned_to = '$staff_name_safe'

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

include "it_staff_header.php";
include "it_staff_sidebar.php";

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

    border: 1px solid #e2e8f0;

    box-shadow:
        0 4px 15px
        rgba(15, 23, 42, 0.04);

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

                IT Staff Dashboard

            </h4>

            <small class="text-muted">

                Manage your assigned complaints

            </small>

        </div>


        <div>

            <i
                class="bi bi-person-workspace fs-3"
            ></i>

        </div>

    </div>



    <!-- =====================================
         WELCOME
    ====================================== -->

    <div class="content-card mb-4">

        <h2>

            Welcome,
            <?php echo htmlspecialchars($name); ?>

            <i
                class="bi bi-person-badge fs-3"
            ></i>

        </h2>

        <p class="mb-0">

            View your assigned complaints,
            update their status, and add remarks.

        </p>

    </div>



    <!-- =====================================
         STATISTICS
    ====================================== -->

    <div class="row g-4">


        <!-- TOTAL -->

        <div class="col-md-3">

            <a
                href="view_complaints.php"
                class="text-decoration-none text-dark"
            >

                <div class="stat-card">

                    <div
                        class="
                            d-flex
                            justify-content-between
                            align-items-center
                        "
                    >

                        <div>

                            <span
                                class="
                                    text-muted
                                    mb-1
                                    d-block
                                "
                            >

                                Total Assigned

                            </span>

                            <h4>

                                <?php
                                echo $total_complaints;
                                ?>

                            </h4>

                        </div>


                        <div class="stat-icon">

                            <i
                                class="bi bi-file-text"
                            ></i>

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

                    <div
                        class="
                            d-flex
                            justify-content-between
                            align-items-center
                        "
                    >

                        <div>

                            <span
                                class="
                                    text-muted
                                    mb-1
                                    d-block
                                "
                            >

                                Pending

                            </span>

                            <h4>

                                <?php
                                echo $pending_complaints;
                                ?>

                            </h4>

                        </div>


                        <div class="stat-icon">

                            <i
                                class="bi bi-clock"
                            ></i>

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

                    <div
                        class="
                            d-flex
                            justify-content-between
                            align-items-center
                        "
                    >

                        <div>

                            <span
                                class="
                                    text-muted
                                    mb-1
                                    d-block
                                "
                            >

                                In Progress

                            </span>

                            <h4>

                                <?php
                                echo $in_progress_complaints;
                                ?>

                            </h4>

                        </div>


                        <div class="stat-icon">

                            <i
                                class="bi bi-arrow-repeat"
                            ></i>

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

                    <div
                        class="
                            d-flex
                            justify-content-between
                            align-items-center
                        "
                    >

                        <div>

                            <span
                                class="
                                    text-muted
                                    mb-1
                                    d-block
                                "
                            >

                                Resolved

                            </span>

                            <h4>

                                <?php
                                echo $resolved_complaints;
                                ?>

                            </h4>

                        </div>


                        <div class="stat-icon">

                            <i
                                class="bi bi-check-circle"
                            ></i>

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

                    <div
                        class="
                            d-flex
                            justify-content-between
                            align-items-center
                        "
                    >

                        <div>

                            <span
                                class="
                                    text-muted
                                    mb-1
                                    d-block
                                "
                            >

                                Unserviceable

                            </span>

                            <h4>

                                <?php
                                echo $unserviceable_complaints;
                                ?>

                            </h4>

                        </div>


                        <div class="stat-icon">

                            <i
                                class="
                                    bi
                                    bi-exclamation-triangle
                                "
                            ></i>

                        </div>

                    </div>

                </div>

            </a>

        </div>


    </div>



    <!-- =====================================
         STATUS CHART
    ====================================== -->

    <div class="row g-4 mt-1">


        <div class="col-md-6">

            <div class="chart-card">

                <h5 class="mb-1">

                    My Complaints by Status

                </h5>

                <small class="text-muted">

                    Current status of complaints assigned
                    to you

                </small>


                <div class="chart-container">

                    <canvas
                        id="statusChart"
                    ></canvas>

                </div>

            </div>

        </div>


        <!-- QUICK INFORMATION -->

        <div class="col-md-6">

            <div class="content-card h-100">

                <h5 class="mb-1">

                    Complaint Summary

                </h5>

                <small class="text-muted">

                    Your current workload

                </small>


                <div class="mt-4">


                    <div
                        class="
                            d-flex
                            justify-content-between
                            align-items-center
                            mb-3
                        "
                    >

                        <span>

                            Pending

                        </span>

                        <strong
                            class="text-warning"
                        >

                            <?php
                            echo $pending_complaints;
                            ?>

                        </strong>

                    </div>


                    <div
                        class="
                            d-flex
                            justify-content-between
                            align-items-center
                            mb-3
                        "
                    >

                        <span>

                            In Progress

                        </span>

                        <strong
                            style="color:#6d28d9;"
                        >

                            <?php
                            echo $in_progress_complaints;
                            ?>

                        </strong>

                    </div>


                    <div
                        class="
                            d-flex
                            justify-content-between
                            align-items-center
                            mb-3
                        "
                    >

                        <span>

                            Resolved

                        </span>

                        <strong
                            class="text-success"
                        >

                            <?php
                            echo $resolved_complaints;
                            ?>

                        </strong>

                    </div>


                    <div
                        class="
                            d-flex
                            justify-content-between
                            align-items-center
                            mb-3
                        "
                    >

                        <span>

                            Unserviceable

                        </span>

                        <strong
                            class="text-danger"
                        >

                            <?php
                            echo $unserviceable_complaints;
                            ?>

                        </strong>

                    </div>


                    <hr>


                    <div
                        class="
                            d-flex
                            justify-content-between
                            align-items-center
                        "
                    >

                        <strong>

                            Total Assigned

                        </strong>

                        <strong>

                            <?php
                            echo $total_complaints;
                            ?>

                        </strong>

                    </div>


                </div>

            </div>

        </div>


    </div>



    <!-- =====================================
         RECENT ASSIGNED COMPLAINTS
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

                    Recent Assigned Complaints

                </h5>

                <small class="text-muted">

                    Latest complaints assigned to you

                </small>

            </div>


            <a
                href="view_complaints.php"
                class="btn btn-primary btn-sm"
            >

                View All

                <i
                    class="
                        bi
                        bi-arrow-right
                        ms-1
                    "
                ></i>

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

                            $status_class =
                                "text-muted";

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

                            <small
                                class="text-muted"
                            >

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
                            colspan="5"
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

                                No assigned complaints found.

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
   STATUS CHART
========================================= */

new Chart(

    document.getElementById("statusChart"),

    {

        type: "doughnut",

        data: {

            labels:
                <?php
                echo json_encode(
                    $status_labels
                );
                ?>,

            datasets: [

                {

                    data:
                        <?php
                        echo json_encode(
                            $status_data
                        );
                        ?>,

                    backgroundColor: [

                        "#ffc107",
                        "#6d28d9",
                        "#198754",
                        "#dc3545"

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

include "it_staff_footer.php";

?>