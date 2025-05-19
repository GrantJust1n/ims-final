<?php



$host = 'localhost';  
$username = 'root';   
$password = '';     
$dbname = 'inventory_db';  

function conn() {
    global $host, $username, $password, $dbname; 
    // Create the connection
    $connect = new mysqli($host, $username, $password, $dbname);

    if ($connect->connect_error) {
        die("Connection failed: " . $connect->connect_error);
    }
    return $connect;  
}
?>
