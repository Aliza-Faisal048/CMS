<?php

session_start();

include "../connection.php";

include "../includes/header.php";
include "../includes/sidebar.php";

$email = $_SESSION["email"];

$id = $_GET["id"];

$query = "SELECT complaints.*, problem_table.problem_detail
          FROM complaints
          LEFT JOIN problem_table
          ON complaints.c_detail = problem_table.id
          WHERE complaints.id='$id'
          AND complaints.email='$email'";

$run = mysqli_query($conn, $query);

$complaint = mysqli_fetch_assoc($run);

if ($complaint["status"] == "Pending") {
    echo '<span class="status-badge status-pending">Pending</span>';
}
elseif ($complaint["status"] == "In Progress") {
    echo '<span class="status-badge status-progress">In Progress</span>';
}
elseif ($complaint["status"] == "Resolved") {
    echo '<span class="status-badge status-resolved">Resolved</span>';
}

?>

<div class="main-content">

    <!-- TOP HEADER -->

    <div class="top-header">

        <div>

            <h4 class="mb-1">
                Complaint Details
            </h4>

            <small class="text-muted">
                View your complaint details
            </small>

        </div>

    </div>


    <!-- COMPLAINT DETAILS -->

    <div class="content-card">

        <h4 class="mb-4">
            Complaint #<?php echo $complaint["id"]; ?>
        </h4>

        <div class="mb-3">

            <label class="form-label">
                Category
            </label>

            <input
                type="text"
                class="form-control"
                value="<?php echo $complaint["c_category"]; ?>"
                readonly>

        </div>


        <div class="mb-3">

            <label class="form-label">
                Problem
            </label>

            <input
                type="text"
                class="form-control"
                value="<?php echo $complaint["problem_detail"]; ?>"
                readonly>

        </div>


        <div class="mb-3">

            <label class="form-label">
                Description
            </label>

            <textarea
                class="form-control"
                rows="5"
                readonly><?php echo $complaint["c_description"]; ?></textarea>

        </div>


        <div class="mb-4">

            <label class="form-label d-block">
                Status
            </label>

            <?php if ($complaint["status"] == "Pending") { ?>

                <span class="status-badge status-pending">
                    Pending
                </span>

            <?php } elseif ($complaint["status"] == "In Progress") { ?>

                <span class="status-badge status-progress">
                    In Progress
                </span>

            <?php } elseif ($complaint["status"] == "Resolved") { ?>

                <span class="status-badge status-resolved">
                    Resolved
                </span>

            <?php } else { ?>

                <span class="status-badge">
                    <?php echo $complaint["status"]; ?>
                </span>

            <?php } ?>

        </div>


        <a
            href="all_complaints.php"
            class="btn btn-secondary">

            Back to All Complaints

        </a>

    </div>

</div>


<?php

include "../includes/footer.php";

?>