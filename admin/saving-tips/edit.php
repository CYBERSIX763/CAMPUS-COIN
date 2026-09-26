
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


$tip_id = intval($_GET["id"] ?? 0);

$error = "";


if ($tip_id <= 0) {
    redirect("index.php");
}


/*
|--------------------------------------------------------------------------
| Get Tip
|--------------------------------------------------------------------------
*/

$stmt = $conn->prepare(
    "SELECT
        tip_id,
        title,
        content,
        category_id,
        minimum_impact_score,
        is_active
     FROM saving_tips
     WHERE tip_id = ?"
);

$stmt->bind_param(
    "i",
    $tip_id
);

$stmt->execute();

$result = $stmt->get_result();

$tip = $result->fetch_assoc();

$stmt->close();


if (!$tip) {
    redirect("index.php");
}


/*
|--------------------------------------------------------------------------
| Update Tip
|--------------------------------------------------------------------------
*/

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $title = trim($_POST["title"] ?? "");
    $content = trim($_POST["content"] ?? "");
    $category_id = trim($_POST["category_id"] ?? "");
    $minimum_impact_score = trim($_POST["minimum_impact_score"] ?? "");


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


        if ($category_id == "") {

            $category_value = null;

        } else {

            $category_value = intval($category_id);

        }


        $minimum_impact_score = intval($minimum_impact_score);


        /*
        |------------------------------------------------------------------
        | Update
        |------------------------------------------------------------------
        */

        if ($category_value === null) {

            $stmt = $conn->prepare(
                "UPDATE saving_tips
                 SET
                    title = ?,
                    content = ?,
                    category_id = NULL,
                    minimum_impact_score = ?
                 WHERE tip_id = ?"
            );

            $stmt->bind_param(
                "ssii",
                $title,
                $content,
                $minimum_impact_score,
                $tip_id
            );

        } else {

            $stmt = $conn->prepare(
                "UPDATE saving_tips
                 SET
                    title = ?,
                    content = ?,
                    category_id = ?,
                    minimum_impact_score = ?
                 WHERE tip_id = ?"
            );

            $stmt->bind_param(
                "ssiii",
                $title,
                $content,
                $category_value,
                $minimum_impact_score,
                $tip_id
            );
        }


        if ($stmt->execute()) {

            $stmt->close();

            

            redirect("index.php");

        } else {

            $error = "Unable to update saving tip.";

            $stmt->close();
        }
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
        Edit Saving Tip
    </h1>


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
                    value="<?php echo htmlspecialchars($tip["title"]); ?>"
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
                ><?php echo htmlspecialchars($tip["content"]); ?></textarea>

            </div>


            <div style="margin-bottom: 15px;">

                <label>
                    Category ID
                </label>

                <br>

                <input
                    type="number"
                    name="category_id"
                    value="<?php echo $tip["category_id"] === null ? "" : htmlspecialchars($tip["category_id"]); ?>"
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
                    value="<?php echo htmlspecialchars($tip["minimum_impact_score"]); ?>"
                    min="0"
                    required
                    style="width: 100%; padding: 10px;"
                >

            </div>


            <button type="submit">
                Save Changes
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
