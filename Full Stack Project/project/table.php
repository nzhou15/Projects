<?php session_start();?>
<?php   // security
  // connect to the database
  class MyDB extends SQLite3{
      function __construct() { $this->open('../../COMP307/TAmanagement.db'); }
  }
  $db = new MyDB();
  if(!$db) { echo $db->lastErrorMsg(); }

  // verify if ticket exists, ticket expired, ticket permission
  $ticket = $_COOKIE["ticket"];
  $id = $_COOKIE["ID"];
  $type = $_COOKIE["type"];

  if($ticket == null){
    header("Location: https://www.cs.mcgill.ca/~jzhou70/project/login_page.php");
    exit();
  }

  $query = $db->query("SELECT id, expire, permission, COUNT(1) AS num FROM tickets WHERE ticket = $ticket")->fetchArray();
  $time = date("Y-m-d H:i:s");
  if($time > $query['expire']) {
      $ret = $db->exec("DELETE FROM tickets WHERE ticket = $ticket;") or die(print_r($db->errorInfo(), true));
      if(!$ret) { echo $db->lastErrorMsg(); }
      $message = "Session Timeout.";
      echo "<script type='text/javascript'>alert('$message'); window.location.href='login_page.php';</script>";
  }

  if($type != $query['permission']){
      $message = "Permission dismatch.";
      echo "<script type='text/javascript'>alert('$message'); window.location.href='login_page.php';</script>";
  }
?>

<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
<link rel="stylesheet" type="text/css" href="topnav.css">
<style>
@import url(https://fonts.googleapis.com/css?family=Roboto+Slab:300);

h1 {
  font-weight: normal;
  font-variant: small-caps;
  text-align: center;
}

table * {height: auto; min-height: none;} /* fixed ie9 & <*/
table {
  box-sizing: border-box;
  font-size: 1rem;
  background: #DADED4;
  table-layout: fixed;
  margin: 1rem auto;
  width: 98%;
  box-shadow: 0 0 4px 2px rgba(0,0,0,.4);
  border-collapse: collapse;
  border: 1px solid rgba(0,0,0,.5);
  border-top: 0 none;
}
thead {
  background: #DADED4;
  text-align: center;
  z-index: 2;
}
thead tr {
  /* padding-left: 8%; */
  box-shadow: 0 4px 6px rgba(0,0,0,.2);
  z-index: 2;
}

th {
  border-right: 2px solid rgba(0,0,0,.2);
  padding: .7rem 0;
  font-size: 1.5rem;
  font-weight: normal;
  font-variant: small-caps;
}
tbody {
  display: block;
  /* height: calc(50vh - 1px); */
  min-height: calc(200px + 1 px);
  /*use calc for fixed ie9 & <*/
  overflow-Y: scroll;
  /* color: #000; */
}
tr {
  display: block;
  overflow: hidden;
}
tbody tr:nth-child(odd) {
  background: rgba(0,0,0,.1);
}
th, td {
  width: 10%;
  /* float: center; */
}
td {
  /* padding: .5rem 0 .5rem 1rem; */
  border-right: 2px solid rgba(0,0,0,.2);
}
/* td:nth-child(2n) {color: #fff;} */
 
th:last-child, td:last-child {
  width: 17%;
  text-align: center;
  border-right: 0 none;
  padding-left: 0;
}

[data-campo='Duties'] {
  width: 40%;
}  


@media only screen and (max-width:800px) {
  table {
    border-top: 1px solid ;
  }
  /* thead {display: none;} */
  tbody {
    height: auto;
    max-height: 55vh;
  }
  tr {
    border-bottom: 2px solid rgba(0,0,0,.35);
  }
  /* tbody tr:nth-child(odd) {background: #15BFCC;}
  tbody tr:nth-child(even) {background:#FF7361;} */
  td {
    display: block;
    width: 100%;
    min-width: 100%;
    padding: .4rem .5rem .4rem 40%;
    border-right: 0 none;
  }
  td:before {
    content: attr(data-campo);
    /* background: rgba(0,0,0,.1); */
    position: absolute;
    left: 0;
    top: 0;
    bottom: 0;
    width: auto;
    min-width: 37%;
    padding-left: .5rem;
    /* font-family: monospace; */
    font-size: 150%;
    font-variant: small-caps;
    line-height: 1.8;
  }
  tbody td:last-child {
    text-align: left;
    padding-left: 40%;
  }
  td:nth-child(even) {
    /* background: rgba(0,0,0,.2); */
  }
}

button {
  position: relative;
  display: inline-flex;
  justify-content: center;
  align-items: center;
  border-radius: 5px;
  background: #39603D;
  color: white;
  box-shadow: 0px 6px 24px 0px rgba(0, 0, 0, 0.2);
  overflow: hidden;
  border: none;
  margin: 1.5%;
  /* float: center; */
}

button:after {
  content: " ";
  width: 0%;
  height: 100%;
  background: #DADED4;
  position: absolute;
  transition: all 0.4s ease-in-out;
  right: 0;
}

button:hover::after {
  right: auto;
  left: 0;
  width: 100%;
}

button span {
  text-align: center;
  text-decoration: none;
  width: 100%;
  padding: 17px 25px;
  font-size: 1.125em;
  font-weight: 700;
  letter-spacing: 0.3em;
  z-index: 20;
  transition: all 0.3s ease-in-out;
}

button:hover span {
  color: #39603D;
  animation: scaleUp 0.3s ease-in-out;
}

</style>
</head>

<body>
<?php 
    $course = $_POST["course"];
    $pieces = explode(" ", $course);
    // echo $course;
    $res = $db->query("SELECT * FROM CourseAndTA WHERE course_type  = '$pieces[0]' AND course_num  = '$pieces[1]';");
    echo "<h1>$course</h1><table><thead><tr><th data-campo='TA'>TA names</th></tr></thead><tbody>";

    while($row = $res->fetchArray()){
        $tname = $row["TA_name"] . "</td></tr>";
        echo "<tr><th data-campo='TA'>$tname</th>";
    }
    echo "</tbody></table>";
    $db->close;
    $back = "<button onclick=\"history.back()\"><span>Back</span></button>";
    echo $back;
?>
</body>
</html>