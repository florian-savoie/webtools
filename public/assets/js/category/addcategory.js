$(document).ready(function() {
    // au click sur le bouton d'ajout
    $('#addcategory').click(function() {
        // Récupération de la valeur de l'input category-input
        var namecategory = $('#category-input').val();
        var colortegory = $('#category-color').val();
        // Effectuer la requête Ajax
        $.ajax({
            url: './add-category',
            method: 'POST',
            data: {
                action: 'add-category',
                namecategory: namecategory,
                colortegory: colortegory,
            },
            dataType: 'json', // Attendre une réponse JSON du serveur
            success: function (response) {
                // Vérifier la réponse du serveur
                if (response.hasOwnProperty('error')) {
                    // Erreur lors de l'ajout de la catégorie
                    console.log('Erreur lors de l\'ajout de la catégorie :', response.error);
                } else {
                    // La catégorie a été ajoutée avec succès
                    window.location.href = "./showcategory";
                }
            },
            error: function (xhr, status, error) {
                // Gérer les erreurs lors de la requête Ajax
                console.error('Une erreur s\'est produite lors de la requête :', error);
            }
        });
    });
});
