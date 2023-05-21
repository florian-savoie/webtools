document.querySelectorAll('.hiddenproject').forEach(function (hidden) {
    hidden.addEventListener('click', function () {
        var idligne = this.dataset.valeur;
        var etat = this.dataset.etat;
console.log(etat);
console.log(idligne);
        // Effectuer la requête Ajax
        $.ajax({
            url: '/hidden',
            method: 'POST',
            data: {
                action: 'hidden',
                idligne: idligne,
                etat: etat
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
                    var butoncheck = document.querySelector('.hiddenproject[data-etat="' + etat + '"][data-valeur="' + idligne + '"]');
                    var textetat = document.getElementById('status'+idligne);
                    if (etat === "false") {
                        butoncheck.setAttribute('data-etat', 'true');
                        console.log(textetat);
                        textetat.style.color = "red"
                        textetat.innerHTML  = '"Actuellement caché"';

                    } else if (etat === "true") {
                        butoncheck.setAttribute('data-etat', 'false');
                        console.log(textetat);
                        textetat.style.color = "green"
                        textetat.innerHTML  = '"Actuellement publié"';

                    }
                }
            },
            error: function (xhr, status, error) {
                // Gérer les erreurs lors de la requête Ajax
                console.error('Une erreur s\'est produite lors de la requête :', error);
            }
        });
    });
});