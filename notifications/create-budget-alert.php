<?php

require_once "../config/config.php";


/*
|--------------------------------------------------------------------------
| User Must Be Logged In
|--------------------------------------------------------------------------
*/

if (!isLoggedIn()) {
    redirect("../authentication/login.php");
}


$user_id = getUserId();


/*
|--------------------------------------------------------------------------
| Get Active Budgets
|--------------------------------------------------------------------------
*/

$stmt = $conn->prepare(
    "SELECT
        b.budget_id,
        b.category_id,
        b.amount,
        b.month_year,
        b.alert_percentage,
        c.name AS category_name
     FROM budgets b
     INNER JOIN categories c
        ON b.category_id = c.category_id
     WHERE b.user_id = ?
     AND b.is_active = 1"
);

$stmt->bind_param(
    "i",
    $user_id
);

$stmt->execute();

$budgets = $stmt->get_result();


/*
|--------------------------------------------------------------------------
| Check Each Budget
|--------------------------------------------------------------------------
*/

while ($budget = $budgets->fetch_assoc()) {

    $budget_amount = floatval(
        $budget["amount"]
    );


    if ($budget_amount <= 0) {
        continue;
    }


    /*
    |--------------------------------------------------------------------------
    | Get Expenses For Same Month + Category
    |--------------------------------------------------------------------------
    */

    $expense_stmt = $conn->prepare(
        "SELECT
            COALESCE(SUM(amount), 0) AS total_spent
         FROM expenses
         WHERE user_id = ?
         AND category_id = ?
         AND expense_date = ?"
    );

    $expense_stmt->bind_param(
        "iis",
        $user_id,
        $budget["category_id"],
        $budget["month_year"]
    );

    $expense_stmt->execute();

    $expense_result =
        $expense_stmt->get_result();

    $expense_data =
        $expense_result->fetch_assoc();

    $expense_stmt->close();


    $total_spent = floatval(
        $expense_data["total_spent"]
    );


    /*
    |--------------------------------------------------------------------------
    | Calculate Percentage
    |--------------------------------------------------------------------------
    */

    $percentage =
        ($total_spent / $budget_amount) * 100;


    /*
    |--------------------------------------------------------------------------
    | Decide Notification
    |--------------------------------------------------------------------------
    */

    if ($percentage >= 100) {

        $title = "Budget Exceeded";

        $notification_state = "exceeded";

        $message =
            "Your " .
            $budget["category_name"] .
            " budget for " .
            $budget["month_year"] .
            " has been exceeded. You have spent Rs. " .
            number_format($total_spent, 2) .
            " out of Rs. " .
            number_format($budget_amount, 2) .
            ".";


    } elseif (
        $percentage >=
        floatval($budget["alert_percentage"])
    ) {

        $title = "Budget Alert";

        $notification_state = "alert";

        $message =
            "Your " .
            $budget["category_name"] .
            " budget for " .
            $budget["month_year"] .
            " has reached " .
            number_format($percentage, 1) .
            "%.";


    } else {

        continue;
    }


    /*
    |--------------------------------------------------------------------------
    | Notification Type
    |--------------------------------------------------------------------------
    |
    | Must match database ENUM:
    |
    | budget
    | saving
    | system
    | report
    | goal
    |
    */

    $type = "budget";


    /*
    |--------------------------------------------------------------------------
    | Unique Notification Key
    |--------------------------------------------------------------------------
    |
    | ALERT and EXCEEDED must have different keys.
    |
    */

    $notification_key =
        $type .
        "_" .
        $notification_state .
        "_" .
        $user_id .
        "_" .
        $budget["budget_id"] .
        "_" .
        $budget["month_year"];


    /*
    |--------------------------------------------------------------------------
    | Insert Only Once
    |--------------------------------------------------------------------------
    */

    $notification_stmt = $conn->prepare(
        "INSERT IGNORE INTO notifications
        (
            user_id,
            title,
            message,
            type,
            is_read,
            notification_key
        )
        VALUES (?, ?, ?, ?, 0, ?)"
    );

    $notification_stmt->bind_param(
        "issss",
        $user_id,
        $title,
        $message,
        $type,
        $notification_key
    );


    if (!$notification_stmt->execute()) {

        $notification_stmt->close();
        $stmt->close();

        die(
            "Notification insert failed."
        );
    }


    $notification_stmt->close();
}


$stmt->close();


/*
|--------------------------------------------------------------------------
| Redirect Only When Opened Directly
|--------------------------------------------------------------------------
*/

if (
    basename($_SERVER["PHP_SELF"])
    == "create-budget-alert.php"
) {

    redirect("index.php");
}

?>