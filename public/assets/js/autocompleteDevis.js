document.addEventListener('DOMContentLoaded', function() {
    var addressInput = document.getElementById('devis_adresse_intervention');
    var suggestionsContainer = document.createElement('div');
    suggestionsContainer.classList.add('form-control');
    suggestionsContainer.style.display = 'none'; // Masquer la div par défaut
    var typingTimer; // Timer identifier
    var doneTypingInterval = 100; // Temps d'attente en millisecondes (0.1 secondes)

    if (addressInput) {
        addressInput.addEventListener('input', function() {
            clearTimeout(typingTimer); // Efface le timer lorsqu'une nouvelle frappe est détectée
            var input = this.value;
            if (input.length > 2) {
                typingTimer = setTimeout(function() {
                    fetch('/autocomplete?query=' + encodeURIComponent(input))
                        .then(response => response.json())
                        .then(data => {
                            suggestionsContainer.innerHTML = ''; // Effacer les anciennes suggestions
                            if (data.features && data.features.length > 0) {
                                data.features.forEach(feature => {
                                    var suggestionElement = document.createElement('div');
                                    suggestionElement.innerText = feature.properties.label;
                                    suggestionElement.onclick = function() {
                                        addressInput.value = feature.properties.label;
                                        suggestionsContainer.style.display = 'none'; // Masquer la div après sélection
                                    };
                                    suggestionsContainer.appendChild(suggestionElement);
                                });
                                suggestionsContainer.style.display = 'block'; // Afficher la div s'il y a des suggestions
                            } else {
                                suggestionsContainer.style.display = 'none'; // Masquer la div s'il n'y a pas de suggestions
                            }
                        })
                        .catch(error => {
                            console.error('Erreur lors de la récupération des données :', error);
                            suggestionsContainer.style.display = 'none'; // Masquer la div en cas d'erreur
                        });
                }, doneTypingInterval);
            } else {
                suggestionsContainer.style.display = 'none'; // Masquer la div si la longueur de l'entrée est inférieure à 3
            }
        });
    }

    addressInput.parentNode.insertBefore(suggestionsContainer, addressInput.nextSibling);
});