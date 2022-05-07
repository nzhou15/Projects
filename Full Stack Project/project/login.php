<?php session_start(); ?>
<?php
  // conmect to sqlite3 database  
  class mySqlite extends SQLite3{
    function __construct(){
        $this->open('../../COMP307/TAmanagement.db'); 
    }
  }
  $db = new mySqlite();
  if(!$db){ echo $db->lastErrorMsg(); }  

  $found = 0;
  $results = $db->query('SELECT * FROM users');
  while ($row = $results->fetchArray()) {
    // find the record with correct username
    if($row['username'] == $_POST['username']){
      $found = 1;

      // check if the password is correct
      if(password_verify($_POST['password'], $row['password'])){
        // echo "Correct password." . "<br>";

        // generate a random unique ticket id for this login
        $ticket = rand();
        $query = $db->query("SELECT COUNT(1) AS num FROM tickets WHERE ticket = $ticket")->fetchArray();
        // echo $query['num'] . "<br>";

        while($query['num'] != 0){
          $ticket = rand();
          $query = $db->query("SELECT COUNT(1) AS num FROM tickets WHERE ticket = $ticket")->fetchArray();
        }
        $type = $row['types'];

        // handle the case that a user's account belong to more than one user type
        $t = rtrim(strtok($type, ";"));
        $stud = false;
        $prof = false;
        $admin = false;
        $ta = false;
        $sys = false;
        while($t != NULL){
          echo $t . "<br>";
          if($t == "student") { $stud = true; }
          if($t == "prof") { $prof = true; }
          if($t == "admin") { $admin = true; }
          if($t == "TA") { $ta = true; }
          if($t == "sysop") { $sys = true; }
          $t = rtrim(strtok(";"));
        }

        if($sys){
          $type = "sysop";
        }elseif($admin){
          $type = "admin";
        }elseif($prof){
          $type = "prof";
        }elseif($ta){
          $type = "TA";
        }else {
          $type = "student";
        }
        // echo "type:" . $type;
        
        $expire = date("Y-m-d H:i:s", strtotime("+60 minutes"));    // this ticket expires after 30 minutes
        // echo $type;
        $id = $row['ID'];
        setcookie("type","{$type}",time()+60*60*24*1);
        setcookie("ID","{$id}",time()+60*60*24*1);
        setcookie("ticket","{$ticket}",time()+60*60*24*1);

        $query = $db->exec("INSERT INTO tickets(ticket, expire, permission, id) VALUES ($ticket, '$expire', '$type', '$id');");

        $_SESSION['ticket'] = $ticket;
        $_SESSION['type'] = $type;
        $_SESSION['ID'] = $row['ID'];
        header("Location: https://www.cs.mcgill.ca/~jzhou70/project/home_page.php");        
        exit();
      }else{
        // return an error message, then redirect to login page
        $message = "Wrong password.";
        echo "<script type='text/javascript'>alert('$message'); window.location.href='login_page.php'; </script>";
      }
    }
  }
  
  // check if the username exist in the database, otherwise show an error message.
  if($found == 0){
    $message = "Username " . $_POST['username'] . " does not exist.";
    echo "<script type='text/javascript'>alert('$message'); window.location.href='login_page.php'; </script>";
  }
?>



