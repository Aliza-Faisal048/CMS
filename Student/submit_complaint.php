```php
<?php

session_start();

include "../connection.php";

include "../includes/header.php";
include "../includes/sidebar.php";

$email = $_SESSION["email"];
$role = $_SESSION["role"];

$problems = [];


// Get selected category

if (isset($_POST["category"])) {

    $category = $_POST["category"];

    $query = "SELECT * FROM problem_table
              WHERE p_category='$category'
              AND role='$role'";

    $run = mysqli_query($conn, $query);

    if (mysqli_num_rows($run) > 0) {

        while ($row = mysqli_fetch_assoc($run)) {

            $problems[] = $row;

        }

    }

}


// Submit complaint

if (isset($_POST["submit-btn"])) {
    $c_category = $_POST["category"];
    $c_detail = $_POST["problem"];
    $c_description = $_POST["description"];

    $query = "INSERT INTO complaints
              (email, c_category, c_detail, c_description, role)
              VALUES
              ('$email', '$c_category', '$c_detail', '$c_description' , '$role')";

    $run = mysqli_query($conn, $query);

    if ($run) {
        echo "Complaint submitted successfully";
    } else {
        echo "Complaint not submitted";
    }
}

?>


<div class="main-content">


    <!-- TOP HEADER -->

    <div class="top-header">

        <div>

            <h4 class="mb-1">
                Submit Complaint
            </h4>

            <small class="text-muted">
                Submit a new complaint
            </small>

        </div>

    </div>



    <!-- FORM -->

    <div class="content-card">


        <h4 class="mb-4">
            Complaint Details
        </h4>


        <form
            method="POST"
            onsubmit="return validateComplaintForm();">

            <!-- Category -->

            <div class="mb-3">

                <label class="form-label">
                    Category
                </label>


                <select
                    name="category"
                    class="form-select"
                    onchange="this.form.submit()">

                    <option value="">
                        Select Category
                    </option>

                    <option
                        value="Academic"
                        <?php

                        if (isset($_POST["category"]) && $_POST["category"] == "Academic") {
                            echo "selected";
                        }

                        ?>>

                        Academic

                    </option>

                    <option
                        value="Facilities"
                        <?php

                        if (isset($_POST["category"]) && $_POST["category"] == "Facilities") {
                            echo "selected";
                        }

                        ?>>

                        Facilities

                    </option>


                    <option
                        value="IT"
                        <?php

                        if (isset($_POST["category"]) && $_POST["category"] == "IT") {
                            echo "selected";
                        }

                        ?>>

                        IT / Technical

                    </option>


                    <option
                        value="Transport"
                        <?php

                        if (isset($_POST["category"]) && $_POST["category"] == "Transport") {
                            echo "selected";
                        }

                        ?>>

                        Transport

                    </option>


                    <option
                        value="Hostel"
                        <?php

                        if (isset($_POST["category"]) && $_POST["category"] == "Hostel"
                        ) {
                            echo "selected";
                        }

                        ?>>

                        Hostel

                    </option>


                </select>

            </div>
         <!-- PROBLEM -->

            <?php

            if (isset($_POST["category"]) && $_POST["category"] != "") {

            ?>

                <div class="mb-3">

                    <label class="form-label">
                        <?php echo $_POST["category"]; ?> Problem
                    </label>


                    <select name="problem" class="form-select">


                        <option value="">
                            Select Problem
                        </option>


                        <?php

                        foreach ($problems as $problem) {

                        ?>

                            <option value="<?php echo $problem["problem_detail"]; ?>">

                                <?php echo $problem["problem_detail"]; ?>

                            </option>

                        <?php

                        }

                        ?>


                    </select>

                </div>

            <?php

            }

            ?>

            <!-- Description -->

            <div class="mb-3">

                <label class="form-label">
                    Description
                </label>


                <textarea
                    name="description"
                    rows="6"
                    class="form-control"
                    placeholder="Describe your complaint..."><?php

                    if (isset($_POST["description"])) {
                        echo $_POST["description"];
                    }

                    ?></textarea>

            </div>

            <!-- Submit -->

            <button
                type="submit"
                class="btn btn-main" name="submit-btn">

                <i class="bi bi-send"></i>
                    Submit Complaint                

            </button>

        </form>

    </div>

</div>

<?php

include "../includes/footer.php";

?>
```
