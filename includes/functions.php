<?php


// Redirect to another page

function redirect($page)
{
    header("Location: " . $page);
    exit();
}


// Clean user input

function cleanInput($data)
{
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);

    return $data;
}


// Check if a value is empty

function isEmpty($value)
{
    return empty(trim($value));
}


// Format money

function formatMoney($amount)
{
    return "Rs. " . number_format($amount, 2);
}


// Format date

function formatDate($date)
{
    return date("d M Y", strtotime($date));
}


// Create a simple success message

function successMessage($message)
{
    $_SESSION['success_message'] = $message;
}


// Create a simple error message

function errorMessage($message)
{
    $_SESSION['error_message'] = $message;
}


// Get success message

function getSuccessMessage()
{
    if (isset($_SESSION['success_message'])) {

        $message = $_SESSION['success_message'];

        unset($_SESSION['success_message']);

        return $message;
    }

    return "";
}


// Get error message

function getErrorMessage()
{
    if (isset($_SESSION['error_message'])) {

        $message = $_SESSION['error_message'];

        unset($_SESSION['error_message']);

        return $message;
    }

    return "";
}

?>