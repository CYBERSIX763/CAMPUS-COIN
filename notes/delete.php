<?php

require_once "../config/config.php";


/*
|--------------------------------------------------------------------------
| Login Check
|--------------------------------------------------------------------------
*/

if (!isLoggedIn()) {
    redirect("../authentication/login.php");
}


$user_id = getUserId();


/*
|--------------------------------------------------------------------------
| Get Note ID
|--------------------------------------------------------------------------
*/

$note_id = isset($_GET["id"]) ? intval($_GET["id"]) : 0;


if ($note_id <= 0) {
    redirect("index.php");
}


/*
|--------------------------------------------------------------------------
| Delete Note
|--------------------------------------------------------------------------
|
| user_id is included so a user cannot delete another user's note
| simply by changing the ID in the URL.
|
*/

$stmt = $conn->prepare(
    "DELETE FROM notes
     WHERE note_id = ?
     AND user_id = ?"
);

$stmt->bind_param(
    "ii",
    $note_id,
    $user_id
);


$stmt->execute();

$stmt->close();


/*
|--------------------------------------------------------------------------
| Return To Notes
|--------------------------------------------------------------------------
*/

redirect("index.php");

?>