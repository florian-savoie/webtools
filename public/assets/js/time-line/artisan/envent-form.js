let erreur = "";
const inputContainer = document.getElementById("input-container");
const messageAlert = document.getElementById("messageAlert");
const spanNbrPhoto = document.getElementById("spanNbrPhoto");
const maxAddPhoto = document.getElementById("max-add-photo");
const validExtensions = /(\.jpg|\.jpeg|\.png|\.webp|\.bmp)$/i;
spanNbrPhoto.textContent = "0";
messageAlert.style.visibility = "hidden";


function showMessageError(messageAlert, erreur) {
    messageAlert.style.visibility = "visible";
    messageAlert.textContent = erreur;
}

function addInput() {
    messageAlert.style.visibility = "hidden";
    const inputCount = inputContainer.querySelectorAll("input").length;
    let hasEmptyInput = false;
    let inputDouble = false;
    let validPhoto = true;

    // Vérifie si un champ est vide et si une photo est valide
    const inputs = inputContainer.querySelectorAll("input");
    for (let i = 0; i < inputs.length; i++) {
        if (inputs[i].value === "") {
            hasEmptyInput = true;
            break;
        }
        if (!validExtensions.test(inputs[i].value) || inputs[i].size > 5000000) {
            validPhoto = false;
            break;
        }
    }

    spanNbrPhoto.textContent = inputCount;

    // Vérifie si un champ est en double
    for (let a = 0; a < inputs.length; a++) {
        for (let b = 0; b < inputs.length; b++) {
            if (inputs[a] !== inputs[b]) {
                if (inputs[a].value === inputs[b].value) {
                    inputDouble = true;
                    break;
                }
            }
        }
        if (inputs[a].value === "") {
            hasEmptyInput = true;
            break;
        }
    }

    // Si un champ est en double, affiche une alerte et ne crée pas un nouvel élément input
    if (inputDouble) {
        erreur = "Vous avez déjà ajouté cette photo.";
        showMessageError(messageAlert, erreur);
        spanNbrPhoto.textContent = inputCount - 1;

        return;
    } else {
        spanNbrPhoto.textContent = inputCount;

    }

    // Si un champ est vide, affiche une alerte et ne crée pas un nouvel élément input
    if (hasEmptyInput) {
        erreur = "Veuillez remplir tous les champs avant d'ajouter une nouvelle photo."
        showMessageError(messageAlert, erreur);
        spanNbrPhoto.textContent = inputCount - 1;

        return;
    } else {
        spanNbrPhoto.textContent = inputCount;

    }

    // Si une photo est invalide, affiche une alerte et ne crée pas un nouvel élément input
    if (!validPhoto) {
        erreur = "Veuillez sélectionner des fichiers images valides de taille maximale 5 Mo.";
        showMessageError(messageAlert, erreur);
        spanNbrPhoto.textContent = inputCount - 1;

        return;
    } else {
        spanNbrPhoto.textContent = inputCount;

    }
    if (inputs.length < 5) {
        const inputId = `input-${inputCount + 1}`;
        const inputGroup = `
    <div class="input-group mt-2 d-flex align-items-center" id="div-${inputId}">
<label for="${inputId}">${inputCount + 1}.&nbsp;&nbsp;&nbsp;</label>
 <input type="file" class="form-control" id="${inputId}" name="${inputId}" value="" placeholder="test" />
      <span class="delete mdi mdi-close-circle mx-2" style="color: rgb(217,9,9)"></span>
    </div>
  `;
        inputContainer.insertAdjacentHTML("beforeend", inputGroup);
        addDeleteHandler(inputId);
    } else {

    }

    checkMaxAddPhoto();
}

function addDeleteHandler(inputId) {
    const input = document.getElementById(inputId);

    const deleteBtn = document.querySelector(`#div-${inputId} .delete`);
    deleteBtn.addEventListener("click", () => {
        if (input.value !== "") {
            let nbrPhoto = parseInt(spanNbrPhoto.textContent); // Convertir la valeur en entier
            spanNbrPhoto.textContent = nbrPhoto - 1; // Décrémenter la valeur de 1 et mettre à jour le contenu du texte
        }
        const inputDiv = document.getElementById(`div-${inputId}`);
        inputDiv.parentNode.removeChild(inputDiv);


        checkMaxAddPhoto();
    });
}


function checkMaxAddPhoto() {
    const inputCount = inputContainer.querySelectorAll("input").length;
    if (inputCount >= 5) {
        maxAddPhoto.style.visibility = "visible";
    } else {
        maxAddPhoto.style.visibility = "hidden";
    }
}

/*   addDeleteHandler("input-1");*/
inputContainer.addEventListener("input", addInput);

