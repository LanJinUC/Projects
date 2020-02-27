<?php
// This is the page of the interface when the client confirms the order
include "Utility.php";
?>

<head>
    <meta charset="UTF-8">
    <title>Order Page</title>
    <style>
        .v1 {
            border-left: 3px solid black;
            height: 60%;
        }

        .v2 {
            border-right: 3px solid black;
            height: 60%;
        }
    </style>
</head>

<!-- global font style -->
<body style="font-family: Consolas,Monaco,Lucida Console,Liberation Mono,DejaVu Sans Mono,Bitstream Vera Sans Mono,Courier New, monospace;">

<div style="background-color:#FFA500;clear:both;text-align:center;">
    ORDER CONFIRMATION
</div>

<?php
// We print out some information about the item and the buyer here
if ($_GET["item_id"] != null) {
    $_SESSION["current_item_id_to_buy"] = $_GET["item_id"];
}
$item_id = $_SESSION["current_item_id_to_buy"];
$client_id_of_buyer = $_SESSION["client_id"];

// Get the balance of the client
$conn = Utility\get_a_connection();
$sql = $conn->prepare("select * from client where id = ?");
$sql->bind_param("s", $client_id_of_buyer);
$sql->execute();
$result = $sql->get_result()->fetch_assoc();
$ucid = $result['ucid'];

$sql = $conn->prepare("select * from ucalgary_member where ucid = ?");
$sql->bind_param("i", $ucid);
$sql->execute();
$result = $sql->get_result()->fetch_assoc();
$balance = $result['balance'];

// Get the item's name, item's price, item's condition, item's type
$sql = $conn->prepare("select * from item where id = ?");
$sql->bind_param("s", $item_id);
$sql->execute();
$result = $sql->get_result()->fetch_assoc();
$item_name = $result['name'];
$item_price = $result['price'];
$item_condition = $result['condition'];
$item_type = $result['type'];

$seller_id = $result['client_id_of_seller'];
// Get the seller's username, seller's phone-number
$sql = $conn->prepare("select * from client where id = ?");
$sql->bind_param("s", $seller_id);
$sql->execute();
$result = $sql->get_result()->fetch_assoc();
$seller_username = $result['username'];
$seller_phone_number = $result['phone_number']

?>

<div class="v1 v2" id="menu" style="background-color:#dddddd;
         height:100%;
         width:100%;
         float:left;
         text-align: center">

    <form style="margin-top:35px" action="<?php echo $_SERVER['PHP_SELF']; ?>" method="POST">

        <p style="font-size:20px;">Order Details</p>

        <p style="font-size:14px;">Item name: <?php echo $item_name ?></p>
        <p style="font-size:14px;">Item condition: <?php echo $item_condition ?></p>
        <p style="font-size:14px;">Item type: <?php echo $item_type ?></p>
        <p style="font-size:14px;">Item price: <?php echo $item_price ?></p>
        <p style="font-size:14px;">Current balance: <?php echo $balance ?></p>
        <p style="font-size:14px;">Seller's username: <?php echo $seller_username ?></p>
        <p style="font-size:14px;">Seller's phone number: <?php echo $seller_phone_number ?></p>

        <p style="font-size:20px;">Please fill out your purchase information here.</p>


        <p style="font-size:14px;">First Name of Receiver</p>
        <label>
            <input type="text" size="20" name="first_name_of_receiver" style="font-size:15px;">
        </label>

        <p style="font-size:14px;">Middle Init of Receiver</p>
        <label>
            <input type="text" size="20" name="middle_init_of_receiver" style="font-size:15px;">
        </label>

        <p style="font-size:14px;">Last Name of Receiver</p>
        <label>
            <input type="text" size="20" name="last_name_of_receiver" style="font-size:15px;">
        </label>

        <p style="font-size:14px;">Address of Receiver</p>
        <label>
            <input type="text" size="20" name="address_of_receiver" style="font-size:15px;">
        </label>

        <p style="font-size:14px;">Phone Number of Receiver (Format: 1-xxx-xxx-xxx)</p>
        <label>
            <input type="text" size="30" name="phone_number_of_receiver" style="font-size:15px;">
        </label>

        <!-- Choose the shipping method -->
        <p style="font-size:14px;">Shipping Method</p>
        <label>
            <select name="shipping_method" style="font-size:15px;">
                <option value="contact_by_buyer">Contact by Buyer</option>
                <option value="online_delivery">Online Delivery</option>
            </select>
        </label>
        <p style="font-size:14px;"></p>

        <input type="submit" name="confirm_order" value="Confirm Order" style="font-size:20px;"/>
        <input type="submit" name="cancel_order" value="Cancel Order" style="font-size:20px;">

        <?php

        if (isset($_POST["cancel_order"])) {
            Utility\alert("Order has been canceled!");
            echo "<script>window.location.href='index.php';</script>";
        }

        if (isset($_POST["confirm_order"])) {
            // Check the first name of the receiver
            if (Utility\is_valid_first_or_last_name($_POST["first_name_of_receiver"])) {
                // Check the middle name of the receiver
                if (Utility\is_valid_middle_initial($_POST["middle_init_of_receiver"])) {
                    // Check the last name of the receiver
                    if (Utility\is_valid_first_or_last_name($_POST["last_name_of_receiver"])) {
                        // Check the phone number of the receiver
                        if (Utility\is_valid_phone_number($_POST["phone_number_of_receiver"])) {
                            // Check if shipping method is online delivery but no enough balance
                            if ($_POST["shipping_method"] === "online_delivery" && $balance < $item_price) {
                                Utility\alert("No enough balance in the account");
                            } else {
                                // Ok, now we add the id as buyer id
                                $sql = $conn->prepare("insert ignore into buyer (client_id) values (?);");
                                $sql->bind_param("s", $client_id_of_buyer);
                                $sql->execute();
                                // And then set the buyer id in the item
                                $sql = $conn->prepare("update item set client_id_of_buyer = ? where id = ?;");
                                $sql->bind_param("ss", $client_id_of_buyer, $item_id);
                                $sql->execute();
                                // Finally, we make the order
                                $sql = $conn->prepare("insert into `order` 
                                        (id, 
                                        item_id, 
                                        total_price, 
                                        address_of_receiver, 
                                        shipping_method, 
                                        first_name_of_receiver,
                                        middle_initial_of_receiver,
                                        last_name_of_receiver,
                                        phone_number_of_receiver,
                                        date_of_order,
                                        client_id_of_seller,
                                        client_id_of_buyer) 
                                        values (uuid(), ?, ?, ?, ?, ?, ?, ?, ?, curdate(), ?, ?);");
                                $sql->bind_param("sdssssssss",
                                    $item_id,
                                    $item_price,
                                    $_POST["address_of_receiver"],
                                    $_POST["shipping_method"],
                                    $_POST["first_name_of_receiver"],
                                    $_POST["middle_init_of_receiver"],
                                    $_POST["last_name_of_receiver"],
                                    $_POST["phone_number_of_receiver"],
                                    $seller_id,
                                    $client_id_of_buyer);
                                if ($sql->execute()) {
                                    // If the buyer choice 'online_delivery', we decrease the balance from its UCAccount and
                                    // update the ucmember balance
                                    if ($_POST["shipping_method"] === "online_delivery") {
                                        $sub_sql = $conn->prepare("update ucalgary_member set balance = balance - ? where ucid = ?;");
                                        $sub_sql->bind_param("di", $item_price, $ucid);
                                        $sub_sql->execute();
                                    }
                                    Utility\alert("Order is made successfully!");
                                    // Return the main page
                                    echo "<script>window.location.href='index.php';</script>";
                                }
                            }
                        } else {
                            Utility\alert("Invalid format of the phone number, it should be like x-xxx-xxx-xxx!");
                        }
                    } else {
                        Utility\alert("The last name should be in uppercase!");
                    }
                } else {
                    Utility\alert("The middle init should be in uppercase or empty!");
                }
            } else {
                Utility\alert("The first name should be in uppercase!");
            }
        }
        ?>
    </form>

</div>

<div id="footer" style="background-color:#FFA500;clear:both;text-align:center;">
    UCalgary Online Secondhand Trading System - CPSC 471 Project Group 3
</div>

<?php
$sql->close();
mysqli_close($conn);
?>

</body>


