// Récupération de l'élément de formulaire avec l'id "image-form"
const form = document.getElementById("image-form");

// Ajout d'un écouteur d'événement qui réagira sur l'événement de soumission du formulaire
// et qui exécutera une fonction.
form.addEventListener("submit", function (event) {
    // Empêcher le comportement par défaut lors de l'envoi du formulaire (rechargement de la page)
    event.preventDefault();

    // Récupération de tous les éléments input de type "file"
    const inputCount = inputContainer.querySelectorAll("input[type='file']").length;
    const files = [];

    // Parcourir tous les éléments input pour récupérer les fichiers sélectionnés
    for (let i = 1; i <= inputCount; i++) {
        setTimeout(function () {
        }, 500);
        const fileInput = document.getElementById(`input-${i}`);
        const file = fileInput.files[0];

        if (file && fileInput.value.trim() !== "") {
            // Si un fichier est sélectionné et que l'input n'est pas vide, l'ajouter au tableau
            files.push(file);
        }
    }

    // Si aucun fichier n'est sélectionné, afficher une alerte
    if (files.length === 0) {
        alert("Veuillez choisir au moins une image.");
        return;
    }

    // Création d'un nouvel objet FormData, qui sera envoyé dans la requête AJAX
    const formData = new FormData();

    // Ajout de clé valeurs au FormData

    // Ajouter la clé "photoIndex" avec la valeur inputCount
    formData.append("photoIndex", files.length);

    // Ajout de l'élément textarea avec l'id "image-form" à FormData
    formData.append("description", document.querySelector("#image-form textarea").value);

    // Pour chaque fichier sélectionné, exécuter le script de compression et ajouter les données au formulaire
    for (let i = 0; i < files.length; i++) {
        setTimeout(function () {
        }, 500);
        const file = files[i];

        // Lecture du fichier avec FileReader
        const reader = new FileReader();

        // Fonction exécutée lorsque le fichier est chargé
        reader.onload = function (event) {
            // Création d'un nouvel objet Image à partir des données du fichier chargé
            const img = new Image();

            // Fonction exécutée lorsque l'objet Image est chargé
            img.onload = function () {
                // Création d'un canvas pour réduire la taille de l'image
                const canvas = document.createElement("canvas");

                // Définition d'une largeur et hauteur maximum pour l'image
                let maxWidth = 640;
                let maxHeight = 400;

                // Si la largeur de l'image est supérieure à la largeur maximale définie, réduire la taille de l'image
                let width = img.width;
                let height = img.height;
                if (width > maxWidth) {
                    height *= maxWidth / width;
                    width = maxWidth;
                }

                // Si la hauteur de l'image est supérieure à la hauteur maximale définie, réduire la taille de l'image
                if (height > maxHeight) {
                    width *= maxHeight / height;
                    height = maxHeight;
                }

                // Définition de la largeur et de la hauteur du canvas
                canvas.width = width;
                canvas.height = height;

                // Dessin de l'image sur le canvas en réduisant sa taille
                const ctx = canvas.getContext("2d");
                ctx.drawImage(img, 0, 0, width, height);

                // Récupération des données compressées de l'image au format jpeg
                const compressedImageData = canvas.toDataURL("image/jpeg", 0.9);

                // Ajout des données compressées au FormData
                formData.append("images[]", compressedImageData);

                // Si toutes les images ont été traitées, envoyer le formulaire
                if (i === files.length - 1) {
                    sendFormData(formData);
                    setTimeout(function () {
                        window.location.reload();
                    }, 500);
                }
            };

            // On charge les données de l'objet FileReader dans l'objet Image
            img.src = event.target.result;
        };

        // Lecture du fichier avec FileReader
        reader.readAsDataURL(file);
    }
});

// Fonction qui envoie le FormData en AJAX à l'aide d'une requête POST
function sendFormData(formData) {
    // Création d'une nouvelle requête XMLHttp
    const xhr = new XMLHttpRequest();

    // Préparation de la requête en spécifiant la méthode et l'URL de destination
    xhr.open("POST", "/upload");

    // Définition d'une fonction callback qui sera exécutée lorsque la réponse de la requête sera reçue
    xhr.onload = function () {
        // Si le statut de la réponse est 200 (OK), afficher un message de succès et un message de retour du serveur dans la console
        if (xhr.status === 200) {
            console.log(xhr.responseText);

        }
        // Sinon, afficher un message d'erreur avec la réponse du serveur dans la console
        else {
            console.error(xhr.responseText);
        }
    };

    // Envoi de la requête avec le FormData en tant que corps de la requête
    xhr.send(formData);
}