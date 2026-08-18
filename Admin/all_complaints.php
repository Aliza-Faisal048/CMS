<?php

session_start();

include "../connection.php";

include "admin_header.php";
include "admin_sidebar.php";


$category = "";
$email = "";
$role = "";


// Get filters

if (isset($_GET["category"])) {
    $category = $_GET["category"];
}

if (isset($_GET["email"])) {
    $email = $_GET["email"];
}

if (isset($_GET["role"])) {
    $role = $_GET["role"];
}

// Get complaints

$query = "SELECT * FROM complaints WHERE 1=1";


if ($category != "") {

    $query .= " AND c_category='$category'";

}


if ($email != "") {

    $query .= " AND email LIKE '%$email%'";

}

if ($role != "") {

    $query .= " AND role LIKE '%$role%'";

}


$query .= " ORDER BY id DESC";


$run = mysqli_query($conn, $query);

?>

<div class="main-content">

    <!-- TOP HEADER -->

    <div class="top-header">

        <div>

            <h4 class="mb-1">
                All Complaints
            </h4>

            <small class="text-muted">
                View and manage student complaints
            </small>

        </div>

    </div>


    <!-- FILTERS -->

    <div class="content-card mb-4">

        <h5 class="mb-3">
            Filter Complaints
        </h5>


        <form method="GET">

            <div class="row g-3">

                <!-- Category -->

                <div class="col-md-3">

                    <label class="form-label">
                        Category
                    </label>

                    <select name="category" class="form-select">

                        <option value="">
                            All Categories
                        </option>

                        <option value="Academic"
                            <?php
                            if ($category == "Academic") {
                                echo "selected";
                            }
                            ?>>
                            Academic
                        </option>

                        <option value="Facilities"
                            <?php
                            if ($category == "Facilities") {
                                echo "selected";
                            }
                            ?>>
                            Facilities
                        </option>

                        <option value="IT"
                            <?php
                            if ($category == "IT") {
                                echo "selected";
                            }
                            ?>>
                            IT / Technical
                        </option>

                        <option value="Transport"
                            <?php
                            if ($category == "Transport") {
                                echo "selected";
                            }
                            ?>>
                            Transport
                        </option>

                        <option value="Hostel"
                            <?php
                            if ($category == "Hostel") {
                                echo "selected";
                            }
                            ?>>
                            Hostel
                        </option>

                    </select>

                </div>

                <!-- Roles -->

                <div class="col-md-3">

                    <label class="form-label">
                        Role
                    </label>

                    <select name="role" class="form-select">

                        <option value="">
                            Roles
                        </option>

                        <option value="Student"
                            <?php
                            if ($role == "Student") {
                                echo "selected";
                            }
                            ?>>
                            Student
                        </option>

                        <option value="Teacher"
                            <?php
                            if ($role == "Teacher") {
                                echo "selected";
                            }
                            ?>>
                            Teacher
                        </option>

                    </select>

                </div>

                <!-- Email -->

                <div class="col-md-4">

                    <label class="form-label">
                        Email
                    </label>

                    <input
                        type="text"
                        name="email"
                        class="form-control"
                        placeholder="Enter email"
                        value="<?php echo $email; ?>">

                </div>


                <!-- Button -->

                <div class="col-md-2 d-flex align-items-end">

                    <button
                        type="submit"
                        class="btn btn-primary w-100">

                        Filter

                    </button>

                </div>

            </div>

        </form>

    </div>


    <!-- COMPLAINT TABLE -->

    <div class="content-card">

        <h5 class="mb-4">
            Complaints
        </h5>


        <div class="table-responsive">

            <table class="table table-bordered table-hover">

                <thead>

                    <tr>

                        <th>
                            ID
                        </th>

                        <th>
                            Email
                        </th>

                        <th>
                            Role
                        </th>

                        <th>
                            Category
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

                        while ($row = mysqli_fetch_assoc($run)) {

                    ?>

                        <tr>

                            <td>
                                <?php echo $row["id"]; ?>
                            </td>

                            <td>
                                <?php echo $row["email"]; ?>
                            </td>

                            <td>
                                <?php echo $row["role"]; ?>
                            </td>

                            <td>
                                <?php echo $row["c_category"]; ?>
                            </td>

                                        
                            <td>

                                <?php if ($row["status"] == "Pending") { ?>

                                    <span class="status-badge status-pending">
                                        Pending
                                    </span>

                                <?php } elseif ($row["status"] == "In Progress") { ?>

                                    <span class="status-badge status-progress">
                                        In Progress
                                    </span>

                                <?php } elseif ($row["status"] == "Resolved") { ?>

                                    <span class="status-badge status-resolved">
                                        Resolved
                                    </span>

                                <?php } else { ?>

                                    <span class="status-badge">
                                        <?php echo $row["status"]; ?>
                                    </span>

                                <?php } ?>

                            </td>

                            <td>

                                <a
                                    href="edit_complaint.php?id=<?php echo $row["id"]; ?>"
                                    class="btn btn-primary btn-sm">

                                    View / Edit

                                </a>


                                <a
                                    href="delete_complaint.php?id=<?php echo $row["id"]; ?>"
                                    class="btn btn-danger btn-sm"
                                    onclick="return confirm('Are you sure you want to delete this complaint?');">

                                    Delete

                                </a>

                            </td>

                        </tr>

                    <?php

                        }

                    } else {

                    ?>

                        <tr>

                            <td colspan="5" class="text-center">

                                No complaints found.

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

include "admin_footer.php";

?>