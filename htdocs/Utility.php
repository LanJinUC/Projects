<?php
// The page is the collection of data structures, constants and functions we frequently use in our web.
namespace Utility {
    // We store all constants here
    const database_host = "localhost";
    const database_user = "root";
    const database_password = "gMga2gv6ZVEgxwkGzsT7";
    const database_name = "cpsc_471_project_g3";
    const maximal_visit_counter = 20000000;
    const item_typename = array(
        "books" => "Books",
        "electronic_books" => "Electronic Books",
        "consumer_electronics" => "Consumer Electronics",
        "food" => "Food",
        "personal_computers" => "Personal Computers",
        "software" => "Software",
        "sports_and_outdoors" => "Sports and Outdoors",
        "music" >= "Music",
        "musical_instrument" => "Musical Instrument",
        "video_games" => "Video Games",
        "clothes" => "Clothes",
        "office_products" => "Office Products",
        "others" => "Others");
    const item_cond = array(
        "new" => "New",
        "used_open_box" => "Used open box",
        "used_very_good" => "Used very good",
        "used_good" => "Used good",
        "used_acceptable" => "Used acceptable"
    );

    // Start session here
    if (session_status() == PHP_SESSION_NONE) {
        session_start();
    }

    // Create and return a connection, the user should manually close it
    function get_a_connection()
    {
        // Create connection
        $conn = mysqli_connect(database_host, database_user, database_password, database_name);

        // Check connection
        if (mysqli_connect_errno()) {
            exit("Failed to connect the database" . mysqli_connect_error());
        }
        mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
        $conn->set_charset("utf8mb4");
        return $conn;
    }

    // Obtain the client's username by its client id
    function get_client_username($client_id)
    {
        $conn = get_a_connection();
        $sql = $conn->prepare("select * from client where id = ?");
        $sql->bind_param("s", $client_id);
        $sql->execute();
        $username = $sql->get_result()->fetch_assoc()['username'];
        $sql->close();
        mysqli_close($conn);
        return $username;
    }

    // Update the page visit counter of an item, reset it to 0 if counter == maximal_visit_counter, otherwise increase by 1
    function update_page_visit_counter($item_id)
    {
        $conn = get_a_connection();
        // Get the counter
        $sql = $conn->prepare("select page_visit_counter from item where id = ?;");
        $sql->bind_param("s", $item_id);
        $sql->execute();
        $counter = $sql->get_result()->fetch_assoc()['page_visit_counter'];
        ++$counter;
        if ($counter === maximal_visit_counter) {
            $counter = 0;
        }
        // Update the counter
        $sql = $conn->prepare("update item set page_visit_counter = ? where id = ?;");
        $sql->bind_param("is", $counter, $item_id);
        $sql->execute();
        // Close the connection
        $sql->close();
        mysqli_close($conn);
    }

    // Validate if the length of the item's description >= 5
    function description_min_length($description)
    {
        return (strlen($description) >= 5);
    }

    // Validate if the name of the item >= 3
    function item_min_length($name)
    {
        return (strlen($name) >= 3);
    }

    // Validate the item's condition
    function is_valid_item_condition($condition)
    {
        $temp = array('used_acceptable', 'used_good', 'used_very_good', 'used_open_box', 'new');
        return (in_array($condition, $temp));
    }

    // Validate the item's price/the total price of an item in the order
    function is_valid_price($price)
    {
        return ($price >= 0.00);
    }

    // Validate the item's type
    function is_valid_item_type($type)
    {
        $temp = array('books',
            'electronic_books',
            'consumer_electronics',
            'food',
            'personal_computers',
            'software',
            'sports_and_outdoors',
            'music',
            'musical_instrument',
            'video_games',
            'clothes',
            'office_products',
            'others');
        return (in_array($type, $temp));
    }

    // Validate the length of the comment
    function comment_min_length($content)
    {
        return strlen($content) >= 5;
    }

    // Validate the length of the password
    function password_min_length($password)
    {
        return strlen($password) >= 6;
    }

    // Validate the length of the username of clients and administrators
    function username_min_length($username)
    {
        return strlen($username) >= 6;
    }

    // Validate the length of the password question
    function password_question_min_length($question)
    {
        return strlen($question) >= 6;
    }

    // Validate the length of the answer of the password question
    function answer_for_password_question_min_length($answer)
    {
        return strlen($answer) >= 6;
    }

    // Validate if the admin username is unique
    function is_admin_username_unique($username)
    {
        $is_unique = true;
        $conn = get_a_connection();
        $sql = $conn->prepare("select * from administrator where username = ?");
        $sql->bind_param("s", $username);
        $sql->execute();
        if ($sql->get_result()->num_rows !== 0) {
            $is_unique = false;
        }
        $sql->close();
        mysqli_close($conn);
        return $is_unique;
    }

    // Validate if the client username is unique
    function is_client_username_unique($username)
    {
        $is_unique = true;
        $conn = get_a_connection();
        $sql = $conn->prepare("select * from client where username = ?");
        $sql->bind_param("s", $username);
        $sql->execute();
        if ($sql->get_result()->num_rows !== 0) {
            $is_unique = false;
        }
        $sql->close();
        mysqli_close($conn);
        return $is_unique;
    }

    // Validate the format of the phone number
    function is_valid_phone_number($phone_number)
    {
        if (strlen($phone_number) !== 13) {
            return false;
        }
        // Canadian phone numbers start with '1'
        if ($phone_number[0] != '1') {
            return false;
        }
        if ($phone_number[1] != '-') {
            return false;
        }
        if ($phone_number[5] != '-') {
            return false;
        }
        if ($phone_number[9] != '-') {
            return false;
        }
        for ($i = 0; $i < 13; ++$i) {
            if ($i != 1 && $i != 5 && $i != 9) {
                if (!is_numeric($phone_number[$i])) {
                    return false;
                }
            }
        }
        return true;
    }

    // Validate the email address
    function is_valid_email($email)
    {
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return false;
        }
        return true;
    }

    // Validate the gender
    function is_valid_gender($gender)
    {
        return ($gender === 'm' || $gender === "f");
    }

    // Validate the balance
    function is_valid_balance($balance)
    {
        return ($balance >= 0.00);
    }

    // Validate the length of the content in the announcement
    function content_minimum_length($content)
    {
        return strlen($content) >= 6;
    }

    // Validate the middle initial
    function is_valid_middle_initial($middle_initial)
    {
        if (strlen($middle_initial) === 0) {
            return true;
        }
        if (strlen($middle_initial) === 1) {
            return ($middle_initial >= 'A' && $middle_initial <= 'Z');
        }
        return false;
    }

    // Validate the first/last name
    function is_valid_first_or_last_name($name)
    {
        if ($name === null) {
            return false;
        }
        return (preg_match('/^[A-Z]+$/', $name) == true);
    }

    // Valid the date of birth yyyy-mm-dd 1993-01-11
    function is_valid_date($date)
    {
        $tempDate = explode('-', $date);
        // checkdate(month, day, year)
        if (checkdate($tempDate[1], $tempDate[2], $tempDate[0])) {
            $year = (int)($tempDate[0]);
            if ($year < 1900 || $year > 2019) {
                return false;
            }
            if (strlen($tempDate[1]) != 2 || strlen($tempDate[2]) != 2) {
                return false;
            }
            return true;
        };
        return false;
    }

    // Pop out an alert message
    function alert($message) {
        echo "<script type='text/javascript'>alert('$message');</script>";
    }
}
