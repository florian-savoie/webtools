document.addEventListener('DOMContentLoaded', function() {
    var modalUpdateCategoryButtons = document.querySelectorAll('.modalupdatecategory');

    modalUpdateCategoryButtons.forEach(function(button) {
        button.addEventListener('click', function() {
            var categoryId = this.dataset.idcategory;
            var inputId = "updatecategory-input-" + categoryId;
            var inputcolor = "category-color-" + categoryId;
            var newCategoryName = document.getElementById(inputId).value;
            var newCategorycolor = document.getElementById(inputcolor).value;

            // Effectuer la requête Ajax
            $.ajax({
                url: '/update-category',
                method: 'POST',
                data: {
                    action: 'update-category',
                    newname: newCategoryName,
                    idligne: categoryId,
                    color: newCategorycolor,
                },
                dataType: 'json',
                success: function(response) {
                    if (response.hasOwnProperty('error')) {
                        console.log('Erreur lors de la modification :', response.error);
                    } else {
                        console.log(response);
                                                window.location.href = "/showcategory";

                    }
                },
                error: function(xhr, status, error) {
                    console.error('Une erreur s\'est produite lors de la requête :', error);
                }
            });
        });
    });
});
