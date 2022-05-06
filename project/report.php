<!DOCTYPE html>
<html>
<head>
<style>
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
    margin-right: 2%;
    margin-top: 0.5%;
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
</style>
</head>

<body>
<div class="header">
    <form action="delete_ticket.php"><input type="submit" value="Logout"></form>
</div>

<?php 
    class MyDB extends SQLite3{
        function __construct(){
            $this->open('../../COMP307/TAmanagement.db');
        }
    }

    $db = new MyDB();
    if(!$db){ echo $db->lastErrorMsg(); }

    $val = $_POST["ta"];
    $piece = explode("-", $val);
    //echo $piece[0];
    echo "TA Cohort:<br>";
    $res = $db->query("SELECT * FROM TACohort WHERE student_ID = '$piece[1]';");
    while($row = $res->fetchArray()){
        if($row["grad_ugrad"]==1){$row["grad_ugrad"]="Yes";}else{$row["grad_ugrad"]="No";}
        if($row["open_to_other_courses"]==1){$row["open_to_other_courses"]="Yes";}else{$row["open_to_other_courses"]="No";}
        if($row["priority"]==1){$row["priority"]="Yes";}else{$row["priority"]="No";}
        echo "TA Name: " . $row["legal_name"] . "<br>" . "   Preferred Name: " . $row["TA_name"] . "<br>" . "   Student ID: " . $row["student_ID"] . "<br>" . "   Email: " . $row["email"] . "<br><br>";
        echo "Location: " . $row["location"] . "<br>" . "   Phone: " . $row["phone"] . "<br>" . "   Degree: " . $row["degree"] . "<br>" . "   Graduate? : " . $row["grad_ugrad"] . "<br><br>";
        echo "Term: " . $row["term_month_year"] . "<br>" . "   Appplied Date: " . $row["date_applied"] . "<br>" . "   Course applied for: " . $row["course_applied_for"] . "<br>" . "   open_to_other_courses? : " . $row["open_to_other_courses"] . "<br><br>";
        echo "Supervisor: " . $row["supervisor_name"] . "<br>" . "   Work hours: " . $row["hours"] . "<br>" . "   Notes: " . $row["notes"] . "<br>" . "   Priority? : " . $row["priority"] . "<br><br>";
    }

    echo "TA History :<br>";
    // if this student has been a TA
    $ta_history = $db->query("SELECT term_month_year, course_type, course_num, AVG(score) as avg FROM rating WHERE student_ID='$piece[1]' GROUP BY term_month_year, course_type, course_num;");
    $found=0;
    echo "Student Rating Average for: <br>";
    while($row = $ta_history->fetchArray()){
        $found=1;
        echo $row["term_month_year"] . "-" . $row["course_type"] . " " . $row["course_num"] . ": " . $row["avg"] . "<br>";
    }// if this student has not been a TA
    if($found==0){ echo "This student has never been a TA before;<br>"; }

    echo "<br>Student Rating Comments for: <br>";
    $ta_history = $db->query("SELECT term_month_year, course_type, course_num, comment FROM rating WHERE student_ID='$piece[1]';");
    while($row = $ta_history->fetchArray()){
        $found=1;
        if($row["term_month_year"]<>$past) { echo "For " . $row["term_month_year"] . "-" . $row["course_type"] . " " . $row["course_num"] . ": <br>"; }
        echo $row["comment"] . "<br>";
        $past = $row["term_month_year"];
    }// if this student has not been a TA
    if($found==0){ echo "This student has never been a TA before;<br>"; }

    echo "<br>Professor performance log: <br>";
    if($found==0){ echo "This student has never been a TA before;<br>"; }
    else {
        $ta_history = $db->query("SELECT * FROM performance_log WHERE taId='$piece[1]' GROUP BY profname, term_month_year, course_type, course_num ORDER BY commentTime;");
        while($row = $ta_history->fetchArray()){
            $found=1;
            if(($row["term_month_year"].$row["course_type"].$row["course_num"].$row["profname"])<>$past) { echo "For " . $row["term_month_year"] . "-" . $row["course_type"] . " " . $row["course_num"] . ", " . $row["profname"] . "comment: <br>"; }
            echo $row["commentTime"] . "  " . $row["comment"] . "<br>";
            $past = $row["term_month_year"].$row["course_type"].$row["course_num"].$row["profname"];       
        }
    }

    echo "<br>The courses being assigned this term: <br>";
    date_default_timezone_set('UTC');
    $year = date('Y');
    $semester = date('n');
    if($semester>=9 && $semester<=12) { $semester = "Fall"; }
    elseif($semester>=1 && $semester<=4) { $semester = "Winter"; }
    else { $semester = "Summer"; }
    $year = $semester . " " . $year;
    $ta_history = $db->query("SELECT course_type, course_num FROM CourseAndTA WHERE student_ID='$piece[1]' AND term_month_year='$year';");
    $found=0;
    while($row = $ta_history->fetchArray()){
        $found=1;
        echo $row['course_type'] . " " . $row['course_num'];
    }// if this student did not be a TA this term
    if($found==0){ echo "This student did not be a TA in " . $year . "<br>"; }

    echo "<br>Wishlist<br>";
    $flag = 0;
    //profid, profname, term_month_year, course_type, course_num, TA_name, student_ID
    if($f = fopen("./wishlist.csv","r")){
        while(!feof($f)){
            $line = fgets($f);
            $line = explode(",", $line);
            if($piece[1]==$line[6] && $year==$line[2]) { 
                echo $line[0]." ".$line[1]." ".$line[3].$line[4]."<br>"; 
                $flag = 1;
            }
        }
        fclose($f);
    }
    if($flag==0) { echo "No professor wants this student!<br>"; }

    // if this student is a freshmen
    $db->close;
    $back = "<br><button onclick=\"history.back()\"><span>Back</span></button>";
    echo $back;
?>
</body>
</html>