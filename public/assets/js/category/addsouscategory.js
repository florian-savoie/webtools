$('.addsouscategory').click(function() {
    let name = $('#souscategory-input-' + $(this).data('idcategory')).val();
    let idcategory = $(this).data('idcategory');

    $.ajax({
        url: '/add-souscategory',
        type: 'post',
        dataType: 'json',
        data: {
            action: 'add-souscategory',
            name: name,
            idcategory: idcategory
        },
        success: function(response) {
            console.log(response);
                                                                                        window.location.href = "/showcategory";

        },
        error: function(xhr, status, error) {
            console.log(error);
            // Gérer l'erreur
        }
    });

});