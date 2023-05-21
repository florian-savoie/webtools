document.querySelectorAll('.vote-button').forEach(function (button) {
    button.addEventListener('click', function () {
        var emoticonCode = this.dataset.emoticon;
        var photoId = this.dataset.photoId;

        // Effectuer la requête Ajax
        $.ajax({
            url: '/likeTimeline',
            method: 'POST',
            data: {
                action: 'add',
                emoticon: emoticonCode,
                photoId: photoId
            },
            dataType: 'json', // Attendre une réponse JSON du serveur
            success: function (response) {
                // Vérifier la réponse du serveur
                if (response.hasOwnProperty('error')) {
                    // Erreur lors de l'ajout du vote
                    console.log('Erreur lors de l\'ajout du vote :', response.error);
                } else {
                    // Le vote a été ajouté avec succès
                    console.log('Vote enregistré avec succès');
                    console.log('Réponse du serveur :', response);

                    // Mise à jour de l'affichage du nombre de likes (ajoute )
                    var spanemoji = document.getElementById('emoji' + photoId + emoticonCode);
                    spanemoji.hidden = false
console.log(spanemoji);
                    // affichage du bouton pour un futur retrait du like
                    var buttondelete = document.querySelector('.delete-vote[data-photo-id="' + photoId + '"][data-emoticon="' + emoticonCode + '"]');
                    buttondelete.hidden = false

                    var divlikehidden = document.querySelector('.reaction-emoji');
                    console.log(divlikehidden);
                    divlikehidden.hidden = false;

                   // Parcourir tous les boutons de posibiliter de like pour les retirer de l'affichage
                    var buttons = document.querySelectorAll('.vote-button[data-photo-id="' + photoId + '"]');
                     // Parcourir tous les boutons
                    buttons.forEach(function (buttonss) {
                        // cacher  chaque bouton
                        var buttons = document.querySelectorAll('.vote-button[data-photo-id="' + photoId + '"]');
                        buttonss.hidden = true
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