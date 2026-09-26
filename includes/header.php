<?php
require_once __DIR__ . "/../config/config.php";
?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>
        <?php echo isset($page_title)
            ? htmlspecialchars($page_title) . " | Campus Coin"
            : "Campus Coin";
        ?>
    </title>

    <link rel="stylesheet"
          href="<?php echo BASE_URL; ?>assets/css/style.css">

</head>

<body>