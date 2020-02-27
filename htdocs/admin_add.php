<?php
// This is the interface for an administrator to add a new administrator
include "Utility.php";
?>

<head>
    <meta charset="UTF-8">
    <title>Add an Administrator</title>
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

<h2>Add an Administrator</h2>

<form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="POST">

    <p style="font-size:18px;">New Admin Info</p>
    <label>
        Username: <input type="text" name="input_username" size="40" style="font-size:20px;">
    </label>
    <br><br>

    <label>
        Password: <input type="password" name="input_password" size="40" style="font-size:20px;">
    </label>
    <br><br>

    <label>
        Address: <input type="text" name="input_address" size="41" style="font-size:20px;">
    </label>
    <br><br>

    <label>
        Phone Number: <input type="text" name="input_phone_number" size="36" style="font-size:20px;">
    </label>
    <br><br>

    <label>
        First name in uppercase: <input type="text" name="input_first_name" size="14" style="font-size:20px;">
    </label>
    <br><br>

    <label>
        Middle init in uppercase/empty: <input type="text" name="input_middle_init" size="7" style="font-size:20px;">
    </label>
    <br><br>

    <label>
        Last name in uppercase: <input type="text" name="input_last_name" size="15" style="font-size:20px;">
    </label>
    <br><br>

    <label>
        Date of Birth (Example:1993-01-01): <input type="text" name="date_of_birth" size="15" style="font-size:20px;">
    </label>
    <br><br>

    <input type="submit" name="create_the_account" value="Create the Account" style="font-size:20px;">

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

if (isset($_POST["create_the_account"])) {
    // Check username
    if (Utility\username_min_length($_POST["input_username"])) {
        if (Utility\is_admin_username_unique($_POST["input_username"])) {
            // Check password
            if (Utility\password_min_length($_POST["input_password"])) {
                // Check phone number
                if (Utility\is_valid_phone_number($_POST["input_phone_number"])) {
                    // Check first name
                    if (Utility\is_valid_first_or_last_name($_POST["input_first_name"])) {
                        // Check middle init
                        if (Utility\is_valid_middle_initial($_POST["input_middle_init"])) {
                            // Check last name
                            if (Utility\is_valid_first_or_last_name($_POST["input_last_name"])) {
                                // Check the birthday
                                if (Utility\is_valid_date($_POST["date_of_birth"])) {
                                    // Ok, now we add it to 'administrator'
                                    $conn = Utility\get_a_connection();
                                    $sql = $conn->prepare("insert into administrator (id, 
                                                   password, 
                                                   address, 
                                                   phone_number, 
                                                   date_of_registration, 
                                                   date_of_birth, 
                                                   username,
                                                   first_name, 
                                                   middle_initial, 
                                                   last_name) values 
                                    (uuid(), ?, ?, ?, curdate(), ?, ?, ?, ?, ?);");
                                    $sql->bind_param("ssssssss",
                                        $_POST["input_password"],
                                        $_POST["input_address"],
                                        $_POST["input_phone_number"],
                                        $_POST["date_of_birth"],
                                        $_POST["input_username"],
                                        $_POST["input_first_name"],
                                        $_POST["input_middle_init"],
                                        $_POST["input_last_name"]);
                                    if ($sql->execute()) {
                                        Utility\alert("Account created successfully!");
                                        // Return to the last page
                                        echo "<script>window.location.href='manage_database.php';</script>";
                                    }
                                } else {
                                    Utility\alert("Invalid format of the birthday!");
                                }
                            } else {
                                Utility\alert("Invalid last name, should be in uppercase!");
                            }
                        } else {
                            Utility\alert("Invalid middle init, should be in uppercase or empty!");
                        }
                    } else {
                        Utility\alert("Invalid first name, should be in uppercase!");
                    }
                } else {
                    Utility\alert("Invalid format of the phone number, it should be like 1-xxx-xxx-xxx!");
                }
            } else {
                Utility\alert("Minimum length of the password should be >= 6!");
            }
        } else {
            Utility\alert("Username exists among administrators!");
        }
    } else {
        Utility\alert("Minimum length of the username should be >= 6!");
    }
}

?>

</body>