<?php
// This page is the interface for a new ucmember to register
include "Utility.php";
?>

<head>
    <meta charset="UTF-8">
    <title>Register an Account</title>
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
            <li><a href="index.php" class="btn brand z-depth-0">BACK</a></li>   <!-- press BACK to go homepage -->
        </ul>
    </div>
</nav>

<section class="container grey-text">
    <h5 class="center">Create your account</h5>

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

        <label>Username</label>
        <label>
            <input type="text" name="username">
        </label>
        <label></label>

        <label>Password</label>
        <label>
            <input type="password" name="password">
        </label>
        <label></label>

        <label>Password Question</label>
        <label>
            <input type="text" name="question">
        </label>
        <label></label>

        <label>Answer of the Question</label>
        <label>
            <input type="text" name="answer">
        </label>
        <label></label>

        <label>Phone Number</label>
        <label>
            <input type="text" name="phone_number">
        </label>
        <label></label>

        <label>Address</label>
        <label>
            <input type="text" name="address">
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
    $can_register = false;
    $account_inserted = false;

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
                Utility\alert("Current UCID has been registered!");
            } else {
                $can_register = true;
            }
        }
    }

    if ($can_register === true) {
        // Check the length of the username
        if (Utility\username_min_length($_POST["username"]) === true) {
            // Check if the username is unique
            if (Utility\is_client_username_unique($_POST["username"]) === true) {
                // Check the length of the password
                if (Utility\password_min_length($_POST["password"]) === true) {
                    // Check the length of the password question
                    if (Utility\password_question_min_length($_POST["question"]) === true) {
                        // Check the length of the answer of the password question
                        if (Utility\answer_for_password_question_min_length($_POST["answer"]) === true) {
                            // Check the format of the phone number
                            if (Utility\is_valid_phone_number($_POST["phone_number"]) === true) {
                                // Insert a new item to 'client'
                                $sql = $conn->prepare("insert into client 
                                    (id, 
                                     ucid, 
                                     password, 
                                     password_question, 
                                     answer_of_password_question, 
                                     address, 
                                     phone_number, 
                                     date_of_registration, username) values (uuid(), ?, ?, ?, ?, ?, ?, curdate(), ?);");
                                $sql->bind_param("issssss",
                                    $_POST["ucid"],
                                    $_POST["password"],
                                    $_POST["question"],
                                    $_POST["answer"],
                                    $_POST["address"],
                                    $_POST["phone_number"],
                                    $_POST["username"]);
                                $sql->execute();
                                Utility\alert("Successfully registered!");
                                $account_inserted = true;
                            } else {
                                Utility\alert("The correct format phone number should be 1-xxx-xxx-xxx!");
                            }
                        } else {
                            Utility\alert("Minimum length of the answer for password question should be >= 6!");
                        }
                    } else {
                        Utility\alert("Minimum length of the password question should be >= 6!");
                    }
                } else {
                    Utility\alert("Minimum length of the password should be >= 6!");
                }
            } else {
                Utility\alert("Username exists!");
            }
        } else {
            Utility\alert("Minimum length of the username should be >= 6!");
        }
    }

    // Close connection
    $sql->close();
    mysqli_close($conn);
    // Return to the main page if the account is created
    if ($account_inserted === true) {
        echo "<script>window.location.href='index.php';</script>";
    }
}

include('footer.php');
?>
