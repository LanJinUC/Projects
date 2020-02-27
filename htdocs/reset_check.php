<?php
// This page is the interface to double-check a registered user by its campus email and its UCID before resetting its password
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

        <label>UCID</label>
        <label>
            <input type="text" name="ucid">
        </label>
        <label></label>

        <label>Campus Email</label>
        <label>
            <input type="text" name="campus_email">
        </label>
        <label></label>

        <div class="center">
            <input type="submit" name="submit" value="Submit" class="btn brand z-depth-0">
        </div>
    </form>
</section>

<?php

if (isset($_POST['submit'])) {

    // Create connection
    $conn = Utility\get_a_connection();
    $can_reset = false;
    $account_reset = false;

    // Check UCID
    $ucid_number = intval($_POST["ucid"]);
    // Check if the username and the password exists
    $sql = $conn->prepare("select * from ucalgary_member where ucid = ?");
    $sql->bind_param("i", $ucid_number);
    $sql->execute();
    // No matches found, pop out a message
    if ($sql->get_result()->num_rows === 0) {
        Utility\alert("UCID does not exist!");
    } else {
        // If UCID exists, check if the UCID and the email matches
        $sql = $conn->prepare("select * from ucalgary_member where ucid = ? and campus_email_address = ?");
        $sql->bind_param("is", $ucid_number, $_POST["campus_email"]);
        $sql->execute();
        if ($sql->get_result()->num_rows === 0) {
            Utility\alert("UCID and the campus email address does not match!");
        } else {
            // Check if the current member is already registered
            $sql = $conn->prepare("select * from client where ucid = ?");
            $sql->bind_param("i", $ucid_number);
            $sql->execute();
            if ($sql->get_result()->num_rows !== 0) {
                $can_reset = true;
            } else {
                Utility\alert("Current UCID has not been registered!");
            }
        }
    }

    if ($can_reset === true) {

        $sql = $conn->prepare("select password_question from client where ucid = ?;");
        $sql->bind_param("i", $ucid_number);
        $sql->execute();

        // Pass 'ucid' to reset_new_password.php by using session
        $_SESSION['ucid'] = $ucid_number;
        $_SESSION['password_question'] = $sql->get_result()->fetch_assoc()["password_question"];

        // Close connection
        $sql->close();
        mysqli_close($conn);

        // Turn to next page to press the answer and reset the password
        echo "<script>window.location.href='reset_new_password.php';</script>";
    } else {
        // Close connection
        $sql->close();
        mysqli_close($conn);
    }
}

include('footer.php');
?>