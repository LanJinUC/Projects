<?php
// This page indicates the view when a client watches other client by clicking its username in the comments/item page
include "Utility.php";
?>

<head>
    <meta charset="UTF-8">
    <title>Client Info View</title>
</head>

<body style="font-family: Consolas,Monaco,Lucida Console,Liberation Mono,DejaVu Sans Mono,Bitstream Vera Sans Mono,Courier New, monospace;">

<div id="header">
    <form style="margin-top:10px;"
          method="post"
          action="<?php echo $_SERVER['PHP_SELF']; ?>">
        <fieldset>
            <?php

            // If an admin logins, she or he should check the information from backend
            if (isset($_SESSION["administrator_id"])) {
                Utility\alert("Administrator should check the client's information from the database manager!");
                echo "<script>window.location.href='index.php';</script>";
            }

            // Show the username as text and items_published, publish_an_item, account_setting, my_order, logout as 5 buttons
            echo '<legend>Welcome!</legend>' . $_SESSION["client_username"] . "   ";
            echo '<input type="submit" name="client_personal_info" value="Personal Info" style = "font-size:20px">';
            echo '<input type="submit" name="published_items" value="My Published Items" style = "font-size:20px">';
            echo '<input type="submit" name="check_orders_made" value="Check orders made" style = "font-size:20px">';
            echo '<input type="submit" name="check_orders_received" value="Check orders received" style = "font-size:20px">';
            echo '<input type="submit" name="wish_list" value="Wish List" style = "font-size:20px">';
            echo '<input type="submit" name="publish_an_item" value="Publish an Item" style = "font-size:20px">';
            echo '<input type="submit" name="client_logout" value="Logout" style = "font-size:20px">';
            ?>
        </fieldset>
    </form>
</div>

<?php

if (isset($_POST["publish_an_item"])) {
    echo "<script>window.location.href='publish_an_item.php';</script>";
}

if (isset($_POST["published_items"])) {
    echo "<script>window.location.href='items_published.php';</script>";
}

if (isset($_POST["check_orders_made"])) {
    echo "<script>window.location.href='orders_made.php';</script>";
}

if (isset($_POST["check_orders_received"])) {
    echo "<script>window.location.href='orders_received.php';</script>";
}

if (isset($_POST["wish_list"])) {
    echo "<script>window.location.href='wish_list.php';</script>";
}

if (isset($_POST["client_logout"])) {
    // Update and account status to offline
    $conn = Utility\get_a_connection();
    $sql = $conn->prepare("update client set account_status = 'offline' where id = ?;");
    $sql->bind_param("s", $_SESSION["client_id"]);
    $sql->execute();
    $sql->close();
    // Unset
    unset($_SESSION["client_id"]);
    unset($_SESSION["client_username"]);
    // Turn to the main page
    echo "<script>window.location.href='index.php';</script>";
}

if (isset($_POST["client_personal_info"])) {
    // Call 'client_personal_info.php'
    echo "<script>window.location.href='client_personal_info.php';</script>";
}
?>

<?php
// Get all info here! Assume we already have $_GET['client_username']
$conn = Utility\get_a_connection();
$sql = $conn->prepare("select * from client where username = ?");

$sql->bind_param("s", $_GET['client_username']);
$sql->execute();
$result = $sql->get_result()->fetch_assoc();
$address = $result["address"];
$account_status = $result["account_status"];
$phone_number = $result["phone_number"];
$date_of_registration = $result["date_of_registration"];
$username = $result["username"];

$sql->close();
mysqli_close($conn);
?>

<h3>Client Info View</h3>
<p>Username: <?php echo $username; ?></p>
<p>Address: <?php echo $address; ?></p>
<p>Account Status: <?php echo $account_status; ?></p>
<p>Phone Number: <?php echo $phone_number; ?></p>
<p>Date of Registration: <?php echo $date_of_registration; ?></p>


</body>
