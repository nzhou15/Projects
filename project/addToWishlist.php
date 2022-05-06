<?php
    $myfile = fopen("./wishlist.csv","ab") or die("Unable to open file!");
    //profid, profname, term_month_year, course_type, course_num, TA_name, student_ID
    $data = $_POST["wishlist"];
    fwrite($myfile, $data . "\r\n");
    fclose($myfile);
    $message = "Add successfully!";
    echo "<script type='text/javascript'>alert('$message'); history.go(-1); </script>";
?>
