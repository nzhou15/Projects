<?php session_start(); ?>
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
    // echo $ticket;
    // echo $type;
    // echo $id;

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
<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
<link href="https://fonts.googleapis.com/css?family=Work+Sans:400,600,800&display=swap" rel="stylesheet">
<link href="https://fonts.googleapis.com/css?family=Arvo&display=swap" rel="stylesheet">
<link rel="stylesheet" type="text/css" href="home_style.css">
</head>
<body>

<div class="header">
    <form action="delete_ticket.php"><input type="submit" value="Logout"></form>
</div>

<ul class="menu cf" style="position: absolute; z-index: 999; margin-left: 22%;">
    <li><a id="menu0" onclick="showContent('menu0', 'green')" class="active">Rate a TA</a></li>
    <li>
        <a id="menu1" onclick="showContent('menu1', 'yellow')" class="none">Sysop Tasks</a></a>
        <ul class="submenu">
        <li><a onclick="showContent('menu1', 'yellow'),yellowDisplay('manage_users','yellow_menu0')" class="active">Manage user accounts</a></li>
        <li><a onclick="showContent('menu1', 'yellow'),yellowDisplay('add_users','yellow_menu1')" class="active">Add Users</a></li>
        <li><a onclick="showContent('menu1', 'yellow'),yellowDisplay('import_users','yellow_menu2')" class="active">Import Professors and Courses</a></li>
        </ul>			
    </li>
    <li><a id="menu2" onclick="showContent('menu2', 'blue')" class="none">TA Management</a></li>
    <li>
        <a id="menu3" onclick="showContent('menu3', 'orange')" class="none">TA Administration</a>
        <ul class="submenu">
        <li><a onclick="showContent('menu3', 'orange'),showOrangeContent('ta_cohort', 'cohort_menu')" class="active">Import TA cohort</a><br></li>
        <li><a onclick="showContent('menu3', 'orange'),showOrangeContent('ta_info', 'orangecontent1')" class="active">TA info/history</a></li>
        <li><a onclick="showContent('menu3', 'orange'),showOrangeContent('course_ta_history', 'orangecontent2')" class="active">Course TA history</a></li>
        <li><a onclick="showContent('menu3', 'orange'),showOrangeContent('add_ta', 'orangecontent3')" class="active">Add TA to a course</a></li>
        <li><a onclick="showContent('menu3', 'orange'),showOrangeContent('remove_ta', 'orangecontent4')" class="active">Remove TA from a course</a></li>
        </ul>	
    </li>
</ul>

<div class="topnav" id="myTopnav">
    <a id="menu0" onclick="showContent('menu0', 'green')" class="active">Rate a TA</a>
    <a id="menu1" onclick="showContent('menu1', 'yellow')" class="none">Sysop Tasks</a>
    <a id="menu2" onclick="showContent('menu2', 'blue')" class="none">TA Management</a>
    <a id="menu3" onclick="showContent('menu3', 'orange')" class="none">TA Administration</a>
    <a href="javascript:void(0);" class="icon" onclick="myFunction()">
        <i class="fa fa-bars"></i>
    </a>
</div>

<?php 
    // $type = $_SESSION["types"];
    if ($type == 'student'){
        echo '<style type="text/css"> #menu1 { display: none; } #menu2 { display: none; } #menu3 { display: none; } </style>';
        echo "<h1 class=\"rainbow-text\">Welcome Our Student!</h1>";
    }elseif ($type == 'prof' or $type == 'TA'){
        echo '<style type="text/css"> #menu1 { display: none; } #menu3 { display: none; } </style>';
        if($type == 'prof'){ echo "<h1 class=\"rainbow-text\">Welcome Our Professor!</h1>"; }
        else { echo "<h1 class=\"rainbow-text\">Welcome Our Teaching Assistant!</h1>"; }
    }elseif ($type == 'admin'){
        echo '<style type="text/css"> #menu1 { display: none; } </style>';
        echo "<h1 class=\"rainbow-text\">Welcome Our Admini<br>strator!</h1>";
    }else{
        echo "<h1 class=\"rainbow-text\">Welcome Our System Operator!</h1>";
    }
?>
  
<div id="green">
    <div class="frame">
        <div class="rating_green">
            <h2>Rating TA</h2>
            <form action="rating.php" method="post">
                <p>Which course would you like to evaluate?</p>
                <?php 
                    // $id = $_SESSION['ID'];
                    $res = $db->query("SELECT courses FROM users WHERE ID='$id';")->fetchArray();
                
                    // retrieve term_month_year and course
                    $arr = array();
                    $courses = rtrim(strtok($res['courses'], ";"));   
                    while($courses != NULL){
                        // echo $courses . "<br>";
                        array_push($arr, $courses);
                        $courses = rtrim(strtok(";"));
                    }
                    echo "<select name=\"ta_course\">";
                    echo "<option value=\"\">-- Please choose a course --</option>";
                    $terms = array();   // store terms in array
                    foreach($arr as $item){
                        $t = strtok($item, "-");
                        array_push($terms, $t);
                        $c = strtok("-");
                        $value = $t . "," . $c;
                        echo "<option value='$value'>" . $c . "</option>";
                    }           
                    echo "</select><br><br>";    
                ?>
                <button type="submit" value="Submit" <?php if ($type != 'student'){ ?> disabled <?php   } ?>><span>Submit</span></button>
            </form>
        </div>
    </div>
</div>

<div class="yellow" id="yellow" style="display:none">
<div class="frame">
    <div id="manage_users">
        <a onclick="yellowDisplay('manage_users','yellow_menu0')" class="active">Manage user accounts</a>
        <div class="hint" id="hint0">The management of user accounts(edit, delete).</div>
               
        <div id="yellow_menu0" style="display:none">
            <p>Select one user for editing or deleting. </p>
            <table>
            <form action="delete.php" method="post">
            <?php
                echo "<select name=\"user\">";
                echo "<option value=\"\">-- Please choose a user to edit or delete--</option>";
                $res = $db->query('SELECT * FROM users');
                while($row = $res->fetchArray()){
                    $tmp = $row['username'];
                    echo "<option value='$tmp'>" . $tmp .  "</option>";
                }
                echo "</select><br><br>";    
            ?>
            <br>
            <tr>
                <td class="actions">
                    <button type="submit" name="choice" value="delete"><span>Delete</span></button>
                </td>
                <td class="actions">
                    <button type="submit" name="choice" value="edit"><span>Edit</span></button>
                </td>
                </form>
                <td class="actions">
                    <button onclick="yellowDisplayEverything('yellow_menu0')"><span>Back</span></button>
                </td>
            </tr>
            </table>
        </div>
    </div>

    <div id="add_users" style="margin-top: 2%;">
        <a onclick="yellowDisplay('add_users','yellow_menu1')" class="active">Add Users</a>
        <div class="hint" id="hint1">The management of user accounts(add).</div>
        
        <div id="yellow_menu1" style="display:none;">
            <table>
            <form action="addUser.php" method="post">
                <br>First name: <input type="text" name="first_name"><br>
                Last name: <input type="text" name="last_name"><br>
                Email: <input type="text" name="email"><br>
                ID: <input type="text" name="ID">
                <br>
                Username: <input type="text" name="username"><br>
                Password: <input type="text" name="password"><br>
                User type: <input type="text" name="usertype"><br>
                <br><br>
                <tr>
                    <td><button type="submit" value="Add User"><span>Add</span></button></td> 
            </form>
                    <td><button onclick="yellowDisplayEverything('yellow_menu1')"><span>Back</span></button></td>
                </tr></table> 
        </div>
    </div>

    <div id="import_users" style="margin-top: 2%;">
        <a onclick="yellowDisplay('import_users','yellow_menu2')" class="active">Import Professors and Courses</a>
        <div class="hint" id="hint2">Quick import of profs and courses from a CSV file, and a manual way to input professors and courses.</div>
        
        <div id="yellow_menu2" style="display:none">
            <h3>Quick import</h3>
            <table>
            <form action="import.php" method="post">
                <button type="submit" value="Import"><span>Import</span></button>
            </form>
            <br>
            <h3>Manually Adding</h3>
            <form action="manualAdd.php" method="post">
                Prof ID: <input type="text" name="profid"><br>
                Term_month_year: <input type="text" name="tmy"><br>
                Course number: <input type="text" name="coursenum"><br>
                Course Name: <input type="text" name="coursename"><br>
                Instructor Name: <input type="text" name="insname"><br><br>
                <tr>
                    <td><button type="submit" value="Add"><span>Add</span></button></td>
            </form>
                    <td><button onclick="yellowDisplayEverything('yellow_menu2')"><span>Back</span></button></td>
                </tr></table>
        </div>
    </div>
</div>
</div>

<div id="blue" style="display:none">
<div class="frame">
    <div class="blue_content">
    <?php
        if($type == "TA"){
            echo "<h2> TA Select Course</h2>";
            $res = $db->query("SELECT course_type, course_num, term_month_year FROM CourseAndTA WHERE student_ID = '$id';");
            echo "<form action=\"blue_dashboard.php\" method=\"post\">";
            echo "<select id=\"course-select\" name=\"course\">";
            echo "<option value=\"\">-- Please choose a course --</option>";
            while($row = $res->fetchArray()){
                $course_type = $row['course_type'];
                $course_num = $row['course_num'];
                $course = $course_type . " " .$course_num;
                // echo $course;
                echo "<option value='$course'>" . $course . "</option>";
            }
            echo "</select><br><br>";

            $res = $db->query("SELECT DISTINCT term_month_year FROM CourseAndTA WHERE student_ID = '$id';");
            echo "<select id=\"term-select\" name=\"term\">";
            echo "<option value=\"\">-- Please choose a term --</option>";
            while($row = $res->fetchArray()){
                $term = $row['term_month_year'];
                // echo $term;
                echo "<option value='$term'>" . $term . "</option>";
            }
            echo "</select><br><br>";
            echo "<input type=\"submit\" name=\"selection\" value=\"submit\">";    
            echo "</form>";
        }
        else{
            echo "<h2>Professor Select Course</h2>";
            $res = $db->query("SELECT ct.course_type, ct.course_num from CourseAndTA ct, profCourse pc
            where ct.course_type = pc.course_type and ct.course_num = pc.course_num and ct.term_month_year = pc.term_month_year 
            and pc.profID = '$id';");

            echo "<form action=\"blue_dashboard.php\" method=\"post\">";
            echo "<select id=\"course-select\" name=\"course\">";
            echo "<option value=\"\">-- Please choose a course --</option>";
            while($row = $res->fetchArray()){ 
                $course_type = $row['course_type'];
                $course_num = $row['course_num'];
                $course = $course_type . " " . $course_num;
                echo "<option value='$course'>" . $course . "</option>";
            }
            echo "</select><br><br>";

            $res = $db->query("SELECT DISTINCT ct.term_month_year from CourseAndTA ct, profCourse pc
            where ct.course_type = pc.course_type and ct.course_num = pc.course_num and ct.term_month_year = pc.term_month_year 
            and pc.profID = '$id';");
            echo "<select id=\"term-select\" name=\"term\">";
            echo "<option value=\"\">-- Please choose a term --</option>";
            while($row = $res->fetchArray()){
                $term = $row['term_month_year'];
                echo "<option value='$term'>" . $term . "</option>";
            }
            echo "</select><br><br>";
            echo "<button type=\"submit\" name=\"selection\" value=\"submit\"><span>Submit</span></button>";
            echo "</form>";
        }
    ?>
    <br>
    </div>
    </div>
</div>

<div class="orange" id="orange" style="display:none">
<div class="frame">

    <div id="ta_cohort">
        <a id="orangemenu0" onclick="showOrangeContent('ta_cohort', 'cohort_menu')" class="active">Import TA cohort</a><br>
        <div class="hint" id="hint3">Import two CSV files or manually import called CourseQuota.csv and TACohort.csv.</div>

        <div class="orange_submenu" id="cohort_menu" style="display: none;">
            <br><a id="cohort_menu0" onclick="displaySubmenu('orangecontent0_sub0')" style="font-size:15px;">Import TA cohort by csv</a><br>
            <a id="cohort_menu1" onclick="displaySubmenu('orangecontent0_sub1')" style="font-size:15px;">Manully import cousre information</a><br>
            <a id="cohort_menu2" onclick="displaySubmenu('orangecontent0_sub2')" style="font-size:15px;">Manully import TA information</a><br>
        </div>

        <div id="orangecontent0_sub0" style="display: none;">
            <p>Press button to import csv file.</p>
            <form action="importCSV.php" method="post">
                <button type="submit" value="Update"><span>Update</span></button>
            </form>
        </div>

        <div id="orangecontent0_sub1" style="display: none;">
            <form action="importCourse.php" method="post">
                Term-Month-Year: <input type="text" name="term"><br/>
                Course Number : <input type="number" name="course"><br/>
                Course Type: <input type="text" name="type"><br/>
                Course Name: <input type="text" name="cname"><br/>
                Instructor Name: <input type="text" name="iname"><br/>
                Course Enrollment Number: <input type="number" name="cenum"><br/>
                TA Quota: <input type="number" name="taquota"><br/>
                <button type="submit" value="Submit"><span>Submit</span></button>
            </form>
        </div>

        <div id="orangecontent0_sub2" style="display: none;">
            <table>
            <form action="importTA.php" method="post">
                Term-Month-Year : <input type="text" name="tterm"><br/>
                TA Preferred Name : <input type="text" name="tpname"><br/>
                Student ID : <input type="text" name="id"><br/>
                Legal Name : <input type="text" name="lname"><br/>
                Email : <input type="text" name="email"><br/>
                Gradguate or Undergraduate :
                <input type="radio" name="grad" value="1">Gradguate
                <input type="radio" name="grad" value="0">Undergraduate<br/>
                Supervisor Name : <input type="text" name="super"><br/>
                Have priority :
                <input type="radio" name="priority" value="1">Have priority
                <input type="radio" name="priority" value="0">Do not have priority<br/>
                Hours(90/180) :
                <input type="radio" name="hours" value="90">90
                <input type="radio" name="hours" value="180">180<br>
                Applied Date : <input type="date" name="date"><br/>
                Location : <input type="text" name="location"><br/>
                Phone : <input type="text" name="phone"><br/>
                Which degree? :
                <select name="degree" id="degree-select">
                    <option value="arts">Arts</option>
                    <option value="science">Science</option>
                    <option value="arts and science">Arts and Science</option>
                    <option value="engineer">Engineer</option>
                    <option value="management">Management</option>
                </select><br/>
                Which course applied for : <input type="text" name="acource"><br/>
                Open for other course? :
                <input type="radio" name="open" value="1">Yes
                <input type="radio" name="open" value="0">No<br/>
                Notes : <input type="text" name="notes"><br/>
                <tr>
                    <td><button type="submit" value="Submit"><span>Submit</span></button></td>
            </form>
                    <!-- <td><button onclick="orangeDisplayEverything('orangecontent0')"><span>Back</span></button></td> -->
                </tr></table>
        </div>
    </div>

    <div id="ta_info">
        <a id="orangemenu1" onclick="showOrangeContent('ta_info', 'orangecontent1')" class="none">TA info/history</a>
        <div class="hint" id="hint4">The TA info/history feature gathers all the information about the selected TA from all sources.</div>

        <div id="orangecontent1" style="display: none;">
            <!-- <h2>TA to be selected</h2> -->
            <table>
            <?php
                $res = $db->query('SELECT student_ID, legal_name FROM TACohort');
                $db->close;
                echo "<br><form action=\"report.php\" method=\"post\">";
                echo "<select name=\"ta\" id=\"ta-select\">";
                echo "<option value> -- Please choose a TA -- </option>";
                while($row = $res->fetchArray()){
                    $tmp = $row['legal_name'];
                    $val = $tmp . "-" . $row['student_ID'];
                    echo "<option value=\"$val\">" . $tmp . "</option>";
                }
                echo "</select><br>";
                echo "<tr><td><button type=\"submit\" value=\"Submit\"><span>Submit</span></button></td>";
                echo "</form>";
            ?>
                <td><button onclick="orangeDisplayEverything('orangecontent1')"><span>Back</span></button></td>
                </tr></table>
        </div>
    </div>

    <div id="course_ta_history">
        <a id="orangemenu2" onclick="showOrangeContent('course_ta_history', 'orangecontent2')" class="none">Course TA history</a>  
        <div class="hint" id="hint5">The course TA history feature displays a table of each TA with the courses they have been 
            assigned to this term and the courses they have been assigned to in the past.</div>

        <div id="orangecontent2" style="display: none;">
            <!-- <h2>Course to be selected</h2> -->
            <table>
            <?php
                $res = $db->query('SELECT course_type,course_num FROM CourseAndTA');
                $db->close;
                echo "<form action=\"table.php\" method=\"post\">";
                echo "<br><select name=\"course\" id=\"course-select\">";
                echo "<option value> -- Please choose a course -- </option>";
                while($row = $res->fetchArray()){
                    $tmp = $row['course_type'] . " " .$row['course_num'];
                    echo "<option value=\"$tmp\">" . $tmp . "</option>";
                }
                echo "</select><br>";
                echo "<tr><td><button type=\"submit\" value=\"Submit\"><span>Submit</span></button></td>";
                echo "</form>";
            ?>
            <td><button onclick="orangeDisplayEverything('orangecontent2')"><span>Back</span></button></td>
            </tr></table>
        </div>
    </div>
    
    <!--add TA to a course / remove TA from a course-->
    <div id="add_ta">
        <a id="orangemenu3" onclick="showOrangeContent('add_ta', 'orangecontent3')" class="none">Add TA to a course</a>  
        <div class="hint" id="hint6">The add TA to a course feature.</div>

        <div id="orangecontent3" style="display: none;">
            <!-- <h2>add TA to a course</h2> -->
            <table>
            <form action="addTAtoCourse.php" method="post">
                TA Student ID : <input type="text" name="addta" placeholder="e.g., 2608xxxxx">
                Term : <input type="text" name="addterm" placeholder="e.g., Winter 2022">
                Course : <input type="text" name="addcourse" placeholder="e.g., COMP 307">
                <tr><td><button type="submit" value="Add"><span>Add</span></button></td>
            </form>
            <td><button onclick="orangeDisplayEverything('orangecontent3')"><span>Back</span></button></td>
            </tr></table>
        </div>
    </div>

    <div id="remove_ta">
        <a id="orangemenu4" onclick="showOrangeContent('remove_ta', 'orangecontent4')" class="none">Remove TA from a course</a>  
        <div class="hint" id="hint7">The remove TA to a course feature.</div>

        <div id="orangecontent4" style="display: none;">
            <table>
            <?php
                echo "<form action=\"removeTAfromCourse.php\" method=\"post\">";
                $res = $db->query('SELECT * FROM CourseAndTA');
                echo "<select name=\"remove\" id=\"course-select\">";
                echo "<option value> -- Please choose a term -- </option>";
                while($row = $res->fetchArray()){
                    $val = $row['term_month_year'] . "-" . $row['course_type'] . "-" . $row['course_num'] . "-" . $row['TA_name'] . "-" . $row['student_ID'];
                    $value = $row['student_ID'] . " " . $row['TAID'];
                    echo "<option value=\"$value\">" . $val . "</option>";
                }
                echo "</select><br>";
                echo "<tr><td><button type=\"submit\" value=\"Remove\"><span>Remove</span></button></td>";
                echo "</form>";
            ?>           
            <td><button onclick="orangeDisplayEverything('orangecontent4')"><span>Back</span></button></td>
            <tr><table>
 
        </div>
    </div>
</div>
</div>


<script type="text/javascript" language="javascript">

    function hideEverything(){
        document.getElementById("orangecontent0_sub0").style.display="none";
        document.getElementById("orangecontent0_sub1").style.display="none";
        document.getElementById("orangecontent0_sub2").style.display="none";

        document.getElementById("ta_cohort").style.display="none";
        document.getElementById("ta_info").style.display="none";
        document.getElementById("course_ta_history").style.display="none";
        document.getElementById("add_ta").style.display="none";
        document.getElementById("remove_ta").style.display="none";
    }
    
    function displaySubmenu(theContent){
        // document.getElementById("orangecontent0_sub0").style.display="none";
        // document.getElementById("orangecontent0_sub1").style.display="none";
        // document.getElementById("orangecontent0_sub2").style.display="none";
        
        hideEverything();
        document.getElementById("ta_cohort").style.display="block";

        document.getElementById("cohort_menu").style.display="none";
        document.getElementById(theContent).style.display="block";
    }

    function showOrangeContent(menuElement, theContent) {
        // document.getElementById("ta_cohort").style.display="none";
        // document.getElementById("ta_info").style.display="none";
        // document.getElementById("course_ta_history").style.display="none";
        // document.getElementById("add_ta").style.display="none";
        // document.getElementById("remove_ta").style.display="none";

        hideEverything();

        document.getElementById("hint3").style.display="none";
        document.getElementById("hint4").style.display="none";
        document.getElementById("hint5").style.display="none";
        document.getElementById("hint6").style.display="none";
        document.getElementById("hint7").style.display="none";
        
        document.getElementById(theContent).style.display="block";
        document.getElementById(menuElement).style.display="block";
    }

    function orangeDisplayEverything(content){
        // hide current content
        document.getElementById(content).style.display="none";
        document.getElementById("cohort_menu").style.display="none";
        hideEverything();

        // display everything
        document.getElementById("ta_cohort").style.display="block";
        document.getElementById("ta_info").style.display="block";
        document.getElementById("course_ta_history").style.display="block";
        document.getElementById("add_ta").style.display="block";l
        document.getElementById("remove_ta").style.display="block";

        document.getElementById("hint3").style.display="block";
        document.getElementById("hint4").style.display="block";
        document.getElementById("hint5").style.display="block";
        document.getElementById("hint6").style.display="block";
        document.getElementById("hint7").style.display="block";
    }


    function yellowDisplayEverything(content){
        // hide current content
        document.getElementById(content).style.display="none";

        // display everything
        document.getElementById("manage_users").style.display="block";
        document.getElementById("add_users").style.display="block";
        document.getElementById("import_users").style.display="block";
        document.getElementById("hint0").style.display="block";
        document.getElementById("hint1").style.display="block";
        document.getElementById("hint2").style.display="block";
    }

    function yellowDisplay(menuElement, theContent) {
        // Hide everything
        document.getElementById("manage_users").style.display="none";
        document.getElementById("add_users").style.display="none";
        document.getElementById("import_users").style.display="none";
        
        // Hide hints
        document.getElementById("hint0").style.display="none";
        document.getElementById("hint1").style.display="none";
        document.getElementById("hint2").style.display="none";
        
        // Show the one that was selected
        document.getElementById(theContent).style.display="block";
        document.getElementById(menuElement).style.display="block";
    }

    function showContent(menuElement, theContent) {
        // Hide everything
        document.getElementById("green").style.display="none";
        document.getElementById("yellow").style.display="none";
        document.getElementById("blue").style.display="none";
        document.getElementById("orange").style.display="none";
        
        // Remove "active" class
        document.getElementById("menu0").className="none";
        document.getElementById("menu1").className="none";
        document.getElementById("menu2").className="none";
        document.getElementById("menu3").className="none";
        
        // Show the one that was selected
        document.getElementById(theContent).style.display="block";
        document.getElementById(menuElement).className="active";
    }

    function myFunction() {
        var x = document.getElementById("myTopnav");
        if (x.className === "topnav") {
            x.className += " responsive";
        } else {
            x.className = "topnav";
        }
    }
</script>
</body>
</html>

