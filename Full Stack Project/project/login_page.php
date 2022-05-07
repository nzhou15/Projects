<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="stylesheet" type="text/css" href="pageChange.css">
<script src="pageChange.js"></script>
</head>
<body>
    <div class="control">
        <div class="item">
            <div class="active">Login</div><div>Register</div>
        </div>
        <div class="content">
            <div style="display: block;">
                <form action="login.php" method="post">
                    <p>Username</p>
                    <input type="text" placeholder="username" name="username">
                    <p>Password</p>
                    <input type="password" placeholder="3-18 character password" name="password">
                    <br>
                    <button type="submit" value="Login"><span>Login</span></button>
                </form>
            </div>
            <div>
                <form action="register.php" method="post">
                    <p>First name</p>
                    <input type="text" placeholder="first name" name="first_name">
                    <p>Last name</p>
                    <input type="text" placeholder="last name" name="last_name">
                    <p>Email</p>
                    <input type="text" placeholder="first.last@mail.mcgill.ca" name="email">
                    <p>Student ID number</p>
                    <input type="text" placeholder="9 digit ID number" name="id">
                    <p>Username</p>
                    <input type="text" placeholder="username" name="username">
                    <p>Password</p>
                    <input type="password" placeholder="3-18 character password" name="password">
                    <p>Courses</p>
                    <p class="hint">Hold down the Ctrl (windows) or Command (Mac) button to select multiple options.</p>
                    <?php
                        class mySqlite extends SQLite3{
                            function __construct(){ $this->open('../../COMP307/TAmanagement.db'); }
                        }
                        $db = new mySqlite();
                        if(!$db){ echo $db->lastErrorMsg(); }  
                        $res = $db->query("SELECT * FROM courses;");
                        
                        // multiple selecion dropdown list containing courses
                        echo "<select name=\"courses[]\" multiple>";
                        echo "<option value=\"\">-- Please choose your courses --</option>";
                        while($row = $res->fetchArray()){
                            $term = $row['term_month_year'];
                            $course_type = $row['course_type'];
                            $course_num = $row['course_num'];
                            $course = $term . "-" . $course_type . " " . $course_num;
                            echo "<option value='$course'>" . $term . " - " . $course_type . " " . $course_num . "</option>";
                        }
                        echo "</select><br><br>";    
                    ?>                      
                    <button type="submit" value="Register"><span>Register</span></button>
                </form>
            </div>
        </div>
    </div>
</body>
</html>