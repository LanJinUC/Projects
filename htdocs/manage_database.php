<?php
// This page indicates the database manager controlled by administrators
include "Utility.php";
?>

<head>
    <meta charset="UTF-8">
    <title>Database Manager</title>
    <!-- Put all JS functions we are gonna use here! -->
    <script>
        function doDeleteUCMember(ucid) {
            if (confirm("Are you sure to delete the ucmember？")) {
                window.location = 'manage_database_actions.php?action=del_ucmember&ucid=' + ucid;
            }
        }

        function doDeleteAnnouncement(id) {
            if (confirm("Are you sure to delete the announcement？")) {
                window.location = 'manage_database_actions.php?action=del_announcement&id=' + id;
            }
        }

        function doDeleteItem(id) {
            if (confirm("Are you sure to un-publish the item？")) {
                window.location = 'manage_database_actions.php?action=del_item&id=' + id;
            }
        }

        function doDeleteOrder(id) {
            if (confirm("Are you sure to un-publish the order？")) {
                window.location = 'manage_database_actions.php?action=del_order&id=' + id;
            }
        }

        function doDeleteClient(id) {
            if (confirm("Are you sure to delete the client？")) {
                window.location = 'manage_database_actions.php?action=del_client&id=' + id;
            }
        }

        function doDeleteAdmin(id) {
            if (confirm("Are you sure to delete the administrator？")) {
                window.location = 'manage_database_actions.php?action=del_admin&id=' + id;
            }
        }

        function doDeleteComment(id) {
            if (confirm("Are you sure to delete the comment?")) {
                window.location = 'manage_database_actions.php?action=del_comment&id=' + id;
            }
        }
    </script>
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
            echo '<input type="submit" name="publish_announcement" value="Publish Announcement" style = "font-size:20px">';
            echo '<input type="submit" name="main_page" value="Main Page" style = "font-size:20px">';
            echo '<input type="submit" name="admin_logout" value="Logout" style = "font-size:20px">';
            ?>
        </fieldset>
    </form>
</div>

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

if (isset($_POST["publish_announcement"])) {
    // Call 'publish_announcement.php'
    echo "<script>window.location.href='publish_announcement.php';</script>";
}
?>

<div style="text-align: center;">
    <h2>Database Manager</h2>
    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="POST">
        <input type="submit" name="manage_announcements" value="Manage Announcements" style="font-size:20px;">
        <input type="submit" name="manage_clients" value="Manage Clients" style="font-size:20px;">
        <input type="submit" name="manage_administrators" value="Manage Admins" style="font-size:20px;">
        <input type="submit" name="add_administrators" value="Add an Admin" style="font-size:20px;">
        <input type="submit" name="manage_ucmember" value="Manage UCMember" style="font-size:20px;">
        <input type="submit" name="manage_comments" value="Manage Comments" style="font-size:20px;">
        <input type="submit" name="manage_orders" value="Manage Order" style="font-size:20px;">
        <input type="submit" name="manage_items" value="Manage Items" style="font-size:20px;">
    </form>
</div>
<hr>

<div style="background-color:#EEEEEE">
    <?php

    // Create a connection
    $conn = Utility\get_a_connection();

    // Administrators have no permission to edit/add ucmember since the data is given by the campus
    // They can only read it or delete it
    if (isset($_POST["manage_ucmember"])) {

        echo '<table style="width:100%" border=1>';
        echo '<tr><th>UCID</th><th>CAMPUS EMAIL ADDRESS</th><th>DATE OF BIRTH</th><th>GENDER</th><th>BALANCE</th><th>FIRST NAME</th><th>MIDDLE INIT</th><th>LAST NAME</th><th>OPERATIONS</th></tr>';

        $stmt = "select * from ucalgary_member order by ucid";
        $result = $conn->query($stmt);
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                echo '<tr>';
                echo '<td align="center">' . $row["ucid"] . '</td>';
                echo '<td align="center">' . $row["campus_email_address"] . '</td>';
                echo '<td align="center">' . $row["date_of_birth"] . '</td>';
                echo '<td align="center">' . strtoupper($row["gender"]) . '</td>';
                echo '<td align="center">' . $row["balance"] . '</td>';
                echo '<td align="center">' . $row["first_name"] . '</td>';
                echo '<td align="center">' . $row["middle_initial"] . '</td>';
                echo '<td align="center">' . $row["last_name"] . '</td>';
                echo '<td align="center">' . "<a href='javascript:doDeleteUCMember({$row['ucid']})'>Delete</a>" . '</td>';
                echo '</tr>';
            }
        }
    }

    // Administrators have permission to delete an announcement
    if (isset($_POST["manage_announcements"])) {

        echo '<table style="width:100%" border="1">';
        echo '<tr><th>TITLE</th><th>POST DATE</th><th>OPERATIONS</th></tr>';

        $stmt = "select * from announcement order by post_date desc";
        $result = $conn->query($stmt);
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                echo '<tr>';
                echo '<td align="center">' . $row["title"] . '</td>';
                echo '<td align="center">' . $row["post_date"] . '</td>';
                $temp_string = $row['id'];
                echo '<td align="center">' . "<a href='javascript:doDeleteAnnouncement(\"$temp_string\")'>Delete</a>";
                echo '</td>';
                echo '</tr>';
            }
        }
    }

    // Administrators have permission to un-publish an item
    if (isset($_POST["manage_items"])) {

        echo '<table style="width:100%" border="1">';
        echo '<tr><th>NAME</th><th>CONDITION</th><th>PRICE</th><th>TYPE</th><th>DAYS_TO_EXPIRE</th><th>SELLER ID</th><th>OPERATIONS</th></tr>';

        $stmt = "select * from item order by page_visit_counter desc, name";
        $result = $conn->query($stmt);
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                echo '<tr>';
                echo '<td align="center">' . '<a href="item.php?id=' . $row["id"] . '">' . $row["name"] . '</td>';
                echo '<td align="center">' . $row["condition"] . '</td>';
                echo '<td align="center">' . $row["price"] . '</td>';
                echo '<td align="center">' . $row["type"] . '</td>';
                echo '<td align="center">' . $row["days_to_expire"] . '</td>';
                echo '<td align="center">' . $row["client_id_of_seller"] . '</td>';
                $temp_string = $row['id'];
                echo '<td align="center">' . "<a href='javascript:doDeleteItem(\"$temp_string\")'>Un-publish</a>";
                echo '</td>';
                echo '</tr>';
            }
        }
    }

    // Administrators have permission to delete an order
    if (isset($_POST["manage_orders"])) {

        echo '<table style="width:100%" border="1">';
        echo '<tr><th>ID</th><th>ITEM ID</th><th>TOTAL PRICE</th><th>ADDRESS</th><th>SHIPPING METHOD</th><th>SELLER ID</th><th>BUYER ID</th><th>OPERATIONS</th></tr>';

        $stmt = "select * from `order` order by date_of_order desc";
        $result = $conn->query($stmt);
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                echo '<tr>';
                echo '<td align="center">' . $row["id"] . '</td>';
                echo '<td align="center">' . '<a href="item.php?id=' . $row["item_id"] . '">' . $row["item_id"] . '</td>';
                echo '<td align="center">' . $row["total_price"] . '</td>';
                echo '<td align="center">' . $row["address_of_receiver"] . '</td>';
                echo '<td align="center">' . $row["shipping_method"] . '</td>';
                echo '<td align="center">' . $row["client_id_of_seller"] . '</td>';
                echo '<td align="center">' . $row["client_id_of_buyer"] . '</td>';
                $temp_string = $row['id'];
                echo '<td align="center">' . "<a href='javascript:doDeleteOrder(\"$temp_string\")'>Delete</a>";
                echo '</tr>';
            }
        }
    }

    // Administrators have permission to edit/delete a client
    if (isset($_POST["manage_clients"])) {

        echo '<table style="width:100%" border="1">';
        echo '<tr><th>ID</th><th>USERNAME</th><th>UCID</th><th>ADDRESS</th><th>ACCOUNT STATUS</th><th>PHONE NUMBER</th><th>DATE OF REGISTRATION</th><th>OPERATIONS</th></tr>';

        $stmt = "select * from client order by username";
        $result = $conn->query($stmt);
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                echo '<tr>';
                echo '<td align="center">' . $row["id"] . '</td>';
                echo '<td align="center">' . $row["username"] . '</td>';
                echo '<td align="center">' . $row["ucid"] . '</td>';
                echo '<td align="center">' . $row["address"] . '</td>';
                echo '<td align="center">' . $row["account_status"] . '</td>';
                echo '<td align="center">' . $row["phone_number"] . '</td>';
                echo '<td align="center">' . $row["date_of_registration"] . '</td>';
                $temp_string = $row['id'];
                $_SESSION["current_client_to_edit"] = $temp_string;
                echo '<td align="center">' . "<a href='javascript:doDeleteClient(\"$temp_string\")'>Delete</a>" . " " . "<a href='admin_edit_client_info.php?client_id=$temp_string'>Edit</a>";
                echo '</tr>';
            }
        }
    }

    // Administrators have permission to delete admins
    if (isset($_POST["manage_administrators"])) {

        echo '<table style="width:100%" border="1">';
        echo '<tr><th>USERNAME</th><th>ADDRESS</th><th>PHONE NUMBER</th><th>DATE OF REGISTRATION</th><th>DATE OF BIRTH</th><th>FIRST NAME</th><th>MIDDLE INIT</th><th>LAST NAME</th><th>OPERATIONS</th></tr>';

        $stmt = "select * from administrator order by username";
        $result = $conn->query($stmt);
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                echo '<tr>';
                echo '<td align="center">' . $row["username"] . '</td>';
                echo '<td align="center">' . $row["address"] . '</td>';
                echo '<td align="center">' . $row["phone_number"] . '</td>';
                echo '<td align="center">' . $row["date_of_registration"] . '</td>';
                echo '<td align="center">' . $row["date_of_birth"] . '</td>';
                echo '<td align="center">' . $row["first_name"] . '</td>';
                echo '<td align="center">' . $row["middle_initial"] . '</td>';
                echo '<td align="center">' . $row["last_name"] . '</td>';
                $temp_string = $row['id'];
                echo '<td align="center">' . "<a href='javascript:doDeleteAdmin(\"$temp_string\")'>Delete</a>";
                echo '</tr>';
            }
        }
    }

    // Administrators have permission to add an admin
    if (isset($_POST["add_administrators"])) {
        // Call 'admin_add.php'
        echo "<script>window.location.href='admin_add.php';</script>";
    }

    // Administrators have permission to delete comments
    if (isset($_POST["manage_comments"])) {

        echo '<table style="width:100%" border="1">';
        echo '<tr><th>ID</th><th>ITEM ID</th><th>CLIENT ID</th><th>POST DATE</th><th>CONTENT</th><th>OPERATIONS</th></tr>';

        $stmt = "select * from comment order by post_date desc, item_id;";
        $result = $conn->query($stmt);
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                echo '<tr>';
                echo '<td align="center">' . $row["id"] . '</td>';
                echo '<td align="center">' . $row["item_id"] . '</td>';
                echo '<td align="center">' . $row["client_id"] . '</td>';
                echo '<td align="center">' . $row["post_date"] . '</td>';
                echo '<td align="center">' . $row["content"] . '</td>';
                $temp_string = $row['id'];
                echo '<td align="center">' . "<a href='javascript:doDeleteComment(\"$temp_string\")'>Delete</a>";
                echo '</tr>';
            }
        }
    }

    // Close the connection
    mysqli_close($conn);
    ?>
</div>

<div id="footer" style="background-color:#FFA500;clear:both;text-align:center;">
    UCalgary Online Secondhand Trading System - CPSC 471 Project Group 3
</div>

</body>




