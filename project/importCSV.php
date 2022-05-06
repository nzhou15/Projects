<?php
    class MyDB extends SQLite3{
        function __construct(){
            $this->open('../../COMP307/TAmanagement.db');
        }
    }

    // open database
    $db = new MyDB();
    if(!$db){ echo $db->lastErrorMsg(); }

    // open and read CourseQuota.csv
    $open_course = fopen("CourseQuota.csv", "r") or die("Unable to open file!");
    while (($data = fgetcsv($open_course, 1000, ",")) !== FALSE) {   
        $res = $db->query("SELECT COUNT(*) as count FROM CourseQuota WHERE term_month_year='$data[0]' AND course_num='$data[1]' AND course_type='$data[2]';");  
        $rows = $res->fetchArray();
        if($rows['count']>0)continue;
        $ret = $db->exec("INSERT INTO CourseQuota (term_month_year,course_num,course_type,course_name,instructor_name,course_enrollment_num,TA_quota) VALUES ('$data[0]','$data[1]','$data[2]','$data[3]','$data[4]','$data[5]','$data[6]');") or die(print_r($db->errorInfo(), true));
        if(!$ret) {         
            echo $db->lastErrorMsg();
         } else {
            echo "Records created successfully (course)\n";
         }
    }
    fclose($open_course); // close CourseQuota.csv
    
    // open and read TACohort.csv
    $open_ta = fopen("TACohort.csv", "r") or die("Unable to open file!");
    while (($data = fgetcsv($open_ta, 1000, ",")) !== FALSE) {   
        $res = $db->query("SELECT COUNT(*) as count FROM TACohort WHERE student_ID='$data[2]';");  
        $rows = $res->fetchArray();
        if($rows['count']>0)continue;
        $ret = $db->exec("INSERT INTO TACohort (term_month_year, TA_name, student_ID, legal_name, email, grad_ugrad, supervisor_name, priority, hours, date_applied, location, phone, degree, course_applied_for, open_to_other_courses, notes) VALUES ('$data[0]','$data[1]','$data[2]','$data[3]','$data[4]','$data[5]','$data[6]','$data[7]','$data[8]','$data[9]','$data[10]','$data[11]','$data[12]','$data[13]','$data[14]','$data[15]');") or die(print_r($db->errorInfo(), true));
        if(!$ret) {
            echo $db->lastErrorMsg();
         } else {
            echo "Records created successfully (TA)\n";
         }
    }
    fclose($open_ta);
    $db->close;
    $message = "Import TA cohort successfully.";
    echo "<script type='text/javascript'>alert('$message'); window.location.href='home_page.php'; </script>";

?>