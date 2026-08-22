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
include "it_staff_header.php";
include "it_staff_sidebar.php";

/* =========================================
   GET IT STAFF INFORMATION
========================================= */

$user_id = intval($_SESSION["user_id"]);


$user_query = "
    SELECT
        id,
        name,
        email,
        profile_picture
    FROM user_table
    WHERE id = '$user_id'
    LIMIT 1
";


$user_run = mysqli_query(
    $conn,
    $user_query
);


if (
    !$user_run ||
    mysqli_num_rows($user_run) == 0
) {

    session_destroy();

    header("Location: ../login.php");
    exit();

}


$user =
    mysqli_fetch_assoc($user_run);


$staff_name =
    $user["name"];

$staff_email =
    $user["email"];

$profile_picture =
    $user["profile_picture"];


/* =========================================
   FILTER VALUES
========================================= */

$category_filter =
    isset($_GET["category"])
        ? trim($_GET["category"])
        : "";


$status_filter =
    isset($_GET["status"])
        ? trim($_GET["status"])
        : "";


$search =
    isset($_GET["search"])
        ? trim($_GET["search"])
        : "";


/* =========================================
   ESCAPE SEARCH VALUES
========================================= */

$category_safe =
    mysqli_real_escape_string(
        $conn,
        $category_filter
    );


$status_safe =
    mysqli_real_escape_string(
        $conn,
        $status_filter
    );


$search_safe =
    mysqli_real_escape_string(
        $conn,
        $search
    );


$staff_name_safe =
    mysqli_real_escape_string(
        $conn,
        $staff_name
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
        c.asset_type,
        c.lab_number,
        c.status,
        c.admin_remarks,
        c.c_description,

        GROUP_CONCAT(
            cp.problem_detail
            SEPARATOR '|||'
        ) AS problems

    FROM complaints c

    LEFT JOIN complaint_problems cp
        ON c.id = cp.complaint_id

    WHERE c.assigned_to = '$staff_name_safe'

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

            c.complaint_code
                LIKE '%$search_safe%'

            OR c.email
                LIKE '%$search_safe%'

            OR c.asset_id
                LIKE '%$search_safe%'

            OR c.c_category
                LIKE '%$search_safe%'

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


$run =
    mysqli_query(
        $conn,
        $query
    );


?>

<!-- =========================================
     MAIN CONTENT
========================================= -->

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

                Complaints assigned to you by the administrator

            </small>

        </div>


        <div>

            <i class="bi bi-clipboard-check fs-3"></i>

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
                        href="assigned_complaints.php"
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


        <div
            class="d-flex
                   justify-content-between
                   align-items-center
                   mb-3"
        >

            <div>

                <h5 class="mb-1">

                    My Assigned Complaints

                </h5>

                <small class="text-muted">

                    Complaints currently assigned to

                    <strong>

                        <?php

                        echo htmlspecialchars(
                            $staff_name
                        );

                        ?>

                    </strong>

                </small>

            </div>


            <?php

            $complaint_count =

                $run
                ? mysqli_num_rows($run)
                : 0;

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


                        /* STATUS CLASS */

                        if (
                            $status ===
                            "Pending"
                        ) {

                            $status_class =
                                "status-pending";

                        }

                        elseif (
                            $status ===
                            "In Progress"
                        ) {

                            $status_class =
                                "status-progress";

                        }

                        elseif (
                            $status ===
                            "Resolved"
                        ) {

                            $status_class =
                                "status-resolved";

                        }

                        elseif (
                            $status ===
                            "Unserviceable"
                        ) {

                            $status_class =
                                "status-unserviceable";

                        }

                        else {

                            $status_class = "";

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
                                        class="bi bi-dot"
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



                        <!-- ACTION -->

                        <td>

                            <a
                                href="view_complaint.php?id=<?php

                                    echo $row["id"];

                                ?>"
                                class="
                                    btn
                                    btn-primary
                                    btn-sm
                                "
                            >

                                <i
                                    class="bi bi-eye"
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


                            <p class="mt-3 mb-2">

                                <?php

                                if (
                                    $category_filter !== ""
                                    ||
                                    $status_filter !== ""
                                    ||
                                    $search !== ""
                                ) {

                                    echo
                                    "No assigned complaints match your filters.";

                                }

                                else {

                                    echo
                                    "No complaints have been assigned to you.";

                                }

                                ?>

                            </p>


                            <?php

                            if (
                                $category_filter !== ""
                                ||
                                $status_filter !== ""
                                ||
                                $search !== ""
                            ) {

                            ?>

                                <a
                                    href="assigned_complaints.php"
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
include "it_staff_footer.php";
?>