
<?php

require_once "../config/config.php";

if (!isLoggedIn()) {
    redirect("../authentication/login.php");
}

$page_title = "Edit Saving Goal";

$user_id = getUserId();

$error = "";
$success = "";


// Check goal ID
if (!isset($_GET["id"]) || !is_numeric($_GET["id"])) {
    redirect("index.php");
}

$goal_id = intval($_GET["id"]);


// Get the user's goal
$stmt = $conn->prepare(
    "SELECT
        goal_id,
        goal_name,
        target_amount,
        current_amount,
        target_date,
        description,
        status
     FROM budget_goals
     WHERE goal_id = ?
     AND user_id = ?"
);

$stmt->bind_param("ii", $goal_id, $user_id);

$stmt->execute();

$result = $stmt->get_result();

if ($result->num_rows != 1) {

    $stmt->close();

    redirect("index.php");
}

$goal = $result->fetch_assoc();

$stmt->close();


// Update goal
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $goal_name = trim($_POST["goal_name"]);
    $target_amount = floatval($_POST["target_amount"]);
    $target_date = trim($_POST["target_date"]);
    $description = trim($_POST["description"]);
    $status = trim($_POST["status"]);


    // Check required fields
    if ($goal_name == "" || $target_amount <= 0 || $target_date == "") {

        $error = "Please fill in all required fields.";

    } else {

        $stmt = $conn->prepare(
            "UPDATE budget_goals
             SET goal_name = ?,
                 target_amount = ?,
                 target_date = ?,
                 description = ?,
                 status = ?
             WHERE goal_id = ?
             AND user_id = ?"
        );

        $stmt->bind_param(
            "sdsssii",
            $goal_name,
            $target_amount,
            $target_date,
            $description,
            $status,
            $goal_id,
            $user_id
        );


        if ($stmt->execute()) {

            $success = "Saving goal updated successfully.";

            // Update the values displayed in the form
            $goal["goal_name"] = $goal_name;
            $goal["target_amount"] = $target_amount;
            $goal["target_date"] = $target_date;
            $goal["description"] = $description;
            $goal["status"] = $status;

        } else {

            $error = "Failed to update saving goal.";

        }

        $stmt->close();
    }
}


require_once "../includes/header.php";
require_once "../includes/navbar.php";
require_once "../includes/sidebar.php";

?>

<main class="main-content">

    <h1 class="page-title">
        Edit Saving Goal
    </h1>

    <p class="page-description">
        Update your saving goal information.
    </p>


    <?php if ($success != "") { ?>

        <div style="
            background-color: #dcfce7;
            color: #166534;
            padding: 12px;
            margin-bottom: 20px;
            border-radius: 6px;
        ">
            <?php echo htmlspecialchars($success); ?>
        </div>

    <?php } ?>


    <?php if ($error != "") { ?>

        <div style="
            background-color: #fee2e2;
            color: #991b1b;
            padding: 12px;
            margin-bottom: 20px;
            border-radius: 6px;
        ">
            <?php echo htmlspecialchars($error); ?>
        </div>

    <?php } ?>


    <div class="card">

        <form method="POST">

            <div style="margin-bottom: 15px;">

                <label>
                    Goal Name
                </label>

                <br>

                <input
                    type="text"
                    name="goal_name"
                    value="<?php echo htmlspecialchars($goal["goal_name"]); ?>"
                    required
                    style="
                        width: 100%;
                        padding: 10px;
                        margin-top: 5px;
                    "
                >

            </div>


            <div style="margin-bottom: 15px;">

                <label>
                    Target Amount (PKR)
                </label>

                <br>

                <input
                    type="number"
                    name="target_amount"
                    min="1"
                    step="0.01"
                    value="<?php echo htmlspecialchars($goal["target_amount"]); ?>"
                    required
                    style="
                        width: 100%;
                        padding: 10px;
                        margin-top: 5px;
                    "
                >

            </div>


            <div style="margin-bottom: 15px;">

                <label>
                    Target Date
                </label>

                <br>

                <input
                    type="date"
                    name="target_date"
                    value="<?php echo htmlspecialchars($goal["target_date"]); ?>"
                    required
                    style="
                        width: 100%;
                        padding: 10px;
                        margin-top: 5px;
                    "
                >

            </div>


            <div style="margin-bottom: 15px;">

                <label>
                    Status
                </label>

                <br>

                <select
                    name="status"
                    style="
                        width: 100%;
                        padding: 10px;
                        margin-top: 5px;
                    "
                >

                    <option
                        value="active"
                        <?php if ($goal["status"] == "active") echo "selected"; ?>
                    >
                        Active
                    </option>

                    <option
                        value="completed"
                        <?php if ($goal["status"] == "completed") echo "selected"; ?>
                    >
                        Completed
                    </option>

                    <option
                        value="cancelled"
                        <?php if ($goal["status"] == "cancelled") echo "selected"; ?>
                    >
                        Cancelled
                    </option>

                </select>

            </div>


            <div style="margin-bottom: 20px;">

                <label>
                    Description
                </label>

                <br>

                <textarea
                    name="description"
                    rows="3"
                    style="
                        width: 100%;
                        padding: 10px;
                        margin-top: 5px;
                    "
                ><?php echo htmlspecialchars($goal["description"]); ?></textarea>

            </div>


            <button
                type="submit"
                class="btn btn-primary"
            >
                Save Changes
            </button>


            <a
                href="index.php"
                class="btn"
                style="
                    background-color: #e5e7eb;
                    color: #333;
                    margin-left: 5px;
                "
            >
                Cancel
            </a>

        </form>

    </div>

</main>

<?php

require_once "../includes/footer.php";

?>

