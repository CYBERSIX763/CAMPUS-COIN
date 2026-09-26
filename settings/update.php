
<?php

require_once "../config/config.php";


/*
|--------------------------------------------------------------------------
| Check Login
|--------------------------------------------------------------------------
*/

if (!isLoggedIn()) {
    redirect("../authentication/login.php");
}


$user_id = getUserId();


/*
|--------------------------------------------------------------------------
| Only Allow POST
|--------------------------------------------------------------------------
*/

if ($_SERVER["REQUEST_METHOD"] != "POST") {
    redirect("index.php");
}


/*
|--------------------------------------------------------------------------
| Get Settings
|--------------------------------------------------------------------------
*/

$dark_mode = isset($_POST["dark_mode"])
    ? intval($_POST["dark_mode"])
    : 0;

$font_size = trim(
    $_POST["font_size"] ?? "medium"
);


/*
|--------------------------------------------------------------------------
| Validate Dark Mode
|--------------------------------------------------------------------------
*/

if ($dark_mode != 0 && $dark_mode != 1) {
    $dark_mode = 0;
}


/*
|--------------------------------------------------------------------------
| Validate Font Size
|--------------------------------------------------------------------------
*/

$allowed_sizes = [
    "small",
    "medium",
    "large"
];

if (!in_array($font_size, $allowed_sizes)) {
    $font_size = "medium";
}


/*
|--------------------------------------------------------------------------
| Check User Profile
|--------------------------------------------------------------------------
*/

$stmt = $conn->prepare(
    "SELECT profile_id
     FROM user_profiles
     WHERE user_id = ?"
);

$stmt->bind_param(
    "i",
    $user_id
);

$stmt->execute();

$result = $stmt->get_result();

$profile_exists = $result->num_rows > 0;

$stmt->close();


/*
|--------------------------------------------------------------------------
| Update Existing Profile
|--------------------------------------------------------------------------
*/

if ($profile_exists) {

    $stmt = $conn->prepare(
        "UPDATE user_profiles
         SET
            dark_mode = ?,
            font_size = ?,
            updated_at = NOW()
         WHERE user_id = ?"
    );

    $stmt->bind_param(
        "isi",
        $dark_mode,
        $font_size,
        $user_id
    );

}


/*
|--------------------------------------------------------------------------
| Create Profile If Missing
|--------------------------------------------------------------------------
*/

else {

    $stmt = $conn->prepare(
        "INSERT INTO user_profiles
        (
            user_id,
            dark_mode,
            font_size
        )
        VALUES (?, ?, ?)"
    );

    $stmt->bind_param(
        "iis",
        $user_id,
        $dark_mode,
        $font_size
    );

}


/*
|--------------------------------------------------------------------------
| Save Settings
|--------------------------------------------------------------------------
*/

$stmt->execute();

$stmt->close();


/*
|--------------------------------------------------------------------------
| Return To Settings
|--------------------------------------------------------------------------
*/

redirect("index.php");

?>