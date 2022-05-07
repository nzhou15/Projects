<?php session_start(); ?>
<?php   // security
    // connect to the database
    class MyDB extends SQLite3{
        function __construct() { $this->open('../../COMP307/TAmanagement.db'); }
    }
    $db = new MyDB();
    if(!$db) { echo $db->lastErrorMsg(); }

    // verify if ticket exists, ticket expired, ticket permission
    $ticket = $_COOKIE["ticket"];
    $id = $_COOKIE["ID"];
    $type = $_COOKIE["type"];
    if($ticket == null){
        header("Location: https://www.cs.mcgill.ca/~jzhou70/project/login_page.php");
        exit();
    }

    $query = $db->query("SELECT id, expire, permission, COUNT(1) AS num FROM tickets WHERE ticket = $ticket")->fetchArray();
    $time = date("Y-m-d H:i:s");
    if($time > $query['expire']) {
        $ret = $db->exec("DELETE FROM tickets WHERE ticket = $ticket;") or die(print_r($db->errorInfo(), true));
        if(!$ret) { echo $db->lastErrorMsg(); }
        $message = "Session Timeout.";
        echo "<script type='text/javascript'>alert('$message'); window.location.href='login_page.php';</script>";
    }

    if($type != $query['permission']){
        $message = "Permission dismatch.";
        echo "<script type='text/javascript'>alert('$message'); window.location.href='login_page.php';</script>";
    }
?>
<!DOCTYPE html>
<html>
<head>
<style>
html, body {
    height: 100%;
    background: url('images/orange_background.jpeg') no-repeat center center fixed; 
    -webkit-background-size: cover;
    -moz-background-size: cover;
    -o-background-size: cover;
    background-size: cover;
}

.header {
    background-color: #3C403D;
    height: 3%;
    text-align: right;
}

.header input {
    color: white;
    background: none;
    border: none;
}

.frame{
    width: 50%;
    height: 70%;
    background: #DADED4;
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    margin: auto ;
    border-radius: 10px;

    padding: 20px 0;
    box-sizing: border-box;
    box-shadow: 0 0 5px 5px #DADED4;
}

</style>
</head>
<body>
<div class="header">
    <form action="delete_ticket.php"><input type="submit" value="Logout"></form>
</div>

<div class="frame">
<?php
    $name = $_POST["edit_user"];
    $fname = $_POST["first_name"];
    $lname = $_POST["last_name"];
    $email = $_POST["email"];
    $courses = $_POST["courses"];
    $usertypes =  $_POST["types"];

    $courses_arr;
    foreach($courses as $item){
        $courses_arr = $courses_arr . $item . ";";
    }

    $types_arr;
    foreach($usertypes as $item){
        $types_arr = $types_arr . $item . ";";
    }
    // var_dump($types_arr);

    class MyDB extends SQLite3 {
        function __construct(){
            $this->open('../../COMP307/TAmanagement.db');
        }
    }
    $db = new MyDB();
    if(!$db){ echo $db->lastErrorMsg(); }

    // store username and password
    $read = $db->query("SELECT ID FROM users WHERE username = '$name';")->fetchArray();
    $id = $read['ID'];
    $read2 = $db->query("SELECT password FROM users WHERE username = '$name';")->fetchArray();
    $pwd = $read2['password'];

    $db->exec("DELETE FROM users WHERE username = '$name';") or die(print_r($db->errorInfo(), true));
    $ret = $db->exec("INSERT INTO users(fname, lname, email, ID, username, password, courses, types) VALUES('$fname', '$lname', '$email', '$id', '$name', '$pwd', '$courses_arr', '$types_arr');") or die(print_r($db->errorInfo(), true));//->fetchArray();
    //$ret = $db->exec("UPDATE users SET fname='$fname', lname='$lname', email='$email', courses='new_course', types='$usertype'");
    if(!$ret) {
        echo $db->lastErrorMsg();
    } else {
        $message = "User $name edited successfully.";
        echo "<script type='text/javascript'>alert('$message'); window.location.href='home_page.php'; </script>";
    }
?>
</div>
</body>
</html>