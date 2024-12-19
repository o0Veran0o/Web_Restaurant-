<?php
$host = "localhost";
$user = "nabokval";
$password ="webove aplikace";
$database = "nabokval";
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

// connect to mysql database
try{
    $connect = mysqli_connect($host, $user, $password, $database);
} catch (mysqli_sql_exception $ex) {
    echo 'Error';
}
?>
