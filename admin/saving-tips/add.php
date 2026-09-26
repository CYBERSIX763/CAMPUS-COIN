
<?php

require_once "../../config/config.php";


/*
|--------------------------------------------------------------------------
| Admin Access
|--------------------------------------------------------------------------
*/

if (!isLoggedIn()) {
    redirect("../../authentication/login.php");
}

if ($_SESSION["user_role"] != "admin") {
    redirect("../../dashboard/index.php");
}


$page_title = "Add Saving Tip";

$error = "";


/*
|--------------------------------------------------------------------------
| Add Tip
|--------------------------------------------------------------------------
*/

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $title = trim($_POST["title"] ?? "");
    $content = trim($_POST["content"] ?? "");
    $category_id = trim($_POST["category_id"] ?? "");
    $minimum_impact_score = trim($_POST["minimum_impact_score"] ?? "");


    /*
    |----------------------------------------------------------------------
    | Validation
    |----------------------------------------------------------------------
    */

    if ($title == "") {

        $error = "Please enter a title.";

    } elseif ($content == "") {

        $error = "Please enter the tip content.";

    } elseif (
        $minimum_impact_score == "" ||
        !is_numeric($minimum_impact_score)
    ) {

        $error = "Please enter a valid minimum impact score.";

    } else {



        /*
        |------------------------------------------------------------------
        | Category
        |------------------------------------------------------------------
        */
    }
        if ($category_id == "") {

            $category_value = null;

        } else {

            $category_value = intval($category_id);

        }


        $minimum_impact_score = intval($minimum_impact_score);


        /*
        |------------------------------------------------------------------
        | Insert
        |------------------------------------------------------------------
        */

if ($category_value === null) {

    $stmt = $conn->prepare(
        "INSERT INTO saving_tips
        (
            title,
            content,
            category_id,
            minimum_impact_score,
            is_active
        )
        VALUES (?, ?, NULL, ?, 1)"
    );

    $stmt->bind_param(
        "ssi",
        $title,
        $content,
        $minimum_impact_score
    );

} else {

    $stmt = $conn->prepare(
        "INSERT INTO saving_tips
        (
            title,
            content,
            category_id,
            minimum_impact_score,
            is_active
        )
        VALUES (?, ?, ?, ?, 1)"
    );

    $stmt->bind_param(
        "ssii",
        $title,
        $content,
        $category_value,
        $minimum_impact_score
    );
}

if ($stmt->execute()) {

    $saving_tip_id = $conn->insert_id;

    $stmt->close();

    logAdminAction(
        getUserId(),
        "create",
        "saving_tip",
        $saving_tip_id,
        "Created saving tip: " . $title
    );

    redirect("index.php");

} else {

    $error = "Unable to create saving tip.";

    $stmt->close();
}
}


require_once "../../includes/header.php";
require_once "../../includes/navbar.php";
?>

<div class="admin-layout">

<?php
require_once "../sidebar.php";
?>

<main class="main-content">

    <h1 class="page-title">
        Add Saving Tip
    </h1>

    <p class="page-description">
        Create a new saving tip.
    </p>


    <?php if ($error != "") { ?>

        <div style="
            background-color: #fee2e2;
            color: #991b1b;
            padding: 12px;
            margin-bottom: 20px;
            border-radius: 6px;
        ">

            <?php echo htmlspecialchars($error); ?>

        </div>

    <?php } ?>


    <div class="card">

        <form method="POST">


            <div style="margin-bottom: 15px;">

                <label>
                    Title
                </label>

                <br>

                <input
                    type="text"
                    name="title"
                    value="<?php echo isset($title) ? htmlspecialchars($title) : ""; ?>"
                    required
                    style="width: 100%; padding: 10px;"
                >

            </div>


            <div style="margin-bottom: 15px;">

                <label>
                    Content
                </label>

                <br>

                <textarea
                    name="content"
                    rows="5"
                    required
                    style="width: 100%; padding: 10px;"
                ><?php echo isset($content) ? htmlspecialchars($content) : ""; ?></textarea>

            </div>


            <div style="margin-bottom: 15px;">

                <label>
                    Category ID
                </label>

                <br>

                <input
                    type="number"
                    name="category_id"
                    value="<?php echo isset($category_id) ? htmlspecialchars($category_id) : ""; ?>"
                    placeholder="Leave empty for all categories"
                    style="width: 100%; padding: 10px;"
                >

            </div>


            <div style="margin-bottom: 20px;">

                <label>
                    Minimum Impact Score
                </label>

                <br>

                <input
                    type="number"
                    name="minimum_impact_score"
                    value="<?php echo isset($minimum_impact_score) ? htmlspecialchars($minimum_impact_score) : "0"; ?>"
                    min="0"
                    required
                    style="width: 100%; padding: 10px;"
                >

            </div>


            <button type="submit">
                Add Saving Tip
            </button>


            <a
                href="index.php"
                style="margin-left: 10px;"
            >
                Cancel
            </a>

        </form>

    </div>

</main>

</div>
<?php

require_once "../../includes/footer.php";
?>