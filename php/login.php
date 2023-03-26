<?php
require_once("DatabaseDriver.php");
session_start();

if(isset($_POST['username']) && isset($_POST['password'])) {
    // connexion à la base de données
    $db_file = new DatabaseDriver();

    $username = htmlspecialchars($_POST['username']);
    $password = sha1(htmlspecialchars($_POST['password']));

    $db_file->verifConnexion($username,$password);
}
?>