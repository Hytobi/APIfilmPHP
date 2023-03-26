<?php

class DatabaseDriver
{
    var $db;

    function __construct()
    {
        $this->db = new PDO("sqlite:../table/projet.sqlite3");
    }

    function verifConnexion($pseudo,$mdp){
        $st = $this->db->prepare("SELECT count(*),id FROM utilisateur WHERE pseudo = :pseudo AND mdp = :mdp ;");
        $st->execute(array($pseudo,$mdp));
        while ($row = $st->fetch(PDO::FETCH_ASSOC)){
            if($row["count(*)"]){
                $_SESSION['pseudo'] = $pseudo;
                $_SESSION['id'] = $row["id"];
                header('Location: home.php');
                exit();
            }else
            {
                header('Location: ../index.php?erreur=1'); // utilisateur ou mot de passe incorrect
                exit();
            }
        }
    }
}
