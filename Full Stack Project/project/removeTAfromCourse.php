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
    $value = $_POST["remove"];
    $ID = explode(" ", $value);
    $res = $db->exec("DELETE FROM CourseAndTA WHERE TAID='$ID[1]';");

    $count = $db->query("SELECT COUNT(TAID) as count FROM CourseAndTA WHERE student_ID='$ID[0]';")->fetchArray();
    $types = $db->query("SELECT types FROM user WHERE ID='$ID[0]';")->fetchArray();
    if($count['count']==0){
        $types = explode(";", $types);
        $update_type = "";
        foreach ($types as $type) {
            if ($type<>"TA") { $update_type = $update_type . $type . ";"; }
        }
        $update = $db->exec("UPDATE user SET types='$update_type' WHERE ID='$ID[0]';") or die(print_r($db->errorInfo(), true));
    }

    $db->close;
    $message = "Remove TA from course successfully.";
    echo "<script type='text/javascript'>alert('$message'); history.back();</script>";
?>