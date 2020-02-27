<?php
// This page indicates the view when the client watches its personal information.
include "Utility.php";
?>

<head>
    <title>UCalgary Secondhand Online Store</title>
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

<?php

$conn = Utility\get_a_connection();
$sql = $conn->prepare("select * from client where id = ?");
$sql->bind_param("s", $_SESSION["client_id"]);
$sql->execute();
$result = $sql->get_result()->fetch_assoc();

$current_ucid = $result["ucid"];
$current_address = $result["address"];
$current_phone_number = $result["phone_number"];
$date_of_registration = $result["date_of_registration"];

$sql = $conn->prepare("select * from ucalgary_member where ucid = ?");
$sql->bind_param("i", $current_ucid);
$sql->execute();
$result = $sql->get_result()->fetch_assoc();

$current_balance = $result["balance"];
$date_of_birth = $result["date_of_birth"];
$current_email = $result["campus_email_address"];

?>

<section class="container grey-text">
    <h5 class="center">Account Information</h5>

    <form class="white" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="POST">

        <label>UCID</label>
        <label>
            <input type="text" name="ucid" value="<?php echo $current_ucid; ?>" readonly="readonly">;
        </label>

        <label>Campus Email</label>
        <label>
            <input type="text" name="campus_email" value="<?php echo $current_email; ?>" readonly="readonly">;
        </label>

        <label>Current Balance</label>
        <label>
            <input type="text" name="current_balance" value="<?php echo $current_balance; ?>" readonly="readonly">;
        </label>

        <label>Date of Birth</label>
        <label>
            <input type="text" name="date_of_birth" value="<?php echo $date_of_birth; ?>" readonly="readonly">;
        </label>

        <label>Date of Registration</label>
        <label>
            <input type="text" name="date_of_registration" value="<?php echo $date_of_registration; ?>"
                   readonly="readonly">;
        </label>

        <label>Current Username</label>
        <label>
            <input type="text" name="username" value="<?php echo $_SESSION["client_username"]; ?>" readonly="readonly">;
        </label>

        <label>New Username</label>
        <label>
            <input type="text" name="new_username">
        </label>
        <div class="right">
            <input type="submit" name="submit_username" value="Update Username" class="btn brand z-depth-0">
        </div>

        <label>Current Phone Number</label>
        <label>
            <input type="text" name="phone_number" value="<?php echo $current_phone_number; ?>" readonly="readonly">;
        </label>

        <label>New Phone Number</label>
        <label>
            <input type="text" name="new_phone_number">
        </label>
        <div class="right">
            <input type="submit" name="submit_phone_number" value="Update Phone Number" class="btn brand z-depth-0">
        </div>

        <label>Current Address</label>
        <label>
            <input type="text" name="current_address" value="<?php echo $current_address; ?>" readonly="readonly">;
        </label>

        <label>New Address</label>
        <label>
            <input type="text" name="new_address">
        </label>
        <div class="right">
            <input type="submit" name="submit_address" value="Update Address" class="btn brand z-depth-0">
        </div>

        <label>New Password</label>
        <label>
            <input type="password" name="new_password">
        </label>
        <div class="right">
            <input type="submit" name="submit_password" value="Update Password" class="btn brand z-depth-0">
        </div>

        <label>New Password Question</label>
        <label>
            <input type="text" name="new_question">
        </label>
        <div class="right">
            <input type="submit" name="submit_password_question" value="Update Password Question"
                   class="btn brand z-depth-0">
        </div>

        <label>New Answer of the Question</label>
        <label>
            <input type="text" name="new_answer">
        </label>
        <div class="right">
            <input type="submit" name="submit_password_answer" value="Update Answer" class="btn brand z-depth-0">
        </div>

    </form>
</section>

<?php
// Update the username
if (isset($_POST["submit_username"])) {
    if (Utility\is_client_username_unique($_POST["new_username"])) {
        if (Utility\username_min_length($_POST["new_username"])) {
            $sql = $conn->prepare("update client set username = ? where id = ?");
            $sql->bind_param("ss", $_POST["new_username"], $_SESSION["client_id"]);
            $sql->execute();
            $_SESSION["client_username"] = $_POST["new_username"];
            Utility\alert("Username updated successfully!");
            echo "<script>window.location.href='index.php';</script>";
        } else {
            Utility\alert("Minimum length of the username should be >= 6!");
        }
    } else {
        Utility\alert("Username exists!");
    }
}
// Update the phone number
if (isset($_POST["submit_phone_number"])) {
    if (Utility\is_valid_phone_number($_POST["new_phone_number"])) {
        $sql = $conn->prepare("update client set phone_number = ? where id = ?;");
        $sql->bind_param("ss", $_POST["new_phone_number"], $_SESSION["client_id"]);
        $sql->execute();
        Utility\alert("Phone number updated successfully!");
        echo "<script>window.location.href='index.php';</script>";
    } else {
        Utility\alert("Invalid format of phone number!");
    }
}
// Update the address
if (isset($_POST["submit_address"])) {
    $sql = $conn->prepare("update client set address = ? where id = ?;");
    $sql->bind_param("ss", $_POST["new_address"], $_SESSION["client_id"]);
    $sql->execute();
    Utility\alert("Address updated successfully!");
    echo "<script>window.location.href='index.php';</script>";
}
// Update the password
if (isset($_POST["submit_password"])) {
    if (Utility\password_min_length($_POST["new_password"])) {
        $sql = $conn->prepare("update client set password = ? where id = ?;");
        $sql->bind_param("ss", $_POST["new_password"], $_SESSION["client_id"]);
        $sql->execute();
        Utility\alert("Password updated successfully!");
        echo "<script>window.location.href='index.php';</script>";
    } else {
        Utility\alert("Minimum length of the password should be >= 6!");
    }
}
// Update the new password question
if (isset($_POST["submit_password_question"])) {
    if (Utility\password_question_min_length($_POST["new_question"])) {
        $sql = $conn->prepare("update client set password_question = ? where id = ?;");
        $sql->bind_param("ss", $_POST["new_question"], $_SESSION["client_id"]);
        $sql->execute();
        Utility\alert("New password question updated successfully!");
        echo "<script>window.location.href='index.php';</script>";
    } else {
        Utility\alert("Minimum length of the password question should be >= 6!");
    }
}
// Update the new answer of the password question
if (isset($_POST["submit_password_answer"])) {
    if (Utility\answer_for_password_question_min_length($_POST["new_answer"])) {
        $sql = $conn->prepare("update client set answer_of_password_question = ? where id = ?");
        $sql->bind_param("ss", $_POST["new_answer"], $_SESSION["client_id"]);
        $sql->execute();
        Utility\alert("New answer of the password question updated successfully!");
        echo "<script>window.location.href='index.php';</script>";
    } else {
        Utility\alert("Minimum length of the answer for password question should be >= 6!");
    }
}


$sql->close();
mysqli_close($conn);
?>

<div id="footer" style="background-color:#FFA500;clear:both;text-align:center;">
    UCalgary Online Secondhand Trading System - CPSC 471 Project Group 3
</div>
