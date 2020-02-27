<?php
// This page includes a collection of triggers for operations done by administrators in the data manager
include "Utility.php";

if ($_GET['action'] === 'del_ucmember') {
    $conn = Utility\get_a_connection();
    $sql = $conn->prepare("delete from ucalgary_member where ucid = ?;");
    $sql->bind_param('i', $_GET['ucid']);
    $sql->execute();
    $sql->close();
    mysqli_close($conn);
    Utility\alert("UCMember deleted successfully!");
    // Return to the data manager
    echo "<script>window.location.href='manage_database.php';</script>";
} else if ($_GET['action'] === 'del_announcement') {
    $conn = Utility\get_a_connection();
    $sql = $conn->prepare("delete from announcement where id = ?;");
    $sql->bind_param('s', $_GET['id']);
    $sql->execute();
    $sql->close();
    mysqli_close($conn);
    Utility\alert("Announcement deleted successfully!");
    // Return to the data manager
    echo "<script>window.location.href='manage_database.php';</script>";
} else if ($_GET['action'] === 'del_item') {

    $conn = Utility\get_a_connection();
    $sql = $conn->prepare("delete from item where id = ?;");
    $sql->bind_param('s', $_GET['id']);
    $sql->execute();

    // delete client_id in BUYER if no BUYERs are assigned in any item
    $stmt = "delete from buyer where buyer.client_id not in (select client_id_of_buyer from item where client_id_of_buyer is not null);";
    $conn->query($stmt);

    // delete client_id in SELLER if no SELLERs are assigned in any item
    $stmt = "delete from seller where seller.client_id not in (select client_id_of_seller from item where client_id_of_seller is not null);";
    $conn->query($stmt);

    $sql->close();
    mysqli_close($conn);
    Utility\alert("Item un-published successfully!");
    // Return to the data manager
    echo "<script>window.location.href='manage_database.php';</script>";
} // When an order is removed, the buyer id of the item should be set to null
else if ($_GET['action'] === 'del_order') {

    // First we obtain the buyer id, item id
    $conn = Utility\get_a_connection();
    $sql = $conn->prepare("select * from `order` where id = ?");
    $sql->bind_param("s", $_GET['id']);
    $sql->execute();
    $result = $sql->get_result()->fetch_assoc();

    $client_id_of_buyer = $result["client_id_of_buyer"];
    $item_id = $result["item_id"];

    // Then we set the buyer id in the item to null
    $sql = $conn->prepare("update item set client_id_of_buyer = null where id = ?;");
    $sql->bind_param("s", $item_id);
    $sql->execute();

    // Then we delete the order
    $sql = $conn->prepare("delete from `order` where id = ?;");
    $sql->bind_param("s", $_GET['id']);
    $sql->execute();

    // Then we check if such buyer id does not appear in any items, remove it from the buyer list
    $stmt = "delete from buyer where buyer.client_id not in (select client_id_of_buyer from item where client_id_of_buyer is not null);";
    $conn->query($stmt);

    $sql->close();
    mysqli_close($conn);
    Utility\alert("Order removed successfully!");

    // Return to the data manager
    echo "<script>window.location.href='manage_database.php';</script>";
} else if ($_GET['action'] === 'del_client') {

    $conn = Utility\get_a_connection();
    $sql = $conn->prepare("delete from client where id = ?");
    $sql->bind_param("s", $_GET['id']);
    $sql->execute();
    $sql->close();
    mysqli_close($conn);
    Utility\alert("Client account removed successfully!");

    // Return to the data manager
    echo "<script>window.location.href='manage_database.php';</script>";
} else if ($_GET['action'] === 'del_admin') {

    if ($_GET['id'] === $_SESSION['administrator_id']) {
        Utility\alert("Cannot delete the account which is currently used!");
    } else {
        $conn = Utility\get_a_connection();
        $sql = $conn->prepare("delete from administrator where id = ?");
        $sql->bind_param("s", $_GET['id']);
        $sql->execute();
        $sql->close();
        mysqli_close($conn);
        Utility\alert("Administrator removed successfully!");
    }

    // Return to the data manager
    echo "<script>window.location.href='manage_database.php';</script>";
} else if ($_GET['action'] === 'del_comment') {

    $conn = Utility\get_a_connection();
    $sql = $conn->prepare("delete from comment where id = ?");
    $sql->bind_param("s", $_GET['id']);
    $sql->execute();
    $sql->close();
    mysqli_close($conn);
    Utility\alert("Comment removed successfully!");

    // Return to the data manager
    echo "<script>window.location.href='manage_database.php';</script>";
} else if ($_GET['action'] === 'complete_order') {

    var_dump($_GET['id']);
    // We first get the shipping method, total price, item id, buyer id and the seller id of the order
    $conn = Utility\get_a_connection();
    $sql = $conn->prepare("select * from `order` where id = ?");
    $sql->bind_param("s", $_GET['id']);
    $sql->execute();
    $result = $sql->get_result()->fetch_assoc();
    $client_id_of_buyer = $result['client_id_of_buyer'];
    $client_id_of_seller = $result['client_id_of_seller'];
    $total_price = $result['total_price'];
    $item_id = $result['item_id'];

    // We delete the order
    $sql = $conn->prepare("delete from `order` where id = ?");
    $sql->bind_param("s", $_GET['id']);
    $sql->execute();

    // We delete the item
    $sql = $conn->prepare("delete from item where id = ?");
    $sql->bind_param("s", $item_id);
    $sql->execute();

    // We are gonna double check here
    // delete client_id in BUYER if no BUYERs are assigned in any item
    $stmt = "delete from buyer where buyer.client_id not in (select client_id_of_buyer from item where client_id_of_buyer is not null);";
    $conn->query($stmt);
    // delete client_id in SELLER if no SELLERs are assigned in any item
    $stmt = "delete from seller where seller.client_id not in (select client_id_of_seller from item where client_id_of_seller is not null);";
    $conn->query($stmt);

    Utility\alert("This order is completed successfully!");
    // Return to the orders_received.php
    echo "<script>window.location.href='orders_received.php';</script>";
}

?>