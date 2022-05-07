<?php
    if($_POST['ta'] == '' or $_POST['star'] == ''){
        $message = "Please choose a TA or give a score.";
        echo "<script type='text/javascript'>alert('$message'); window.location.href='rating.php'; </script>";
        exit();
    }

    $arr = array();
    $str = strtok($_POST['ta'], ",");   
    while($str != NULL){
        array_push($arr, $str);
        // echo $str . "<br>";
        $str = strtok(",");
    }
    $term = $arr[0];
    $course_type = strtok($arr[1], " ");
    $course_num = strtok(" ");
    $tname = $arr[2];
    $sid = $arr[3];
    // echo $sid;
   
    // insert this record in the table 
    class mySqlite extends SQLite3{
        function __construct(){
            $this->open('../../COMP307/TAmanagement.db');
        }
    }
    $db = new mySqlite();
    if(!$db){ echo $db->lastErrorMsg(); }

    $score = $_POST['star'];    
    $query = $db->query("SELECT COUNT(*) AS num FROM rating;")->fetchArray();
    $rid = $query['num'] + 1;
    $comment = $_POST['comment'];
    $query = $db->exec("INSERT INTO rating(term_month_year, course_type, course_num, tname, score, comment, student_ID, rid) VALUES ('$term', '$course_type', $course_num, '$tname', $score, '$comment', '$sid', $rid);");
    if(!$query) {
        echo $db->lastErrorMsg();
    } else {
        $message = "Successfully rated $tname in $arr[1].";
        echo "<script type='text/javascript'>alert('$message'); window.location.href='home_page.php'; </script>";
    }
?>