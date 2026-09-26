<?php

// Start session if it has not already started

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}


// Check if user is logged in

function isLoggedIn()
{
    global $conn;

    if (!isset($_SESSION['user_id'])) {
        return false;
    }

    $user_id = intval($_SESSION['user_id']);


    /*
    |--------------------------------------------------------------------------
    | Verify Account Still Exists And Is Active
    |--------------------------------------------------------------------------
    */

    $stmt = $conn->prepare(
        "SELECT is_active
         FROM users
         WHERE user_id = ?"
    );

    if (!$stmt) {
        return false;
    }

    $stmt->bind_param(
        "i",
        $user_id
    );

    $stmt->execute();

    $result = $stmt->get_result();

    $user = $result->fetch_assoc();

    $stmt->close();


    /*
    |--------------------------------------------------------------------------
    | Account Missing Or Deactivated
    |--------------------------------------------------------------------------
    */

    if (!$user || intval($user["is_active"]) !== 1) {

        $_SESSION = array();

        if (ini_get("session.use_cookies")) {

            $params = session_get_cookie_params();

            setcookie(
                session_name(),
                "",
                time() - 42000,
                $params["path"],
                $params["domain"],
                $params["secure"],
                $params["httponly"]
            );
        }

        session_destroy();

        return false;
    }


    return true;
}


// Get current user's ID

function getUserId()
{
    if (isset($_SESSION['user_id'])) {
        return $_SESSION['user_id'];
    }

    return null;
}


// Log the user in

function loginUser($user_id, $user_name, $user_role)
{
    $_SESSION['user_id'] = $user_id;
    $_SESSION['user_name'] = $user_name;
    $_SESSION['user_role'] = $user_role;
}


// Log the user out

function logoutUser()
{
    $_SESSION = array();


    /*
    |--------------------------------------------------------------------------
    | Remove Session Cookie
    |--------------------------------------------------------------------------
    */

    if (ini_get("session.use_cookies")) {

        $params = session_get_cookie_params();

        setcookie(
            session_name(),
            "",
            time() - 42000,
            $params["path"],
            $params["domain"],
            $params["secure"],
            $params["httponly"]
        );
    }


    /*
    |--------------------------------------------------------------------------
    | Destroy Session
    |--------------------------------------------------------------------------
    */

    session_destroy();
}


// Check if current user is an admin

// Check if current user is an admin

function isAdmin()
{
    global $conn;

    if (!isset($_SESSION['user_id'])) {
        return false;
    }

    $user_id = intval($_SESSION['user_id']);

    $stmt = $conn->prepare(
        "SELECT role, is_active
         FROM users
         WHERE user_id = ?"
    );

    if (!$stmt) {
        return false;
    }

    $stmt->bind_param(
        "i",
        $user_id
    );

    $stmt->execute();

    $result = $stmt->get_result();

    $user = $result->fetch_assoc();

    $stmt->close();

    if (!$user) {
        return false;
    }

    if (intval($user["is_active"]) !== 1) {
        return false;
    }

    return $user["role"] === "admin";
}


?>