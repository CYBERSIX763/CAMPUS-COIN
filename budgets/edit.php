<?php

require_once "../config/config.php";

// User must be logged in
if (!isLoggedIn()) {
    redirect("../authentication/login.php");
}

$page_title = "Edit Budget";

$user_id = getUserId();
$error = "";
$success = "";

// Get budget ID from URL
if (!isset($_GET["id"]) || !is_numeric($_GET["id"])) {
    redirect("index.php");
}

$budget_id = intval($_GET["id"]);

/*
|--------------------------------------------------------------------------
| Get Budget
|--------------------------------------------------------------------------
*/

$stmt = $conn->prepare(
    "SELECT budget_id, category_id, amount, month_year, alert_percentage
     FROM budgets
     WHERE budget_id = ?
     AND user_id = ?
     AND is_active = 1"
);

$stmt->bind_param(
    "ii",
    $budget_id,
    $user_id
);

$stmt->execute();

$result = $stmt->get_result();

if ($result->num_rows != 1) {

    $stmt->close();

    redirect("index.php");
}

$budget = $result->fetch_assoc();

$stmt->close();

/*
|--------------------------------------------------------------------------
| Update Budget
|--------------------------------------------------------------------------
*/

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $category_id = intval($_POST["category_id"]);
    $amount = floatval($_POST["amount"]);
    $month_year = trim($_POST["month_year"]);
    $alert_percentage = intval($_POST["alert_percentage"]);

    if ($category_id <= 0 || $amount <= 0 || $month_year == "") {

        $error = "Please fill in all required fields.";

    } elseif ($alert_percentage < 1 || $alert_percentage > 100) {

        $error = "Alert percentage must be between 1 and 100.";

    } else {

        /*
        |--------------------------------------------------------------------------
        | Check Category
        |--------------------------------------------------------------------------
        */

        $stmt = $conn->prepare(
            "SELECT category_id
             FROM categories
             WHERE category_id = ?
             AND type = 'expense'
             AND is_active = 1
             AND (user_id IS NULL OR user_id = ?)"
        );

        $stmt->bind_param(
            "ii",
            $category_id,
            $user_id
        );

        $stmt->execute();

        $category_result = $stmt->get_result();

        $stmt->close();

        if ($category_result->num_rows == 0) {

            $error = "Invalid expense category.";

        } else {

            /*
            |--------------------------------------------------------------------------
            | Check Duplicate Budget
            |--------------------------------------------------------------------------
            */

            $stmt = $conn->prepare(
                "SELECT budget_id
                 FROM budgets
                 WHERE user_id = ?
                 AND category_id = ?
                 AND month_year = ?
                 AND is_active = 1
                 AND budget_id != ?"
            );

            $stmt->bind_param(
                "iisi",
                $user_id,
                $category_id,
                $month_year,
                $budget_id
            );

            $stmt->execute();

            $duplicate_result = $stmt->get_result();

            $stmt->close();

            if ($duplicate_result->num_rows > 0) {

                $error = "A budget already exists for this category and month.";

            } else {

                /*
                |--------------------------------------------------------------------------
                | Update Budget
                |--------------------------------------------------------------------------
                */

                $stmt = $conn->prepare(
                    "UPDATE budgets
                     SET category_id = ?,
                         amount = ?,
                         month_year = ?,
                         alert_percentage = ?
                     WHERE budget_id = ?
                     AND user_id = ?"
                );

                $stmt->bind_param(
                    "idsiii",
                    $category_id,
                    $amount,
                    $month_year,
                    $alert_percentage,
                    $budget_id,
                    $user_id
                );

                if ($stmt->execute()) {

                    $success = "Budget updated successfully.";

                    // Update displayed values
                    $budget["category_id"] = $category_id;
                    $budget["amount"] = $amount;
                    $budget["month_year"] = $month_year;
                    $budget["alert_percentage"] = $alert_percentage;

                } else {

                    $error = "Failed to update budget.";

                }

                $stmt->close();
            }
        }
    }
}

/*
|--------------------------------------------------------------------------
| Get Expense Categories
|--------------------------------------------------------------------------
*/

$category_stmt = $conn->prepare(
    "SELECT category_id, name
     FROM categories
     WHERE type = 'expense'
     AND is_active = 1
     AND (user_id IS NULL OR user_id = ?)
     ORDER BY name ASC"
);

$category_stmt->bind_param("i", $user_id);

$category_stmt->execute();

$categories = $category_stmt->get_result();

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
        Edit Budget
    </h1>

    <p class="page-description">
        Update your budget details.
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
                    Expense Category
                </label>

                <br>

                <select
                    name="category_id"
                    required
                    style="
                        width: 100%;
                        padding: 10px;
                        margin-top: 5px;
                    "
                >

                    <?php while ($category = $categories->fetch_assoc()) { ?>

                        <option
                            value="<?php echo $category["category_id"]; ?>"
                            <?php
                            if ($category["category_id"] == $budget["category_id"]) {
                                echo "selected";
                            }
                            ?>
                        >

                            <?php echo htmlspecialchars($category["name"]); ?>

                        </option>

                    <?php } ?>

                </select>

            </div>


            <div style="margin-bottom: 15px;">

                <label>
                    Budget Amount (PKR)
                </label>

                <br>

                <input
                    type="number"
                    name="amount"
                    min="1"
                    step="0.01"
                    value="<?php echo htmlspecialchars($budget["amount"]); ?>"
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
                    Month
                </label>

                <br>

                <input
                    type="month"
                    name="month_year"
                    value="<?php echo htmlspecialchars($budget["month_year"]); ?>"
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
                    Alert Percentage
                </label>

                <br>

                <input
                    type="number"
                    name="alert_percentage"
                    min="1"
                    max="100"
                    value="<?php echo htmlspecialchars($budget["alert_percentage"]); ?>"
                    required
                    style="
                        width: 100%;
                        padding: 10px;
                        margin-top: 5px;
                    "
                >

            </div>


            <button
                type="submit"
                class="btn btn-primary"
            >
                Update Budget
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