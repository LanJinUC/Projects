<?php
include "Utility.php";
?>

<head>
    <meta charset="utf-8">
    <title>Publish an Item</title>
</head>

<!-- global font style -->
<body style="font-family: Consolas,Monaco,Lucida Console,Liberation Mono,DejaVu Sans Mono,Bitstream Vera Sans Mono,Courier New, monospace;">

<div style="background-color:#FFA500;clear:both;text-align:center;">
    PUBLISH AN ITEM
</div>

<div class="v1 v2" id="menu" style="background-color:#dddddd;
         height:100%;
         width:100%;
         float:left;
         text-align: center">

    <form style="margin-top:35px" action="<?php echo $_SERVER['PHP_SELF']; ?>" method="POST">

        <p style="font-size:20px;">Please fill out your information of item here.</p>

        <p style="font-size:14px;">The name of your item</p>
        <label>
            <input type="text" size="20" name="name_of_item" style="font-size:15px;">
        </label>

        <p style="font-size:14px;">Select the condition of your item</p>
        <label>
            <select name="item_condition" style="font-size:15px;">
                <option value="new"><?php echo Utility\item_cond["new"] ?></option>
                <option value="used_open_box"><?php echo Utility\item_cond["used_open_box"] ?></option>
                <option value="used_very_good"><?php echo Utility\item_cond["used_very_good"] ?></option>
                <option value="used_good"><?php echo Utility\item_cond["used_good"] ?></option>
                <option value="used_acceptable"><?php echo Utility\item_cond["used_acceptable"] ?></option>
            </select>
        </label><br>

        <p style="font-size:14px;">Select the category of your item</p>
        <label>
            <select name="item_type" style="font-size:15px;">
                <option value="books"><?php echo Utility\item_typename["books"] ?></option>
                <option value="electronic_books"><?php echo Utility\item_typename["electronic_books"] ?></option>
                <option value="consumer_electronics"><?php echo Utility\item_typename["consumer_electronics"] ?></option>
                <option value="food"><?php echo Utility\item_typename["food"] ?></option>
                <option value="personal_computers"><?php echo Utility\item_typename["personal_computers"] ?></option>
                <option value="software"><?php echo Utility\item_typename["software"] ?></option>
                <option value="sports_and_outdoors"><?php echo Utility\item_typename["sports_and_outdoors"] ?></option>
                <option value="music">Music</option>
                <option value="musical_instrument"><?php echo Utility\item_typename["musical_instrument"] ?></option>
                <option value="video_games"><?php echo Utility\item_typename["video_games"] ?></option>
                <option value="clothes"><?php echo Utility\item_typename["clothes"] ?></option>
                <option value="office_products"><?php echo Utility\item_typename["office_products"] ?></option>
                <option value="others"><?php echo Utility\item_typename["others"] ?></option>
            </select>
        </label><br>

        <p style="font-size:14px;">Item Price:</p>
        <label>
            <input type="text" size="22" name="item_price" style="font-size:15px;">
        </label>

        <p style="font-size:14px;">Item Description</p><label>
            <textarea name="item_description" rows="8" cols="60" style="font-size:15px;"></textarea>
        </label>

        <p style="font-size:14px;">Picture Upload</p>
        Upload Img:<input type="file" name="img"/>

        <input type="submit" name="upload" value="Upload" style="font-size:20px;">
        <input type="submit" name="submit" value="Submit" style="font-size:20px;">
        <input type="submit" name="cancel" value="Cancel" style="font-size:20px;">

        <?php
        if (isset($_POST["cancel"])) {
            Utility\alert("Item has been canceled!");
            echo "<script>window.location.href='index.php';</script>";
        }
        else if (isset($_POST["upload"])) {
            Utility\alert("Picture format incorrect!");
        }
        else if (isset($_POST["submit"])) {
            // Check the length of the item's name
            if (Utility\item_min_length($_POST["name_of_item"])) {
                // Check the description of the item
                if (Utility\description_min_length($_POST["item_description"])) {
                    // Check the price of the item
                    if (Utility\is_valid_price($_POST["item_price"])) {
                        // Create the item
                        $conn = Utility\get_a_connection();
                        $sql = $conn->prepare("insert into item (id, name, description, `condition`, price, type, client_id_of_seller) values (uuid(), ?, ?, ?, ?, ?, ?);");
                        $sql->bind_param("sssdss", $_POST['name_of_item'], $_POST['item_description'], $_POST['item_condition'], $_POST['item_price'], $_POST['item_type'], $_SESSION["client_id"]);
                        if ($sql->execute()) {
                            Utility\alert("Item created successfully!");
                            // Turn to the main page
                            echo "<script>window.location.href='index.php';</script>";
                        }
                        $sql->close();
                        mysqli_close($conn);
                    } else {
                        Utility\alert("The price of the item should be >= 0.00");
                    }
                } else {
                    Utility\alert("The length of the item's description should be >= 5!");
                }
            } else {
                Utility\alert("The length of the item's name should be >= 3!");
            }
        }

        ?>
    </form>

</div>

<div id="footer" style="background-color:#FFA500;clear:both;text-align:center;">
    UCalgary Online Secondhand Trading System - CPSC 471 Project Group 3
</div>

</body>