document.querySelectorAll('.modalCenterdellsouscat').forEach(function (modaldelet) {
    modaldelet.addEventListener('click', function () {
        var name = this.dataset.name;
        var idcategory = this.dataset.idcategory;
        console.log(name);
        console.log(idcategory);
        console.log("flo");

        // Effectuer la requête Ajax


$.ajax({
    url: './delete-souscategory',
    method: 'POST',
    data: {
        action: 'delete-souscategory',
        name: name,
        idcategory: idcategory
    },
    dataType: 'json',
    success: function(response) {
        if (response.hasOwnProperty('error')) {
            console.log('Erreur lors de la modification :', response.error);
        } else {
            console.log(response);
            window.location.href = "./showcategory";
        }
    },
    error: function(xhr, status, error) {
        console.error('Une erreur s\'est produite lors de la requête :', error);
    }
});
});
});