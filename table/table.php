<?php
try {
    $file_db=new PDO('sqlite:projet.sqlite3');
    $file_db->exec("CREATE TABLE IF NOT EXISTS utilisateur(
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            nom TEXT,
            prenom TEXT,
            pseudo TEXT,
            mdp TEXT)");

    $file_db->exec("CREATE TABLE IF NOT EXISTS movies(
            id INTEGER PRIMARY KEY,
            titre TEXT,
            image TEXT)");

    $file_db->exec("CREATE TABLE IF NOT EXISTS tvs(
            id INTEGER PRIMARY KEY,
            titre TEXT,
            image TEXT)");

    $file_db->exec("CREATE TABLE IF NOT EXISTS genreF(
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            nom TEXT)");

    $file_db->exec("CREATE TABLE IF NOT EXISTS genreM(
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            nom TEXT)");

    $file_db->exec("CREATE TABLE IF NOT EXISTS movieGenre(
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            id_film TEXT,
            id_genre TEXT)");

    $file_db->exec("CREATE TABLE IF NOT EXISTS tvsGenre(
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            id_tv TEXT,
            id_genre TEXT)");


    $file_db->exec("CREATE TABLE IF NOT EXISTS ratings(
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            id_utilisateur TEXT,
            id_film TEXT,
            notes INT)");

    function fillGenresTable() {
        $api_key = "1c88e69ed0e21060bf3c2ad07566a3e7";
        $url = "https://api.themoviedb.org/3/genre/movie/list?api_key=" . $api_key;
        $json = file_get_contents($url);
        $data = json_decode($json, true);

        $db = new SQLite3('projet.sqlite3');

        $stmt = $db->prepare('INSERT INTO genreM (id, nom) VALUES (:id, :name)');
        $stmt->bindParam(':id', $id);
        $stmt->bindParam(':name', $name);

        foreach ($data['genres'] as $genre) {
            $id = $genre['id'];
            $name = $genre['name'];
            $stmt->execute();
        }

        $db->close();
    }
    function fillGenresTableTv() {
        $api_key = "1c88e69ed0e21060bf3c2ad07566a3e7";
        $url = "https://api.themoviedb.org/3/genre/tv/list?api_key=" . $api_key;
        $json = file_get_contents($url);
        $data = json_decode($json, true);

        $db = new SQLite3('projet.sqlite3');

        $stmt = $db->prepare('INSERT INTO genreF (id, nom) VALUES (:id, :name)');
        $stmt->bindParam(':id', $id);
        $stmt->bindParam(':name', $name);

        foreach ($data['genres'] as $genre) {
            $id = $genre['id'];
            $name = $genre['name'];
            $stmt->execute();
        }

        $db->close();
    }

    $file_db=null;
}catch (PDOException $e){
    echo $e->getMessage();
}
?>