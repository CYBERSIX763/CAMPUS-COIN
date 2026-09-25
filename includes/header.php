<?php

if (!isset($page_title)) {
    $page_title = SITE_NAME;
}

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>
        <?php echo $page_title; ?> | <?php echo SITE_NAME; ?>
    </title>

    <!-- Main CSS -->
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>../assets/css/style.css">

</head>

<body>