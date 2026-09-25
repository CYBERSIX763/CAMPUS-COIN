<?php

require_once "../config/config.php";

// User must be logged in
if (!isLoggedIn()) {
    redirect("../authentication/login.php");
}

$page_title = "Saving Goals";

$error = "";
$success = "";


/*
|--------------------------------------------------------------------------
| Create Saving Goal
|--------------------------------------------------------------------------
*/

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $user_id = getUserId();

    $goal_name = trim($_POST["goal_name"]);
    $target_amount = floatval($_POST["target_amount"]);
    $target_date = trim($_POST["target_date"]);
    $description = trim($_POST["description"]);

    // Basic validation
    if ($goal_name == "" || $target_amount <= 0 || $target_date == "") {

        $error = "Please fill in all required fields.";

    } else {

        /*
        |--------------------------------------------------------------------------
        | Insert Saving Goal
        |--------------------------------------------------------------------------
        */

        $stmt = $conn->prepare(
            "INSERT INTO budget_goals
            (user_id, goal_name, target_amount, current_amount, target_date, description, status)
            VALUES (?, ?, ?, 0, ?, ?, 'active')"
        );

        $stmt->bind_param(
            "isdss",
            $user_id,
            $goal_name,
            $target_amount,
            $target_date,
            $description
        );

        if ($stmt->execute()) {

            $success = "Saving goal created successfully.";

        } else {

            $error = "Failed to create saving goal.";

        }

        $stmt->close();
    }
}


/*
|--------------------------------------------------------------------------
| Get User Saving Goals
|--------------------------------------------------------------------------
*/

$user_id = getUserId();

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
     WHERE user_id = ?
     ORDER BY target_date ASC"
);

$stmt->bind_param("i", $user_id);

$stmt->execute();

$goals = $stmt->get_result();


/*
|--------------------------------------------------------------------------
| Page
|--------------------------------------------------------------------------
*/

require_once "../includes/header.php";
require_once "../includes/navbar.php";
require_once "../includes/sidebar.php";

?>

<main class="main-content">

    <h1 class="page-title">
        Saving Goals
    </h1>

    <p class="page-description">
        Create and track your personal saving goals.
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


    <!-- Create Saving Goal -->

    <div class="card">

        <h2>
            Create Saving Goal
        </h2>

        <br>

        <form method="POST">

            <div style="margin-bottom: 15px;">

                <label>
                    Goal Name
                </label>

                <br>

                <input
                    type="text"
                    name="goal_name"
                    placeholder="Example: New Laptop"
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
                    placeholder="Example: 100000"
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
                    required
                    style="
                        width: 100%;
                        padding: 10px;
                        margin-top: 5px;
                    "
                >

            </div>


            <div style="margin-bottom: 20px;">

                <label>
                    Description
                </label>

                <br>

                <textarea
                    name="description"
                    rows="3"
                    placeholder="What are you saving for?"
                    style="
                        width: 100%;
                        padding: 10px;
                        margin-top: 5px;
                    "
                ></textarea>

            </div>


            <button
                type="submit"
                class="btn btn-primary"
            >
                Create Goal
            </button>

        </form>

    </div>


    <!-- Existing Saving Goals -->

    <div class="card">

        <h2>
            My Saving Goals
        </h2>

        <br>

        <?php if ($goals->num_rows > 0) { ?>

            <table
                border="1"
                cellpadding="10"
                cellspacing="0"
                width="100%"
            >

                <tr>

                    <th>
                        Goal
                    </th>

                    <th>
                        Target
                    </th>

                    <th>
                        Saved
                    </th>

                    <th>
                        Target Date
                    </th>

                    <th>
                        Status
                    </th>

                </tr>


                <?php while ($goal = $goals->fetch_assoc()) { ?>

                    <tr>

                        <td>
                            <?php echo htmlspecialchars($goal["goal_name"]); ?>
                        </td>


                        <td>
                            Rs.
                            <?php echo number_format($goal["target_amount"], 2); ?>
                        </td>


                        <td>
                            Rs.
                            <?php echo number_format($goal["current_amount"], 2); ?>
                        </td>


                        <td>
                            <?php echo htmlspecialchars($goal["target_date"]); ?>
                        </td>


                        <td>
                            <?php echo htmlspecialchars($goal["status"]); ?>
                        </td>

                    </tr>

                <?php } ?>

            </table>

        <?php } else { ?>

            <p>
                You have not created any saving goals yet.
            </p>

        <?php } ?>

    </div>

</main>

<?php

require_once "../includes/footer.php";

?>