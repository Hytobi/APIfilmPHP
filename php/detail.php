<?php
session_start();
if(!isset($_SESSION["pseudo"])){
    header("location: ../index.php");
}
require_once('ApiDriver.php');
if(isset($_GET['id'])&&isset($_GET['type'])){
    $movie = getDetail($_GET['id'],$_GET['type']);
    ?>
    <head>
        <title>Film & Séries populaire</title>
        <link rel="stylesheet" type="text/css" href="../ressources/css/detail.css">
    </head>
    <body>

    <div class="container">
        <div class="row">
            <div class="col-md-6 col-lg-4">
                <div class="movie">
                    <div class="img-container">
                        <img class="locandina" src="https://image.tmdb.org/t/p/w500<?=$movie->poster_path?>"/>
                    </div>
                </div>
            </div>
            <div class="col-md-6 col-lg-8">
                <h2><?=$movie->title?></h2>
                <p><?=$movie->overview?></p>
                <p><strong>Release date:</strong> <?=$movie->release_date?></p>
                <div class="rating">
                    <div class="pops">
                        <span class="sceau_pop" data-value="1"><img src=""></span>
                        <span class="sceau_pop" data-value="2"><img src=""></span>
                        <span class="sceau_pop" data-value="3"><img src=""></span>
                        <span class="sceau_pop" data-value="4"><img src=""></span>
                        <span class="sceau_pop" data-value="5"><img src=""></span>
                    </div>
                </div>
            </div>
            <div class="col-md-6 col-lg-4">
                <div class="movie-rating" id="note">
                    <?php
                    require_once "requeteDataBase.php";
                    $averageRating = 0;
                    $averageRating = getAverageRating($_GET['id'],$_GET['type']);
                    echo "Average Rating: " . $averageRating . " / 5";
                    ?>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        $(document).on('click', '.sceau_pop', function() {
            let id_A = "<?=$_GET['id']?>";
            console.log(id_A);
            let type = "<?=$_GET['type']?>";
            let note = $(this).data('value');
            let titre;
            if(type=="movie"){
                titre="<?=$movie->title?>";
            }else{
                titre="<?=$movie->name?>";
            }
            console.log('<?=json_encode($movie->genres)?>');
            $.ajax({
                url: 'requeteDataBase.php',
                type: 'POST',
                data: { action:"addRate",id_A: id_A, note: note, type: type,image: "<?=$movie->poster_path?>",title:titre ,genres:'<?=json_encode($movie->genres)?>'},
                success: function(response) {
                    // Mettre Ã  jour l'affichage de la note du film
                    $.ajax({
                        url: 'requeteDataBase.php',
                        type: 'POST',
                        data: { action:"getAverageRating",id_A: id_A, type: type },
                        success: function(response) {
                            $('#note').html('Average Rating: ' + response + ' / 5');
                        },
                    });
                },
                error: function (response){
                    console.log(response);
                }
            });
        });

    </script>
    </body>

    <?php
}else{
    header("location: home.php");
}
