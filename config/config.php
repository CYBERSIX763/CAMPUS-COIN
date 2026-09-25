<?php
session_start();
// Prevent browser from caching protected pages
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");
header("Expires: 0");

// Load database connection

require_once __DIR__ . "/database.php";


// Load project constants

require_once __DIR__ . "/constants.php";


// Load session functions

require_once __DIR__ . "/../includes/session.php";


// Load common functions

require_once __DIR__ . "/../includes/functions.php";


// Website settings

$site_name = SITE_NAME;

$currency = DEFAULT_CURRENCY;


// Set timezone

date_default_timezone_set("Asia/Karachi");


// --------------------------------------------------
// DATABASE TEST
// --------------------------------------------------

if (basename($_SERVER['PHP_SELF']) == "config.php") {

    echo "<h1>" . SITE_NAME . "</h1>";

    echo "<hr>";

    echo "<h2>Configuration Test</h2>";

    echo "<p>Database Connection: <strong>Successful</strong></p>";

    echo "<p>Database: <strong>" . $database . "</strong></p>";

    echo "<p>Currency: <strong>" . $currency . "</strong></p>";

    echo "<p>Timezone: <strong>Asia/Karachi</strong></p>";

    echo "<p>Session: <strong>Started</strong></p>";

    echo "<hr>";

    echo "<p>Campus Coin configuration is working!</p>";
}

?>