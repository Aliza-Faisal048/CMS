<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();

include "../connection.php";
// =========================================
// AMS API CONFIGURATION
// =========================================

$ams_api_url = "https://ams-production-bd97.up.railway.app/api/assets.php";

$ams_api_key = getenv("AMS_API_KEY");


// =========================================
// GET DATA FROM AMS API
// =========================================

function getAMSAssets($url, $apiKey, $type = null)
{
    if ($type !== null) {
        $url .= "?type=" . urlencode($type);
    }

    $ch = curl_init($url);

    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_HTTPHEADER => [
            "Authorization: Bearer " . $apiKey,
            "Accept: application/json"
        ],
        CURLOPT_TIMEOUT => 15,
        CURLOPT_SSL_VERIFYPEER => true
    ]);

    $response = curl_exec($ch);

    if ($response === false) {
        return null;
    }

    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);


    if ($httpCode !== 200) {
        return null;
    }

    $data = json_decode($response, true);

    if (
        !is_array($data) ||
        !isset($data["success"]) ||
        $data["success"] !== true
    ) {
        return null;
    }

    return $data;
}

include "../includes/header.php";
include "../includes/sidebar.php";


// =========================================
// SESSION
// =========================================

$email = $_SESSION["email"];
$role = $_SESSION["role"];


// =========================================
// VARIABLES
// =========================================

$labs = [];
$assets = [];
$problems = [];

$selected_lab = "";
$selected_asset_type = "";
$selected_asset = "";
$selected_category = "";

$message = "";
$message_type = "";


// =========================================
// GET LABS FROM AMS API
// =========================================

$ams_data = getAMSAssets(
    $ams_api_url,
    $ams_api_key
);

if ($ams_data && isset($ams_data["data"])) {

    foreach ($ams_data["data"] as $asset_type => $asset_list) {

        foreach ($asset_list as $asset) {

            // Only show serviceable assets
            if (
                isset($asset["status"]) &&
                $asset["status"] === "Serviceable" &&
                !empty($asset["lab"])
            ) {

                $labs[] = $asset["lab"];

            }

        }

    }

}

// Remove duplicate labs
$labs = array_unique($labs);

// Sort labs
sort($labs);
// =========================================
// KEEP SELECTED VALUES
// =========================================

if (isset($_POST["lab_number"])) {

    $selected_lab = $_POST["lab_number"];

}

if (isset($_POST["asset_type"])) {

    $selected_asset_type = $_POST["asset_type"];

}

if (isset($_POST["asset_id"])) {

    $selected_asset = $_POST["asset_id"];

}

if (isset($_POST["category"])) {

    $selected_category = $_POST["category"];

}


// =========================================
// GET ASSETS FROM AMS API
// =========================================

if (
    $selected_lab != "" &&
    $selected_asset_type != ""
) {

    $allowed_asset_types = [
        "desktop",
        "laptop",
        "printer",
        "projector"
    ];

    if (
        in_array(
            $selected_asset_type,
            $allowed_asset_types,
            true
        )
    ) {

        $ams_type_data = getAMSAssets(
            $ams_api_url,
            $ams_api_key,
            $selected_asset_type
        );

        if (
            $ams_type_data &&
            isset($ams_type_data["data"])
        ) {

            foreach (
                $ams_type_data["data"]
                as $asset
            ) {

                if (
                    isset($asset["status"]) &&
                    $asset["status"] === "Serviceable" &&
                    isset($asset["lab"]) &&
                    $asset["lab"] === $selected_lab
                ) {

                    $assets[] = [
                        "asset_tag" => $asset["asset_tag"],
                        "department" => $asset["department"]
                    ];

                }

            }

        }

    }

}


// =========================================
// GET PROBLEMS
// =========================================

if ($selected_category != "") {

    $category_safe = mysqli_real_escape_string(
        $conn,
        $selected_category
    );

    $role_safe = mysqli_real_escape_string(
        $conn,
        $role
    );

    $problem_query = "
        SELECT id, problem_detail
        FROM problem_table
        WHERE p_category = '$category_safe'
        AND role = '$role_safe'
        ORDER BY id
    ";

    $problem_run = mysqli_query(
        $conn,
        $problem_query
    );

    if ($problem_run) {

        while ($row = mysqli_fetch_assoc($problem_run)) {

            $problems[] = $row;

        }

    }

}


// =========================================
// SUBMIT COMPLAINT
// =========================================

if (isset($_POST["submit-btn"])) {


    $lab_number = $_POST["lab_number"] ?? "";
    $asset_type = $_POST["asset_type"] ?? "";
    $asset_id = $_POST["asset_id"] ?? "";
    $category = $_POST["category"] ?? "";
    $description = trim($_POST["description"] ?? "");

    $selected_problems = $_POST["problems"] ?? [];

    $other_selected = isset($_POST["other_problem"]);

    $other_description = trim(
        $_POST["other_description"] ?? ""
    );


    // =====================================
    // VALIDATION
    // =====================================

    if (
        $lab_number == "" ||
        $asset_type == "" ||
        $asset_id == "" ||
        $category == ""
    ) {

        $message = "Please complete all required selections.";

        $message_type = "danger";

    }

    elseif (
        count($selected_problems) == 0 &&
        !$other_selected
    ) {

        $message = "Please select at least one problem.";

        $message_type = "danger";

    }

    elseif (
        $other_selected &&
        $other_description == ""
    ) {

        $message = "Please describe the other problem.";

        $message_type = "danger";

    }

    else {


        // =================================
        // VALIDATE ASSET TYPE
        // =================================

        $allowed_asset_types = [
            "desktop",
            "laptop",
            "printer",
            "projector"
        ];


        if (!in_array($asset_type, $allowed_asset_types)) {

            $message = "Invalid asset type.";

            $message_type = "danger";

        }

        else {


            // =================================
            // GET SELECTED ASSET FROM AMS API
            // =================================

            $asset_data = getAMSAssets(
                $ams_api_url,
                $ams_api_key,
                $asset_type
            );

            $asset = null;

            if (
                $asset_data &&
                isset($asset_data["data"])
            ) {

                foreach (
                    $asset_data["data"]
                    as $ams_asset
                ) {

                    if (
                        isset($ams_asset["asset_tag"]) &&
                        $ams_asset["asset_tag"] === $asset_id &&
                        isset($ams_asset["lab"]) &&
                        $ams_asset["lab"] === $lab_number &&
                        isset($ams_asset["status"]) &&
                        $ams_asset["status"] === "Serviceable"
                    ) {

                        $asset = $ams_asset;

                        break;

                    }

                }

            }

            

            // =================================
            // CHECK ASSET
            // =================================

            if ($asset === null) {

                $message =
                    "The selected asset could not be found in AMS.";

                $message_type = "danger";

            }

            else {

                $department = $asset["department"] ?? "";


                // =================================
                // ESCAPE VALUES
                // =================================

                $email_safe = mysqli_real_escape_string(
                    $conn,
                    $email
                );

                $role_safe = mysqli_real_escape_string(
                    $conn,
                    $role
                );

                $lab_safe = mysqli_real_escape_string(
                    $conn,
                    $lab_number
                );

                $asset_type_safe = mysqli_real_escape_string(
                    $conn,
                    $asset_type
                );

                $asset_id_safe_cms = mysqli_real_escape_string(
                    $conn,
                    $asset_id
                );

                $category_safe = mysqli_real_escape_string(
                    $conn,
                    $category
                );

                $description_safe = mysqli_real_escape_string(
                    $conn,
                    $description
                );

                $department_safe = mysqli_real_escape_string(
                    $conn,
                    $department
                );


                // =================================
                // GENERATE UNIQUE COMPLAINT CODE
                // =================================

                $year = date("Y");

                $asset_type_name = ucfirst($asset_type);


                // Get the highest existing sequence for this
                // asset and current year

                $prefix = $department . "-" .
                        $asset_type_name . "-" .
                        $asset_id . "-" .
                        $year . "-";


                $prefix_safe = mysqli_real_escape_string(
                    $conn,
                    $prefix
                );


                $sequence_query = "
                    SELECT complaint_code
                    FROM complaints
                    WHERE complaint_code LIKE '$prefix_safe%'
                    ORDER BY id DESC
                    LIMIT 1
                ";


                $sequence_run = mysqli_query(
                    $conn,
                    $sequence_query
                );


                $sequence = 1;


                if (
                    $sequence_run &&
                    mysqli_num_rows($sequence_run) > 0
                ) {

                    $sequence_row =
                        mysqli_fetch_assoc(
                            $sequence_run
                        );


                    $existing_code =
                        $sequence_row["complaint_code"];


                    /*
                    * Extract the number after the final "-"
                    *
                    * Example:
                    *
                    * CS-Desktop-PC-002-2026-01
                    *
                    * Result:
                    *
                    * 01
                    */

                    $parts = explode(
                        "-",
                        $existing_code
                    );


                    $last_part =
                        end($parts);


                    if (is_numeric($last_part)) {

                        $sequence =
                            intval($last_part) + 1;

                    }

                }


                $sequence = str_pad(
                    $sequence,
                    2,
                    "0",
                    STR_PAD_LEFT
                );


                $complaint_code =
                    $prefix .
                    $sequence;


                // =================================
                // CREATE SUBJECT
                // =================================

                $subject = "IT Complaint";


                // =================================
                // INSERT COMPLAINT
                // =================================

                $insert_query = "
                    INSERT INTO complaints
                    (
                        complaint_code,
                        email,
                        c_subject,
                        c_category,
                        asset_id,
                        c_detail,
                        c_description,
                        status,
                        role,
                        lab_number,
                        asset_type
                    )
                    VALUES
                    (
                        '$complaint_code',
                        '$email_safe',
                        '$subject',
                        '$category_safe',
                        '$asset_id_safe_cms',
                        '',
                        '$description_safe',
                        'Pending',
                        '$role_safe',
                        '$lab_safe',
                        '$asset_type_safe'
                    )
                ";


                $insert_run = mysqli_query(
                    $conn,
                    $insert_query
                );


                if (!$insert_run) {

                    $message =
                        "Complaint could not be submitted: "
                        . mysqli_error($conn);

                    $message_type = "danger";

                }

                else {


                    // =================================
                    // GET COMPLAINT ID
                    // =================================

                    $complaint_id = mysqli_insert_id(
                        $conn
                    );


                    // =================================
                    // SAVE SELECTED PROBLEMS
                    // =================================

                    foreach (
                        $selected_problems
                        as $problem
                    ) {

                        $problem_safe =
                            mysqli_real_escape_string(
                                $conn,
                                $problem
                            );


                        $problem_insert = "
                            INSERT INTO complaint_problems
                            (
                                complaint_id,
                                problem_detail
                            )
                            VALUES
                            (
                                '$complaint_id',
                                '$problem_safe'
                            )
                        ";


                        mysqli_query(
                            $conn,
                            $problem_insert
                        );

                    }


                    // =================================
                    // SAVE OTHER PROBLEM
                    // =================================

                    if ($other_selected) {

                        $other_safe =
                            mysqli_real_escape_string(
                                $conn,
                                "Other: " .
                                $other_description
                            );


                        $other_insert = "
                            INSERT INTO complaint_problems
                            (
                                complaint_id,
                                problem_detail
                            )
                            VALUES
                            (
                                '$complaint_id',
                                '$other_safe'
                            )
                        ";


                        mysqli_query(
                            $conn,
                            $other_insert
                        );

                    }


                    // =================================
                    // STATUS HISTORY
                    // =================================

                    $history_email = mysqli_real_escape_string(
                        $conn,
                        $email
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
                            'Pending',
                            '$history_email',
                            'Complaint submitted and waiting for IT staff assignment'
                        )
                    ";


                    mysqli_query(
                        $conn,
                        $history_query
                    );


                    // =================================
                    // SUCCESS
                    // =================================

                    $message =
                        "Complaint submitted successfully! "
                        . "Complaint ID: "
                        . $complaint_code;

                    $message_type = "success";


                    // Clear form

                    $selected_lab = "";
                    $selected_asset_type = "";
                    $selected_asset = "";
                    $selected_category = "";

                    $assets = [];
                    $problems = [];

                }

            }

        }

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
                Launch IT Complaint
            </h4>

            <small class="text-muted">
                Report a problem with an IT asset
            </small>

        </div>

    </div>



    <!-- =====================================
         MESSAGE
    ====================================== -->

    <?php if ($message != "") { ?>

        <div class="alert alert-<?php echo $message_type; ?>">

            <?php echo htmlspecialchars($message); ?>

        </div>

    <?php } ?>



    <!-- =====================================
         FORM
    ====================================== -->

    <div class="content-card">

        <h4 class="mb-4">
            Complaint Details
        </h4>


        <form method="POST">


            <!-- =================================
                 LAB
            ================================== -->

            <div class="mb-4">

                <label class="form-label">
                    Select Lab
                </label>


                <select
                    name="lab_number"
                    class="form-select"
                    onchange="this.form.submit()"
                    required>

                    <option value="">
                        Select Lab
                    </option>


                    <?php foreach ($labs as $lab) { ?>

                        <option
                            value="<?php echo htmlspecialchars($lab); ?>"
                            <?php

                            if (
                                $selected_lab == $lab
                            ) {
                                echo "selected";
                            }

                            ?>>

                            <?php echo htmlspecialchars($lab); ?>

                        </option>

                    <?php } ?>

                </select>

            </div>



            <!-- =================================
                 ASSET TYPE
            ================================== -->

            <?php if ($selected_lab != "") { ?>

                <div class="mb-4">

                    <label class="form-label">
                        Select Asset Type
                    </label>


                    <select
                        name="asset_type"
                        class="form-select"
                        onchange="this.form.submit()"
                        required>

                        <option value="">
                            Select Asset Type
                        </option>


                        <option
                            value="desktop"
                            <?php

                            if (
                                $selected_asset_type ==
                                "desktop"
                            ) {
                                echo "selected";
                            }

                            ?>>

                            Desktop

                        </option>


                        <option
                            value="laptop"
                            <?php

                            if (
                                $selected_asset_type ==
                                "laptop"
                            ) {
                                echo "selected";
                            }

                            ?>>

                            Laptop

                        </option>


                        <option
                            value="printer"
                            <?php

                            if (
                                $selected_asset_type ==
                                "printer"
                            ) {
                                echo "selected";
                            }

                            ?>>

                            Printer

                        </option>


                        <option
                            value="projector"
                            <?php

                            if (
                                $selected_asset_type ==
                                "projector"
                            ) {
                                echo "selected";
                            }

                            ?>>

                            Projector

                        </option>

                    </select>

                </div>

            <?php } ?>



            <!-- =================================
                 ASSET
            ================================== -->

            <?php

            if (
                $selected_lab != "" &&
                $selected_asset_type != ""
            ) {

            ?>

                <div class="mb-4">

                    <label class="form-label">
                        Select Asset
                    </label>


                    <select
                        name="asset_id"
                        class="form-select"
                        onchange="this.form.submit()"
                        required>

                        <option value="">
                            Select Asset
                        </option>


                        <?php foreach ($assets as $asset) { ?>

                            <option
                                value="<?php echo htmlspecialchars($asset["asset_tag"]); ?>"
                                <?php

                                if (
                                    $selected_asset ==
                                    $asset["asset_tag"]
                                ) {
                                    echo "selected";
                                }

                                ?>>

                                <?php
                                echo htmlspecialchars(
                                    $asset["asset_tag"]
                                );
                                ?>

                                -

                                <?php
                                echo htmlspecialchars(
                                    $asset["department"]
                                );
                                ?>

                            </option>

                        <?php } ?>

                    </select>


                    <?php

                    if (count($assets) == 0) {

                    ?>

                        <small class="text-danger">

                            No serviceable assets found
                            in this lab.

                        </small>

                    <?php

                    }

                    ?>

                </div>

            <?php

            }

            ?>



            <!-- =================================
                 CATEGORY
            ================================== -->

            <?php

            if ($selected_asset != "") {

            ?>

                <div class="mb-4">

                    <label class="form-label">
                        Complaint Category
                    </label>


                    <select
                        name="category"
                        class="form-select"
                        onchange="this.form.submit()"
                        required>

                        <option value="">
                            Select Category
                        </option>


                        <option
                            value="Hardware"
                            <?php

                            if (
                                $selected_category ==
                                "Hardware"
                            ) {
                                echo "selected";
                            }

                            ?>>

                            Hardware

                        </option>


                        <option
                            value="Software"
                            <?php

                            if (
                                $selected_category ==
                                "Software"
                            ) {
                                echo "selected";
                            }

                            ?>>

                            Software

                        </option>


                        <option
                            value="Network"
                            <?php

                            if (
                                $selected_category ==
                                "Network"
                            ) {
                                echo "selected";
                            }

                            ?>>

                            Network

                        </option>

                    </select>

                </div>

            <?php

            }

            ?>



            <!-- =================================
                 PROBLEMS
            ================================== -->

            <?php

            if (
                $selected_category != ""
            ) {

            ?>

                <div class="mb-4">

                    <label class="form-label">

                        Select Problem(s)

                    </label>


                    <?php

                    if (count($problems) > 0) {

                        foreach (
                            $problems as $problem
                        ) {

                    ?>

                        <div class="form-check mb-2">

                            <input
                                type="checkbox"
                                class="form-check-input"
                                name="problems[]"
                                value="<?php echo htmlspecialchars($problem["problem_detail"]); ?>"
                                id="problem_<?php echo $problem["id"]; ?>">


                            <label
                                class="form-check-label"
                                for="problem_<?php echo $problem["id"]; ?>">

                                <?php

                                echo htmlspecialchars(
                                    $problem["problem_detail"]
                                );

                                ?>

                            </label>

                        </div>

                    <?php

                        }

                    }

                    else {

                    ?>

                        <div class="alert alert-warning">

                            No predefined problems were found
                            for this category.

                        </div>

                    <?php

                    }

                    ?>


                    <!-- OTHER -->

                    <div class="form-check mb-3">

                        <input
                            type="checkbox"
                            class="form-check-input"
                            name="other_problem"
                            id="other_problem"
                            onchange="toggleOther()"> 


                        <label
                            class="form-check-label"
                            for="other_problem">

                            Other

                        </label>

                    </div>


                    <div
                        id="other_box"
                        style="display:none;">

                        <label class="form-label">

                            Describe the problem

                        </label>


                        <textarea
                            name="other_description"
                            class="form-control"
                            rows="4"
                            placeholder="Describe your problem..."></textarea>

                    </div>

                </div>

            <?php

            }

            ?>



            <!-- =================================
                 DESCRIPTION
            ================================== -->

            <?php

            if (
                $selected_category != ""
            ) {

            ?>

                <div class="mb-4">

                    <label class="form-label">

                        Additional Description

                    </label>


                    <textarea
                        name="description"
                        class="form-control"
                        rows="5"
                        placeholder="Add any additional information..."><?php

                        if (
                            isset($_POST["description"])
                        ) {

                            echo htmlspecialchars(
                                $_POST["description"]
                            );

                        }

                        ?></textarea>

                </div>



                <!-- =================================
                     SUBMIT
                ================================== -->

                <button
                    type="submit"
                    name="submit-btn"
                    class="btn btn-main">

                    <i class="bi bi-send"></i>

                    Submit Complaint

                </button>

            <?php

            }

            ?>


        </form>

    </div>

</div>



<script>

function toggleOther() {

    var checkbox =
        document.getElementById("other_problem");

    var box =
        document.getElementById("other_box");


    if (checkbox.checked) {

        box.style.display = "block";

    }

    else {

        box.style.display = "none";

    }

}

</script>


<?php

include "../includes/footer.php";

?>