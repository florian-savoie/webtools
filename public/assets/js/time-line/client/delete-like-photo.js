
document.querySelectorAll('.delete-vote').forEach(function (buttondelete) {
    buttondelete.addEventListener('click', function () {
        var emoticonCode = this.dataset.emoticon;
        var photoId = this.dataset.photoId;

        // Effectuer la requête Ajax
        $.ajax({
            url: '/likeTimeline',
            method: 'POST',
            data: {
                action: 'delete',
                emoticon: emoticonCode,
                photoId: photoId
            },
            success: function (response) {
                // Vérifier la réponse du serveur
                if (response.hasOwnProperty('error')) {
                    // Erreur lors de l'ajout du vote
                    console.log('Erreur lors de l\'ajout du vote :', response.error);
                } else {
                    // Le vote a été ajouté avec succès
                    console.log('Vote enregistré avec succès');
                    console.log('Réponse du serveur :', response);

                    // cacher le boutton retirer le like
                    var buttonDelete = document.querySelector('.delete-vote[data-photo-id="' + photoId + '"][data-emoticon="' + emoticonCode + '"]');
                    console.log(buttonDelete);
                    buttonDelete.hidden = true;


                    var divlikehidden = document.querySelector('.reaction-emoji');
                    console.log(divlikehidden);
                    divlikehidden.hidden = true;

                    // retirer le like qui avait etait mit
                    var spanlikeemoji = document.getElementById('emoji' + photoId + emoticonCode);
                    spanlikeemoji.hidden = true;

                    // Sélectionner tout les boutons qui permettron a nouveau de liker pour les re afficher
                    var buttons = document.querySelectorAll('.vote-button[data-photo-id="' + photoId + '"]');
                    // Parcourir tous les boutons
                    buttons.forEach(function(buttonss) {
                        // Réafficher chaque bouton
                        var buttons = document.querySelectorAll('.vote-button[data-photo-id="' + photoId + '"]');
                        buttonss.hidden = false

                    });

                }
            },
            error: function (xhr, status, error) {
                // Gérer les erreurs lors de la requête Ajax
                console.error('Une erreur s\'est produite lors de la requête :', error);
            }
        });
    });
});
