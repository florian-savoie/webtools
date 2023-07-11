function deleteUser(id) {
    var idmember = id;


    $.ajax({
        url: './deleteuser',
        method: 'POST',
        data: {
            idmember: idmember,
        },
        dataType: 'json',
        success: function (response) {
            if (response.hasOwnProperty('error')) {
                console.log('Erreur lors de la mise à jour du rôle :', response.error);
            } else {
                console.log('Rôle mis à jour avec succès :', response);
            }
        },
        error: function (xhr, status, error) {
            console.error('Une erreur s\'est produite lors de la requête :', error);
        }
    });
}