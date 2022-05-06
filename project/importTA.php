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

// if((isset($_POST['tterm']) && !empty($_POST['tterm']))
// && (isset($_POST['tpname']) && !empty($_POST['tpname']))
// && (isset($_POST['id']) && !empty($_POST['id']))
// && (isset($_POST['lname']) && !empty($_POST['lname']))
// && (isset($_POST['email']) && !empty($_POST['email']))
// && (isset($_POST['grad']) && !empty($_POST['grad']))
// && (isset($_POST['super']) && !empty($_POST['super']))
// && (isset($_POST['priority']) && !empty($_POST['priority']))
// && (isset($_POST['hours']) && !empty($_POST['hours']))
// && (isset($_POST['date']) && !empty($_POST['date']))
// && (isset($_POST['location']) && !empty($_POST['location']))
// && (isset($_POST['phone']) && !empty($_POST['phone']))
// && (isset($_POST['degree']) && !empty($_POST['degree']))
// && (isset($_POST['acource']) && !empty($_POST['acource']))
// && (isset($_POST['open']) && !empty($_POST['open']))
// && (isset($_POST['notes']) && !empty($_POST['notes']))){
    $tterm = $_POST['tterm'];
    $tpname = $_POST['tpname'];
    $id = $_POST['id'];
    $lname = $_POST['lname'];
    $email = $_POST['email'];
    $grad = $_POST['grad'];
    $super = $_POST['super'];
    $priority = $_POST['priority'];
    $hours = $_POST['hours'];
    $date = $_POST['date'];
    $location = $_POST['location'];
    $phone = $_POST['phone'];
    $degree = $_POST['degree'];
    $acource = $_POST['acource'];
    $open = $_POST['open'];
    $notes = $_POST['notes'];

    $res = $db->query("SELECT COUNT(*) as count FROM TACohort WHERE student_ID='$id';");  
    $rows = $res->fetchArray();

    if ($rows['count']==0) { 

        $ret = $db->exec("INSERT INTO TACohort (term_month_year, TA_name, student_ID, legal_name, email, grad_ugrad, supervisor_name, priority, hours, date_applied, location, phone, degree, course_applied_for, open_to_other_courses, notes) VALUES ('$tterm','$tpname','$id','$lname','$email','$grad','$super','$priority','$hours','$date','$location','$phone','$degree','$acource','$open','$notes');") or die(print_r($db->errorInfo(), true));

        if(!$ret) {
            echo $db->lastErrorMsg();
        } else {
            echo "Records created successfully\n";
        }
    }
    $db->close;
    $message = "Manually import TA information successfully.";
    echo "<script type='text/javascript'>alert('$message'); window.location.href='home_page.php'; </script>";
?>
