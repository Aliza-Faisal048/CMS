<?php

session_start();

include "../connection.php";


/* =========================================
   ACCESS CONTROL
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


$staff_name = $staff["name"];
$staff_email = $staff["email"];



/* =========================================
   UPDATE COMPLAINT STATUS
========================================= */

$message = "";
$message_type = "";


if (isset($_POST["update-status"])) {

    $complaint_id = intval(
        $_POST["complaint_id"] ?? 0
    );

    $new_status = trim(
        $_POST["status"] ?? ""
    );

    $admin_remarks = trim(
        $_POST["admin_remarks"] ?? ""
    );

    /* =====================================
       ALLOWED IT STAFF STATUSES
    ===================================== */

    $allowed_statuses = [
        "In Progress",
        "Resolved",
        "Unserviceable"
    ];


    if ($complaint_id <= 0) {

        $message = "Invalid complaint.";
        $message_type = "danger";

    }

    elseif (
        !in_array(
            $new_status,
            $allowed_statuses
        )
    ) {

        $message = "Invalid status selected.";
        $message_type = "danger";

    }

    else {


        /* =================================
           GET COMPLAINT

           IMPORTANT:
           Complaint must belong to
           logged-in IT staff.
        ================================= */

        $staff_name_safe =
            mysqli_real_escape_string(
                $conn,
                $staff_name
            );


        $complaint_query = "
            SELECT
                id,
                complaint_code,
                status,
                assigned_to
            FROM complaints
            WHERE id = '$complaint_id'
            AND assigned_to = '$staff_name_safe'
            LIMIT 1
        ";


        $complaint_run =
            mysqli_query(
                $conn,
                $complaint_query
            );


        if (
            !$complaint_run ||
            mysqli_num_rows(
                $complaint_run
            ) == 0
        ) {

            $message =
                "You are not assigned to this complaint.";

            $message_type =
                "danger";

        }

        else {


            $complaint =
                mysqli_fetch_assoc(
                    $complaint_run
                );


            $old_status =
                $complaint["status"];


            /* =============================
               ONLY CREATE HISTORY IF
               STATUS ACTUALLY CHANGED
            ============================= */

            if ($old_status === $new_status) {

                $message =
                    "Complaint is already marked as "
                    . $new_status
                    . ".";

                $message_type =
                    "warning";

            }

            else {


                $new_status_safe =
                    mysqli_real_escape_string(
                        $conn,
                        $new_status
                    );

                $admin_remarks_safe =
                    mysqli_real_escape_string(
                        $conn,
                        $admin_remarks
                    );


                /* =============================
                   UPDATE STATUS
                ============================= */
                $update_query = "
                    UPDATE complaints
                    SET
                        status = '$new_status_safe',
                        admin_remarks = '$admin_remarks_safe'
                    WHERE id = '$complaint_id'
                    AND assigned_to = '$staff_name_safe'
                ";


                $update_run =
                    mysqli_query(
                        $conn,
                        $update_query
                    );


                if (!$update_run) {

                    $message =
                        "Complaint could not be updated: "
                        . mysqli_error($conn);

                    $message_type =
                        "danger";

                }

                else {


                    /* =============================
                       STATUS HISTORY
                    ============================= */

                    $status_remarks =
                        "Complaint status changed from "
                        . $old_status
                        . " to "
                        . $new_status
                        . " by IT staff.";

                    $status_remarks_safe =
                        mysqli_real_escape_string(
                            $conn,
                            $status_remarks
                        );


                    $staff_email_safe =
                        mysqli_real_escape_string(
                            $conn,
                            $staff_email
                        );


                    $history_query = "
                        INSERT INTO complaint_status_history
                        (
                            complaint_id,
                            status,
                            changed_by,
                            remarks
                        )
                        VALUES
                        (
                            '$complaint_id',
                            '$new_status_safe',
                            '$staff_email_safe',
                            '$status_remarks_safe'
                        )
                    ";


                    mysqli_query(
                        $conn,
                        $history_query
                    );


                    $message =
                        "Complaint status updated successfully.";

                    $message_type =
                        "success";

                }

            }

        }

    }

}



/* =========================================
   GET ASSIGNED COMPLAINTS
========================================= */

$staff_name_safe =
    mysqli_real_escape_string(
        $conn,
        $staff_name
    );


$query = "
    SELECT

        c.id,
        c.complaint_code,
        c.email,
        c.role,
        c.c_category,
        c.asset_id,
        c.asset_type,
        c.lab_number,
        c.c_description,
        c.status,
        c.admin_remarks,
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
";


$run = mysqli_query(
    $conn,
    $query
);


/* =========================================
   HEADER + SIDEBAR
========================================= */

include "it_header.php";
include "it_sidebar.php";

?>


<div class="main-content">


    <!-- =====================================
         PAGE HEADER
    ====================================== -->

    <div class="top-header">

        <div>

            <h4 class="mb-1">
                Assigned Complaints
            </h4>

            <small class="text-muted">

                View and update complaints assigned
                to you.

            </small>

        </div>


        <div>

            <i
                class="bi bi-clipboard-check fs-3"
            ></i>

        </div>

    </div>



    <!-- =====================================
         MESSAGE
    ====================================== -->

    <?php if ($message !== "") { ?>

        <div
            class="alert alert-<?php
                echo $message_type;
            ?>"
        >

            <?php

            echo htmlspecialchars(
                $message
            );

            ?>

        </div>

    <?php } ?>



    <!-- =====================================
         COMPLAINTS
    ====================================== -->

    <div class="content-card">


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

                    My Assigned Complaints

                </h5>

                <small class="text-muted">

                    Complaints currently assigned to you

                </small>

            </div>


            <?php

            $complaint_count = 0;

            if ($run) {

                $complaint_count =
                    mysqli_num_rows($run);

            }

            ?>


            <span class="text-muted">

                <?php

                echo $complaint_count;

                ?>

                complaint(s)

            </span>

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
                            Asset
                        </th>

                        <th>
                            Problem
                        </th>

                        <th>
                            Status
                        </th>

                        <th>
                            Action
                        </th>

                    </tr>

                </thead>


                <tbody>


                <?php

                if (
                    $run &&
                    mysqli_num_rows($run) > 0
                ) {

                    while (
                        $row =
                        mysqli_fetch_assoc($run)
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
                                "status-pending";

                        }

                ?>


                    <tr>


                        <!-- =====================
                             COMPLAINT ID
                        ====================== -->

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



                        <!-- =====================
                             SUBMITTED BY
                        ====================== -->

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



                        <!-- =====================
                             CATEGORY
                        ====================== -->

                        <td>

                            <?php

                            echo htmlspecialchars(
                                $row["c_category"]
                            );

                            ?>

                        </td>



                        <!-- =====================
                             ASSET
                        ====================== -->

                        <td>

                            <?php

                            if (
                                !empty(
                                    $row["asset_id"]
                                )
                            ) {

                                echo htmlspecialchars(
                                    $row["asset_id"]
                                );

                            }

                            else {

                            ?>

                                <span class="text-muted">

                                    No asset

                                </span>

                            <?php

                            }

                            ?>

                        </td>



                        <!-- =====================
                             PROBLEM
                        ====================== -->

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

                                <span class="text-muted">

                                    No problem specified

                                </span>

                            <?php

                            }

                            ?>

                        </td>



                        <!-- =====================
                             STATUS
                        ====================== -->

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



                        <!-- =====================
                             ACTION
                        ====================== -->

                        <td>

                            <button
                                type="button"
                                class="
                                    btn
                                    btn-primary
                                    btn-sm
                                "
                                data-bs-toggle="modal"
                                data-bs-target="#complaintModal<?php
                                    echo $row["id"];
                                ?>"
                            >

                                <i
                                    class="bi bi-eye me-1"
                                ></i>

                                View

                            </button>

                        </td>


                    </tr>



                    <!-- =================================
                         VIEW / UPDATE MODAL
                    ================================== -->

                    <div
                        class="
                            modal
                            fade
                        "
                        id="complaintModal<?php
                            echo $row["id"];
                        ?>"
                        tabindex="-1"
                    >

                        <div
                            class="
                                modal-dialog
                                modal-lg
                                modal-dialog-scrollable
                            "
                        >

                            <div class="modal-content">


                                <!-- MODAL HEADER -->

                                <div class="modal-header">

                                    <h5 class="modal-title">

                                        Complaint #

                                        <?php

                                        echo htmlspecialchars(
                                            $row["complaint_code"]
                                            ?: $row["id"]
                                        );

                                        ?>

                                    </h5>


                                    <button
                                        type="button"
                                        class="btn-close"
                                        data-bs-dismiss="modal"
                                    ></button>

                                </div>



                                <!-- MODAL BODY -->

                                <div class="modal-body">


                                    <!-- BASIC INFORMATION -->

                                    <div class="row g-3 mb-4">


                                        <div class="col-md-6">

                                            <label
                                                class="form-label"
                                            >

                                                Submitted By

                                            </label>

                                            <input
                                                type="text"
                                                class="form-control"
                                                value="<?php

                                                    echo htmlspecialchars(
                                                        $row["email"]
                                                    );

                                                ?>"
                                                readonly
                                            >

                                        </div>


                                        <div class="col-md-6">

                                            <label
                                                class="form-label"
                                            >

                                                Role

                                            </label>

                                            <input
                                                type="text"
                                                class="form-control"
                                                value="<?php

                                                    echo htmlspecialchars(
                                                        ucfirst(
                                                            $row["role"]
                                                        )
                                                    );

                                                ?>"
                                                readonly
                                            >

                                        </div>


                                        <div class="col-md-6">

                                            <label
                                                class="form-label"
                                            >

                                                Category

                                            </label>

                                            <input
                                                type="text"
                                                class="form-control"
                                                value="<?php

                                                    echo htmlspecialchars(
                                                        $row["c_category"]
                                                    );

                                                ?>"
                                                readonly
                                            >

                                        </div>


                                        <div class="col-md-6">

                                            <label
                                                class="form-label"
                                            >

                                                Asset

                                            </label>

                                            <input
                                                type="text"
                                                class="form-control"
                                                value="<?php

                                                    echo htmlspecialchars(
                                                        $row["asset_id"]
                                                        ?: "No asset"
                                                    );

                                                ?>"
                                                readonly
                                            >

                                        </div>


                                        <div class="col-md-6">

                                            <label
                                                class="form-label"
                                            >

                                                Asset Type

                                            </label>

                                            <input
                                                type="text"
                                                class="form-control"
                                                value="<?php

                                                    echo htmlspecialchars(
                                                        $row["asset_type"]
                                                        ?: "N/A"
                                                    );

                                                ?>"
                                                readonly
                                            >

                                        </div>


                                        <div class="col-md-6">

                                            <label
                                                class="form-label"
                                            >

                                                Lab

                                            </label>

                                            <input
                                                type="text"
                                                class="form-control"
                                                value="<?php

                                                    echo htmlspecialchars(
                                                        $row["lab_number"]
                                                        ?: "N/A"
                                                    );

                                                ?>"
                                                readonly
                                            >

                                        </div>

                                    </div>



                                    <!-- PROBLEMS -->

                                    <div class="mb-4">

                                        <label class="form-label">

                                            Reported Problem(s)

                                        </label>


                                        <div
                                            class="
                                                border
                                                rounded
                                                p-3
                                                bg-light
                                            "
                                        >

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

                                                <div class="mb-2">

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

                                                <span class="text-muted">

                                                    No problem specified.

                                                </span>

                                            <?php

                                            }

                                            ?>

                                        </div>

                                    </div>



                                    <!-- DESCRIPTION -->

                                    <div class="mb-4">

                                        <label class="form-label">

                                            Description

                                        </label>


                                        <textarea
                                            class="form-control"
                                            rows="4"
                                            readonly
                                        ><?php

                                            echo htmlspecialchars(
                                                $row["c_description"]
                                            );

                                        ?></textarea>

                                    </div>



                                    <!-- =================================
                                        ADMIN REMARKS
                                    ================================= -->

                                    <div class="mb-4">

                                        <label class="form-label">

                                            <i class="bi bi-chat-left-text me-1"></i>

                                            Remarks

                                        </label>

                                        <textarea
                                            name="admin_remarks"
                                            class="form-control"
                                            rows="4"
                                            placeholder="Add remarks about this complaint..."
                                        ><?php

                                            echo htmlspecialchars(
                                                $row["admin_remarks"] ?? ""
                                            );

                                        ?></textarea>

                                        <small class="text-muted">

                                            Add remarks about the work performed,
                                            findings, or resolution.

                                        </small>

                                    </div>



                                    <!-- CURRENT STATUS -->

                                    <div class="alert alert-info">

                                        <strong>

                                            Current Status:

                                        </strong>

                                        <?php

                                        echo htmlspecialchars(
                                            $row["status"]
                                        );

                                        ?>

                                    </div>



                                    <!-- STATUS UPDATE -->

                                    <form
                                        method="POST"
                                    >

                                        <input
                                            type="hidden"
                                            name="complaint_id"
                                            value="<?php
                                                echo $row["id"];
                                            ?>"
                                        >


                                        <div class="mb-3">

                                            <label
                                                class="form-label"
                                            >

                                                Update Status

                                            </label>


                                            <select
                                                name="status"
                                                class="form-select"
                                                required
                                            >

                                                <option value="">

                                                    Select new status

                                                </option>


                                                <option
                                                    value="In Progress"
                                                >

                                                    In Progress

                                                </option>


                                                <option
                                                    value="Resolved"
                                                >

                                                    Resolved

                                                </option>


                                                <option
                                                    value="Unserviceable"
                                                >

                                                    Unserviceable

                                                </option>

                                            </select>

                                        </div>


                                        <button
                                            type="submit"
                                            name="update-status"
                                            class="
                                                btn
                                                btn-primary
                                            "
                                        >

                                            <i
                                                class="
                                                    bi
                                                    bi-check-circle
                                                    me-1
                                                "
                                            ></i>

                                            Update Status

                                        </button>

                                    </form>


                                </div>



                                <!-- MODAL FOOTER -->

                                <div class="modal-footer">

                                    <button
                                        type="button"
                                        class="btn btn-secondary"
                                        data-bs-dismiss="modal"
                                    >

                                        Close

                                    </button>

                                </div>


                            </div>

                        </div>

                    </div>


                <?php

                    }

                }

                else {

                ?>


                    <tr>

                        <td
                            colspan="7"
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

                                No complaints are currently
                                assigned to you.

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



<?php

include "it_footer.php";

?>