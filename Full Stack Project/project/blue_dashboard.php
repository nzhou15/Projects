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

  if($type == "TA") { echo '<style type="text/css"> #menu1 { display: none; } #menu2 { display: none; } #menu3 { display: none; } </style>';}
?>

<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
<link rel="stylesheet" type="text/css" href="blue_dashboard_style.css">
</head>

<body>
<div class="header">
    <form action="delete_ticket.php"><input type="submit" value="Logout"></form>
</div>

<div class="topnav" id="myTopnav">
  <a id="menu0" onclick="showContent('menu0', 'content0')" class="active">Office Hour</a>
  <a id="menu1" onclick="showContent('menu1', 'content1')" class="none">TA Performance</a>
  <a id="menu2" onclick="showContent('menu2', 'content2')" class="none">TA Wishlist</a>
  <!-- <a id="menu3" onclick="showContent('menu3', 'content3')" class="none">TA Wishlist</a> -->
  <a href="javascript:void(0);" class="icon" onclick="myFunction()">
    <i class="fa fa-bars"></i>
  </a>
</div>

<div id="content0" >
<h1>Office Hours</h1>
<table>
 <thead>
	<tr>
    <th data-campo='Time'>Time</th>
    <th data-campo='Location'>Location</th>
    <th data-campo='Duties'>Duties</th>
	</tr>
 </thead>
 <tbody>
<?php 
  $course = $_POST['course'];
  $course_type = strtok($course, " ");
  $course_num = strtok(" ");
  $term = $_POST['term'];
  
  $res = $db->query("SELECT time, location, duties FROM OH WHERE id='$id' AND term_month_year='$term' 
  AND course_type='$course_type' AND course_num=$course_num ORDER BY time;");
  while($row = $res->fetchArray()){ 
    $time = $row['time'];
    $location = $row['location'];
    $duties = $row['duties'];
    // $str = $time . " " . $location . " " . $duties;
    // echo $str;
    echo "<tr><th data-campo='Time'>$time</th>
    <th data-campo='Location'>$location</th>
    <th data-campo='Duties'>$duties</th></tr>";
  }
  echo "</tbody></table><form action=\"OH.php\" method=\"post\">";
  $value = $term . "-" . $course;
  echo "<button type=\"submit\" name=\"choice\" value='$value'><span>Add Office Hours</span></button></form>";
?>
</div>

<div id="content1" style="display:none">
<h1>TA Performance</h1>
<?php
  $thisTerm = $_POST['term'];
  $thisCourse = explode(" ", $_POST['course']);

  $res = $db->query("SELECT TA_name, student_ID FROM CourseAndTA  WHERE term_month_year='$thisTerm' AND course_num='$thisCourse[1]' AND course_type='$thisCourse[0]'");
  echo "Select a TA for " . $POST['course'] . $_POST['term'] . " to leave comment.";
  echo "<form action=\"perform.php\" method=\"post\">";
  echo "<select id=\"TA\" name=\"TA\">";
  echo "<option value>-- Please select a TA --</option>";
  while($row = $res->fetchArray()){
    $term = $row['TA_name'];
    $name_id_term_course = $row['TA_name'] . " " . $row['student_ID'] . " " . $thisTerm . " " . $_POST['course'];
    echo "<option value='$name_id_term_course'>" . $term . "</option>";
  }
  echo "</select><br><br>";
  echo "Please leave an comment for TA: <br>";
  echo "<textarea rows=\"5\" cols=\"60\" name=\"comment\"></textarea><br>";
  echo "<button type=\"submit\" name=\"selection\" value=\"submit\"><span>Submit</span></button>";
  echo "</form>";
?>
</div>

<div id="content2" style="display:none">
<h1>TA Wishlist</h1>
<p>Please select a TA to add into TA wishlist.</p>
<?php
  $term = $_POST['term'];
  $course = $_POST['course'];
  $piece = explode(" ", $course);
  $course_type = $piece[0];
  $course_num = $piece[1];
  $profid = $_COOKIE["ID"];

  $db = new MyDB();
  if(!$db){ echo $db->lastErrorMsg(); }

  $res = $db->query("SELECT legal_name, student_ID FROM TACohort WHERE term_month_year='$term';");
  $db->close;
  echo "<form action=\"addToWishlist.php\" method=\"post\">";
  echo "<select name=\"wishlist\" id=\"course-select\">";
  echo "<option value> -- Please choose a student -- </option>";
  $profname = $db->query("SELECT DISTINCT instructor_assigned_name FROM profCourse WHERE profID = '$profid'; ")->fetchArray()[0];
  while($row = $res->fetchArray()){
      $tmp = $profid . "," . $profname . "," . $term . "," . $course_type . "," . $course_num . "," . $row['legal_name'] . "," . $row['student_ID'];
      echo "<option value=\"$tmp\">" . $row['legal_name'] . ": " .$row['student_ID'] . "</option>";
  }
  echo "</select><br>";
  echo "<button type=\"submit\" value=\"Submit\"><span>Submit</span></button>";
  echo "</form>";
?>
</div>


<script>
function myFunction() {
  var x = document.getElementById("myTopnav");
  if (x.className === "topnav") {
    x.className += " responsive";
  } else {
    x.className = "topnav";
  }
}

function showContent(menuElement, theContent) {
	// Hide everything
  document.getElementById("content0").style.display="none";
	document.getElementById("content1").style.display="none";
	document.getElementById("content2").style.display="none";
	
	// Remove "active" class
	document.getElementById("menu0").className="none";
	document.getElementById("menu1").className="none";
	document.getElementById("menu2").className="none";
	
	// Show the one that was selected
	document.getElementById(theContent).style.display="block";
	document.getElementById(menuElement).className="active";
}
</script>

</body>
<html>