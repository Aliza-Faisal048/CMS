<?php

session_start();

include "../connection.php";

include "../includes/header.php";
include "../includes/sidebar.php";


/* =========================================
   LOGIN CHECK
   ========================================= */

if (!isset($_SESSION["user_id"])) {

    header("Location: ../login.php");
    exit();

}


$email = $_SESSION["email"];

$email_safe = mysqli_real_escape_string(
    $conn,
    $email
);


/* =========================================
   GET COMPLAINT ID
   ========================================= */

if (!isset($_GET["id"])) {

    header("Location: track_complaints.php");
    exit();

}


$id = intval($_GET["id"]);


/* =========================================
   GET COMPLAINT
   ========================================= */

$query = "
    SELECT
        c.id,
        c.complaint_code,
        c.email,
        c.c_subject,
        c.c_category,
        c.asset_id,
        c.assigned_to,
        c.c_description,
        c.admin_remarks,
        c.status,
        c.role

    FROM complaints c

    WHERE c.id = '$id'
    AND c.email = '$email_safe'

    LIMIT 1
";


$run = mysqli_query(
    $conn,
    $query
);


if (
    !$run ||
    mysqli_num_rows($run) == 0
) {

    echo "

        <div class='main-content'>

            <div class='alert alert-danger'>

                Complaint not found.

            </div>

        </div>

    ";

    include "../includes/footer.php";

    exit();

}


$complaint =
    mysqli_fetch_assoc($run);


/* =========================================
   GET PROBLEMS
   ========================================= */

$problem_query = "
    SELECT
        problem_detail

    FROM complaint_problems

    WHERE complaint_id = '$id'

    ORDER BY id ASC
";


$problem_run = mysqli_query(
    $conn,
    $problem_query
);


$problems = [];


if ($problem_run) {

    while (
        $problem =
        mysqli_fetch_assoc($problem_run)
    ) {

        $problems[] =
            $problem["problem_detail"];

    }

}

?>


<div class="main-content">


    <!-- =====================================
         TOP HEADER
         ===================================== -->

    <div class="top-header">

        <div>

            <h4 class="mb-1">
                Complaint Details
            </h4>

            <small class="text-muted">
                View your complaint details
            </small>

        </div>


        <div>

            <i class="bi bi-file-text fs-3"></i>

        </div>

    </div>



    <!-- =====================================
         COMPLAINT DETAILS
         ===================================== -->

    <div class="content-card">


        <h4 class="mb-4">

            Complaint #

            <?php

            echo htmlspecialchars(
                $complaint["complaint_code"]
            );

            ?>

        </h4>



        <!-- =================================
             CATEGORY
             ================================= -->

        <div class="mb-4">

            <label class="form-label">
                Category
            </label>


            <input
                type="text"
                class="form-control"

                value="<?php

                    echo htmlspecialchars(
                        $complaint["c_category"]
                    );

                ?>"

                readonly>

        </div>



        <!-- =================================
             PROBLEMS
             ================================= -->

        <div class="mb-4">

            <label class="form-label">
                Problem Details
            </label>


            <?php

            if (count($problems) > 0) {

            ?>

                <div class="problem-list">


                    <?php

                    foreach (
                        $problems
                        as $problem
                    ) {

                    ?>

                        <div class="mb-2">

                            <i
                                class="
                                    bi
                                    bi-check-circle
                                    me-2
                                "
                            ></i>

                            <?php

                            echo htmlspecialchars(
                                $problem
                            );

                            ?>

                        </div>

                    <?php

                    }

                    ?>


                </div>


            <?php

            }

            else {

            ?>

                <div class="text-muted">

                    No problem details available.

                </div>

            <?php

            }

            ?>

        </div>



        <!-- =================================
             DESCRIPTION
             ================================= -->

        <div class="mb-4">

            <label class="form-label">
                Description
            </label>


            <textarea
                class="form-control"
                rows="5"
                readonly><?php

                echo htmlspecialchars(
                    $complaint[
                        "c_description"
                    ]
                );

                ?></textarea>

        </div>



        <!-- =================================
             ASSET
             ================================= -->

        <?php

        if (
            !empty(
                $complaint["asset_id"]
            )
        ) {

        ?>

            <div class="mb-4">

                <label class="form-label">
                    Asset
                </label>


                <input
                    type="text"
                    class="form-control"

                    value="<?php

                        echo htmlspecialchars(
                            $complaint[
                                "asset_id"
                            ]
                        );

                    ?>"

                    readonly>

            </div>

        <?php

        }

        ?>



        <!-- =================================
             STATUS
             ================================= -->

        <div class="mb-4">

            <label class="form-label">
                Status
            </label>


            <?php

            $status =
                $complaint["status"];


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

            else {

                $status_class = "";

            }

            ?>


            <div>

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

            </div>

        </div>



        <!-- =================================
             ADMIN REMARKS
             ================================= -->

        <?php

        if (
            !empty(
                $complaint["admin_remarks"]
            )
        ) {

        ?>

            <div class="mb-4">

                <label class="form-label">
                    Admin Remarks
                </label>


                <textarea
                    class="form-control"
                    rows="4"
                    readonly><?php

                    echo htmlspecialchars(
                        $complaint[
                            "admin_remarks"
                        ]
                    );

                    ?></textarea>

            </div>

        <?php

        }

        ?>

        <a
            href="dashboard.php"
            class="btn btn-secondary"
        >

            <i class="bi bi-arrow-left me-1"></i>

            Back to Dashboard

        </a>


    </div>

</div>


<?php

include "../includes/footer.php";

?>