<?php

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
if((isset($_POST['term']) && !empty($_POST['term']))
&& (isset($_POST['course']) && !empty($_POST['course']))
&& (isset($_POST['type']) && !empty($_POST['type']))
&& (isset($_POST['cname']) && !empty($_POST['cname']))
&& (isset($_POST['iname']) && !empty($_POST['iname']))
&& (isset($_POST['cenum']) && !empty($_POST['cenum']))
&& (isset($_POST['taquota']) && !empty($_POST['taquota']))){
    $term = $_POST['term'];
    $course = $_POST['course'];
    $type = $_POST['type'];
    $cname = $_POST['cname'];
    $iname = $_POST['iname'];
    $cenum = $_POST['cenum'];
    $taquota = $_POST['taquota'];

    $res = $db->query("SELECT COUNT(*) as count FROM CourseQuota WHERE term_month_year='$tterm' AND course_num='$tpname' AND course_type='$id';");
    $rows = $res->fetchArray();
    if ($rows['count']==0) { 
        
        $ret = $db->exec("INSERT INTO CourseQuota (term_month_year,course_num,course_type,course_name,instructor_name,course_enrollment_num,TA_quota) VALUES ('$term','$course','$type','$cname','$iname','$cenum','$taquota');") or die(print_r($db->errorInfo(), true));

        if(!$ret) {
            echo $db->lastErrorMsg();
        } else {
            echo "Records created successfully\n";
        }
    }
    $db->close;
    $message = "Manually import course information successfully.";
    echo "<script type='text/javascript'>alert('$message'); window.location.href='home_page.php'; </script>";
}
?>
