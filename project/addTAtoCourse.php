<?php 
    class MyDB extends SQLite3{
        function __construct(){
            $this->open('../../COMP307/TAmanagement.db');
        }
    }

    // open database
    $db = new MyDB();
    if(!$db){ echo $db->lastErrorMsg(); }

    // read from user's import
    $student_id = $_POST["addta"];
    $term = $_POST["addterm"];
    $course_name = $_POST["addcourse"];
    $pieces = explode(" ", $course_name);
    $course_type = $pieces[0];
    $course_num = $pieces[1];

    // check if the user exist
    $user = $db->query("SELECT types, COUNT(ID) as count FROM user WHERE ID='$pieces[1]';")->fetchArray();
    if($$user['count']>0) {
        // check if the import TA duplicate
        $res = $db->query("SELECT *, COUNT(*) as count FROM CourseAndTA WHERE term_month_year = '$term' AND course_type = '$course_type' AND course_num = '$course_num';");
        $rows = $res->fetchArray();

        if($rows['count']==0) {
            $id = $db->query("SELECT MAX(TAID) FROM CourseAndTA;")->fetchArray()[0];
            $tmp = $db->query("SELECT *, COUNT(*) as count FROM TACohort WHERE student_ID = '$student_id' AND term_month_year='$term';")->fetchArray();
            // check if the import TA applied for work
            if($tmp['count']>0){
                $legal_name = $tmp['legal_name'];
                $hours = $tmp['hours'];
                $require = $db->query("SELECT assigned_hours, COUNT(assigned_hours) as count FROM CourseAndTA WHERE student_ID = '$student_id' AND term_month_year='$term';")->fetchArray();
                // check if this TA exceed required work hours
                if($require['count']==0) {
                    $id=1+$id;
                    $ret = $db->exec("INSERT INTO CourseAndTA (term_month_year, course_num, course_type, TA_name, student_ID, assigned_hours, TAID) VALUES ('$term','$course_num','$course_type','$legal_name','$student_id','$hours','$id');") or die(print_r($db->errorInfo(), true));
                    if(!$ret) { echo $db->lastErrorMsg(); } 
                    else { 
                        echo "Add TA successfully\n"; 
                        $types = $user['types'] . "TA" . ";";
                        $update = $db->exec("UPDATE user SET types='$types' WHERE ID='$pieces[1]';") or die(print_r($db->errorInfo(), true));
                    }
                } elseif($require['count']==1) {
                    // if this student is open to other course
                    if($tmp['open_to_other_courses']==1) {
                        $work_hour = $require['assigned_hours'];
                        // both applied hours and has-worked-hours both be 90
                        if($hours==90 && $work_hour==90) {
                            $ret = $db->exec("INSERT INTO CourseAndTA (term_month_year, course_num, course_type, TA_name, student_ID, assigned_hours, TAID) VALUES ('$term','$course_num','$course_type','$legal_name','$student_id','$hours','1+$id');") or die(print_r($db->errorInfo(), true));
                            if(!$ret) { echo $db->lastErrorMsg(); } 
                            else { 
                                echo "Add TA successfully\n"; 
                                $types = $user['types'] . "TA" . ";";
                                $update = $db->exec("UPDATE user SET types='$types' WHERE ID='$pieces[1]';") or die(print_r($db->errorInfo(), true));
                            }
                        } else {
                            $message = "This TA has already exceeded highest work hours\n";
                        }
                    } else { $message = "This TA do not accept opening to other course\n"; }
                } else { $message = "This TA has already exceeded highest work hours\n"; }
            } else {
                $message = "This student did not apply. Please check student ID carefully.\n";
            }
        } else {
            $message = "This TA has already in this course.\n";
        }
    } else {
        $message = "This student do not exist in system.\n";
    }

    $db->close;
    echo "<script type='text/javascript'>alert('$message'); history.back(); </script>";
?>