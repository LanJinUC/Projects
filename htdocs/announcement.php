<?php
// This is the interface of an announcement
include "Utility.php";

// Turn to the main page if no one logins
if (!isset($_SESSION["client_id"]) && !isset($_SESSION["administrator_id"])) {
    Utility\alert("You must login first!");
    echo "<script>window.location.href='index.php';</script>";
}

// Create a connection
$conn = Utility\get_a_connection();

$sql = $conn->prepare("select * from announcement where id = ?");
$sql->bind_param("s", $_GET["id"]);
$sql->execute();
$result = $sql->get_result()->fetch_assoc();

$title = $result["title"];
$post_date = $result["post_date"];
$content = $result["content"];

// Close the connection
$sql->close();
mysqli_close($conn);
?>

<head>
    <meta charset="UTF-8">
    <title>Announcement: <?php echo $title; ?></title>
</head>

<body style="font-family: Consolas,Monaco,Lucida Console,Liberation Mono,DejaVu Sans Mono,Bitstream Vera Sans Mono,Courier New, monospace;">


<style>
    div {
        border: 1px solid black;
        padding: 25px 25px 25px 25px;
        background-color: rgb(240, 238, 221);
    }
</style>

<h3>Title: <?php echo $title; ?></h3>
<h4>Post date: <?php echo $post_date; ?></h4>
<p>Content:</p>
<div><?php echo $content; ?></div>
<br>
<input type="button" style="font-size:20px;" value="Back" onclick="window.location.href='index.php';"/>

<body>