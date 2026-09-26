
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
| Get Note
|--------------------------------------------------------------------------
*/

$stmt = $conn->prepare(
    "SELECT
        note_id,
        title,
        content,
        related_date
     FROM notes
     WHERE note_id = ?
     AND user_id = ?"
);

$stmt->bind_param(
    "ii",
    $note_id,
    $user_id
);

$stmt->execute();

$result = $stmt->get_result();

$note = $result->fetch_assoc();

$stmt->close();


/*
|--------------------------------------------------------------------------
| Note Not Found
|--------------------------------------------------------------------------
*/

if (!$note) {
    redirect("index.php");
}


$page_title = "Edit Note";

$error = "";
$success = "";


/*
|--------------------------------------------------------------------------
| Update Note
|--------------------------------------------------------------------------
*/

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $title = trim($_POST["title"] ?? "");
    $content = trim($_POST["content"] ?? "");
    $related_date = trim($_POST["related_date"] ?? "");


    /*
    |--------------------------------------------------------------------------
    | Validation
    |--------------------------------------------------------------------------
    */

    if ($title == "") {

        $error = "Please enter a note title.";

    } elseif ($content == "") {

        $error = "Please enter note content.";

    } else {

        /*
        |--------------------------------------------------------------------------
        | Convert Empty Date To NULL
        |--------------------------------------------------------------------------
        */

        if ($related_date == "") {
            $related_date = null;
        }


        /*
        |--------------------------------------------------------------------------
        | Update
        |--------------------------------------------------------------------------
        */

        $stmt = $conn->prepare(
            "UPDATE notes
             SET title = ?,
                 content = ?,
                 related_date = ?
             WHERE note_id = ?
             AND user_id = ?"
        );

        $stmt->bind_param(
            "sssii",
            $title,
            $content,
            $related_date,
            $note_id,
            $user_id
        );


        if ($stmt->execute()) {

            $success = "Note updated successfully.";

            /*
            |--------------------------------------------------------------------------
            | Update Local Values
            |--------------------------------------------------------------------------
            */

            $note["title"] = $title;
            $note["content"] = $content;
            $note["related_date"] = $related_date;

        } else {

            $error = "Failed to update note.";
        }


        $stmt->close();
    }
}


require_once "../includes/header.php";
require_once "../includes/navbar.php";
require_once "../includes/sidebar.php";

?>

<main class="main-content">

    <h1 class="page-title">
        Edit Note
    </h1>

    <p class="page-description">
        Update your personal note.
    </p>


    <?php if ($success != "") { ?>

        <div style="
            background-color: #dcfce7;
            color: #166534;
            padding: 12px;
            margin-bottom: 20px;
            border-radius: 6px;
        ">

            <?php echo htmlspecialchars($success); ?>

        </div>

    <?php } ?>


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
                    value="<?php echo htmlspecialchars($note["title"]); ?>"
                    maxlength="150"
                    required
                    style="
                        width: 100%;
                        padding: 10px;
                        margin-top: 5px;
                    "
                >

            </div>


            <div style="margin-bottom: 15px;">

                <label>
                    Content
                </label>

                <br>

                <textarea
                    name="content"
                    rows="8"
                    required
                    style="
                        width: 100%;
                        padding: 10px;
                        margin-top: 5px;
                    "
                ><?php echo htmlspecialchars($note["content"]); ?></textarea>

            </div>


            <div style="margin-bottom: 20px;">

                <label>
                    Related Date
                </label>

                <br>

                <input
                    type="date"
                    name="related_date"
                    value="<?php echo htmlspecialchars($note["related_date"] ?? ""); ?>"
                    style="
                        width: 100%;
                        padding: 10px;
                        margin-top: 5px;
                    "
                >

            </div>


            <button
                type="submit"
                class="btn btn-primary"
            >
                Update Note
            </button>


            <a
                href="index.php"
                class="btn"
                style="
                    margin-left: 5px;
                    background-color: #e5e7eb;
                    color: #333;
                "
            >
                Cancel
            </a>

        </form>

    </div>

</main>


<?php

require_once "../includes/footer.php";

?>
