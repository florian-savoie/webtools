document.querySelectorAll('.modaldelete').forEach(function (modaldelet) {
    modaldelet.addEventListener('click', function () {
        var idligne = this.dataset.idpublication;
        console.log(idligne);
        // Effectuer la requête Ajax
        $.ajax({
            url: '/delete-publication',
            method: 'POST',
            data: {
                action: 'delete-publication',
                idligne: idligne,
            },
            dataType: 'json', // Attendre une réponse JSON du serveur
            success: function (response) {
                // Vérifier la réponse du serveur
                if (response.hasOwnProperty('error')) {
                    // Erreur lors de l'ajout du vote
                    console.log('Erreur lors de l\'ajout du vote :', response.error);
                } else {
                    // Le vote a été ajouté avec succès
                    const blockcaroussel = document.getElementById("blockCaroussel"+idligne);
                    blockcaroussel.hidden = true ;

                    console.log(response);
                }
            },
            error: function (xhr, status, error) {
                // Gérer les erreurs lors de la requête Ajax
                console.error('Une erreur s\'est produite lors de la requête :', error);
            }
        });
    });
});