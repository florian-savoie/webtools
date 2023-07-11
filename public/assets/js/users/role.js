function updaterole(id) {
    var idmember = id;
    var role = document.getElementById('selectrole' + id).value;

    console.log(role);
    console.log(idmember);

    $.ajax({
        url: './updaterole',
        method: 'POST',
        data: {
            idmember: idmember,
            role: role,
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