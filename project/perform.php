<?php
    $rec = explode(" ", $_POST['TA']);
    $TAstuID = $rec[2];
    //echo $TAid;
    $TAname = $rec[0] . " " . $rec[1];
    //echo $TAname;

    $comment = $_POST['comment'];
    $profid = $_COOKIE["ID"];

    class MyDB extends SQLite3{
        function __construct() { $this->open('../../COMP307/TAmanagement.db'); }
    }
    $db = new MyDB();
    if(!$db) { echo $db->lastErrorMsg(); }

    $res = $db->query("SELECT DISTINCT instructor_assigned_name FROM profCourse WHERE profID = '$profid'; ")->fetchArray();
    $profname = $res[0];
    $coursetype = $rec[5];

    $coursenum = $rec[6];

    $term = $rec[3] . " " . $rec[4];
    $curTime = date("Y/m/d") . " " . date("h:i:sa");

    // check if there is records in the performance table already
    $check = $db->query("SELECT lid, COUNT(*) as count FROM performance_log;")->fetchArray();
    $lid = $check['count'] + 1;

    $ret = $db->exec("INSERT INTO performance_log (lid, profname, taId, TA_name, term_month_year, course_type, course_num, comment, commentTime) VALUES ('$lid', '$profname', '$TAstuID', '$TAname', '$term', '$coursetype', '$coursenum', '$comment', '$curTime');")or die(print_r($db->errorInfo(), true));
    if(!$ret) { echo $db->lastErrorMsg(); } 
    else {
        //  echo "Comment TA successfully.\n"; 
        $message = "Comment $TAname successfully.";
        echo "<script type='text/javascript'>alert('$message'); history.go(-1);</script>";
        
    }

    
?>