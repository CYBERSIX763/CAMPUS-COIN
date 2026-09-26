
<?php

require_once "../config/config.php";

if (!isLoggedIn()) {
    redirect("../authentication/login.php");
}

$user_id = getUserId();


// Check goal ID
if (!isset($_GET["id"]) || !is_numeric($_GET["id"])) {
    redirect("index.php");
}

$goal_id = intval($_GET["id"]);


// Get the goal
$stmt = $conn->prepare(
    "SELECT goal_id, goal_name, target_amount, current_amount, status
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


$error = "";
$success = "";


/*
|--------------------------------------------------------------------------
| Add Savings
|--------------------------------------------------------------------------
*/

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $amount = floatval($_POST["amount"]);


    if ($amount <= 0) {

        $error = "Please enter a valid amount.";

    } elseif ($goal["status"] != "active") {

        $error = "This saving goal is not active.";

    } elseif ($goal["current_amount"] >= $goal["target_amount"]) {

        $error = "This saving goal has already been completed.";

    } else {

        $remaining = $goal["target_amount"] - $goal["current_amount"];


        // Don't allow savings to go above the target
        if ($amount > $remaining) {

            $error = "You only need Rs. " .
                     number_format($remaining, 2) .
                     " more to complete this goal.";

        } else {

            $new_amount = $goal["current_amount"] + $amount;


            // Automatically complete the goal
            if ($new_amount >= $goal["target_amount"]) {

                $new_status = "completed";

            } else {

                $new_status = "active";

            }


            $stmt = $conn->prepare(
                "UPDATE budget_goals
                 SET current_amount = ?,
                     status = ?
                 WHERE goal_id = ?
                 AND user_id = ?"
            );

            $stmt->bind_param(
                "dsii",
                $new_amount,
                $new_status,
                $goal_id,
                $user_id
            );


            if ($stmt->execute()) {

                $success = "Savings added successfully.";

                $goal["current_amount"] = $new_amount;
                $goal["status"] = $new_status;

            } else {

                $error = "Failed to add savings.";

            }

            $stmt->close();
        }
    }
}


require_once "../includes/header.php";
require_once "../includes/navbar.php";
require_once "../includes/sidebar.php";

?>

<main class="main-content">

    <h1 class="page-title">
        Add Savings
    </h1>

    <p class="page-description">
        Add money to your saving goal.
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

        <h2>
            <?php echo htmlspecialchars($goal["goal_name"]); ?>
        </h2>

        <p>
            Target:
            Rs. <?php echo number_format($goal["target_amount"], 2); ?>
        </p>

        <p>
            Currently Saved:
            Rs. <?php echo number_format($goal["current_amount"], 2); ?>
        </p>

        <br>


        <?php if ($goal["status"] == "active") { ?>

            <form method="POST">

                <label>
                    Amount to Add
                </label>

                <br>

                <input
                    type="number"
                    name="amount"
                    min="0.01"
                    step="0.01"
                    required
                    style="
                        width: 100%;
                        padding: 10px;
                        margin-top: 5px;
                    "
                >

                <br><br>

                <button
                    type="submit"
                    class="btn btn-primary"
                >
                    Add Savings
                </button>

                <a
                    href="index.php"
                    class="btn"
                >
                    Cancel
                </a>

            </form>

        <?php } else { ?>

            <p>
                This goal is already <?php echo htmlspecialchars($goal["status"]); ?>.
            </p>

            <br>

            <a
                href="index.php"
                class="btn"
            >
                Back to Goals
            </a>

        <?php } ?>

    </div>

</main>

<?php

require_once "../includes/footer.php";

?>
