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
/* From codepen.io by Melissa Em */
* {
    box-sizing: border-box;
}

html, body {
    height: 100%;
}

body {
    /* text-align: center; */
    display: flex;
    flex-direction: column;
    /* justify-content: center; */
}

label {
    cursor: pointer;
}

svg {
    width: 3rem;
    height: 3rem;
    padding: 0.15rem;
}

.textboxid
{
    font-size: 100px; // for say a select
width: 400px;
}

/* hide radio buttons */
input[name="star"] {
    display: inline-block;
    width: 0;
    opacity: 0;
    /* margin-left: -2px; */
}

/* hide source svg */
.star-source {
    width: 0;
    height: 0;
    visibility: hidden;
}

/* set initial color to transparent so fill is empty*/
.star {
    color: transparent;
    transition: color 0.2s ease-in-out;
}

/* set direction to row-reverse so 5th star is at the end and ~ can be used to fill all sibling stars that precede last starred element*/
.star-container {
    display: flex;
    flex-direction: row-reverse;
    justify-content: flex-end;
    padding-left: 25%;
}

label:hover ~ label .star, svg.star:hover, input[name="star"]:focus ~ label .star, input[name="star"]:checked ~ label .star {
    color: #A3BCB6;
}

input[name="star"]:checked + label .star {
    animation: starred 0.5s;
}

input[name="star"]:checked + label {
    animation: scaleup 1s;
}

button {
    margin-top: 2%;
    position: relative;
    display: flex;
    justify-content: center;
    align-items: center;
    border-radius: 5px;
    background: #39603D;
    color: white;
    box-shadow: 0px 6px 24px 0px rgba(0, 0, 0, 0.2);
    overflow: hidden;
    border: none;
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
    padding-left: 1%;
    box-sizing: border-box;
    box-shadow: 0 0 5px 5px #DADED4;
}

.rating_content{
    padding-top: 8%;
    text-align: center;
}

.rating_content2{
    padding-left: 35%;
    text-align: center;
}

@keyframes scaleup {
    from {
        transform: scale(1.2);
    }
    to {
        transform: scale(1);
    }
}

@keyframes starred {
    from {
        color: #A3BCB6;
    }
    to {
        color: #A3BCB6;
    }
}
</style>
</head>
<body>
<div class="header">
    <form action="delete_ticket.php"><input type="submit" value="Logout"></form>
</div>

<div class="frame">
    <div class="rating_content">
<form action="insert_rating.php" method="post">
<p>Which TA in this course would you like to evaluate?</p>
<?php  
    // if there is no course selected then pop an alert
    if($_POST['ta_course'] == ''){
        $message = "Please choose a course to evaluate.";
        echo "<script type='text/javascript'>alert('$message'); window.location.href='home_page.php'; </script>";
    }

    $term = strtok($_POST['ta_course'], ",");
    // echo $term;
    $course = strtok(",");
    $course_type = strtok($course, " ");
    $course_num = strtok(" ");
    // echo $course_type . " " .$course_num . "<br>";

    echo "<select class=\"selectmenu\" name=\"ta\">";
    echo "<option value=\"\">-- Please choose a TA --</option>";
    $results = $db->query("SELECT TA_name, student_ID FROM CourseAndTA WHERE course_num = $course_num AND course_type = '$course_type';");
    while ($row = $results->fetchArray()) {
        $ta = $row['TA_name'];
        $sid = $row['student_ID'];
        $value = $_POST['ta_course'] . "," . $ta . "," . $sid;
        echo "<option value='$value'>" . $ta . "</option>";
    }
    echo "</select><br>";
?>

<p>Please give a score to this TA (5 means the best):</p>
<div class="star-source">
    <svg>
        <linearGradient x1="50%" y1="5.41294643%" x2="87.5527344%" y2="65.4921875%" id="grad">
            <stop stop-color="#adc9c3" offset="0%"></stop>
            <stop stop-color="#93ada7" offset="60%"></stop>
            <stop stop-color="#A3BCB6" offset="100%"></stop>
        </linearGradient>
        <symbol id="star" viewBox="153 89 106 108">   
            <polygon id="star-shape" stroke="url(#grad)" stroke-width="5" fill="currentColor" points="206 162.5 176.610737 185.45085 189.356511 150.407797 158.447174 129.54915 195.713758 130.842203 206 95 216.286242 130.842203 253.552826 129.54915 222.643489 150.407797 235.389263 185.45085"></polygon>
        </symbol>
    </svg>
</div>

<div class="star-container">
    <input type="radio" name="star" id="five" value="5">
    <label for="five">
        <svg class="star">
        <use xlink:href="#star"/>
        </svg>
    </label>
    <input type="radio" name="star" id="four" value="4">
    <label for="four">
        <svg class="star">
        <use xlink:href="#star"/>
        </svg>
    </label>
    <input type="radio" name="star" id="three" value="3">
    <label for="three">
        <svg class="star">
        <use xlink:href="#star"/>
        </svg>
    </label>
    <input type="radio" name="star" id="two" value="2">
    <label for="two">
        <svg class="star">
        <use xlink:href="#star" />
        </svg>
    </label>
    <input type="radio" name="star" id="one" value="1">
    <label for="one">
    <svg class="star">
        <use xlink:href="#star" />
    </svg>
    </label>
    <br><br>    
</div>

<p>If you have any comment about this TA, please leave here: (optional)</p>
<textarea rows="5" cols="60" name="comment"></textarea><br>
<div class="rating_content2">
<button type="submit" value="Evaluate"><span>Evaluate</span></button>
</div>
</form>
</div>
</div>
</body>
</html>