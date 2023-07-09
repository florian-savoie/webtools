function addfavorite(id) {
    var addfavorite = document.getElementById('addfavorite'+id);
    var deletefavorite = document.getElementById('deletefavorite'+id);

    addfavorite.hidden = true;
    deletefavorite.hidden = false;
    $.ajax({
        url: './addfavorite',
        method: 'POST',
        data: {
            idarticle:  id,
        },
        dataType: 'json', // Attendre une réponse JSON du serveur
        success: function (response) {
            // Vérifier la réponse du serveur
            if (response.hasOwnProperty('error')) {
                // Erreur lors de l'ajout de la catégorie
                console.log('Erreur lors de l\'ajout de la catégorie :', response.error);
            } else {
                // La catégorie a été ajoutée avec succès
                console.log(response);
            }
        },
        error: function (xhr, status, error) {
            // Gérer les erreurs lors de la requête Ajax
            console.error('Une erreur s\'est produite lors de la requête :', error);
        }
    });
}