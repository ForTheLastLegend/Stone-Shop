$(document).ready(function () {

    const CSRF_TOKEN = $('body').data('csrf-token') || '';
    if (!CSRF_TOKEN) console.error('CSRF token missing on <body>');

    $('.editable-cell').on('focus', function () {
        $(this).data('old-value', $(this).text().trim());
    });

    $('.editable-cell').on('blur', function () {
        const $cell = $(this);
        const idVariante = $cell.data('id');
        const champ = $cell.data('champ');
        const nouvelleValeur = $cell.text().trim();
        const ancienneValeur = $cell.data('old-value');

        if (nouvelleValeur === ancienneValeur) {
            return;
        }

        // On écrase old-value tout de suite pour éviter un double envoi
        // si le curseur revient sur la cellule avant la réponse.
        $cell.data('old-value', nouvelleValeur);

        $.ajax({
            url: '/src/php/ajax/update_variante.php',
            method: 'POST',
            dataType: 'json',
            data: {
                id: idVariante,
                champ: champ,
                valeur: nouvelleValeur,
                csrf_token: CSRF_TOKEN
            },
            success: function (response) {
                if (response.ok) {
                    $cell.css('background-color', '#d4edda');
                    setTimeout(() => $cell.css('background-color', ''), 1000);

                    const champNom = champ.charAt(0).toUpperCase() + champ.slice(1);
                    const toast = $(`
                        <div class="toast-notification">
                            <div class="toast-title"><i class="bi bi-check-circle"></i> Mise à jour réussie</div>
                            <div>${champNom} sauvegardé avec succès.</div>
                            <div class="toast-progress"></div>
                        </div>
                    `);
                    $('body').append(toast);
                    setTimeout(() => toast.remove(), 5000);

                } else {
                    alert('Erreur lors de la mise à jour : ' + (response.erreur || 'Inconnue.'));
                }
            },
            error: function (xhr) {
                console.error("Erreur AJAX:", xhr.responseText);
                alert('Erreur de communication avec le serveur.');
            }
        });
    });

    // Entrée = sauvegarde (pas de saut de ligne dans le contenteditable).
    $('.editable-cell').on('keydown', function (e) {
        if (e.key === 'Enter') {
            e.preventDefault();
            $(this).blur();
        }
    });
});
