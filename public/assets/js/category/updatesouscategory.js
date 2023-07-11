document.addEventListener('DOMContentLoaded', function() {
    var modalUpdatesousCategoryButtons = document.querySelectorAll('.modalupdatesouscategory');
    modalUpdatesousCategoryButtons.forEach(function(button) {
        button.addEventListener('click', function() {
            var categoryId = this.dataset.idcategory;
            var namesouscat = this.dataset.namesouscategory;
            var inputId = "updatesouscategory-input-" + categoryId + namesouscat;
            var name = document.getElementById(inputId).value;
console.log(namesouscat);
console.log(name);
console.log(categoryId);
            // Effectuer la requête Ajax
            $.ajax({
                url: './update-souscategory',
                method: 'POST',
                data: {
                    action: 'update-souscategory',
                    name: name,
                    idcategory: categoryId,
                    ancienname: namesouscat
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
});