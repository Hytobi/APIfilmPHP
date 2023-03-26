<?php
session_start();
// Vérifier si l'utilisateur est connecté
if(!isset($_SESSION["pseudo"])){
    header("Location: login.php");
    exit();
}

function addRate(){
    // Récupérer les données de la requête AJAX
    $id_A = $_POST['id_A'];
    $type = $_POST['type'];
    $note = $_POST['note'];

// Ouvrir la base de données
    $file_db=new SQLite3('../table/projet.sqlite3');
// Vérifier si l'utilisateur a déjà noté ce film
    $stmt = $file_db->prepare("SELECT id FROM ratings WHERE id_utilisateur = :id_u AND id_A = :id_A AND type=:type");
    $stmt->bindValue(":id_u", $_SESSION["id"], SQLITE3_INTEGER);
    $stmt->bindValue(":id_A", $id_A, SQLITE3_INTEGER);
    $stmt->bindValue(":type", $type, SQLITE3_TEXT);
    $result = $stmt->execute();
    $ratingExists = ($result->fetchArray()!==false);

// Insérer ou mettre à jour la note de l'utilisateur pour le film donné
    if($ratingExists){
        $stmt = $file_db->prepare("UPDATE ratings SET notes = :rating WHERE id_utilisateur = :id_u AND id_A = :id_A AND type=:type");
        $stmt->bindValue(":rating", $note, SQLITE3_INTEGER);
        $stmt->bindValue(":id_u",$_SESSION["id"], SQLITE3_INTEGER);
        $stmt->bindValue(":id_A", $id_A, SQLITE3_INTEGER);
        $stmt->bindValue(":type", $type, SQLITE3_TEXT);
        $stmt->execute();
        echo "Déjà existant";
    } else {
        $stmt = $file_db->prepare("INSERT INTO ratings (id_utilisateur, id_A, notes,type) VALUES (:id_u, :id_A, :rating, :type)");
        $stmt->bindValue(":id_u",$_SESSION["id"], SQLITE3_INTEGER);
        $stmt->bindValue(":id_A", $id_A, SQLITE3_INTEGER);
        $stmt->bindValue(":rating", $note, SQLITE3_INTEGER);
        $stmt->bindValue(":type", $type, SQLITE3_TEXT);
        $stmt->execute();
        echo "Pas existant";
    }

// Fermer la base de données
    $file_db->close();
}

function getAverageRating($id,$type){
    $file_db=new SQLite3('../table/projet.sqlite3');
    $query = "SELECT AVG(notes) FROM ratings WHERE id_A = :id_A AND type=:type";
    $statement = $file_db->prepare($query);
    $statement->bindValue(':id_A', $id, SQLITE3_INTEGER);
    $statement->bindValue(':type', $type, SQLITE3_TEXT);
    $result = $statement->execute();

    $row = $result->fetchArray(SQLITE3_NUM);
    if ($row[0] === null) {
        return 'Pas de note';
    }
    return number_format($row[0], 1);
}

function insertMovie($title, $image, $id, $genreIds) {
    echo "$genreIds";
    $db = new SQLite3('../table/projet.sqlite3');
    $query = "SELECT COUNT(*) as count FROM movies WHERE id = :id";
    $statement = $db->prepare($query);
    $statement->bindValue(':id', $id, SQLITE3_INTEGER);
    $result = $statement->execute();
    $row = $result->fetchArray();
    $count = $row['count'];

    if ($count == 0) {
        $query = "INSERT INTO movies (id, titre, image) VALUES (:id, :title, :image)";
        $statement = $db->prepare($query);
        $statement->bindValue(':id', $id, SQLITE3_INTEGER);
        $statement->bindValue(':title', $title, SQLITE3_TEXT);
        $statement->bindValue(':image', $image, SQLITE3_TEXT);
        $statement->execute();
    }

    // Supprimer les anciennes associations de genres
    $query = "DELETE FROM movieGenre WHERE id_film = :movieId";
    $statement = $db->prepare($query);
    $statement->bindValue(':movieId', $id, SQLITE3_INTEGER);
    $statement->execute();

    $genreIds = json_decode($genreIds, true);

    // Insérer les nouvelles associations de genres
    foreach ($genreIds as $genre) {
        $genreId = $genre['id'];
        $query = "INSERT INTO movieGenre (id_film, id_genre) VALUES (:movieId, :genreId)";
        $statement = $db->prepare($query);
        $statement->bindValue(':movieId', $id, SQLITE3_INTEGER);
        $statement->bindValue(':genreId', $genreId, SQLITE3_INTEGER);
        $statement->execute();
    }
    $db->close();

}

function insertTv($title, $image, $id, $genreIds) {
    $db = new SQLite3('../table/projet.sqlite3');
    $query = "SELECT COUNT(*) as count FROM tvs WHERE id = :id";
    $statement = $db->prepare($query);
    $statement->bindValue(':id', $id, SQLITE3_INTEGER);
    $result = $statement->execute();
    $row = $result->fetchArray();
    $count = $row['count'];

    if ($count == 0) {
        $query = "INSERT INTO tvs (id, titre, image) VALUES (:id, :title, :image)";
        $statement = $db->prepare($query);
        $statement->bindValue(':id', $id, SQLITE3_INTEGER);
        $statement->bindValue(':title', $title, SQLITE3_TEXT);
        $statement->bindValue(':image', $image, SQLITE3_TEXT);
        $statement->execute();
    }

    // Supprimer les anciennes associations de genres
    $query = "DELETE FROM tvsGenre WHERE id_tv = :movieId";
    $statement = $db->prepare($query);
    $statement->bindValue(':movieId', $id, SQLITE3_INTEGER);
    $statement->execute();
    $genreIds = json_decode($genreIds, true);

    // Insérer les nouvelles associations de genres
    foreach ($genreIds as $genre) {
        $genreId = $genre['id'];
        $query = "INSERT INTO tvsGenre (id_tv, id_genre) VALUES (:movieId, :genreId)";
        $statement = $db->prepare($query);
        $statement->bindValue(':movieId', $id, SQLITE3_INTEGER);
        $statement->bindValue(':genreId', $genreId, SQLITE3_INTEGER);
        $statement->execute();
    }
    $db->close();

}

function getRatedMovies($sort="DESC") {
    // Connexion à la base de données
    $db = new SQLite3('../table/projet.sqlite3');
    // Requête pour récupérer les films notés avec leur moyenne de notes
    $query = "SELECT movies.id, movies.titre, movies.image as poster_path, AVG(ratings.notes) as averageRating 
              FROM movies 
              LEFT JOIN ratings ON movies.id = ratings.id_A AND ratings.type='movie';
              WHERE ratings.notes IS NOT NULL 
              GROUP BY movies.id 
              ORDER BY averageRating $sort";
    $result = $db->query($query);
    // Création du tableau de films notés
    $ratedMovies = array();
    while ($row = $result->fetchArray(SQLITE3_ASSOC)) {
        $ratedMovies[] = $row;
    }
    // Fermeture de la connexion à la base de données
    $db->close();
    // Encodage du tableau en JSON et renvoi de la réponse
    echo json_encode($ratedMovies);
}

function getRatedTvs($sort="DESC") {
    // Connexion à la base de données
    $db = new SQLite3('../table/projet.sqlite3');
    // Requête pour récupérer les films notés avec leur moyenne de notes
    $query = "SELECT tvs.id, tvs.titre, tvs.image as poster_path, AVG(ratings.notes) as averageRating 
              FROM tvs 
              LEFT JOIN ratings ON tvs.id = ratings.id_A AND ratings.type='tv';
              WHERE ratings.notes IS NOT NULL 
              GROUP BY tvs.id 
              ORDER BY averageRating $sort";
    $result = $db->query($query);
    // Création du tableau de films notés
    $ratedTvs= array();
    while ($row = $result->fetchArray(SQLITE3_ASSOC)) {
        $ratedTvs[] = $row;
    }
    // Fermeture de la connexion à la base de données
    $db->close();
    // Encodage du tableau en JSON et renvoi de la réponse
    echo json_encode($ratedTvs);
}


if($_POST["action"]=="addRate"){
    if($_POST["type"]=="movie"){
        insertMovie($_POST["title"],$_POST["image"],$_POST["id_A"],$_POST["genres"]);
    }else{
        insertTv($_POST["title"],$_POST["image"],$_POST["id_A"],$_POST["genres"]);
    }
    addRate();
}
if($_POST["action"]=="getAverageRating") {
    echo getAverageRating($_POST['id_A'], $_POST['type']);
}
if($_POST["action"]=="getRatedMovies"){
    getRatedMovies();
}
if($_POST["action"]=="getRatedTvs"){
    getRatedTvs();
}

?>
