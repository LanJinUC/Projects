<?php
// This is the interface for an administrator to edit information of a client
include "Utility.php";
?>

<head>
    <meta charset="UTF-8">
    <title>Edit Client Info</title>
</head>

<body style="font-family: Consolas,Monaco,Lucida Console,Liberation Mono,DejaVu Sans Mono,Bitstream Vera Sans Mono,Courier New, monospace;">

<div id="header">
    <form style="margin-top:10px;"
          method="POST"
          action="<?php echo $_SERVER['PHP_SELF']; ?>">
        <fieldset>
            <?php
            // Show the username as text and publish announcement, manage database, account_setting, logout as 4 buttons
            echo '<legend>Welcome!</legend>' . $_SESSION["administrator_username"] . "   ";
            echo '<input type="submit" name="main_page" value="Main Page" style = "font-size:20px">';
            echo '<input type="submit" name="manage_database" value="Database Manager" style = "font-size:20px">';
            echo '<input type="submit" name="admin_logout" value="Logout" style = "font-size:20px">';
            ?>
        </fieldset>
    </form>
</div>

<h2>Edit Client Info</h2>

<?php

// Get all info here!
$conn = Utility\get_a_connection();
$sql = $conn->prepare("select * from client where id = ?");

if (!is_null($_GET['client_id'])) {
    $_SESSION["current_client_to_edit"] = $_GET['client_id'];
}

$sql->bind_param("s", $_SESSION["current_client_to_edit"]);
$sql->execute();
$result = $sql->get_result()->fetch_assoc();
$ucid = $result["ucid"];
$password = $result["password"];
$password_question = $result["password_question"];
$answer_of_password_question = $result["answer_of_password_question"];
$address = $result["address"];
$account_status = $result["account_status"];
$phone_number = $result["phone_number"];
$date_of_registration = $result["date_of_registration"];
$username = $result["username"];

$sql->close();
mysqli_close($conn);

?>

<h3>Current Client Info</h3>
<p>ID: <?php echo $_SESSION["current_client_to_edit"]; ?></p>
<p>Username: <?php echo $username; ?></p>
<p>UCID: <?php echo $ucid; ?></p>
<p>Password: <?php echo $password; ?></p>
<p>Password Question: <?php echo $password_question; ?></p>
<p>Answer of the Password Question: <?php echo $answer_of_password_question; ?></p>
<p>Address: <?php echo $address; ?></p>
<p>Account Status: <?php echo $account_status; ?></p>
<p>Phone Number: <?php echo $phone_number; ?></p>
<p>Date of Registration: <?php echo $date_of_registration; ?></p>

<form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="POST">

    <p style="font-size:18px;">New Client Info</p>
    <label>
        Username: <input type="text" name="input_username" size="40" style="font-size:20px;">
        <input type="submit" name="submit_username" value="Update Username" style="font-size:20px;">
    </label>
    <br><br>

    <label>
        Password: <input type="password" name="input_password" size="40" style="font-size:20px;">
        <input type="submit" name="submit_password" value="Update Password" style="font-size:20px;">
    </label>
    <br><br>

    <label>
        Password Question: <input type="text" name="input_password_question" size="31" style="font-size:20px;">
        <input type="submit" name="submit_password_question" value="Update Password Question" style="font-size:20px;">
    </label>
    <br><br>

    <p style="font-size:16px;">Answer for the Password Question: </p><label>
        <textarea name="input_password_answer_question" rows="10" cols="60" style="font-size:16px;"></textarea>
        <input type="submit" name="submit_answer_for_password_question" value="Update Answer for Password Question"
               style="font-size:20px;">
    </label>
    <br><br>

    <label>
        Address: <input type="text" name="input_address" size="45" style="font-size:20px;">
        <input type="submit" name="submit_address" value="Update Address" style="font-size:20px;">
    </label>
    <br><br>

    <label>
        Phone Number: <input type="text" name="input_phone_number" size="40" style="font-size:20px;">
        <input type="submit" name="submit_phone_number" value="Update Phone Number" style="font-size:20px;">
    </label>
    <br><br>

</form>

<?php

if (isset($_POST["admin_logout"])) {
    // Logout and call'index.php'
    unset($_SESSION["administrator_id"]);
    unset($_SESSION["administrator_username"]);
    unset($_SESSION["current_client_to_edit"]);
    echo "<script>window.location.href='index.php';</script>";
}

if (isset($_POST["main_page"])) {
    // Call 'index.php'
    echo "<script>window.location.href='index.php';</script>";
}

if (isset($_POST["manage_database"])) {
    // Call 'manage_database.php'
    echo "<script>window.location.href='manage_database.php';</script>";
}

// Update the username
if (isset($_POST["submit_username"])) {
    if (Utility\username_min_length($_POST["input_username"])) {
        if (Utility\is_client_username_unique($_POST["input_username"])) {
            var_dump($_POST["input_username"]);
            $new_conn = Utility\get_a_connection();
            $new_sql = $new_conn->prepare("update client set username = ? where id = ?;");
            $new_sql->bind_param("ss", $_POST["input_username"], $_SESSION["current_client_to_edit"]);
            $new_sql->execute();
            Utility\alert("Username updated successfully!");
            $new_sql->close();
            mysqli_close($new_conn);
            // Return to the last page
            echo "<script>window.location.href='manage_database.php';</script>";
        } else {
            Utility\alert("Username exists or you cannot use the same username!");
            // Return to the last page
            echo "<script>window.location.href='manage_database.php';</script>";
        }
    } else {
        Utility\alert("Minimum length of the username should be >= 6!");
        // Return to the last page
        echo "<script>window.location.href='manage_database.php';</script>";
    }
} // Update the password
else if (isset($_POST["submit_password"])) {
    if (Utility\password_min_length($_POST["input_password"])) {
        $new_conn = Utility\get_a_connection();
        $new_sql = $new_conn->prepare("update client set password = ? where id = ?;");
        $new_sql->bind_param("ss", $_POST["input_password"], $_SESSION["current_client_to_edit"]);
        $new_sql->execute();
        Utility\alert("Password updated successfully!");
        $new_sql->close();
        mysqli_close($new_conn);
        // Return to the last page
        echo "<script>window.location.href='manage_database.php';</script>";
    } else {
        Utility\alert("Minimal length of the password should be >= 6!");
        // Return to the last page
        echo "<script>window.location.href='manage_database.php';</script>";
    }
} // Update the password question
else if (isset($_POST["submit_password_question"])) {
    if (Utility\password_question_min_length($_POST["input_password_question"])) {
        $new_conn = Utility\get_a_connection();
        $new_sql = $new_conn->prepare("update client set password_question = ? where id = ?;");
        $new_sql->bind_param("ss", $_POST["input_password_question"], $_SESSION["current_client_to_edit"]);
        $new_sql->execute();
        Utility\alert("Password question updated successfully!");
        $new_sql->close();
        mysqli_close($new_conn);
        // Return to the last page
        echo "<script>window.location.href='manage_database.php';</script>";
    } else {
        Utility\alert("Minimum length of the password question should be >= 6!");
        // Return to the last page
        echo "<script>window.location.href='manage_database.php';</script>";
    }
} // Update the answer for the password question
else if (isset($_POST["submit_answer_for_password_question"])) {
    if (Utility\answer_for_password_question_min_length($_POST["input_password_answer_question"])) {
        $new_conn = Utility\get_a_connection();
        $new_sql = $new_conn->prepare("update client set answer_of_password_question = ? where id = ?;");
        $new_sql->bind_param("ss", $_POST["input_password_answer_question"], $_SESSION["current_client_to_edit"]);
        $new_sql->execute();
        Utility\alert("Answer for the password question updated successfully!");
        $new_sql->close();
        mysqli_close($new_conn);
        // Return to the last page
        echo "<script>window.location.href='manage_database.php';</script>";
    } else {
        Utility\alert("The minimum length for the answer of the password question should be >= 6!");
        // Return to the last page
        echo "<script>window.location.href='manage_database.php';</script>";
    }
} // Update the address
else if (isset($_POST["submit_address"])) {
    $new_conn = Utility\get_a_connection();
    $new_sql = $new_conn->prepare("update client set address = ? where id = ?;");
    $new_sql->bind_param("ss", $_POST["input_address"], $_SESSION["current_client_to_edit"]);
    $new_sql->execute();
    Utility\alert("Address updated successfully!");
    $new_sql->close();
    mysqli_close($new_conn);
    // Return to the last page
    echo "<script>window.location.href='manage_database.php';</script>";
} // Update the phone number
else if (isset($_POST["submit_phone_number"])) {
    if (Utility\is_valid_phone_number($_POST["input_phone_number"])) {
        $new_conn = Utility\get_a_connection();
        $new_sql = $new_conn->prepare("update client set phone_number = ? where id = ?;");
        $new_sql->bind_param("ss", $_POST["input_phone_number"], $_SESSION["current_client_to_edit"]);
        $new_sql->execute();
        Utility\alert("Phone number updated successfully!");
        $new_sql->close();
        mysqli_close($new_conn);
        // Return to the last page
        echo "<script>window.location.href='manage_database.php';</script>";
    } else {
        Utility\alert("Invalid format of the phone number, the correct format should be 1-xxx-xxx-xxx!");
        // Return to the last page
        echo "<script>window.location.href='manage_database.php';</script>";
    }
}


?>

</body>