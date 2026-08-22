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
   INCLUDE ADMIN UI
========================================= */

include "admin_header.php";
include "admin_sidebar.php";

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
   ADMIN INFORMATION
========================================= */

$admin_email = $_SESSION["email"] ?? "Admin";

$admin_email_safe = mysqli_real_escape_string(
    $conn,
    $admin_email
);


$message = "";
$message_type = "";


/* =========================================
   GET IT STAFF
========================================= */

$it_staff = [];


$staff_query = "
    SELECT id, name, email
    FROM user_table
    WHERE role = 'it_staff'
    ORDER BY name ASC
";


$staff_run = mysqli_query(
    $conn,
    $staff_query
);


if ($staff_run) {

    while (
        $staff_row =
        mysqli_fetch_assoc($staff_run)
    ) {

        $it_staff[] = $staff_row;

    }

}


/* =========================================
   UPDATE COMPLAINT
========================================= */

if (isset($_POST["update-btn"])) {

    $admin_remarks = trim(
        $_POST["admin_remarks"] ?? ""
    );


    $status = trim(
        $_POST["status"] ?? ""
    );


    $assigned_to = trim(
        $_POST["assigned_to"] ?? ""
    );


    /* =====================================
       ALLOWED STATUSES
    ===================================== */

    $allowed_statuses = [
        "Pending",
        "In Progress",
        "Resolved",
        "Unserviceable"
    ];


    /* =====================================
       VALIDATION
    ===================================== */

    if (
        !in_array(
            $status,
            $allowed_statuses
        )
    ) {

        $message =
            "Invalid complaint status.";

        $message_type = "danger";

    }

    else {


        /* =================================
           GET CURRENT COMPLAINT
        ================================= */

        $old_query = "
            SELECT
                status,
                assigned_to
            FROM complaints
            WHERE id = '$id'
            LIMIT 1
        ";


        $old_run = mysqli_query(
            $conn,
            $old_query
        );


        if (
            !$old_run ||
            mysqli_num_rows($old_run) == 0
        ) {

            $message =
                "Complaint not found.";

            $message_type = "danger";

        }

        else {


            $old_complaint =
                mysqli_fetch_assoc(
                    $old_run
                );


            $old_status =
                $old_complaint["status"];


            $old_assigned_to =
                $old_complaint["assigned_to"];


            /* =================================
               VALIDATE ASSIGNED STAFF
            ================================= */

            $staff_exists = true;


            if ($assigned_to != "") {

                $assigned_to_safe =
                    mysqli_real_escape_string(
                        $conn,
                        $assigned_to
                    );


                $staff_check_query = "
                    SELECT name
                    FROM user_table
                    WHERE name = '$assigned_to_safe'
                    AND role = 'it_staff'
                    LIMIT 1
                ";


                $staff_check_run =
                    mysqli_query(
                        $conn,
                        $staff_check_query
                    );


                if (
                    !$staff_check_run ||
                    mysqli_num_rows(
                        $staff_check_run
                    ) == 0
                ) {

                    $staff_exists = false;

                }

            }


            if (!$staff_exists) {

                $message =
                    "Selected IT staff member is invalid.";

                $message_type = "danger";

            }

            else {


                /* =================================
                   ESCAPE VALUES
                ================================= */


                $remarks_safe =
                    mysqli_real_escape_string(
                        $conn,
                        $admin_remarks
                    );


                $status_safe =
                    mysqli_real_escape_string(
                        $conn,
                        $status
                    );


                /* =================================
                   ASSIGNED TO VALUE
                ================================= */

                if ($assigned_to == "") {

                    $assigned_sql = "NULL";

                }

                else {

                    $assigned_to_safe =
                        mysqli_real_escape_string(
                            $conn,
                            $assigned_to
                        );

                    $assigned_sql =
                        "'" .
                        $assigned_to_safe .
                        "'";

                }


                /* =================================
                   UPDATE COMPLAINT
                ================================= */

                $update_query = "
                    UPDATE complaints
                    SET
                        admin_remarks = '$remarks_safe',
                        status = '$status_safe',
                        assigned_to = $assigned_sql
                    WHERE id = '$id'
                ";


                $update_run = mysqli_query(
                    $conn,
                    $update_query
                );


                if (!$update_run) {

                    $message =
                        "Complaint could not be updated: "
                        . mysqli_error($conn);

                    $message_type = "danger";

                }

                else {


                    /* =================================
                       STATUS CHANGE
                    ================================= */

                    if ($old_status !== $status) {


                        $status_remarks =
                            "Complaint status changed from "
                            . $old_status
                            . " to "
                            . $status
                            . " by admin.";


                        $status_remarks_safe =
                            mysqli_real_escape_string(
                                $conn,
                                $status_remarks
                            );


                        $status_history_query = "
                            INSERT INTO complaint_status_history
                            (
                                complaint_id,
                                status,
                                changed_by,
                                remarks
                            )
                            VALUES
                            (
                                '$id',
                                '$status_safe',
                                '$admin_email_safe',
                                '$status_remarks_safe'
                            )
                        ";


                        mysqli_query(
                            $conn,
                            $status_history_query
                        );

                    }


                    /* =================================
                       SUCCESS
                    ================================= */

                    header(
                        "Location: edit_complaint.php?id="
                        . $id
                        . "&updated=1"
                    );

                    exit();

                }

            }

        }

    }

}


/* =========================================
   SUCCESS MESSAGE
========================================= */

if (
    isset($_GET["updated"]) &&
    $_GET["updated"] == "1"
) {

    $message =
        "Complaint updated successfully.";

    $message_type = "success";

}


/* =========================================
   GET COMPLAINT
========================================= */

$query = "
    SELECT *
    FROM complaints
    WHERE id = '$id'
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

    echo "Complaint not found.";
    exit();

}


$complaint =
    mysqli_fetch_assoc($run);


/* =========================================
   GET COMPLAINT PROBLEMS
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

?>


<div class="main-content">


    <!-- =====================================
         TOP HEADER
    ====================================== -->

    <div class="top-header">

        <div>

            <h4 class="mb-1">
                Edit Complaint
            </h4>

            <small class="text-muted">
                Manage complaint details and IT staff assignment
            </small>

        </div>

    </div>



    <!-- =====================================
         MESSAGE
    ====================================== -->

    <?php if ($message != "") { ?>

        <div class="alert alert-<?php echo $message_type; ?>">

            <?php

            echo htmlspecialchars(
                $message
            );

            ?>

        </div>

    <?php } ?>



    <!-- =====================================
         COMPLAINT CARD
    ====================================== -->

    <div class="content-card">


        <h4 class="mb-4">

            Complaint #

            <?php

            echo htmlspecialchars(
                $complaint["complaint_code"]
                ?: $complaint["id"]
            );

            ?>

        </h4>



        <!-- =================================
             BASIC INFORMATION
        ================================== -->

        <div class="row g-3 mb-4">


            <!-- SUBMITTED BY -->

            <div class="col-md-6">

                <label class="form-label">
                    Submitted By
                </label>

                <input
                    type="text"
                    class="form-control"
                    value="<?php

                        echo htmlspecialchars(
                            $complaint["email"]
                        );

                    ?>"
                    readonly>

            </div>



            <!-- ROLE -->

            <div class="col-md-6">

                <label class="form-label">
                    User Role
                </label>

                <input
                    type="text"
                    class="form-control"
                    value="<?php

                        echo htmlspecialchars(
                            ucfirst(
                                $complaint["role"]
                            )
                        );

                    ?>"
                    readonly>

            </div>



            <!-- COMPLAINT CODE -->

            <div class="col-md-6">

                <label class="form-label">
                    Complaint ID
                </label>

                <input
                    type="text"
                    class="form-control"
                    value="<?php

                        echo htmlspecialchars(
                            $complaint["complaint_code"]
                            ?: $complaint["id"]
                        );

                    ?>"
                    readonly>

            </div>



            <!-- ASSET -->

            <div class="col-md-6">

                <label class="form-label">
                    Asset
                </label>

                <input
                    type="text"
                    class="form-control"
                    value="<?php

                        echo htmlspecialchars(
                            $complaint["asset_id"]
                            ?: "No asset"
                        );

                    ?>"
                    readonly>

            </div>



            <!-- ASSET TYPE -->

            <div class="col-md-6">

                <label class="form-label">
                    Asset Type
                </label>

                <input
                    type="text"
                    class="form-control"
                    value="<?php

                        echo htmlspecialchars(
                            $complaint["asset_type"]
                            ?: "N/A"
                        );

                    ?>"
                    readonly>

            </div>



            <!-- LAB -->

            <div class="col-md-6">

                <label class="form-label">
                    Lab
                </label>

                <input
                    type="text"
                    class="form-control"
                    value="<?php

                        echo htmlspecialchars(
                            $complaint["lab_number"]
                            ?: "N/A"
                        );

                    ?>"
                    readonly>

            </div>

            <!-- CATEGORY -->

            <div class="col-md-6">

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

        </div>



        <!-- =================================
             ASSIGN IT STAFF
        ================================== -->

        <div class="mb-4">

            <label class="form-label">

                <i class="bi bi-person-plus me-1"></i>

                Assign Complaint To

            </label>


            <select
                name="assigned_to"
                form="complaint-form"
                class="form-select"
            >

                <option value="">

                    Unassigned

                </option>


                <?php

                foreach (
                    $it_staff as $staff
                ) {

                ?>

                    <option
                        value="<?php

                            echo htmlspecialchars(
                                $staff["name"]
                            );

                        ?>"

                        <?php

                        if (
                            $complaint["assigned_to"]
                            === $staff["name"]
                        ) {

                            echo "selected";

                        }

                        ?>
                    >

                        <?php

                        echo htmlspecialchars(
                            $staff["name"]
                        );

                        ?>

                        -

                        <?php

                        echo htmlspecialchars(
                            $staff["email"]
                        );

                        ?>

                    </option>

                <?php

                }

                ?>

            </select>


            <?php

            if (
                count($it_staff) == 0
            ) {

            ?>

                <small class="text-danger">

                    No IT staff accounts were found.

                </small>

            <?php

            }

            else {

            ?>

                <small class="text-muted">

                    Select an IT staff member to assign or reassign this complaint.

                </small>

            <?php

            }

            ?>

        </div>



        <!-- =================================
             CURRENT ASSIGNMENT
        ================================== -->

        <div class="alert alert-light border mb-4">

            <strong>
                Current Assignment:
            </strong>

            <?php

            if (
                !empty(
                    $complaint["assigned_to"]
                )
            ) {

                ?>

                <span class="text-primary">

                    <?php

                    echo htmlspecialchars(
                        $complaint["assigned_to"]
                    );

                    ?>

                </span>

                <?php

            }

            else {

                ?>

                <span class="text-muted">

                    Unassigned

                </span>

                <?php

            }

            ?>

        </div>



        <!-- =================================
             REPORTED PROBLEMS
        ================================== -->

        <div class="mb-4">

            <label class="form-label">

                Reported Problem(s)

            </label>


            <?php

            if (
                count($problems) > 0
            ) {

            ?>

                <div
                    class="border rounded p-3 bg-light"
                >

                    <?php

                    foreach (
                        $problems as $problem
                    ) {

                    ?>

                        <div class="mb-2">

                            <i
                                class="bi bi-dot"
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

                    No problem specified.

                </div>

            <?php

            }

            ?>

        </div>
        <!-- DESCRIPTION -->

        <div class="mb-4">

            <label class="form-label">
                Description
            </label>

            <textarea
                class="form-control"
                rows="5"
                readonly><?php
                    echo htmlspecialchars(
                        $complaint["c_description"]
                    );
                ?></textarea>

        </div>


        <!-- =================================
             EDIT FORM
        ================================== -->

        <form
            method="POST"
            id="complaint-form"
        >

            <!-- ADMIN REMARKS -->

            <div class="mb-3">

                <label class="form-label">

                    Admin Remarks

                </label>


                <textarea
                    name="admin_remarks"
                    class="form-control"
                    rows="4"
                    placeholder="Add remarks about this complaint..."
                ><?php

                    echo htmlspecialchars(
                        $complaint["admin_remarks"]
                    );

                ?></textarea>

            </div>



            <!-- STATUS -->

            <div class="mb-4">

                <label class="form-label">

                    Complaint Status

                </label>


                <select
                    name="status"
                    class="form-select"
                    required
                >

                    <option
                        value="Pending"

                        <?php

                        if (
                            $complaint["status"]
                            === "Pending"
                        ) {

                            echo "selected";

                        }

                        ?>
                    >

                        Pending

                    </option>


                    <option
                        value="In Progress"

                        <?php

                        if (
                            $complaint["status"]
                            === "In Progress"
                        ) {

                            echo "selected";

                        }

                        ?>
                    >

                        In Progress

                    </option>


                    <option
                        value="Resolved"

                        <?php

                        if (
                            $complaint["status"]
                            === "Resolved"
                        ) {

                            echo "selected";

                        }

                        ?>
                    >

                        Resolved

                    </option>


                    <option
                        value="Unserviceable"

                        <?php

                        if (
                            $complaint["status"]
                            === "Unserviceable"
                        ) {

                            echo "selected";

                        }

                        ?>
                    >

                        Unserviceable

                    </option>

                </select>

            </div>



            <!-- =================================
                 BUTTONS
            ================================== -->

            <button
                type="submit"
                name="update-btn"
                class="btn btn-primary"
            >

                <i
                    class="bi bi-save me-1"
                ></i>

                Save Changes

            </button>


            <a
                href="all_complaints.php"
                class="btn btn-secondary"
            >

                <i
                    class="bi bi-arrow-left me-1"
                ></i>

                Back

            </a>


        </form>

    </div>

</div>



<?php

include "admin_footer.php";

?>