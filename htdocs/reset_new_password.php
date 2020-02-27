<?php
// This page is the interface for a validated registered user to reset its password
include "Utility.php";
?>

    <head>
        <meta charset="UTF-8">
        <title>Reset the Account</title>
        <!-- Compiled and minified CSS -->
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0/css/materialize.min.css">
        <style type="text/css">
            .brand {
                background: #cbb09c !important;
            }

            .brand-text {
                color: #cbb09c !important;
            }

            form {
                max-width: 460px;
                margin: 20px auto;
                padding: 20px;
            }
        </style>
    </head>
    <body class="grey lighten-4">
<nav class="white z-depth-0">
    <div class="container">
        <a href="#" class="brand-logo brand-text">UCalgary Secondhand Online Store</a>
        <ul id="nav-mobile" class="right hide-on-small-and-down">
            <li><a href="index.php" class="btn brand z-depth-0">BACK</a></li>
        </ul>
    </div>
</nav>

<section class="container grey-text">
    <h5 class="center">Reset your account</h5>

    <form class="white" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="POST">

        <label>Password Question</label>
        <label>
            <input type="text" name="question" value="<?php echo $_SESSION['password_question']; ?>"
                   readonly="readonly">
        </label>
        <label></label>

        <label>Answer of the Question</label>
        <label>
            <input type="text" name="answer">
        </label>
        <label></label>

        <label>New Password</label>
        <label>
            <input type="password" name="new_password">
        </label>
        <label></label>

        <label>New Password Again</label>
        <label>
            <input type="password" name="new_password_again">
        </label>
        <label></label>

        <div class="center">
            <input type="submit" name="submit" value="Submit" class="btn brand z-depth-0">
        </div>
    </form>
</section>

<?php

if (isset($_POST['submit'])) {

    $password_reset = false;

    // Create connection
    $conn = Utility\get_a_connection();
    // Check if the answer is correct
    $sql = $conn->prepare("select answer_of_password_question from client where ucid = ?;");
    $sql->bind_param("i", $_SESSION["ucid"]);
    $sql->execute();
    if ($_POST["answer"] === $sql->get_result()->fetch_assoc()["answer_of_password_question"]) {
        // Check the length of the new password
        if (Utility\password_min_length($_POST["new_password"]) === true) {
            // Check if the user presses the same password again
            if ($_POST["new_password_again"] === $_POST["new_password"]) {
                // Update the new password in 'client'
                $sql = $conn->prepare("update client set password = ? where ucid = ?;");
                $sql->bind_param("si", $_POST["new_password"], $_SESSION["ucid"]);
                $sql->execute();
                Utility\alert("Password updated!");
                $password_reset = true;
            } else {
                Utility\alert("Password re-entered not matched!");
            }
        } else {
            Utility\alert("Minimum length of the password should be >= 6!");
        }
    } else {
        Utility\alert("Answer not correct!");
    }
    // Close connection
    $sql->close();
    mysqli_close($conn);
    // Return to the main page if the password is reset
    if ($password_reset === true) {
        echo "<script>window.location.href='index.php';</script>";
    }
}

include('footer.php');
?>