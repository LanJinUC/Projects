<?php
// This is the main page
// Client's account for demo: username_100000 : username
// Admin's account for demo: admin_1 : admin_password_1
include "Utility.php";
?>

<html>
<head>
    <meta charset="utf-8">
    <title>UCalgary Online Secondhand Trading System</title>
    <style>
        table, th, td {
            border: 1px solid black;
        }
    </style>
</head>

<!-- global font style -->
<body style="font-family: Consolas,Monaco,Lucida Console,Liberation Mono,DejaVu Sans Mono,Bitstream Vera Sans Mono,Courier New, monospace;">
<div id="container" style="width:100%">

    <div id="header">
        <form style="margin-top:10px;"
              method="post"
              action="<?php echo $_SERVER['PHP_SELF']; ?>">
            <fieldset>
                <?php
                // Show the login form if no one logins
                if (!isset($_SESSION["client_id"]) && !isset($_SESSION["administrator_id"])) {
                    echo '<legend>Login</legend>Username <label><input type="text" size="25" name="username" style = "font-size:15px">';
                    echo '</label><br>Password <label><input type="password" size="25" name="password" style = "font-size:15px"></label><br>';
                    echo '<input type="submit" name="login" value="Login" style = "font-size:20px">';
                    echo '<input type="submit" name="register" value="Register" style = "font-size:20px" formaction="./register.php" >';
                    echo '<input type="submit" name="reset" value="Reset" style = "font-size:20px" formaction="./reset_check.php" >';
                    echo '<input type="submit" name="admin_login" value="Admin Login" style = "font-size:20px">';
                } // Show the welcome interface if a client logins
                else if (isset($_SESSION["client_id"])) {
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
                else if (isset($_SESSION["administrator_id"])) {
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

    // Handle all 'logout' cases
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
        // Refresh current page
        header('location: ' . $_SERVER['HTTP_REFERER']);
    } else if (isset($_POST["admin_logout"])) {
        unset($_SESSION["administrator_id"]);
        unset($_SESSION["administrator_username"]);
        // Refresh current page
        header('location: ' . $_SERVER['HTTP_REFERER']);
    }
    // Client: Personal Info
    if (isset($_POST["client_personal_info"])) {
        // Call 'client_personal_info.php'
        echo "<script>window.location.href='client_personal_info.php';</script>";
    }
    // Admininistrator: Publish Announcement
    if (isset($_POST['publish_announcement'])) {
        // Call 'publish_announcement.php'
        echo "<script>window.location.href='publish_announcement.php';</script>";
    }
    // Admininistrator: Database Manager
    if (isset($_POST['manage_database'])) {
        // Call'manage_database.php'
        echo "<script>window.location.href='manage_database.php';</script>";
    }
    ?>

    <?php
    // Create connection
    $conn = Utility\get_a_connection();

    // If 'Login' button is pressed
    if (isset($_POST['login'])) {

        // Check if the username and the password exists in 'client'
        $sql = $conn->prepare("select * from client where username = ? and password = ?");
        $sql->bind_param("ss", $_POST['username'], $_POST['password']);
        $sql->execute();

        // No matches found, pop out a message
        if ($sql->get_result()->num_rows === 0) {
            Utility\alert("Wrong username or password!");
            $sql->close();
        } else {
            $sql = $conn->prepare("select * from client where username = ?;");
            $sql->bind_param("s", $_POST['username']);
            $sql->execute();

            // Store the id and username in 'client' to the session
            $result = $sql->get_result()->fetch_assoc();

            $_SESSION["client_id"] = $result['id'];
            $_SESSION["client_username"] = $result['username'];

            // Update the status account to 'active'
            $sql = $conn->prepare("update client set account_status = 'active' where username = ?;");
            $sql->bind_param("s", $_SESSION["client_username"]);
            $sql->execute();
            $sql->close();

            // Refresh current page
            header('location: ' . $_SERVER['HTTP_REFERER']);
        }
    } else if (isset($_POST['admin_login'])) {

        // Check if the username and the password exists in 'administrator'
        $sql = $conn->prepare("select * from administrator where username = ? and password = ?");
        $sql->bind_param("ss", $_POST['username'], $_POST['password']);
        $sql->execute();

        // No matches found, pop out a message
        if ($sql->get_result()->num_rows === 0) {
            Utility\alert("Wrong username or password!");
        } else {
            $sql = $conn->prepare("select * from administrator where username = ?;");
            $sql->bind_param("s", $_POST['username']);
            $sql->execute();
            // Store the id and the username in 'administrator' to the session
            $result = $sql->get_result()->fetch_assoc();
            $_SESSION["administrator_id"] = $result['id'];
            $_SESSION["administrator_username"] = $result['username'];
            // Refresh current page
            header('location: ' . $_SERVER['HTTP_REFERER']);
        }
        $sql->close();
    }
    ?>

    <!-- Left menu -->
    <div id="menu"
         style="background-color:#cfcfcf;
         height:100%;
         width:20%;
         float:left;
         text-align: center">

        <!-- Search bar -->
        <p style="font-size:14px;">Keyword</p>
        <form style="margin-top:15px"
              action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>"
              method="POST">
            <label>
                <input type="text" size="20" name="keyword" style="font-size:15px;">
            </label>
            <p style="font-size:14px;">Item Condition</p>
            <label>
                <select name="item_condition" style="font-size:15px;">
                    <option value="all_conditions">All conditions</option>
                    <option value="new">New</option>
                    <option value="used_open_box">Used open box</option>
                    <option value="used_very_good">Used very good</option>
                    <option value="used_good">Used good</option>
                    <option value="used_acceptable">Used acceptable</option>
                </select>
            </label>
            <p style="font-size:14px;">Item Type</p>
            <label>
                <select name="item_type" style="font-size:15px;">
                    <option value="all_types">All types</option>
                    <option value="books">Books</option>
                    <option value="electronic_books">Electronic Books</option>
                    <option value="consumer_electronics">Consumer Electronics</option>
                    <option value="food">Food</option>
                    <option value="personal_computers">Personal Computers</option>
                    <option value="software">Software</option>
                    <option value="sports_and_outdoors">Sports and Outdoors</option>
                    <option value="music">Music</option>
                    <option value="musical_instrument">Musical Instrument</option>
                    <option value="video_games">Video Games</option>
                    <option value="clothes">Clothes</option>
                    <option value="office_products">Office Products</option>
                    <option value="others">Others</option>
                </select>
            </label>
            <input type="submit" name="search" value="Search" style="font-size:20px;">
        </form>

        <table style="width:95%" align="center">
            <tr>
                <th>Announcements</th>
            </tr>

            <?php
            $stmt = "select * from announcement order by post_date desc;";
            $result = $conn->query($stmt);
            if ($result->num_rows > 0) {
                // List name, condition, type, price
                while ($row = $result->fetch_assoc()) {
                    echo '<tr><td align="center">' . '<a href="announcement.php?id=' . $row["id"] . '">' . $row["title"] . '</td></tr>';
                }
            }
            ?>
        </table>

    </div>
    <!-- Result on the right -->
    <div id="content" style="background-color:#EEEEEE;width:80%;float:left;">
        <?php
        echo '<table style="width:100%">';
        echo '<tr><th>Rank</th><th>Item Name</th><th>Condition</th><th>Type</th><th>Price</th></tr>';

        /*** NOTICE: Items that are assigned to buyers should NOT be displayed!***/

        // Search according to the criterion chosen
        if (isset($_POST["search"])) {

            $all_conditions = false;
            $all_types = false;
            if ($_POST["item_condition"] === "all_conditions") {
                $all_conditions = true;
            }
            if ($_POST["item_type"] === "all_types") {
                $all_types = true;
            }

            // Case 1: All conditions + All types
            if ($all_conditions === true && $all_types === true) {
                $sql = $conn->prepare("select * from item where client_id_of_buyer is null and name like ? order by page_visit_counter desc, name;");
                $item_keyword = '%' . $_POST["keyword"] . '%';
                $sql->bind_param('s', $item_keyword);
                $sql->execute();
                // List name, condition, type, price
                $result = $sql->get_result();
                $rank = 1;
                while ($row = $result->fetch_assoc()) {
                    echo '<tr>';
                    echo '<td align="center">' . $rank . '</td>';
                    echo '<td align="center">' . '<a href="item.php?id=' . $row["id"] . '">' . $row["name"] . '</td>';
                    echo '<td align="center">' . Utility\item_cond[$row["condition"]] . '</td>';
                    echo '<td align="center">' . Utility\item_typename[$row["type"]] . '</td>';
                    echo '<td align="center">' . $row["price"] . '</td>';
                    echo '</tr>';
                    ++$rank;
                }
            } // Case 2: All conditions + Some type
            else if ($all_conditions === true && $all_types === false) {
                $sql = $conn->prepare("select * from item where client_id_of_buyer is null and name like ? and type = ? order by page_visit_counter desc, name;");
                $item_keyword = '%' . $_POST["keyword"] . '%';
                $sql->bind_param('ss', $item_keyword, $_POST["item_type"]);
                $sql->execute();
                // List name, condition, type, price
                $result = $sql->get_result();
                $rank = 1;
                while ($row = $result->fetch_assoc()) {
                    echo '<tr>';
                    echo '<td align="center">' . $rank . '</td>';
                    echo '<td align="center">' . '<a href="item.php?id=' . $row["id"] . '">' . $row["name"] . '</td>';
                    echo '<td align="center">' . Utility\item_cond[$row["condition"]] . '</td>';
                    echo '<td align="center">' . Utility\item_typename[$row["type"]] . '</td>';
                    echo '<td align="center">' . $row["price"] . '</td>';
                    echo '</tr>';
                    ++$rank;
                }
            } // Case 3: Some condition + All types
            else if ($all_conditions === false && $all_types === true) {
                $sql = $conn->prepare("select * from item where client_id_of_buyer is null and name like ? and `condition` = ? order by page_visit_counter desc, name;");
                $item_keyword = '%' . $_POST["keyword"] . '%';
                $sql->bind_param('ss', $item_keyword, $_POST["item_condition"]);
                $sql->execute();
                // List name, condition, type, price
                $result = $sql->get_result();
                $rank = 1;
                while ($row = $result->fetch_assoc()) {
                    echo '<tr>';
                    echo '<td align="center">' . $rank . '</td>';
                    echo '<td align="center">' . '<a href="item.php?id=' . $row["id"] . '">' . $row["name"] . '</td>';
                    echo '<td align="center">' . Utility\item_cond[$row["condition"]] . '</td>';
                    echo '<td align="center">' . Utility\item_typename[$row["type"]] . '</td>';
                    echo '<td align="center">' . $row["price"] . '</td>';
                    echo '</tr>';
                    ++$rank;
                }
            } // Case 4: Some condition + Some type
            else {
                $sql = $conn->prepare("select * from item where client_id_of_buyer is null and name like ? and type = ? and `condition` = ? order by page_visit_counter desc, name;");
                $item_keyword = '%' . $_POST["keyword"] . '%';
                $sql->bind_param('sss', $item_keyword, $_POST["item_type"], $_POST["item_condition"]);
                $sql->execute();
                // List name, condition, type, price
                $result = $sql->get_result();
                $rank = 1;
                while ($row = $result->fetch_assoc()) {
                    echo '<tr>';
                    echo '<td align="center">' . $rank . '</td>';
                    echo '<td align="center">' . '<a href="item.php?id=' . $row["id"] . '">' . $row["name"] . '</td>';
                    echo '<td align="center">' . Utility\item_cond[$row["condition"]] . '</td>';
                    echo '<td align="center">' . Utility\item_typename[$row["type"]] . '</td>';
                    echo '<td align="center">' . $row["price"] . '</td>';
                    echo '</tr>';
                    ++$rank;
                }
            }
        } // Otherwise show all items sorting by their page_visit_counter in desc, then item name in asc
        else {
            $stmt = "select * from item where client_id_of_buyer is null order by page_visit_counter desc, name;";
            $result = $conn->query($stmt);
            if ($result->num_rows > 0) {
                // List name, condition, type, price
                $rank = 1;
                while ($row = $result->fetch_assoc()) {
                    echo '<tr>';
                    echo '<td align="center">' . $rank . '</a>' . '</td>';
                    echo '<td align="center">' . '<a href="item.php?id=' . $row["id"] . '">' . $row["name"] . '</td>';
                    echo '<td align="center">' . Utility\item_cond[$row["condition"]] . '</td>';
                    echo '<td align="center">' . Utility\item_typename[$row["type"]] . '</td>';
                    echo '<td align="center">' . $row["price"] . '</td>';
                    echo '</tr>';
                    ++$rank;
                }
            }
        }
        echo '</table>';

        // Close connection
        mysqli_close($conn);
        ?>
    </div>

    <div id="footer" style="background-color:#FFA500;clear:both;text-align:center;">
        UCalgary Online Secondhand Trading System - CPSC 471 Project Group 3
    </div>

</body>

</html>
