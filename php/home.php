<!DOCTYPE html>
<?php session_start();
if(!isset($_SESSION["pseudo"])){
    header("location: ../index.php");
}?>
<html>
<head>
    <title>Film & Séries populaire</title>
    <link rel="stylesheet" type="text/css" href="../ressources/css/home.css">
</head>
<body>
<header>
    <h1>Film & Séries populaire</h1>
    <form>
        <input type="text" id="searchQuery" placeholder="Rechercher un film ou une série...">
        <button type="submit" id="searchButton">Rechercher</button>
    </form>
</header>
<div id="genresF">
    <button id="toggleButton">Afficher ou non que les films notés par la communauté</button>
    <button id="toggleSort" class="invisible" onclick="toggleSort()">Tri par note</button>
    <h2>Genres film</h2>
    <button onclick="filterMovies(0)">All</button>
    <?php
    include 'ApiDriver.php';
    $genres = getGenresMovies();
    foreach ($genres as $genre) {
        echo '<button onclick="filterMovies(' . $genre['id'] . ')">' . $genre['name'] . '</button>';
    }
    ?>
</div>
<div id="genresT">
    <h2>Genres série</h2>
    <button onclick="filterMovies(0)">All</button>
    <?php
    $genres = getGenresTvs();
    foreach ($genres as $genre) {
        echo '<button onclick="filterSeries(' . $genre['id'] . ')">' . $genre['name'] . '</button>';
    }
    ?>
</div>
<h1>Les films populaires</h1>
<div id="movies" class="carousel">
</div>
<h1>Les séries populaires</h1>
<div id="Tvs" class="carousel">
</div>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>

    function displayMovies(movies) {
        $('#movies').empty();
        type='movie';
        $.each(movies, function(index, movie) {
            if(movie.poster_path!=null) {
                $('#movies').append('<img src="https://image.tmdb.org/t/p/w185' + movie.poster_path + '" onclick="envoyerRequete(' + movie.id + ', \'movie\')">');
            }
        });
        if(movies===null){
            $('#movies').append("<span> Il n'y a pas de film pour cette recherche </span>");
        }
    }

    function displayTv(TVs) {
        $('#Tvs').empty();
        type='tv';
        $.each(TVs, function(index, Tv) {
            if(Tv.poster_path!=null) {
                $('#Tvs').append('<img src="https://image.tmdb.org/t/p/w185' + Tv.poster_path + '" onclick="envoyerRequete(' + Tv.id + ', \'tv\')">');
            }
        });
        if(TVs===null){
            $('#Tvs').append("<span> Il n'y a pas de film pour cette recherche </span>");
        }
    }

    function filterMovies(genreId) {
        console.log(genreId);
        $.ajax({
            url: 'ApiDriver.php',
            type: 'POST',
            data: {action: 'getPopularMovies', genreId: genreId},
            success: function(response) {
                let data = JSON.parse(response);
                displayMovies(data);
            }
        });
    }

    function filterSeries(genreId) {
        console.log(genreId);
        $.ajax({
            url: 'ApiDriver.php',
            type: 'POST',
            data: {action: 'getPopularTv', genreId: genreId},
            success: function(response) {
                let data = JSON.parse(response);
                console.log(data);
                displayTv(data);
            }
        });
    }

    function envoyerRequete(id,type) {
        // Créer un objet XMLHttpRequest
        var xhr = new XMLHttpRequest();
        // Définir l'URL de la requête avec l'ID en tant que paramètre
        xhr.open('GET', 'detail.php?id=' + id, true);
        // Envoyer la requête
        xhr.send();
        window.location.href = "detail.php?id="+id+"&type="+type;
        // Réponse de la requête
        xhr.onreadystatechange = function() {
            if (xhr.readyState === 4 && xhr.status === 200) {
                // Traitement de la réponse
                console.log(xhr.responseText);
            }
        };
    }

    function searchMovies() {
        var query = $("#searchQuery").val();
        console.log(query);
        $.ajax({
            url: "ApiDriver.php",
            type: "POST",
            data: { action: "searchMovies", query: query },
            success: function(response) {
                var data = JSON.parse(response);
                console.log(data);
                displayMovies(data);
            }
        });
    }

    function searchTv() {
        var query = $("#searchQuery").val();
        console.log(query);
        $.ajax({
            url: "ApiDriver.php",
            type: "POST",
            data: { action: "searchTv", query: query },
            success: function(response) {
                var data = JSON.parse(response);
                console.log(data);
                displayTv(data);
            }
        });
    }

    function search(){
        searchMovies();
        searchTv();
    }

    $(document).ready(function() {
        filterMovies(0);
        filterSeries(0);
        $("#searchButton").click(function(event) {
            event.preventDefault();
            searchMovies();
        });
    });
    $(document).ready(function() {
        $("#searchButton").click(function(event) {
            event.preventDefault();
            search();
        });
    });

    function getRatedMovies(sort="DESC") {
        $.ajax({
            url: "requeteDataBase.php",
            type: 'POST',
            data: {action: 'getRatedMovies',sort: sort},
            success: function(response) {
                // Code à exécuter en cas de succès
                var data = JSON.parse(response);
                displayMovies(data);
            },
            error: function(xhr, status, error) {
                // Code à exécuter en cas d'erreur
                console.error(error); // Affichage de l'erreur dans la console
            }
        });
        $.ajax({
            url: "requeteDataBase.php",
            type: 'POST',
            data: {action: 'getRatedTvs',sort: sort},
            success: function(response) {
                // Code à exécuter en cas de succès
                var data = JSON.parse(response);
                displayTv(data);
            },
            error: function(xhr, status, error) {
                // Code à exécuter en cas d'erreur
                console.error(error); // Affichage de l'erreur dans la console
            }
        });
    }


    function toggleRatedMovies() {
        var button = $("#toggleButton");
        var button2 = $("#toggleSort");
        if (button.hasClass("on")) {
            button.removeClass("on");
            button.text("montré les films ou séries notés");
            button2.addClass("invisible");
            sortByRating = false;
            ratedMovie=false;
            filterMovies(0);
        } else {
            ratedMovie=true;
            button.addClass("on");
            button.text("Caché les films ou séries notés");
            button2.removeClass("invisible");
            getRatedMovies();
        }
    }


    function toggleSort() {
        sortByRating = !sortByRating;
        if (sortByRating) {
            getRatedMovies("ASC");
        } else {
            getRatedMovies();
        }
    }

    let ratedMovie=false;
    let sortByRating = false;
    $(document).ready(function() {
        filterMovies(0);
        $("#searchButton").click(function(event) {
            event.preventDefault();
            searchMovies();
        });
    });
    $(document).ready(function() {
        $("#searchButton").click(function(event) {
            event.preventDefault();
            searchMovies();
        });
    });
    $(document).ready(function() {
        $("#toggleButton").click(function() {
            toggleRatedMovies();
        });
    });
</script>
</body>
</html>