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
input[type="text"], input[type="radio"] {
    margin-bottom: 1%;
}

/* From uiverse.io by @abrahamcalsin */
button {
    position: relative;
    display: inline-flex;
    justify-content: center;
    align-items: center;
    border-radius: 5px;
    background: #39603D;
    color: white;
    box-shadow: 0px 6px 24px 0px rgba(0, 0, 0, 0.2);
    overflow: hidden;
    border: none;
    margin-top: 1%;
}

button:after {
    content: " ";
    width: 0%;
    height: 100%;
    background: #DADED4;
    position: absolute;
    transition: all 0.4s ease-in-out;
    right: 0;
}

button:hover::after {
    right: auto;
    left: 0;
    width: 100%;
}

button span {
    text-align: center;
    text-decoration: none;
    width: 100%;
    padding: 10px 17px;
    font-size: 1.125em;
    font-weight: 700;
    letter-spacing: 0.3em;
    z-index: 20;
    transition: all 0.3s ease-in-out;
}

button:hover span {
    color: #39603D;
    animation: scaleUp 0.3s ease-in-out;
}

body {
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
    padding-left: 10%;
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
    $button = $_POST["choice"];
    $name = $_POST["user"];
    // echo $name;

    if($button == "delete"){
        $ret = $db->exec("DELETE FROM users WHERE username = '$name';") or die(print_r($db->errorInfo(), true));//->fetchArray();
        if(!$ret) {
            echo $db->lastErrorMsg();
        } else {
            $message = "User $name deleted successfully.";
            echo "<script type='text/javascript'>alert('$message'); window.location.href='home_page.php';</script>";
        }
    }elseif($button == "edit"){
        echo "<h2>Editing User $name.</h2>";
?>
<form action="edit.php" method="post">
First name: <input type="text" name="first_name"><br>
Last name: <input type="text" name="last_name"><br>
Email: <input type="text" name="email"><br>
Associated courses:<br>
<?php
        $res = $db->query("SELECT * FROM courses;");
        echo "<select name=\"courses[]\" multiple>";
        echo "<option value=\"\">-- Please choose associated courses --</option>";
        while($row = $res->fetchArray()){
            $term = $row['term_month_year'];
            $course_type = $row['course_type'];
            $course_num = $row['course_num'];
            $course = $term . "-" . $course_type . " " . $course_num;
            echo "<option value='$course'>" . $term . " - " . $course_type . " " . $course_num . "</option>";
        }
        echo "</select><br><br>";    

        echo "<select name=\"types[]\" multiple>";
        echo "<option value=\"\">-- Please choose associated user types --</option>";
        echo "<option value='student'>student</option>";
        echo "<option value='prof'>prof</option>";
        echo "<option value='TA'>TA</option>";
        echo "<option value='admin'>admin</option>";
        echo "<option value='sysop'>sysop</option>";
        echo "</select><br><br>";    

        echo "<button type=\"submit\" name=\"edit_user\" value='$name'><span>Edit</span></button>";
    }
?>
</form>
</div>
</body>
</html>