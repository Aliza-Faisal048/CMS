<?php

session_start();

include "../connection.php";


/* =========================================
   ADMIN LOGIN CHECK
   ========================================= */

if (
    !isset($_SESSION["role"]) ||
    $_SESSION["role"] !== "hr_admin"
) {

    echo "Access denied";
    exit();

}


/* =========================================
   GET COMPLAINT ID
   ========================================= */

$id = isset($_GET["id"])
    ? intval($_GET["id"])
    : 0;


if ($id <= 0) {

    header("Location: all_complaints.php");
    exit();

}


/* =========================================
   GET COMPLAINT
   ========================================= */

$complaint_query = "
    SELECT *
    FROM complaints
    WHERE id = '$id'
    LIMIT 1
";


$complaint_run = mysqli_query(
    $conn,
    $complaint_query
);


if (
    !$complaint_run ||
    mysqli_num_rows($complaint_run) == 0
) {

    echo "Complaint not found.";
    exit();

}


$complaint = mysqli_fetch_assoc(
    $complaint_run
);


/* =========================================
   GET PROBLEMS
   ========================================= */

$problem_query = "
    SELECT problem_detail
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
        $problem_row =
        mysqli_fetch_assoc($problem_run)
    ) {

        $problems[] =
            $problem_row["problem_detail"];

    }

}


/* =========================================
   GET STATUS HISTORY
   ========================================= */

$history_query = "
    SELECT
        id,
        complaint_id,
        status,
        changed_by,
        remarks,
        changed_at
    FROM complaint_status_history
    WHERE complaint_id = '$id'
    ORDER BY changed_at ASC, id ASC
";


$history_run = mysqli_query(
    $conn,
    $history_query
);


/* =========================================
   INCLUDE ADMIN UI
   ========================================= */

include "admin_header.php";
include "admin_sidebar.php";

?>


<div class="main-content">


    <!-- =====================================
         TOP HEADER
    ====================================== -->

    <div class="top-header">

        <div>

            <h4 class="mb-1">
                Complaint Status History
            </h4>

            <small class="text-muted">
                View the complete status timeline of this complaint
            </small>

        </div>

    </div>



    <!-- =====================================
         COMPLAINT SUMMARY
    ====================================== -->

    <div class="content-card mb-4">


        <div class="d-flex
                    justify-content-between
                    align-items-center
                    mb-4">

            <div>

                <h4 class="mb-1">

                    Complaint #

                    <?php

                    echo htmlspecialchars(
                        $complaint["complaint_code"]
                        ?: $complaint["id"]
                    );

                    ?>

                </h4>

                <small class="text-muted">

                    Complaint ID:
                    <?php

                    echo htmlspecialchars(
                        $complaint["id"]
                    );

                    ?>

                </small>

            </div>


            <!-- CURRENT STATUS -->

            <div>

                <?php

                if (
                    $complaint["status"]
                    === "Pending"
                ) {

                ?>

                    <span
                        class="
                            status-badge
                            status-pending
                        "
                    >

                        Pending

                    </span>

                <?php

                }
                elseif (
                    $complaint["status"]
                    === "In Progress"
                ) {

                ?>

                    <span
                        class="
                            status-badge
                            status-progress
                        "
                    >

                        In Progress

                    </span>

                <?php

                }
                elseif (
                    $complaint["status"]
                    === "Resolved"
                ) {

                ?>

                    <span
                        class="
                            status-badge
                            status-resolved
                        "
                    >

                        Resolved

                    </span>

                <?php

                }
                else {

                ?>

                    <span class="status-badge">

                        <?php

                        echo htmlspecialchars(
                            $complaint["status"]
                        );

                        ?>

                    </span>

                <?php

                }

                ?>

            </div>

        </div>



        <!-- =================================
             SUMMARY DETAILS
        ================================== -->

        <div class="row g-3">


            <!-- SUBMITTED BY -->

            <div class="col-md-6">

                <label class="form-label text-muted">
                    Submitted By
                </label>

                <div class="fw-semibold">

                    <?php

                    echo htmlspecialchars(
                        $complaint["email"]
                    );

                    ?>

                </div>

            </div>



            <!-- ROLE -->

            <div class="col-md-6">

                <label class="form-label text-muted">
                    User Role
                </label>

                <div class="fw-semibold">

                    <?php

                    echo htmlspecialchars(
                        ucfirst(
                            $complaint["role"]
                        )
                    );

                    ?>

                </div>

            </div>



            <!-- CATEGORY -->

            <div class="col-md-6">

                <label class="form-label text-muted">
                    Category
                </label>

                <div class="fw-semibold">

                    <?php

                    echo htmlspecialchars(
                        $complaint["c_category"]
                    );

                    ?>

                </div>

            </div>



            <!-- ASSET -->

            <div class="col-md-6">

                <label class="form-label text-muted">
                    Asset
                </label>

                <div class="fw-semibold">

                    <?php

                    echo htmlspecialchars(
                        $complaint["asset_id"]
                        ?: "No asset"
                    );

                    ?>

                </div>

            </div>



            <!-- LAB -->

            <div class="col-md-6">

                <label class="form-label text-muted">
                    Lab
                </label>

                <div class="fw-semibold">

                    <?php

                    echo htmlspecialchars(
                        $complaint["lab_number"]
                        ?: "N/A"
                    );

                    ?>

                </div>

            </div>



            <!-- ASSIGNED TO -->

            <div class="col-md-6">

                <label class="form-label text-muted">
                    Assigned To
                </label>

                <div class="fw-semibold">

                    <?php

                    if (
                        !empty(
                            $complaint["assigned_to"]
                        )
                    ) {

                        echo htmlspecialchars(
                            $complaint["assigned_to"]
                        );

                    }
                    else {

                        echo "Unassigned";

                    }

                    ?>

                </div>

            </div>

        </div>

    </div>



    <!-- =====================================
         PROBLEMS
    ====================================== -->

    <div class="content-card mb-4">

        <h5 class="mb-3">
            Reported Problem(s)
        </h5>


        <?php

        if (count($problems) > 0) {

            foreach (
                $problems as $problem
            ) {

        ?>

            <div class="mb-2">

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

                No problem details available.

            </span>

        <?php

        }

        ?>

    </div>



    <!-- =====================================
         STATUS TIMELINE
    ====================================== -->

    <div class="content-card">


        <h5 class="mb-4">

            <i class="bi bi-clock-history me-2"></i>

            Status Timeline

        </h5>



        <?php

        if (
            $history_run &&
            mysqli_num_rows($history_run) > 0
        ) {

        ?>

            <div class="status-timeline">


                <?php

                $history_count = 0;


                while (
                    $history =
                    mysqli_fetch_assoc(
                        $history_run
                    )
                ) {

                    $history_count++;


                    $history_status =
                        $history["status"];


                    /* =========================
                       ICON
                    ========================== */

                    if (
                        $history_status
                        === "Pending"
                    ) {

                        $icon =
                            "bi-clock";

                        $icon_color =
                            "timeline-pending";

                    }
                    elseif (
                        $history_status
                        === "In Progress"
                    ) {

                        $icon =
                            "bi-arrow-repeat";

                        $icon_color =
                            "timeline-progress";

                    }
                    elseif (
                        $history_status
                        === "Resolved"
                    ) {

                        $icon =
                            "bi-check-circle";

                        $icon_color =
                            "timeline-resolved";

                    }
                    else {

                        $icon =
                            "bi-info-circle";

                        $icon_color =
                            "timeline-default";

                    }

                ?>


                    <div
                        class="
                            status-timeline-item
                            <?php

                            if (
                                $history_count === 1
                            ) {

                                echo "first";

                            }

                            ?>
                        "
                    >


                        <!-- TIMELINE ICON -->

                        <div
                            class="
                                status-timeline-icon
                                <?php

                                echo $icon_color;

                                ?>
                            "
                        >

                            <i
                                class="
                                    bi
                                    <?php

                                    echo $icon;

                                    ?>
                                "
                            ></i>

                        </div>



                        <!-- TIMELINE CONTENT -->

                        <div
                            class="
                                status-timeline-content
                            "
                        >


                            <!-- STATUS -->

                            <div
                                class="
                                    d-flex
                                    justify-content-between
                                    align-items-center
                                    flex-wrap
                                    gap-2
                                "
                            >

                                <h6 class="mb-0">

                                    <?php

                                    echo htmlspecialchars(
                                        $history_status
                                    );

                                    ?>

                                </h6>


                                <small
                                    class="text-muted"
                                >

                                    <?php

                                    echo htmlspecialchars(
                                        date(
                                            "d M Y, h:i A",
                                            strtotime(
                                                $history["changed_at"]
                                            )
                                        )
                                    );

                                    ?>

                                </small>

                            </div>



                            <!-- REMARKS -->

                            <?php

                            if (
                                !empty(
                                    $history["remarks"]
                                )
                            ) {

                            ?>

                                <p
                                    class="
                                        mb-1
                                        mt-2
                                        text-muted
                                    "
                                >

                                    <?php

                                    echo nl2br(
                                        htmlspecialchars(
                                            $history["remarks"]
                                        )
                                    );

                                    ?>

                                </p>

                            <?php

                            }

                            ?>



                            <!-- CHANGED BY -->

                            <?php

                            if (
                                !empty(
                                    $history["changed_by"]
                                )
                            ) {

                            ?>

                                <small
                                    class="text-muted"
                                >

                                    <i
                                        class="
                                            bi
                                            bi-person
                                            me-1
                                        "
                                    ></i>

                                    Changed by:

                                    <strong>

                                        <?php

                                        echo htmlspecialchars(
                                            $history["changed_by"]
                                        );

                                        ?>

                                    </strong>

                                </small>

                            <?php

                            }

                            ?>

                        </div>

                    </div>


                <?php

                }

                ?>

            </div>

        <?php

        }
        else {

        ?>

            <!-- =================================
                 NO HISTORY
            ================================== -->

            <div class="text-center py-5">

                <i
                    class="
                        bi
                        bi-clock-history
                        fs-1
                        text-muted
                    "
                ></i>

                <p class="text-muted mt-3 mb-0">

                    No status history is available
                    for this complaint.

                </p>

            </div>

        <?php

        }

        ?>

    </div>



    <!-- =====================================
         BUTTONS
    ====================================== -->

    <div class="mt-4">

        <a
            href="all_complaints.php"
            class="btn btn-secondary"
        >

            <i class="bi bi-arrow-left me-1"></i>

            Back to Complaints

        </a>


        <a
            href="edit_complaint.php?id=<?php

                echo $complaint["id"];

            ?>"
            class="btn btn-primary"
        >

            <i class="bi bi-pencil me-1"></i>

            Edit Complaint

        </a>


        <a
            href="assign_complaint.php?id=<?php

                echo $complaint["id"];

            ?>"
            class="btn btn-success"
        >

            <i class="bi bi-person-check me-1"></i>

            Assign Complaint

        </a>

    </div>

</div>



<!-- =========================================
     TIMELINE CSS
========================================= -->

<style>

.status-timeline {
    position: relative;
    padding-left: 15px;
}

.status-timeline-item {
    position: relative;
    display: flex;
    gap: 18px;
    padding-bottom: 30px;
}

.status-timeline-item:not(:last-child)::before {
    content: "";
    position: absolute;
    left: 17px;
    top: 36px;
    bottom: 0;
    width: 2px;
    background-color: #e5e7eb;
}

.status-timeline-icon {
    width: 36px;
    height: 36px;
    min-width: 36px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    position: relative;
    z-index: 2;
}

.timeline-pending {
    background-color: #f59e0b;
}

.timeline-progress {
    background-color: #3b82f6;
}

.timeline-resolved {
    background-color: #22c55e;
}

.timeline-default {
    background-color: #6b7280;
}

.status-timeline-content {
    flex: 1;
    padding-top: 7px;
}

.status-timeline-content h6 {
    font-weight: 600;
}

</style>



<?php

include "admin_footer.php";

?>