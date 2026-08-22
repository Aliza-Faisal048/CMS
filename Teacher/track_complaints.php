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
   GET FILTER VALUES
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


/* =========================================
   GET ASSETS FOR FILTER
   ========================================= */

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


/* =========================================
   BUILD COMPLAINT QUERY
   ========================================= */

$query = "
    SELECT
        c.id,
        c.complaint_code,
        c.c_category,
        c.asset_id,
        c.c_description,
        c.status,
        c.admin_remarks,

        GROUP_CONCAT(
            cp.problem_detail
            SEPARATOR ', '
        ) AS problems

    FROM complaints c

    LEFT JOIN complaint_problems cp
        ON c.id = cp.complaint_id

    WHERE c.email = '$email_safe'
";


/* =========================================
   CATEGORY FILTER
   ========================================= */

if ($category_filter != "") {

    $query .= "
        AND c.c_category = '$category_safe'
    ";

}


/* =========================================
   ASSET FILTER
   ========================================= */

if ($asset_filter != "") {

    $query .= "
        AND c.asset_id = '$asset_safe'
    ";

}


/* =========================================
   STATUS FILTER
   ========================================= */

if ($status_filter != "") {

    $query .= "
        AND c.status = '$status_safe'
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
         ===================================== -->

    <div class="top-header">

        <div>

            <h4 class="mb-1">
                Track Complaints
            </h4>

            <small class="text-muted">
                View the current status and progress
                of your complaints
            </small>

        </div>


        <div>

            <i class="bi bi-clipboard-check fs-3"></i>

        </div>

    </div>


    <!-- =====================================
         SUCCESS MESSAGE
         ===================================== -->

    <?php if (isset($_GET["success"])) { ?>

        <div class="alert alert-success">

            <i class="bi bi-check-circle me-2"></i>

            Complaint submitted successfully.

        </div>

    <?php } ?>


    <!-- =====================================
         FILTERS
         ===================================== -->

    <div class="content-card complaint-filters">

        <div class="d-flex justify-content-between
                    align-items-center mb-3">

            <h5 class="mb-0">

                <i class="bi bi-funnel me-2"></i>

                Filter Complaints

            </h5>

        </div>


        <form method="GET">


            <div class="row g-3">


                <!-- =============================
                     CATEGORY
                     ============================= -->

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

                            if (
                                $category_filter
                                === "Hardware"
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
                                $category_filter
                                === "Software"
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
                                $category_filter
                                === "Network"
                            ) {

                                echo "selected";

                            }

                            ?>
                        >

                            Network

                        </option>

                    </select>

                </div>



                <!-- =============================
                     ASSET
                     ============================= -->

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


                        <?php

                        if ($asset_run) {

                            while (
                                $asset =
                                mysqli_fetch_assoc(
                                    $asset_run
                                )
                            ) {

                                if (
                                    empty(
                                        $asset["asset_id"]
                                    )
                                ) {

                                    continue;

                                }

                        ?>

                            <option
                                value="<?php

                                    echo htmlspecialchars(
                                        $asset["asset_id"]
                                    );

                                ?>"

                                <?php

                                if (
                                    $asset_filter
                                    === $asset["asset_id"]
                                ) {

                                    echo "selected";

                                }

                                ?>
                            >

                                <?php

                                echo htmlspecialchars(
                                    $asset["asset_id"]
                                );

                                ?>

                            </option>

                        <?php

                            }

                        }

                        ?>

                    </select>

                </div>



                <!-- =============================
                     STATUS
                     ============================= -->

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

                            if (
                                $status_filter
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
                                $status_filter
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
                                $status_filter
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
                                $status_filter
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



                <!-- =============================
                     BUTTONS
                     ============================= -->

                <div class="col-12">

                    <button
                        type="submit"
                        class="btn btn-primary me-2"
                    >

                        <i
                            class="bi bi-funnel me-1"
                        ></i>

                        Apply Filters

                    </button>


                    <a
                        href="track_complaints.php"
                        class="btn btn-outline-secondary"
                    >

                        <i
                            class="bi bi-x-circle me-1"
                        ></i>

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

        <div class="d-flex
                    justify-content-between
                    align-items-center
                    mb-3">

            <h5 class="mb-0">

                My Complaints

            </h5>


            <?php

            if (
                $category_filter != "" ||
                $asset_filter != "" ||
                $status_filter != ""
            ) {

            ?>

                <small class="text-muted">

                    Filtered results

                </small>

            <?php

            }

            ?>

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
                        $complaint =
                        mysqli_fetch_assoc($run)
                    ) {


                        $status =
                            $complaint["status"];


                        /* =================================
                           STATUS CLASS
                           ================================= */

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

                            $status_class = "";

                        }

                ?>


                    <tr>


                        <!-- =============================
                             COMPLAINT ID
                             ============================= -->

                        <td>

                            <strong>

                                <?php

                                echo htmlspecialchars(
                                    $complaint[
                                        "complaint_code"
                                    ]
                                );

                                ?>

                            </strong>

                        </td>



                        <!-- =============================
                             CATEGORY
                             ============================= -->

                        <td>

                            <?php

                            echo htmlspecialchars(
                                $complaint[
                                    "c_category"
                                ]
                            );

                            ?>

                        </td>



                        <!-- =============================
                             ASSET
                             ============================= -->

                        <td>

                            <?php

                            if (
                                !empty(
                                    $complaint["asset_id"]
                                )
                            ) {

                                echo htmlspecialchars(
                                    $complaint[
                                        "asset_id"
                                    ]
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



                        <!-- =============================
                             PROBLEMS
                             ============================= -->

                        <td>

                            <?php

                            if (
                                !empty(
                                    $complaint["problems"]
                                )
                            ) {

                                $problem_list =
                                    explode(
                                        ", ",
                                        $complaint[
                                            "problems"
                                        ]
                                    );

                            ?>


                                <div
                                    class="
                                        complaint-problems
                                    "
                                >


                                    <?php

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

                                <span
                                    class="text-muted"
                                >

                                    No problem specified

                                </span>

                            <?php

                            }

                            ?>

                        </td>



                        <!-- =============================
                            STATUS
                            ============================= -->

                        <td>

                            <?php

                            // If status is empty, show Unassigned
                            if (
                                empty($status) ||
                                trim($status) === ""
                            ) {

                                $status = "Unassigned";

                                $status_class = "status-unassigned";

                            }

                            ?>

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



                        <!-- =============================
                             ACTION
                             ============================= -->

                        <td>

                            <button
                                type="button"
                                class="
                                    btn
                                    btn-primary
                                    view-progress-btn
                                "

                                data-complaint-id="<?php

                                    echo $complaint["id"];

                                ?>"

                                data-complaint-code="<?php

                                    echo htmlspecialchars(
                                        $complaint[
                                            "complaint_code"
                                        ]
                                    );

                                ?>"
                            >

                                <i
                                    class="
                                        bi
                                        bi-clock-history
                                        me-1
                                    "
                                ></i>

                                View Progress

                            </button>

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
                                        $category_filter != "" ||
                                        $asset_filter != "" ||
                                        $status_filter != ""
                                    ) {

                                        echo "No complaints match your filters.";

                                    }

                                    else {

                                        echo "You have not submitted any complaints yet.";

                                    }

                                    ?>

                                </p>


                                <?php

                                if (
                                    $category_filter != "" ||
                                    $asset_filter != "" ||
                                    $status_filter != ""
                                ) {

                                ?>

                                    <a
                                        href="track_complaints.php"
                                        class="
                                            btn
                                            btn-outline-primary
                                            mt-3
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
     FLOATING HISTORY OVERLAY
     ========================================= -->

<div
    id="history-overlay"
    class="history-overlay"
>


    <div
        id="history-modal"
        class="history-modal"
    >


        <!-- HEADER -->

        <div class="history-modal-header">

            <div>

                <h5 class="mb-1">

                    Complaint Progress

                </h5>

                <small
                    id="history-complaint-code"
                >
                </small>

            </div>


            <button
                type="button"
                id="close-history"
                class="history-close-btn"
            >

                <i class="bi bi-x-lg"></i>

            </button>

        </div>



        <!-- BODY -->

        <div
            id="history-content"
            class="history-modal-body"
        >

            <div class="history-loading">

                <i
                    class="
                        bi
                        bi-hourglass-split
                    "
                ></i>

                Loading complaint history...

            </div>

        </div>


    </div>

</div>



<script>

document.addEventListener(
    "DOMContentLoaded",
    function () {


        /* =========================================
           ELEMENTS
           ========================================= */

        const historyOverlay =
            document.getElementById(
                "history-overlay"
            );


        const historyContent =
            document.getElementById(
                "history-content"
            );


        const historyComplaintCode =
            document.getElementById(
                "history-complaint-code"
            );


        const closeHistory =
            document.getElementById(
                "close-history"
            );


        const progressButtons =
            document.querySelectorAll(
                ".view-progress-btn"
            );



        /* =========================================
           CHECK ELEMENTS
           ========================================= */

        if (
            !historyOverlay ||
            !historyContent ||
            !historyComplaintCode ||
            !closeHistory
        ) {

            console.error(
                "Complaint history elements are missing."
            );

            return;

        }



        /* =========================================
           OPEN HISTORY
           ========================================= */

        progressButtons.forEach(
            function (button) {


                button.addEventListener(
                    "click",
                    function () {


                        const complaintId =
                            this.getAttribute(
                                "data-complaint-id"
                            );


                        const complaintCode =
                            this.getAttribute(
                                "data-complaint-code"
                            );


                        if (!complaintId) {

                            alert(
                                "Complaint ID is missing."
                            );

                            return;

                        }



                        /* SHOW MODAL */

                        historyComplaintCode.textContent =
                            "Complaint: " +
                            complaintCode;


                        historyOverlay.classList.add(
                            "show"
                        );


                        historyContent.innerHTML = `

                            <div class="history-loading">

                                <i class="
                                    bi
                                    bi-hourglass-split
                                "></i>

                                Loading complaint history...

                            </div>

                        `;



                        /* LOAD HISTORY */

                        fetch(
                            "get_complaint_history.php?id=" +
                            encodeURIComponent(
                                complaintId
                            )
                        )


                        .then(
                            function (response) {

                                if (!response.ok) {

                                    throw new Error(
                                        "Server returned HTTP " +
                                        response.status
                                    );

                                }


                                return response.text();

                            }
                        )


                        .then(
                            function (text) {


                                let data;


                                try {

                                    data =
                                        JSON.parse(
                                            text
                                        );

                                }

                                catch (error) {

                                    throw new Error(
                                        "Invalid JSON returned by get_complaint_history.php"
                                    );

                                }



                                /* SERVER ERROR */

                                if (!data.success) {

                                    historyContent.innerHTML = `

                                        <div
                                            class="
                                                alert
                                                alert-danger
                                            "
                                        >

                                            <i
                                                class="
                                                    bi
                                                    bi-exclamation-triangle
                                                    me-2
                                                "
                                            ></i>

                                            ${escapeHtml(
                                                data.message ||
                                                "Unable to load history."
                                            )}

                                        </div>

                                    `;

                                    return;

                                }



                                /* NO HISTORY */

                                if (
                                    !data.history ||
                                    data.history.length === 0
                                ) {

                                    historyContent.innerHTML = `

                                        <div
                                            class="
                                                history-empty
                                            "
                                        >

                                            <i
                                                class="
                                                    bi
                                                    bi-clock-history
                                                "
                                            ></i>

                                            <p>

                                                No status history
                                                is available yet.

                                            </p>

                                        </div>

                                    `;

                                    return;

                                }



                                /* BUILD TIMELINE */

                                let html = "";


                                data.history.forEach(
                                    function (item) {


                                        let statusClass =
                                            "history-pending";


                                        let statusIcon =
                                            "bi-clock";



                                        if (
                                            item.status ===
                                            "In Progress"
                                        ) {

                                            statusClass =
                                                "history-progress";

                                            statusIcon =
                                                "bi-arrow-repeat";

                                        }


                                        else if (
                                            item.status ===
                                            "Resolved"
                                        ) {

                                            statusClass =
                                                "history-resolved";

                                            statusIcon =
                                                "bi-check-circle";

                                        }
                                        else if (
                                            item.status ===
                                            "Unserviceable"
                                        ) {

                                            statusClass =
                                                "history-unserviceable";

                                            statusIcon =
                                                "bi-x-circle";

                                        }


                                        html += `

                                            <div
                                                class="
                                                    history-item
                                                "
                                            >

                                                <div
                                                    class="
                                                        history-icon
                                                        ${statusClass}
                                                    "
                                                >

                                                    <i
                                                        class="
                                                            bi
                                                            ${statusIcon}
                                                        "
                                                    ></i>

                                                </div>


                                                <div
                                                    class="
                                                        history-details
                                                    "
                                                >

                                                    <div
                                                        class="
                                                            history-status
                                                        "
                                                    >

                                                        ${escapeHtml(
                                                            item.status
                                                        )}

                                                    </div>


                                                    <div
                                                        class="
                                                            history-date
                                                        "
                                                    >

                                                        ${escapeHtml(
                                                            item.changed_at
                                                        )}

                                                    </div>


                                                    ${
                                                        item.remarks
                                                        ?
                                                        `
                                                        <div
                                                            class="
                                                                history-remarks
                                                            "
                                                        >

                                                            <strong>
                                                                Remarks:
                                                            </strong>

                                                            ${escapeHtml(
                                                                item.remarks
                                                            )}

                                                        </div>
                                                        `
                                                        :
                                                        ""
                                                    }


                                                    ${
                                                        item.changed_by
                                                        ?
                                                        `
                                                        <div
                                                            class="
                                                                history-by
                                                            "
                                                        >

                                                            Updated by:

                                                            ${escapeHtml(
                                                                item.changed_by
                                                            )}

                                                        </div>
                                                        `
                                                        :
                                                        ""
                                                    }

                                                </div>

                                            </div>

                                        `;

                                    }
                                );



                                historyContent.innerHTML = `

                                    <div
                                        class="
                                            history-timeline
                                        "
                                    >

                                        ${html}

                                    </div>

                                `;

                            }
                        )


                        .catch(
                            function (error) {

                                console.error(
                                    "Complaint history error:",
                                    error
                                );


                                historyContent.innerHTML = `

                                    <div
                                        class="
                                            alert
                                            alert-danger
                                        "
                                    >

                                        <i
                                            class="
                                                bi
                                                bi-exclamation-triangle
                                                me-2
                                            "
                                        ></i>

                                        ${escapeHtml(
                                            error.message
                                        )}

                                    </div>

                                `;

                            }
                        );

                    }
                );

            }
        );



        /* =========================================
           CLOSE BUTTON
           ========================================= */

        closeHistory.addEventListener(
            "click",
            function () {

                historyOverlay.classList.remove(
                    "show"
                );

            }
        );



        /* =========================================
           CLICK OUTSIDE MODAL
           ========================================= */

        historyOverlay.addEventListener(
            "click",
            function (event) {

                if (
                    event.target ===
                    historyOverlay
                ) {

                    historyOverlay.classList.remove(
                        "show"
                    );

                }

            }
        );



        /* =========================================
           ESCAPE KEY
           ========================================= */

        document.addEventListener(
            "keydown",
            function (event) {

                if (
                    event.key === "Escape"
                ) {

                    historyOverlay.classList.remove(
                        "show"
                    );

                }

            }
        );



        /* =========================================
           HTML ESCAPE
           ========================================= */

        function escapeHtml(value) {

            const div =
                document.createElement(
                    "div"
                );


            div.textContent =
                value ?? "";


            return div.innerHTML;

        }

    }

);

</script>



<?php

include "../includes/footer.php";

?>