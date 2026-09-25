```php
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


$page_title = "Notes";

$user_id = getUserId();

$error = "";
$success = "";


/*
|--------------------------------------------------------------------------
| Create New Note
|--------------------------------------------------------------------------
*/

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $title = trim($_POST["title"]);
    $content = trim($_POST["content"]);
    $related_date = trim($_POST["related_date"]);


    // Check required fields
    if ($title == "" || $content == "") {

        $error = "Please enter a title and content.";

    } else {

        /*
        |--------------------------------------------------------------------------
        | Insert Note
        |--------------------------------------------------------------------------
        */

        if ($related_date == "") {

            $stmt = $conn->prepare(
                "INSERT INTO notes
                (user_id, title, content, related_date)
                VALUES (?, ?, ?, NULL)"
            );

            $stmt->bind_param(
                "iss",
                $user_id,
                $title,
                $content
            );

        } else {

            $stmt = $conn->prepare(
                "INSERT INTO notes
                (user_id, title, content, related_date)
                VALUES (?, ?, ?, ?)"
            );

            $stmt->bind_param(
                "isss",
                $user_id,
                $title,
                $content,
                $related_date
            );
        }


        if ($stmt->execute()) {

            $success = "Note created successfully.";

        } else {

            $error = "Something went wrong while creating the note.";
        }


        $stmt->close();
    }
}


/*
|--------------------------------------------------------------------------
| Get User Notes
|--------------------------------------------------------------------------
*/

$stmt = $conn->prepare(
    "SELECT
        note_id,
        title,
        content,
        related_date,
        created_at,
        updated_at
     FROM notes
     WHERE user_id = ?
     ORDER BY created_at DESC"
);

$stmt->bind_param(
    "i",
    $user_id
);

$stmt->execute();

$notes = $stmt->get_result();

$stmt->close();


require_once "../includes/header.php";
require_once "../includes/navbar.php";
require_once "../includes/sidebar.php";

?>

<main class="main-content">

    <h1 class="page-title">
        Notes
    </h1>

    <p class="page-description">
        Keep important personal notes and reminders.
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


    <!-- Create Note -->

    <div class="card">

        <h2>
            Create Note
        </h2>


        <form method="POST">

            <div style="margin-bottom: 15px;">

                <label>
                    Title
                </label>

                <br>

                <input
                    type="text"
                    name="title"
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
                    rows="5"
                    required
                    style="
                        width: 100%;
                        padding: 10px;
                        margin-top: 5px;
                    "
                ></textarea>

            </div>


            <div style="margin-bottom: 20px;">

                <label>
                    Related Date
                </label>

                <br>

                <input
                    type="date"
                    name="related_date"
                    style="
                        padding: 10px;
                        margin-top: 5px;
                    "
                >

            </div>


            <button
                type="submit"
                class="btn btn-primary"
            >
                Add Note
            </button>

        </form>

    </div>


    <br>


    <!-- Existing Notes -->

    <div class="card">

        <h2>
            My Notes
        </h2>


        <?php if ($notes->num_rows > 0) { ?>

            <?php while ($note = $notes->fetch_assoc()) { ?>

                <div style="
                    border: 1px solid #ddd;
                    padding: 15px;
                    margin-bottom: 15px;
                    border-radius: 6px;
                ">

                    <h3>
                        <?php echo htmlspecialchars($note["title"]); ?>
                    </h3>


                    <p>
                        <?php
                        echo nl2br(
                            htmlspecialchars($note["content"])
                        );
                        ?>
                    </p>


                    <?php if ($note["related_date"] != NULL) { ?>

                        <p>
                            <strong>Related Date:</strong>

                            <?php
                            echo htmlspecialchars(
                                $note["related_date"]
                            );
                            ?>
                        </p>

                    <?php } ?>


                    <small>
                        Created:
                        <?php
                        echo htmlspecialchars(
                            $note["created_at"]
                        );
                        ?>
                    </small>


                    <br><br>


                    <a
                        href="edit.php?id=<?php echo $note["note_id"]; ?>"
                        class="btn"
                    >
                        Edit
                    </a>


                    <a
                        href="delete.php?id=<?php echo $note["note_id"]; ?>"
                        class="btn"
                        onclick="return confirm('Are you sure you want to delete this note?');"
                    >
                        Delete
                    </a>

                </div>

            <?php } ?>

        <?php } else { ?>

            <p>
                You don't have any notes yet.
            </p>

        <?php } ?>

    </div>

</main>


<?php

require_once "../includes/footer.php";

?>
```
