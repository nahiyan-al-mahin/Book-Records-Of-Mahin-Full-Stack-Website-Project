<?php
    $db_server = "your_host";
    $db_user   = "your_username";
    $db_pass   = "your_password";
    $db_name   = "your_database";
    $conn="";

    try
    {
        $conn= mysqli_connect($db_server,
                            $db_user,
                            $db_pass,
                            $db_name);
        mysqli_set_charset($conn, "utf8mb4");
    }
    
    catch(mysqli_sql_exception)
    {
       // echo"Not connected to the database";
    }

    if($conn)
        {
           // echo"Connected";
        }
?>