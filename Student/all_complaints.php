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


/* =========================================
   USER EMAIL
   ========================================= */

$email = $_SESSION["email"] ?? "";

$email_safe = mysqli_real_escape_string(
    $conn,
    $email
);


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


/* =========================================
   GET ASSETS FOR FILTER
   ========================================= */

$assets = [];

$asset_query = "
    SELECT DISTINCT asset_id
    FROM complaints
    WHERE email = '$email_safe'
    AND asset_id IS NOT NULL
    AND asset_id != ''
    ORDER BY asset_id ASC
";

$asset_run = mysqli_query(
    $conn,
    $asset_query
);

if ($asset_run) {

    while ($asset_row = mysqli_fetch_assoc($asset_run)) {

        $assets[] = $asset_row["asset_id"];

    }

}


/* =========================================
   MAIN COMPLAINT QUERY
   ========================================= */

$query = "
    SELECT
        id,
        complaint_code,
        c_category,
        asset_id,
        status
    FROM complaints
    WHERE email = '$email_safe'
";


/* =========================================
   CATEGORY FILTER
   ========================================= */

if ($category_filter !== "") {

    $query .= "
        AND c_category = '$category_safe'
    ";

}


/* =========================================
   ASSET FILTER
   ========================================= */

if ($asset_filter !== "") {

    $query .= "
        AND asset_id = '$asset_safe'
    ";

}


/* =========================================
   STATUS FILTER
   ========================================= */

if ($status_filter !== "") {

    $query .= "
        AND status = '$status_safe'
    ";

}


/* =========================================
   ORDER
   ========================================= */

$query .= "
    ORDER BY id DESC
";


$run = mysqli_query(
    $conn,
    $query
);


/* =========================================
   ERROR CHECK
   ========================================= */

if (!$run) {

    die(
        "Complaint query failed: " .
        mysqli_error($conn)
    );

}

?>


<div class="main-content">


    <!-- =====================================
         HEADER
         ===================================== -->

    <div class="top-header">

        <div>

            <h4 class="mb-1">
                All Complaints
            </h4>

            <small class="text-muted">
                View all complaints you have submitted
            </small>

        </div>

    </div>



    <!-- =====================================
         FILTERS
         ===================================== -->

    <div class="content-card mb-4">

        <h5 class="mb-3">

            <i class="bi bi-funnel me-2"></i>

            Filter Complaints

        </h5>


        <form method="GET">

            <div class="row g-3">


                <!-- CATEGORY -->

                <div class="col-md-4">

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
                            echo (
                                $category_filter === "Hardware"
                            )
                            ? "selected"
                            : "";
                            ?>
                        >
                            Hardware
                        </option>

                        <option
                            value="Software"
                            <?php
                            echo (
                                $category_filter === "Software"
                            )
                            ? "selected"
                            : "";
                            ?>
                        >
                            Software
                        </option>

                        <option
                            value="Network"
                            <?php
                            echo (
                                $category_filter === "Network"
                            )
                            ? "selected"
                            : "";
                            ?>
                        >
                            Network
                        </option>

                    </select>

                </div>



                <!-- ASSET -->

                <div class="col-md-4">

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


                        <?php foreach ($assets as $asset) { ?>

                            <option
                                value="<?php
                                echo htmlspecialchars($asset);
                                ?>"
                                <?php
                                echo (
                                    $asset_filter === $asset
                                )
                                ? "selected"
                                : "";
                                ?>
                            >

                                <?php
                                echo htmlspecialchars($asset);
                                ?>

                            </option>

                        <?php } ?>

                    </select>

                </div>



                <!-- STATUS -->

                <div class="col-md-4">

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
                            value="Pending"
                            <?php
                            echo (
                                $status_filter === "Pending"
                            )
                            ? "selected"
                            : "";
                            ?>
                        >
                            Pending
                        </option>

                        <option
                            value="In Progress"
                            <?php
                            echo (
                                $status_filter === "In Progress"
                            )
                            ? "selected"
                            : "";
                            ?>
                        >
                            In Progress
                        </option>

                        <option
                            value="Resolved"
                            <?php
                            echo (
                                $status_filter === "Resolved"
                            )
                            ? "selected"
                            : "";
                            ?>
                        >
                            Resolved
                        </option>

                    </select>

                </div>



                <!-- BUTTONS -->

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
         ===================================== -->

    <div class="content-card">

        <h4 class="mb-4">
            My Complaints
        </h4>


        <div class="table-responsive">

            <table
                class="table table-bordered table-hover align-middle"
            >

                <thead>

                    <tr>

                        <th>
                            Complaint ID
                        </th>

                        <th>
                            Category
                        </th>

                        <th>
                            Asset
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

                if (mysqli_num_rows($run) > 0) {

                    while (
                        $row =
                        mysqli_fetch_assoc($run)
                    ) {

                        $status =
                            $row["status"];

                ?>

                    <tr>


                        <!-- COMPLAINT ID -->

                        <td>

                            <strong>

                                <?php

                                echo htmlspecialchars(
                                    !empty(
                                        $row["complaint_code"]
                                    )
                                    ? $row["complaint_code"]
                                    : $row["id"]
                                );

                                ?>

                            </strong>

                        </td>



                        <!-- CATEGORY -->

                        <td>

                            <?php

                            echo htmlspecialchars(
                                $row["c_category"]
                            );

                            ?>

                        </td>



                        <!-- ASSET -->

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



                        <!-- STATUS -->

                        <td>

                            <?php

                            if (
                                $status === "Pending"
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
                                $status === "In Progress"
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
                                $status === "Resolved"
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

                            elseif (
                                $status === "Unserviceable"
                            ) {

                            ?>

                                <span
                                    class="
                                        status-badge
                                        status-unserviceable
                                    "
                                >
                                    Unserviceable
                                </span>

                            <?php

                            }

                            else {

                            ?>

                                <span class="text-muted">Unassigned</span>

                            <?php

                            }

                            ?>

                        </td>



                        <!-- ACTION -->

                        <td>

                            <a
                                href="view_complaint.php?id=<?php
                                echo urlencode(
                                    $row["id"]
                                );
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
                                        me-1
                                    "
                                ></i>

                                View

                            </a>

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
                            class="text-center"
                        >

                            <div class="py-4">

                                <i
                                    class="
                                        bi
                                        bi-inbox
                                        fs-1
                                        text-muted
                                    "
                                ></i>

                                <p class="mt-2 mb-0">

                                    <?php

                                    if (
                                        $category_filter !== "" ||
                                        $asset_filter !== "" ||
                                        $status_filter !== ""
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


<?php

include "../includes/footer.php";

?>