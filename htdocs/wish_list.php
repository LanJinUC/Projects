<?php
// This is the interface of the client's wish list
include "Utility.php";
?>

<head>
    <meta charset="utf-8">
    <style type="text/css">
        .container {
            border: 2px solid #ccc;
            background-color: #eee;
            border-radius: 10px;
            padding: 10px;
            margin: 60px; }
        .button_add{
            background-color: #4CAF50; /* Green */
            border: none;
            color: white;
            padding: 10px 22px;
            text-align: center;
            text-decoration: none;
            display: inline-block;
            font-size: 16px;}
        }

    </style>
    <title>Wish List</title>
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
    // Refresh current page
    echo "<script>window.location.href='index.php';</script>";
}
if (isset($_POST["client_personal_info"])) {
    // Call 'client_personal_info.php'
    echo "<script>window.location.href='client_personal_info.php';</script>";
}
?>

<?php
//deleting a wishing list
// Create connection
$conn = Utility\get_a_connection();
$client_id = $_SESSION["client_id"];
$sql = $conn->prepare("select * from idea_list where client_id = ? ");
$sql->bind_param("s", $client_id );
$sql->execute();
$result = $sql->get_result();
while($row = $result->fetch_assoc()) {
    //the table need to be in a form
    echo '<form style="margin-top:15px"
                         action=""
                         method="POST">';
    echo '<div class = "container" align="left">';
    //The table of each wishing list
    echo '<table>';
    echo '<tbody>';
    echo '<tr>';
    echo '<td>'.'Item Name: '.$row['name'].'</td>';
    echo '</tr>';

    echo '<tr>';
    echo '<td>'.'Item Labels: '.$row['keyword']. '</td>';
    echo '</tr>';

    echo '<tr>';
    echo '<td>'.'Item Descriptions: '.$row['description']. '</td>';
    echo '</tr>';
    //the delete button with a little trick
    echo '<td><input type="hidden" name="deleteItem" value="'.$row['list_number'].'"/></td>';
    echo '<td><input type="submit" value="Delete" name="delete" ></td>';
    echo '</tbody>';
    echo '</table>';
    echo '</div>';
    echo '</form>';

}
//deleting the list in sql when the button was pressed
if(isset($_POST['deleteItem'])){
    $delete = $_POST['deleteItem'];
    $sql = $conn->prepare("DELETE FROM idea_list where list_number = '$delete'");
    $sql->execute();
    echo "<script>window.location.href='wish_list.php';</script>";


}

//select * from idea_list where client_id = '5961a7ec-1730-11ea-9afd-f6df79c0597a';
?>
<form style="margin-top:15px"
      action="<?php echo $_SERVER['PHP_SELF']; ?>"
      method="POST">
    <div class = "container_add" align="left">
        <div>
            <textarea rows="2" cols="20" placeholder="Name your item" name="i_name"></textarea>
        </div>

        <div>
            <textarea rows="2" cols="50" placeholder="Label your item. Please using a comma to seperate labels" name="i_label"></textarea>
        </div>

        <div>
            <textarea rows="20" cols="80" placeholder="Please input your description of the item" name="i_description"></textarea>
            <button type="submit" name="add_new_wishing_item" class="button_add">ADD</button>
        </div>
    </div>
</form>

<?php
//add a new wishing list
if(isset($_POST['add_new_wishing_item'])){
    echo htmlspecialchars($_POST['i_name']);
    echo htmlspecialchars($_POST['i_label']);
    echo htmlspecialchars($_POST['i_description']);

    // Create connection
    $conn = Utility\get_a_connection();
    //Check what is the current list-number of the client's wishing list
    $client_id = $_SESSION["client_id"];
    $sql = $conn->prepare("select list_number = ? from idea_list where client_id = ? ");
    $sql->bind_param("is", $list_number, $client_id);
    $sql->execute();

    //If no results found, set $list_number == 1
    if ($sql->get_result()->num_rows === 0){
        $list_number = 1;
    }
    //If the list is not empty, find the biggest list_number and increment it
    else{
        $result = mysqli_query($conn,"SELECT max(list_number) AS maximun from idea_list");
        $row = mysqli_fetch_array($result);
        $list_number = $row["maximun"];
        ++$list_number;
    }

    //insert a new wishlist to 'idea_list'

    $name = $_POST['i_name'];
    $description = $_POST['i_description'];

    $sql = $conn->prepare("insert into idea_list (client_id, list_number, name, description) values (?, ?, ?, ?);");
    $sql->bind_param("siss", $client_id, $list_number, $name, $description);
    $sql->execute();

    //this is for updating the labels( Json array)
    $s = $_POST['i_label'];
    var_dump($s);
    $str_arr = explode (",", $s);

    function f($array) {
        $result = array();
        foreach ($array as $key => $value){
            if(!in_array($value, $result))
                $result[$key]=$value;
        }
        return $result;
    }
    $new_arr = f($str_arr);

    foreach ($new_arr as $key => $value) {
        $sql = $conn->prepare("update idea_list set keyword = JSON_ARRAY_APPEND(keyword, '$', ?) where client_id = ?;");
        $sql->bind_param("ss", $value, $client_id);
        $sql->execute();
    }


    $sql->close();
    mysqli_close($conn);
    echo "<script>window.location.href='wish_list.php';</script>";

}

?>

<div id="footer" style="background-color:#FFA500;clear:both;text-align:center;">
    UCalgary Online Secondhand Trading System - CPSC 471 Project Group 3
</div>

</body>