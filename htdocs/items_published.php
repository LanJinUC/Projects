<?php
include "Utility.php";
?>

<head>
    <meta charset="utf-8">
    <title>All Published Items</title>
</head>

<!-- global font style -->
<body style="font-family: Consolas,Monaco,Lucida Console,Liberation Mono,DejaVu Sans Mono,Bitstream Vera Sans Mono,Courier New, monospace;">

<div id="header">
    <form style="margin-top:10px;"
          method="post"
          action="<?php echo $_SERVER['PHP_SELF']; ?>">
        <fieldset>
            <?php
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
    echo "<script>window.location.href='index.php';</script>";
}
if (isset($_POST["client_personal_info"])) {
    // Call 'client_personal_info.php'
    echo "<script>window.location.href='client_personal_info.php';</script>";
}
?>

<?php
$conn = Utility\get_a_connection();
$sql = $conn->prepare("select * from item where client_id_of_seller = ?");
$sql->bind_param("s", $_SESSION["client_id"]);
$sql->execute();
$result = $sql->get_result();
while ($row = $result->fetch_assoc()) {
    echo '<div><hr>';
    echo '<a style = "font-size:15px;position: relative; left: 30px;" href="item.php?id=' . $row["id"] . '">' . 'Item ID:' . $row["id"] . '</a>';
    echo '<p style = "font-size:15px;position: relative; left: 30px;">Item Name:' . $row['name'] . '</p>';
    echo '<p style = "font-size:15px;position: relative; left: 30px;">Item Condition:' . Utility\item_cond[$row['condition']] . '</p>';
    echo '<p style = "font-size:15px;position: relative; left: 30px;">Item Type:' . Utility\item_typename[$row['type']] . '</p>';
    echo '<p style = "font-size:15px;position: relative; left: 30px;">Item Price:' . $row['price'] . '</p>';
    echo '<hr></div>';
}

$sql->close();
mysqli_close($conn);
?>

<div id="footer" style="background-color:#FFA500;clear:both;text-align:center;">
    UCalgary Online Secondhand Trading System - CPSC 471 Project Group 3
</div>

</body>