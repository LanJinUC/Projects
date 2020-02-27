<?php
// This is the interface for showing an item
include "Utility.php";

// Turn to the main page if no one logins
if (!isset($_SESSION["client_id"]) && !isset($_SESSION["administrator_id"])) {
    Utility\alert("You must login first!");
    echo "<script>window.location.href='index.php';</script>";
}
?>

<head>
    <meta charset="UTF-8">
    <title>Item Page</title>
    <style>
        .v1 {
            border-left: 3px solid black;
            height: 60%;
        }

        .v2 {
            border-right: 3px solid black;
            height: 60%;
        }

        .left {
            float: left;
            width: 35%;
            height: 100%;
            background-color: #dce2bc;
        }

        .right {
            float: right;
            background-color: #eeeeee;
            width: 65%;
            height: 100%;
        }
    </style>
</head>

<!-- global font style -->
<body style="font-family: Consolas,Monaco,Lucida Console,Liberation Mono,DejaVu Sans Mono,Bitstream Vera Sans Mono,Courier New, monospace;">

<div id="header">
    <form style="margin-top:10px;"
          method="post"
          action="<?php echo $_SERVER['PHP_SELF']; ?>">
        <fieldset>
            <?php
            // Show the welcome interface if a client logins
            if (isset($_SESSION["client_id"])) {
                // Show the username as text and items_published, publish_an_item, account_setting, my_order, logout as 5 buttons
                echo '<legend>Welcome!</legend>' . $_SESSION["client_username"] . "   ";
                echo '<input type="submit" name="client_personal_info" value="Personal Info" style = "font-size:20px">';
                echo '<input type="submit" name="published_items" value="My Published Items" style = "font-size:20px">';
                echo '<input type="submit" name="check_orders_made" value="Check orders made" style = "font-size:20px">';
                echo '<input type="submit" name="check_orders_received" value="Check orders received" style = "font-size:20px">';
                echo '<input type="submit" name="wish_list" value="Wish List" style = "font-size:20px">';
                echo '<input type="submit" name="publish_an_item" value="Publish an Item" style = "font-size:20px">';
                echo '<input type="submit" name="client_logout" value="Logout" style = "font-size:20px">';
            } // Show the welcome interface if an admin logins
            else {
                // Show the username as text and publish announcement, manage database, account_setting, logout as 4 buttons
                echo '<legend>Welcome!</legend>' . $_SESSION["administrator_username"] . "   ";
                echo '<input type="submit" name="publish_announcement" value="Publish Announcement" style = "font-size:20px">';
                echo '<input type="submit" name="manage_database" value="Database Manager" style = "font-size:20px">';
                echo '<input type="submit" name="admin_logout" value="Logout" style = "font-size:20px">';
            }
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

if (isset($_POST["admin_logout"])) {
    unset($_SESSION["administrator_id"]);
    unset($_SESSION["administrator_username"]);
    // Turn to the main page
    echo "<script>window.location.href='index.php';</script>";
}


if (isset($_POST["client_personal_info"])) {
    // Call 'client_personal_info.php'
    echo "<script>window.location.href='client_personal_info.php';</script>";
}


if (isset($_POST['publish_announcement'])) {
    // Call 'publish_announcement.php'
    echo "<script>window.location.href='publish_announcement.php';</script>";
}


if (isset($_POST['manage_database'])) {
    // Call'manage_database.php'
    echo "<script>window.location.href='manage_database.php';</script>";
}
?>

<?php

// We obtain all information about the item by its id
$conn = Utility\get_a_connection();
$sql = $conn->prepare("select * from item where id = ?");

if ($_GET['id'] != null) {
    $_SESSION['current_item_id'] = $_GET['id'];
}

$sql->bind_param("s", $_SESSION['current_item_id']);
$sql->execute();
$result = $sql->get_result()->fetch_assoc();

$name = $result["name"];
$description = $result["description"];
$condition = $result['condition'];
$price = $result['price'];
$type = $result['type'];
$picture = $result['picture'];
$days_to_expire = $result['days_to_expire'];
$seller_id = $result['client_id_of_seller'];

// Get the username of the seller
$sql = $conn->prepare("select * from client where client.id = ?");
$sql->bind_param("s", $seller_id);
$sql->execute();
$result = $sql->get_result()->fetch_assoc();

$seller_username = $result['username'];

// Update its page visit counter
Utility\update_page_visit_counter($_SESSION['current_item_id']);

?>

<div style="background-color:#FFA500;clear:both;text-align:center;">
    ITEM INTRODUCTION
</div>

<div id="container" class="v1 v2" style="background-color:#eeeeee;">

    <div class="left">
        <p style="font-size:18px;position: absolute; left: 50px;">ITEM NAME: <?php echo $name; ?></p>
        <br><br>

        <p style="font-size:18px;position: absolute; left: 50px;">ITEM PICTURE: </p><br><br>
        <?php
        if ($picture == null) {
            echo '<img border="1" style ="position: absolute; left: 50px;" src="pic/item.png" height="420" width="420" alt="item_pic"/>';
        } else {
            echo '<img border="1" style ="position: absolute; left: 50px;" src="pic/"' . $picture . 'height="420" width="400" alt="item_pic"/>';
        }
        ?>
    </div>

    <div class="right">
        <p style="font-size:18px;position: relative; left: 50px;">ITEM PUBLISHER:<a
                    href="client_personal_info_view.php?client_username=<?php echo $seller_username ?>"><?php echo $seller_username ?></a>
        </p>
        <p style="font-size:18px;position: relative; left: 50px;">ITEM DESCRIPTION:</p><br>
        <label>
        <textarea name="description" rows="8" cols="120" style="font-size:15px;position: relative; left: 50px;"
                  readonly="readonly"><?php echo $description; ?>
        </textarea>
        </label>
        <p style="font-size:18px;position: relative; left: 50px;">ITEM CONDITION: <?php echo $condition ?> </p>
        <p style="font-size:18px;position: relative; left: 50px;">ITEM TYPE: <?php echo $type ?> </p>
        <p style="font-size:18px;position: relative; left: 50px;">ITEM PRICE: <?php echo $price ?> </p>
        <p style="font-size:18px;position: relative; left: 50px;">DAYS TO EXPIRE: <?php echo $days_to_expire ?> </p>
        <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="POST">
            <input type="submit" name="order_item" value="Order Item"
                   style="font-size:20px;position: relative; left: 48px;">
        </form>
    </div>

    <?php
    if (isset($_POST["order_item"])) {
        if (isset($_SESSION['administrator_id'])) {
            Utility\alert("Administrators are not allowed to order items!");
        } else {
            // Pass current item id and the client id(buyer id) to the order page
            echo "<script>window.location.href='make_order.php?item_id={$_SESSION['current_item_id']}';</script>";
        }
    }
    ?>

</div>

<div style="background-color:#FFA500;clear:both;text-align:center;">
    COMMENTS
</div>

<?php
// Obtain comments and show here
$sql = $conn->prepare("select * from comment where item_id = ? order by post_date desc;");
$sql->bind_param("s", $_SESSION['current_item_id']);
$sql->execute();
$result = $sql->get_result();
while ($row = $result->fetch_assoc()) {
    $temp_username = Utility\get_client_username($row['client_id']);
    echo '<p style = "font-size:15px;position: relative; left: 50px;">Post by: ' . $temp_username . '</p>';
    echo '<p style = "font-size:15px;position: relative; left: 50px;">Post date: ' . $row['post_date'] . '</p>';
    echo '<p style = "font-size:15px;position: relative; left: 50px;">Content: </p>';
    echo '<textarea name="description" rows="4" cols="120" style = "font-size:15px;position: relative; left: 50px;" readonly="readonly">' . $row['content'] . '</textarea>';
}
?>
<hr>
<!-- Only clients can post comments -->
<br>
<form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="POST">
    <textarea name="comment" rows="6" cols="120" style="font-size:15px;position: relative; left: 50px;">Post your comments here!</textarea>
    <input type="submit" name="submit_comment" value="Submit" style="font-size:20px;position: relative; left: 50px;">
</form>

<?php

if (isset($_POST["submit_comment"])) {
    if (isset($_SESSION["administrator_id"])) {
        Utility\alert("Administrators are not allowed to post comments here.");
    } else {
        if (Utility\comment_min_length($_POST['comment'])) {
            // add to table 'comment'
            $sql = $conn->prepare("insert into comment (id, item_id, client_id, post_date, content) values (uuid(), ?, ?, curdate(), ?);");
            $sql->bind_param("sss", $_SESSION['current_item_id'], $_SESSION['client_id'], $_POST['comment']);
            if ($sql->execute()) {
                Utility\alert("Comment posted successfully!");
                // Refresh to the current page
                echo "<script>window.location.href='item.php?id={$_SESSION['current_item_id']}';</script>";
            }
        } else {
            Utility\alert("Minimum length of the comment should be >= 5");
        }
    }
}

$sql->close();
mysqli_close($conn);
?>

</body>


