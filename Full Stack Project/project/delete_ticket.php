<?php
    session_start();
    $ticket = $_COOKIE['ticket'];

    class MyDB extends SQLite3{
        function __construct() { $this->open('../../COMP307/TAmanagement.db'); }
    }
    $db = new MyDB();
    if(!$db) { echo $db->lastErrorMsg(); }

    // delete ticket when users logout
    $ret = $db->exec("DELETE FROM tickets WHERE ticket = $ticket;") or die(print_r($db->errorInfo(), true));
    if(!$ret) { echo $db->lastErrorMsg(); }
    echo "<script type='text/javascript'>window.location.href='login_page.php';</script>";
?>