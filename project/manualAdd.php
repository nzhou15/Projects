<?php session_start(); ?>
<?php   
    // security
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
</style>
</head>

<body>
<?php
    $profid = $_POST["profid"];
    $tmy = $_POST["tmy"];
    $cNumber = $_POST["coursenum"];
    $cName = $_POST["coursename"];
    $insName = $_POST["insname"];

    $duplicate_check = $db->query("SELECT COUNT(*) AS ct FROM profCourse WHERE term_month_year = '$tmy' AND course_num ='$cNumber' AND course_type = '$cName' AND profID = '$profid'")->fetchArray();
    if($duplicate_check['ct'] > 0){
        $message = "Such course and Instruction already exist. Please try again.";
        echo "<script type='text/javascript'>alert('$message'); history.go(-1); </script>";

    } else {
        $ret = $db->exec("INSERT INTO profCourse (profID, term_month_year, course_num, course_type, instructor_assigned_name) 
        VALUES('$profid', '$tmy', '$cNumber', '$cName', '$insName');") or die(print_r($db->errorInfo(), true));//->fetchArray();
        if(!$ret) {
            echo $db->lastErrorMsg();
        } else {
            $message = "User added successfully.";
            echo "<script type='text/javascript'>alert('$message'); history.go(-1); </script>";

        }
        // echo "<button onclick=\"history.back()\"><span>Go back</span></button>";
    }
?>
</body>
</html>