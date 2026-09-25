<?php

// Start session if it has not already started

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}


// Check if user is logged in

function isLoggedIn()
{
    return isset($_SESSION['user_id']);
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

    session_destroy();
}


// Check if current user is an admin

function isAdmin()
{
    if (isset($_SESSION['user_role'])) {
        return $_SESSION['user_role'] === 'admin';
    }

    return false;
}

?>