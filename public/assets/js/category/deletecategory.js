document.querySelectorAll('.modaldeletecategory').forEach(function (modaldelet) {
    modaldelet.addEventListener('click', function () {
        var idligne = this.dataset.idcategory;
        console.log(idligne);
        // Effectuer la requête Ajax
        $.ajax({
            url: '/delete-category',
            method: 'POST',
            data: {
                action: 'delete-category',
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

                    console.log(response);
                                                                window.location.href = "/showcategory";

                }
            },
            error: function (xhr, status, error) {
                // Gérer les erreurs lors de la requête Ajax
                console.error('Une erreur s\'est produite lors de la requête :', error);
            }
        });
    });
});
