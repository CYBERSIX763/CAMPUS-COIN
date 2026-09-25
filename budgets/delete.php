<?php

require_once "../config/config.php";

// User must be logged in
if (!isLoggedIn()) {
    redirect("../authentication/login.php");
}


$user_id = getUserId();

// Check budget ID
if (!isset($_GET["id"]) || !is_numeric($_GET["id"])) {
    redirect("index.php");
}

$budget_id = intval($_GET["id"]);

/*
|--------------------------------------------------------------------------
| Deactivate Budget
|--------------------------------------------------------------------------
*/

$stmt = $conn->prepare(
    "UPDATE budgets
     SET is_active = 0
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

$stmt->close();

redirect("index.php");

?>