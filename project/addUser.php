<?php
    $fname = $_POST["first_name"];
    $lname = $_POST["last_name"];
    $email = $_POST["email"];
    $ID = $_POST["ID"];
    $username = $_POST["username"];
    $password =  $_POST["password"];
    $usertype =  $_POST["usertype"];
    class MyDB extends SQLite3
    {
    function __construct()
    {
        $this->open('../../COMP307/TAmanagement.db');
    }
    }

    $db = new MyDB();

    if(!$db)
    {
    echo $db->lastErrorMsg();
    }

    $duplicate_check = $db->query("SELECT COUNT(*) AS ct FROM users WHERE ID='$ID'")->fetchArray();
    if($duplicate_check['ct'] > 0){
        echo "Cannot add users already exist. Please try again.";
        echo "<br>";
        echo "<button onclick=\"history.back()\">Go back</button>";
    } else {

        $ret = $db->exec("INSERT INTO users (fname, lname, email, ID, username, password, courses, types) VALUES('$fname', '$lname', '$email', '$ID', '$username', '$password', NULL, '$usertype');") or die(print_r($db->errorInfo(), true));//->fetchArray();
        if(!$ret) {
            echo $db->lastErrorMsg();
        } else {
            $message = "User $username added successfully.";
            echo "<script type='text/javascript'>alert('$message'); window.location.href='home_page.php';</script>";
        }


    }
?>