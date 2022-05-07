<?php 
    class MyDB extends SQLite3{
        function __construct(){
            $this->open('../../COMP307/TAmanagement.db');
        }
    }

    $db = new MyDB();
    if(!$db){ echo $db->lastErrorMsg(); }

    $val = $_POST["tahistory"];
    $piece = explode("-", $val);

    $ta_history = $db->query("SELECT term_month_year, course_type, course_num FROM CourseAndTA WHERE student_ID='$piece[1]';");
    $found=0;
    echo "Student Rating Average for: <br>";
    while($row = $ta_history->fetchArray()){
        $found=1;
        echo $row["term_month_year"] . "-" . $row["course_type"] . " " . $row["course_num"] . "<br>";
    }// if this student has not been a TA
    if($found==0){ echo "This student has never been a TA before<br>"; }

    $back = "<br><button onclick=\"history.back()\">Go Back</button>";
    echo $back;
?>