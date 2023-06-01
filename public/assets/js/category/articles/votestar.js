// Sélectionnez tous les éléments avec la classe `.onset-event-ratings`.
var onSetEvents = $('.onset-event-ratings');

// Vérifiez si des éléments ont été trouvés.
if (onSetEvents.length > 0) {
    // Itérez sur chaque élément avec la classe `.onset-event-ratings`.
    onSetEvents.each(function () {
        // Récupérez la note à partir de l'attribut `data-note`.
        var note = $(this).data('note');

        // Initialisez RateYo avec la propriété `rtl` définie sur `false` et la note récupérée.
        $(this).rateYo({
            rtl: false,
            rating: note
        })
            // Écoutez l'événement `rateyo.set` et effectuez la requête Ajax.
            .on('rateyo.set', function (e, data) {
                console.log(data.rating);
                console.log($(this).data('idarticle'));
                // Effectuer la requête Ajax
                $.ajax({
                    url: '/votestar',
                    method: 'POST',
                    data: {
                        note: data.rating,
                        idarticle: $(this).data('idarticle'),
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
            });
    });
}
