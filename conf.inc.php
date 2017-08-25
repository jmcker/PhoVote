<?php
    $db_user = ""; // Username
    $db_pass = ""; // Password
    $db_database = ""; // Database Name
    $db_host = ""; // Server Hostname
    $db_connect = mysqli_connect ($db_host, $db_user, $db_pass); // Connects to the database.
    $db_select = mysqli_select_db ($db_connect, $db_database); // Selects the database.
 
    function form($data) { // Prevents SQL Injection
        global $db_connect;
        $data = ereg_replace("[\'\")(;|`,<>]", "", $data);
        $data = mysql_real_escape_string(trim($data), $db_connect);
        return stripslashes($data);
    }
?>