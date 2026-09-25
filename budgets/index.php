<?php

require_once "../config/config.php";

// User must be logged in
if (!isLoggedIn()) {
    redirect("../authentication/login.php");
}

$page_title = "Budgets";

$error = "";
$success = "";

/*
|--------------------------------------------------------------------------
| Create Budget
|--------------------------------------------------------------------------
*/

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $user_id = getUserId();

    $category_id = intval($_POST["category_id"]);
    $amount = floatval($_POST["amount"]);
    $month_year = trim($_POST["month_year"]);
    $alert_percentage = intval($_POST["alert_percentage"]);

    // Basic validation
    if ($category_id <= 0 || $amount <= 0 || $month_year == "") {

        $error = "Please fill in all required fields.";

    } elseif ($alert_percentage < 1 || $alert_percentage > 100) {

        $error = "Alert percentage must be between 1 and 100.";

    } else {

        /*
        |--------------------------------------------------------------------------
        | Check that the selected category is an expense category
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

        if ($category_result->num_rows == 0) {

            $error = "Invalid expense category.";

        } else {

            $stmt->close();

            /*
            |--------------------------------------------------------------------------
            | Check for an existing budget
            |--------------------------------------------------------------------------
            */

            $stmt = $conn->prepare(
                "SELECT budget_id
                 FROM budgets
                 WHERE user_id = ?
                 AND category_id = ?
                 AND month_year = ?
                 AND is_active = 1"
            );

            $stmt->bind_param(
                "iis",
                $user_id,
                $category_id,
                $month_year
            );

            $stmt->execute();

            $existing_result = $stmt->get_result();

            if ($existing_result->num_rows > 0) {

                $error = "A budget already exists for this category and month.";

            } else {

                $stmt->close();

                /*
                |--------------------------------------------------------------------------
                | Insert Budget
                |--------------------------------------------------------------------------
                */

                $stmt = $conn->prepare(
                    "INSERT INTO budgets
                    (user_id, category_id, amount, month_year, alert_percentage, is_active)
                    VALUES (?, ?, ?, ?, ?, 1)"
                );

                $stmt->bind_param(
                    "iidsi",
                    $user_id,
                    $category_id,
                    $amount,
                    $month_year,
                    $alert_percentage
                );

                if ($stmt->execute()) {

                    $success = "Budget created successfully.";

                } else {

                    $error = "Failed to create budget.";

                }
            }

        }

        $stmt->close();
    }
}

/*
|--------------------------------------------------------------------------
| Get Expense Categories
|--------------------------------------------------------------------------
*/

$user_id = getUserId();

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
| Get User Budgets
|--------------------------------------------------------------------------
*/

$budget_stmt = $conn->prepare(
    "SELECT
        b.budget_id,
        b.amount,
        b.month_year,
        b.alert_percentage,
        b.is_active,
        c.name AS category_name
     FROM budgets b
     INNER JOIN categories c
        ON b.category_id = c.category_id
     WHERE b.user_id = ?
     AND b.is_active = 1
     ORDER BY b.month_year DESC, c.name ASC"
);

$budget_stmt->bind_param("i", $user_id);

$budget_stmt->execute();

$budgets = $budget_stmt->get_result();

require_once "../includes/header.php";
require_once "../includes/navbar.php";
require_once "../includes/sidebar.php";

?>

<main class="main-content">

    <h1 class="page-title">
        Budgets
    </h1>

    <p class="page-description">
        Set spending limits for your expense categories.
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


    <!-- Create Budget -->

    <div class="card">

        <h2>
            Create Budget
        </h2>

        <br>

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

                    <option value="">
                        Select Category
                    </option>

                    <?php while ($category = $categories->fetch_assoc()) { ?>

                        <option value="<?php echo $category["category_id"]; ?>">

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
                    value="80"
                    min="1"
                    max="100"
                    required
                    style="
                        width: 100%;
                        padding: 10px;
                        margin-top: 5px;
                    "
                >

                <small>
                    You will later receive an alert when spending reaches this percentage.
                </small>

            </div>


            <button
                type="submit"
                class="btn btn-primary"
            >
                Create Budget
            </button>

        </form>

    </div>


    <!-- Existing Budgets -->

    <div class="card">

        <h2>
            My Budgets
        </h2>

        <br>

        <?php if ($budgets->num_rows > 0) { ?>

            <table
                border="1"
                cellpadding="10"
                cellspacing="0"
                width="100%"
            >

                <tr>

                    <th>
                        Category
                    </th>

                    <th>
                        Amount
                    </th>

                    <th>
                        Month
                    </th>

                    <th>
                        Alert At
                    </th>

                <th>
                    Actions
                </th>

                </tr>


                <?php while ($budget = $budgets->fetch_assoc()) { ?>

                    <tr>

                        <td>
                            <?php echo htmlspecialchars($budget["category_name"]); ?>
                        </td>

                        <td>
                            Rs. <?php echo number_format($budget["amount"], 2); ?>
                        </td>

                        <td>
                            <?php echo htmlspecialchars($budget["month_year"]); ?>
                        </td>

                        <td>
                            <?php echo htmlspecialchars($budget["alert_percentage"]); ?>%
                        </td>
                        <td>

                <a
                href="edit.php?id=<?php echo $budget["budget_id"]; ?>"
                class="btn"
                style="
                background-color: #e5e7eb;
                color: #333;
                "
                >
                 Edit
                </a>

                    <a
                        href="delete.php?id=<?php echo $budget["budget_id"]; ?>"
                        class="btn"
                        style="
                        background-color: #fee2e2;
                        color: #991b1b;
                        margin-left: 5px;
                        "
                        onclick="return confirm('Are you sure you want to remove this budget?');"
                    >
                        Delete
                    </a>

                    </td>

                    </tr>

                <?php } ?>

            </table>

        <?php } else { ?>

            <p>
                You have not created any budgets yet.
            </p>

        <?php } ?>

    </div>

</main>

<?php

require_once "../includes/footer.php";

?>