```php
<?php

require_once "../config/config.php";


if (!isLoggedIn()) {
    redirect("../authentication/login.php");
}

$user_id = getUserId();


// Get active budgets
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

$stmt->bind_param("i", $user_id);

$stmt->execute();

$budgets = $stmt->get_result();

$stmt->close();


// Check each budget
while ($budget = $budgets->fetch_assoc()) {

    $budget_amount = floatval($budget["amount"]);


    // Get expenses for this budget month
    $expense_stmt = $conn->prepare(
        "SELECT COALESCE(SUM(amount), 0) AS total_spent
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

    $expense_result = $expense_stmt->get_result();

    $expense_data = $expense_result->fetch_assoc();

    $expense_stmt->close();


    $total_spent = floatval($expense_data["total_spent"]);


    if ($budget_amount <= 0) {
        continue;
    }


    // Calculate percentage
    $percentage = ($total_spent / $budget_amount) * 100;


    /*
    |--------------------------------------------------------------------------
    | Decide notification type
    |--------------------------------------------------------------------------
    */

    if ($percentage >= 100) {

        $title = "Budget Exceeded";

        $type = "budget_exceeded";

        $message =
            "Your " .
            $budget["category_name"] .
            " budget for " .
            $budget["month_year"] .
            " has been exceeded.";

    } elseif ($percentage >= $budget["alert_percentage"]) {

        $title = "Budget Alert";

        $type = "budget_alert";

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
    | Create unique notification key
    |--------------------------------------------------------------------------
    */

    $notification_key =
        $type .
        "_" .
        $user_id .
        "_" .
        $budget["budget_id"] .
        "_" .
        $budget["month_year"];


    /*
    |--------------------------------------------------------------------------
    | Insert only if this notification does not exist
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

    $notification_stmt->execute();

    $notification_stmt->close();
}


redirect("index.php");

?>
```
