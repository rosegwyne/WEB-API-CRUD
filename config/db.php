<?php 

$host = 'localhost';
$db_name = 'todo_db';
$username = 'root';
$password = '';
$connection = mysqli_connect(hostname: $host , username: $username , password: $password , database: $db_name);

if (!$connection){
    die ("connection failed:" . mysqli_connect_error());

}

?>