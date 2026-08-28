<?php

session_start();

include "../connection.php";

include "admin_header.php";
include "admin_sidebar.php";


/* =========================================
   ACCESS CONTROL
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
   FILTER VALUES
   ========================================= */

$category_filter = isset($_GET["category"])
    ? trim($_GET["category"])
    : "";

$asset_filter = isset($_GET["asset"])
    ? trim($_GET["asset"])
    : "";

$status_filter = isset($_GET["status"])
    ? trim($_GET["status"])
    : "";

$search = isset($_GET["search"])
    ? trim($_GET["search"])
    : "";



/* =========================================
   ESCAPE FILTER VALUES
   ========================================= */

$category_safe = mysqli_real_escape_string(
    $conn,
    $category_filter
);

$asset_safe = mysqli_real_escape_string(
    $conn,
    $asset_filter
);

$status_safe = mysqli_real_escape_string(
    $conn,
    $status_filter
);

$search_safe = mysqli_real_escape_string(
    $conn,
    $search
);



/* =========================================
   GET ASSETS
   ========================================= */

$asset_query = "
    SELECT DISTINCT asset_id

    FROM complaints

    WHERE asset_id IS NOT NULL

    AND asset_id != ''

    ORDER BY asset_id ASC
";

$asset_run = mysqli_query(
    $conn,
    $asset_query
);



/* =========================================
   MAIN QUERY
   ========================================= */

$query = "
    SELECT

        c.id,
        c.complaint_code,
        c.email,
        c.role,
        c.c_category,
        c.asset_id,
        c.c_detail,
        c.c_description,
        c.status,
        c.admin_remarks,
        c.lab_number,
        c.asset_type,

        GROUP_CONCAT(
            cp.problem_detail
            SEPARATOR '|||'
        ) AS problems

    FROM complaints c

    LEFT JOIN complaint_problems cp
        ON c.id = cp.complaint_id

    WHERE 1=1
";



/* =========================================
   CATEGORY FILTER
   ========================================= */

if ($category_filter !== "") {

    $query .= "
        AND c.c_category = '$category_safe'
    ";

}



/* =========================================
   ASSET FILTER
   ========================================= */

if ($asset_filter !== "") {

    $query .= "
        AND c.asset_id = '$asset_safe'
    ";

}



/* =========================================
   STATUS FILTER
   ========================================= */

if ($status_filter !== "") {

    $query .= "
        AND c.status = '$status_safe'
    ";

}



/* =========================================
   SEARCH
   ========================================= */

if ($search !== "") {

    $query .= "
        AND (
            c.complaint_code LIKE '%$search_safe%'
            OR c.email LIKE '%$search_safe%'
            OR c.asset_id LIKE '%$search_safe%'
        )
    ";

}



/* =========================================
   GROUP + ORDER
   ========================================= */

$query .= "

    GROUP BY c.id

    ORDER BY c.id DESC

";



$run = mysqli_query(
    $conn,
    $query
);

?>



<div class="main-content">


    <!-- =====================================
         PAGE HEADER
    ====================================== -->

    <div class="top-header">

        <div>

            <h4 class="mb-1">
                All Complaints
            </h4>

            <small class="text-muted">
                View and manage all student and teacher complaints
            </small>

        </div>


        <div>

            <i class="bi bi-clipboard-data fs-3"></i>

        </div>

    </div>



    <!-- =====================================
         FILTER CARD
    ====================================== -->

    <div class="content-card mb-4">


        <div class="d-flex
                    justify-content-between
                    align-items-center
                    mb-3">

            <h5 class="mb-0">

                <i class="bi bi-funnel me-2"></i>

                Filter Complaints

            </h5>

        </div>



        <form method="GET">


            <div class="row g-3">


                <!-- =================================
                     SEARCH
                ================================== -->

                <div class="col-md-3">

                    <label class="form-label">
                        Search
                    </label>

                    <input
                        type="text"
                        name="search"
                        class="form-control"
                        placeholder="Complaint ID, email or asset"
                        value="<?php
                            echo htmlspecialchars(
                                $search
                            );
                        ?>"
                    >

                </div>



                <!-- =================================
                     CATEGORY
                ================================== -->

                <div class="col-md-3">

                    <label class="form-label">
                        Category
                    </label>


                    <select
                        name="category"
                        class="form-select"
                    >

                        <option value="">
                            All Categories
                        </option>


                        <option
                            value="Hardware"

                            <?php

                            if (
                                $category_filter ===
                                "Hardware"
                            ) {

                                echo "selected";

                            }

                            ?>
                        >

                            Hardware

                        </option>


                        <option
                            value="Software"

                            <?php

                            if (
                                $category_filter ===
                                "Software"
                            ) {

                                echo "selected";

                            }

                            ?>
                        >

                            Software

                        </option>


                        <option
                            value="Network"

                            <?php

                            if (
                                $category_filter ===
                                "Network"
                            ) {

                                echo "selected";

                            }

                            ?>
                        >

                            Network

                        </option>

                    </select>

                </div>



                <!-- =================================
                     ASSET
                ================================== -->

                <div class="col-md-3">

                    <label class="form-label">
                        Asset
                    </label>


                    <select
                        name="asset"
                        class="form-select"
                    >

                        <option value="">
                            All Assets
                        </option>


                        <?php

                        if ($asset_run) {

                            while (
                                $asset =
                                mysqli_fetch_assoc(
                                    $asset_run
                                )
                            ) {

                                $asset_value =
                                    $asset["asset_id"];

                        ?>

                            <option
                                value="<?php
                                    echo htmlspecialchars(
                                        $asset_value
                                    );
                                ?>"

                                <?php

                                if (
                                    $asset_filter ===
                                    $asset_value
                                ) {

                                    echo "selected";

                                }

                                ?>
                            >

                                <?php

                                echo htmlspecialchars(
                                    $asset_value
                                );

                                ?>

                            </option>

                        <?php

                            }

                        }

                        ?>

                    </select>

                </div>



                <!-- =================================
                     STATUS
                ================================== -->

                <div class="col-md-3">

                    <label class="form-label">
                        Status
                    </label>


                    <select
                        name="status"
                        class="form-select"
                    >

                        <option value="">
                            All Statuses
                        </option>


                        <option
                            value="Unassigned"

                            <?php

                            if (
                                $status_filter ===
                                "Unassigned"
                            ) {

                                echo "selected";

                            }

                            ?>
                        >

                            Unassigned

                        </option>


                        <option
                            value="Pending"

                            <?php

                            if (
                                $status_filter ===
                                "Pending"
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
                                $status_filter ===
                                "In Progress"
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
                                $status_filter ===
                                "Resolved"
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
                                $status_filter ===
                                "Unserviceable"
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

                <div class="col-12">


                    <button
                        type="submit"
                        class="btn btn-primary me-2"
                    >

                        <i class="bi bi-funnel me-1"></i>

                        Apply Filters

                    </button>



                    <a
                        href="all_complaints.php"
                        class="btn btn-outline-secondary"
                    >

                        <i class="bi bi-x-circle me-1"></i>

                        Clear Filters

                    </a>


                </div>


            </div>

        </form>

    </div>



    <!-- =====================================
         COMPLAINT TABLE
    ====================================== -->

    <div class="content-card">


        <div class="d-flex
                    justify-content-between
                    align-items-center
                    mb-3">

            <h5 class="mb-0">

                Complaints

            </h5>


            <?php

            if ($run) {

                $complaint_count =
                    mysqli_num_rows($run);

            }

            else {

                $complaint_count = 0;

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
                            Role
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



                        /* =================================
                           STATUS CLASS
                        ================================= */

                        if (
                            $status === ""
                        ) {

                            $status =
                                "Unassigned";

                        }

                        elseif (
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

                        // else {

                        //     echo 'Unassigned';

                        // }

                ?>


                    <tr>


                        <!-- =========================
                             COMPLAINT ID
                        ========================== -->

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



                        <!-- =========================
                             SUBMITTED BY
                        ========================== -->

                        <td>

                            <?php

                            echo htmlspecialchars(
                                $row["email"]
                            );

                            ?>

                        </td>



                        <!-- =========================
                             ROLE
                        ========================== -->

                        <td>

                            <?php

                            if (
                                $row["role"] ===
                                "student"
                            ) {

                                echo "Student";

                            }

                            elseif (
                                $row["role"] ===
                                "teacher"
                            ) {

                                echo "Teacher";

                            }

                            else {

                                echo htmlspecialchars(
                                    $row["role"]
                                );

                            }

                            ?>

                        </td>



                        <!-- =========================
                             CATEGORY
                        ========================== -->

                        <td>

                            <?php

                            echo htmlspecialchars(
                                $row["c_category"]
                            );

                            ?>

                        </td>



                        <!-- =========================
                             ASSET
                        ========================== -->

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

                                <span
                                    class="text-muted"
                                >

                                    No asset

                                </span>

                            <?php

                            }

                            ?>

                        </td>



                        <!-- =========================
                             PROBLEM
                        ========================== -->

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



                        <!-- =========================
                             STATUS
                        ========================== -->

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



                        <!-- =========================
                             ACTION
                        ========================== -->

                        <td>

                            <div
                                class="
                                    d-flex
                                    gap-2
                                    flex-wrap
                                "
                            >


                                <!-- VIEW -->

                                <a
                                    href="edit_complaint.php?id=<?php

                                        echo $row["id"];

                                    ?>"
                                    class="
                                        btn
                                        btn-primary
                                        btn-sm
                                    "
                                >

                                    <i
                                        class="
                                            bi
                                            bi-eye
                                        "
                                    ></i>

                                    View

                                </a>


                                <!-- DELETE -->

                                <!-- DELETE BUTTON -->

                                <button
                                    type="button"
                                    class="btn btn-outline-danger btn-sm"
                                    data-bs-toggle="modal"
                                    data-bs-target="#deleteModal"
                                    data-id="<?php echo $row["id"]; ?>"
                                    data-code="<?php echo htmlspecialchars(
                                        $row["complaint_code"] ?: $row["id"]
                                    ); ?>"
                                >
                                    <i class="bi bi-trash"></i>
                                    Delete
                                </button>

                            </div>

                        </td>


                    </tr>


                <?php

                    }

                }

                else {

                ?>


                    <tr>

                        <td
                            colspan="8"
                            class="text-center"
                        >


                            <div class="py-5">


                                <i
                                    class="
                                        bi
                                        bi-inbox
                                        fs-1
                                        text-muted
                                    "
                                ></i>


                                <p class="mt-3 mb-2">

                                    <?php

                                    if (
                                        $category_filter !== ""
                                        ||
                                        $asset_filter !== ""
                                        ||
                                        $status_filter !== ""
                                        ||
                                        $search !== ""
                                    ) {

                                        echo
                                        "No complaints match your filters.";

                                    }

                                    else {

                                        echo
                                        "No complaints found.";

                                    }

                                    ?>

                                </p>



                                <?php

                                if (
                                    $category_filter !== ""
                                    ||
                                    $asset_filter !== ""
                                    ||
                                    $status_filter !== ""
                                    ||
                                    $search !== ""
                                ) {

                                ?>

                                    <a
                                        href="all_complaints.php"
                                        class="
                                            btn
                                            btn-outline-primary
                                            btn-sm
                                        "
                                    >

                                        Clear Filters

                                    </a>

                                <?php

                                }


                                ?>

                            </div>


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
     DELETE CONFIRMATION MODAL
========================================= -->

<div
    class="modal fade"
    id="deleteModal"
    tabindex="-1"
    aria-labelledby="deleteModalLabel"
    aria-hidden="true"
>

    <div class="modal-dialog modal-dialog-centered">

        <div class="modal-content delete-modal">


            <!-- HEADER -->

            <div class="modal-header">

                <h5
                    class="modal-title"
                    id="deleteModalLabel"
                >

                    <i class="bi bi-exclamation-triangle me-2"></i>

                    Delete Complaint

                </h5>


                <button
                    type="button"
                    class="btn-close"
                    data-bs-dismiss="modal"
                    aria-label="Close"
                ></button>

            </div>


            <!-- BODY -->

            <div class="modal-body text-center">

                <div class="delete-icon mb-3">

                    <i class="bi bi-trash3"></i>

                </div>


                <h5>
                    Are you sure?
                </h5>


                <p class="text-muted mb-2">

                    You are about to delete complaint

                    <strong id="deleteComplaintCode"></strong>.

                </p>


                <p class="text-muted small mb-0">

                    This will also delete its problem details
                    and status history.

                    <br>

                    <strong>This action cannot be undone.</strong>

                </p>

            </div>


            <!-- FOOTER -->

            <div class="modal-footer">

                <button
                    type="button"
                    class="btn btn-secondary"
                    data-bs-dismiss="modal"
                >

                    Cancel

                </button>


                <a
                    href="all_complaints.php"
                    id="confirmDeleteBtn"
                    class="btn btn-danger"
                >

                    <i class="bi bi-trash3 me-1"></i>

                    Delete Complaint

                </a>

            </div>


        </div>

    </div>

</div>
<script>

document.addEventListener("DOMContentLoaded", function () {

    const deleteModal =
        document.getElementById("deleteModal");

    const confirmDeleteBtn =
        document.getElementById("confirmDeleteBtn");

    const deleteComplaintCode =
        document.getElementById("deleteComplaintCode");


    deleteModal.addEventListener(
        "show.bs.modal",
        function (event) {

            const button =
                event.relatedTarget;

            const complaintId =
                button.getAttribute("data-id");

            const complaintCode =
                button.getAttribute("data-code");


            deleteComplaintCode.textContent =
                "#" + complaintCode;


            confirmDeleteBtn.href =
                "delete_complaint.php?id=" +
                complaintId;

        }
    );

});

</script>

<?php

include "admin_footer.php";

?>