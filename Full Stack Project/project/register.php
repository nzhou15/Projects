<?php 
    // Loop over field names, make sure each one exists and is not empty
    $required = array('first_name', 'last_name', 'email', 'id', 'username', 'password');
    $error = false;
    $empty;
    foreach($required as $field) {
        if (empty($_POST[$field])) {
            $error = true;
            $empty = $field; 
            break;
        }
    }
    if ($error) {
        $message = "Please do not leave $empty empty.";
        echo "<script type='text/javascript'>alert('$message'); history.go(-1); </script>";
    }
    
    // check student id and password formart
    if(strlen($_POST["password"]) < 3 or strlen($_POST["password"]) > 18){
        $message = "Password should be 3-18 characters.";
        echo "<script type='text/javascript'>alert('$message'); history.go(-1); </script>";
    }
    
    if(strlen($_POST["id"]) != 9) { // also should check if it is a valid McGill student ID
        $message = "Please enter a valid McGill student ID.";
        echo "<script type='text/javascript'>alert('$message'); history.go(-1); </script>";
    }
    
    class mySqlite extends SQLite3{
        function __construct(){
            $this->open('../../COMP307/TAmanagement.db'); 
        }
    }
    $db = new mySqlite();
    if(!$db){ echo $db->lastErrorMsg(); }  

    $fname = $_POST['first_name'];
    $lname = $_POST['last_name'];
    $email = $_POST['email'];
    $id = $_POST['id'];
    $username = $_POST['username'];
    $hashed_password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $courses = $_POST['courses'];

    $arr;
    foreach($courses as $item){
        $arr = $arr . $item . ";";
    }
    $value = "('" . $fname . "', '" . $lname . "', '" . $email . "', '" . $id . "', '" . $username . "', '" . $hashed_password . "', '" . $arr . "', 'student');";
    // echo $value . "<br>";   

    $query = $db->exec("INSERT INTO users(fname, lname, email, ID, username, password, courses, types) VALUES $value");
    if(!$query) {
        echo $db->lastErrorMsg();
    } else {
        $message = "Successfully registered.<br> Please login in with your username and password.";
        echo "<script type='text/javascript'>alert('$message'); window.location.href='login_page.php'; </script>";
    }

    // $header = "From: noreply@example.com\r\n";
    // $header.= "MIME-Version: 1.0\r\n";
    // $header.= "Content-Type: text/html; charset=ISO-8859-1\r\n";
    // $header.= "X-Priority: 1\r\n";

    // $status = mail($to, $subject, $message, $header);

    // if($status)
    // {
    //     echo '<p>Your mail has been sent!</p>';
    // } else {
    //     echo '<p>Something went wrong. Please try again!</p>';
    // }
?>