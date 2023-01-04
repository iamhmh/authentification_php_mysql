<?php

ob_start();

if(!isset($_SESSION)){
    session_start();
}

$hostname = "localhost";
$username = "root";
$password = "";
$dbname = "authentification";

$conn = mysqli_connect($hostname, $username, $password, $dbname) or die("La connexion à la base de donnée n'a pas pu être établie");

?>