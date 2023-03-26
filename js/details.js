function typeDetail(id,type) {
    // Créer un objet XMLHttpRequest
    var xhr = new XMLHttpRequest();

    // Définir l'URL de la requête avec l'ID en tant que paramètre
    xhr.open('GET', 'detail.php?id=' + id + '&type=' + type, true);

    // Envoyer la requête
    xhr.send();

    // Réponse de la requête
    xhr.onreadystatechange = function() {
        if (xhr.readyState === 4 && xhr.status === 200) {
            // Traitement de la réponse
            console.log(xhr.responseText);
            window.location.href="detail.php?id="+id+"&type="+type;
        }
    };
}

