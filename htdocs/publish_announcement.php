<?php
// This page is the interface for the administrators to publish an announcement
include "Utility.php";
?>

<head>
    <meta charset="UTF-8">
    <title>Publish an Announcement</title>
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

<h2>Create an Announcement</h2>
<form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="POST">

    <p style="font-size:18px;">TITLE </p><label>
        <input type="text" name="title" size="61" style="font-size:15px;">
    </label>
    <br><br>

    <p style="font-size:18px;">CONTENT </p><label>
        <textarea name="content" rows="10" cols="60" style="font-size:15px;"></textarea>
    </label>
    <br><br>

    <input type="submit" name="submit" value="Submit" style="font-size:20px;">
    <input type="submit" name="clear" value="Clear" style="font-size:20px;">
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

if (isset($_POST["submit"])) {

    // Create a connection
    $conn = Utility\get_a_connection();

    if (Utility\content_minimum_length($_POST["content"])) {
        // Insert to the table 'announcement'
        $sql = $conn->prepare("insert into announcement (id, content, post_date, title) values (uuid(), ?, curdate(), ?);");
        $sql->bind_param("ss", $_POST["content"], $_POST["title"]);
        $sql->execute();
        $sql->close();
        mysqli_close($conn);
        Utility\alert("Announcement created successfully!");
        echo "<script>window.location.href='index.php';</script>";
    } else {
        Utility\alert("Minimum length of the content should be >= 6!");
    }

    // Close the connection
    mysqli_close($conn);
}

?>

</body>