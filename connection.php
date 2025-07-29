<?php
$servername = "localhost"; // changed from "db" to "localhost"
$username = "root";  
$password = ""; 
$dbname = "sql_database_edoc"; 

$database = new mysqli($servername, $username, $password, $dbname);

if ($database->connect_error) {
    die("Échec de la connexion : " . $database->connect_error);
}
?>

