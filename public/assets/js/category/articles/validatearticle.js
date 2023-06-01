function hiddenarticle(id) {
    var checkbox = document.getElementById('checkbox'+id);
    var valeur = checkbox.dataset.etat;

    if (valeur == 0) {
        valeur = 1;
    } else {
        valeur = 0;
    }

    checkbox.dataset.etat = valeur; // Mettre à jour l'attribut data-etat
console.log(valeur);
    // Mettre à jour le texte de l'élément <p> correspondant à l'état de l'article
    var statusElement = document.getElementById('status'+id);
    if (valeur == 1) {
        statusElement.textContent = "Actuellement publié";
        statusElement.style.color = "green";
    } else {
        statusElement.textContent = "Actuellement caché";
        statusElement.style.color = "red";
    }
    $.ajax({
        url: '/validatearticle',
        method: 'POST',
        data: {
            hidden:  valeur,
            idarticle : id,
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