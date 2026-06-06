// Délégués jQuery pour remplacer les handlers inline (onclick/onchange).
// Permet de respecter la règle « aucun JS hors d'un fichier .js » (checklist TI2).

$(document).ready(function () {

    // Confirmation avant soumission d'un formulaire (typiquement les suppressions).
    // Usage : <form data-confirm="Supprimer cette entrée ?">...</form>
    $(document).on('submit', 'form[data-confirm]', function (e) {
        const message = $(this).data('confirm');
        if (message && !window.confirm(message)) {
            e.preventDefault();
        }
    });

    // Soumission automatique du formulaire au changement d'un select/input.
    // Usage : ajouter la classe .js-submit-on-change sur le champ.
    $(document).on('change', '.js-submit-on-change', function () {
        if (this.form) {
            this.form.submit();
        }
    });

});
