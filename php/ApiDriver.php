<?php
function getPopularMovies($genreId) {
    if($genreId==0){
        $url = "https://api.themoviedb.org/3/discover/movie?api_key=bfb176d3db18936a2b6fea4a44281f27&sort_by=popularity.desc";
    }else{
        $url = "https://api.themoviedb.org/3/discover/movie?api_key=bfb176d3db18936a2b6fea4a44281f27&sort_by=popularity.desc&with_genres=$genreId";
    }
    $response = file_get_contents($url);
    $data = json_decode($response, true);
    echo json_encode($data['results']);
}

function getDetail($id,$type){
    $url = "https://api.themoviedb.org/3/".$type."/".$id;
    $params = array(
        "api_key" => "bfb176d3db18936a2b6fea4a44281f27",
        "language" => "fr-FR"
    );
    $url .= "?" . http_build_query($params);
    $response = file_get_contents($url);
    if ($response === false) {
        echo "Erreur : impossible d'accéder à l'API";
        return null;
    } else {
        $ret = json_decode($response);
        return $ret;
    }
}

function searchTv($query) {
    $url = "https://api.themoviedb.org/3/search/tv?api_key=bfb176d3db18936a2b6fea4a44281f27&query=".urlencode($query);
    $response = file_get_contents($url);
    $data = json_decode($response, true);
    echo json_encode($data['results']);
}

function getPopularTv($genreId) {
    if($genreId==0){
        $url = "https://api.themoviedb.org/3/discover/tv?api_key=bfb176d3db18936a2b6fea4a44281f27&sort_by=popularity.desc";
    }else{
        $url = "https://api.themoviedb.org/3/discover/tv?api_key=bfb176d3db18936a2b6fea4a44281f27&sort_by=popularity.desc&with_genres=$genreId";
    }
    $response = file_get_contents($url);
    $data = json_decode($response, true);
    echo json_encode($data['results']);
}
function searchMovies($query) {
    $url = "https://api.themoviedb.org/3/search/movie?api_key=bfb176d3db18936a2b6fea4a44281f27&query=".urlencode($query);
    $response = file_get_contents($url);
    $data = json_decode($response, true);
    echo json_encode($data['results']);
}


function getGenresMovies() {
    $url = "https://api.themoviedb.org/3/genre/movie/list?api_key=bfb176d3db18936a2b6fea4a44281f27";
    $response = file_get_contents($url);
    $data = json_decode($response, true);
    return $data['genres'];
}

function getGenresTvs() {
    $url = "https://api.themoviedb.org/3/genre/tv/list?api_key=bfb176d3db18936a2b6fea4a44281f27";
    $response = file_get_contents($url);
    $data = json_decode($response, true);
    return $data['genres'];
}

if($_POST["action"]=="getPopularMovies"){
    getPopularMovies($_POST["genreId"]);
}else if($_POST["action"]=="searchMovies"){
    searchMovies($_POST["query"]);
}else if($_POST["action"]=="getPopularTv"){
    getPopularTv($_POST["genreId"]);
}
else if($_POST["action"]=="searchTv"){
    searchTv($_POST["query"]);
}
?>