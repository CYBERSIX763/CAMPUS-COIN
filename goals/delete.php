
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


// Delete only the logged-in user's goal
$stmt = $conn->prepare(
    "DELETE FROM budget_goals
     WHERE goal_id = ?
     AND user_id = ?"
);

$stmt->bind_param(
    "ii",
    $goal_id,
    $user_id
);

$stmt->execute();

$stmt->close();


// Return to goals page
redirect("index.php");

?>
