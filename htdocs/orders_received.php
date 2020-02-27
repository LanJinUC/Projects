<?php
include "Utility.php";
?>

<head>
    <meta charset="utf-8">
    <title>Orders Received</title>
    <script>
        function doCompleteOrder(id) {
            if (confirm("Are you sure to complete the order？")) {
                window.location = 'manage_database_actions.php?action=complete_order&id=' + id;
            }
        }
    </script>
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
$sql = $conn->prepare("select * from `order` where client_id_of_seller = ?");
$sql->bind_param("s", $_SESSION["client_id"]);
$sql->execute();
$result = $sql->get_result();
while ($row = $result->fetch_assoc()) {

    $sub_sql = $conn->prepare("select * from item where id = ?");
    $sub_sql->bind_param("s", $row['item_id']);
    $sub_sql->execute();
    $sub_result = $sub_sql->get_result()->fetch_assoc();

    $sub_sql2 = $conn->prepare("select * from client where id = ?");
    $sub_sql2->bind_param("s", $row['client_id_of_buyer']);
    $sub_sql2->execute();
    $sub_result2 = $sub_sql2->get_result()->fetch_assoc();

    echo '<div><hr>';
    echo '<p style = "font-size:15px;position: relative; left: 30px;">Order ID:' . $row['id'] . '</p>';
    echo '<p style = "font-size:15px;position: relative; left: 30px;">Order Date:' . $row['date_of_order'] . '</p>';
    echo '<p style = "font-size:15px;position: relative; left: 30px;">Item ID:' . $row['item_id'] . '</p>';
    echo '<p style = "font-size:15px;position: relative; left: 30px;">Item Name:' . $sub_result['name'] . '</p>';
    echo '<p style = "font-size:15px;position: relative; left: 30px;">Item Condition:' . Utility\item_cond[$sub_result['condition']] . '</p>';
    echo '<p style = "font-size:15px;position: relative; left: 30px;">Item Type:' . Utility\item_typename[$sub_result['type']] . '</p>';
    echo '<p style = "font-size:15px;position: relative; left: 30px;">Item Price:' . $sub_result['price'] . '</p>';
    echo '<p style = "font-size:15px;position: relative; left: 30px;">Buyer Username:' . $sub_result2['username'] . '</p>';
    echo '<p style = "font-size:15px;position: relative; left: 30px;">Buyer Phone Number:' . $sub_result2['phone_number'] . '</p>';
    echo '<p style = "font-size:15px;position: relative; left: 30px;">The First Name of Receiver:' . $row['first_name_of_receiver'] . '</p>';
    echo '<p style = "font-size:15px;position: relative; left: 30px;">The Middle Init of Receiver:' . $row['middle_initial_of_receiver'] . '</p>';
    echo '<p style = "font-size:15px;position: relative; left: 30px;">The Phone Number of Receiver:' . $row['phone_number_of_receiver'] . '</p>';
    echo '<p style = "font-size:15px;position: relative; left: 30px;">The Address of Receiver:' . $row['address_of_receiver'] . '</p>';
    echo '<p style = "font-size:15px;position: relative; left: 30px;">Shipping Method:' . $row['shipping_method'] . '</p>';
    echo '<p style = "font-size:15px;position: relative; left: 30px;">Total Price:' . $row['total_price'] . '</p>';
    $temp_string = $row['id'];
    echo "<a style = 'font-size:15px;position: relative; left: 30px;' href='javascript:doCompleteOrder(\"$temp_string\")'>Complete the Order</a>";
    echo '<br><hr></div>';
}

$sql->close();
mysqli_close($conn);
?>

<div id="footer" style="background-color:#FFA500;clear:both;text-align:center;">
    UCalgary Online Secondhand Trading System - CPSC 471 Project Group 3
</div>

</body>