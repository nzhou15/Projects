<?php
    $term = strtok($_POST['choice'], "-");
    $course = strtok("-");
    $course_type = strtok($course, " ");
    $course_num = strtok(" ");
    // echo "$term - $course_type $course_num";

      // connect to the database
    class MyDB extends SQLite3{
        function __construct() { $this->open('../../COMP307/TAmanagement.db'); }
    }
    $db = new MyDB();
    if(!$db) { echo $db->lastErrorMsg(); }
    
    $time = $_POST['time'];
    $location = $_POST['location'];
    $duties = $_POST['duties'];
    $id = $_COOKIE['ID'];

    $query = $db->query("SELECT COUNT(*) AS num FROM OH;")->fetchArray();
    $oh_id = $query['num']+1;

    $ret = $db->exec("INSERT INTO OH (time, location, duties, id, term_month_year, course_type, course_num, oh_id) VALUES 
    ('$time', '$location', '$duties', '$id', '$term', '$course_type', $course_num, $oh_id);") or die(print_r($db->errorInfo(), true));
    if(!$ret) {
        echo $db->lastErrorMsg();
    } else {
        $message = "Office hour changed successfully.";
        echo "<script type='text/javascript'>alert('$message'); history.go(-2); </script>";
    }


?>