$(document).ready(function () {

    const CSRF_TOKEN = $('body').data('csrf-token') || '';
    if (!CSRF_TOKEN) console.error('CSRF token missing on <body>');

    // element = $input ou $btn, sert à retrouver le <tr> après la réponse.
    function updatePanier(action, idPanier, idVariante, qte, element) {
        $.ajax({
            url: '/src/php/ajax/panier_ajax.php',
            method: 'POST',
            dataType: 'json',
            data: {
                action: action,
                id_panier: idPanier,
                id_variante: idVariante,
                qte: qte,
                csrf_token: CSRF_TOKEN
            },
            success: function (response) {
                if (!response.ok) {
                    alert('Erreur : ' + (response.erreur || 'Mise à jour impossible.'));
                    return;
                }

                const $badge = $('#badge-panier');
                if ($badge.length) {
                    $badge.text(response.nb).removeClass('d-none');
                    if (response.nb === 0) {
                        $badge.addClass('d-none');
                    }
                }

                const $totalPanier = $('.total-panier, .sous-total-global');
                if ($totalPanier.length) {
                    $totalPanier.text(response.total.toFixed(2).replace('.', ',') + ' €');
                }

                if (action === 'remove' || qte <= 0) {
                    // Reload si panier vide pour afficher le message "panier vide".
                    element.closest('tr, .panier-item').fadeOut(300, function () {
                        $(this).remove();
                        if (response.nb === 0) {
                            location.reload();
                        }
                    });
                } else if (action === 'update') {
                    const $ligne = element.closest('tr, .panier-item');
                    const prixUnitaire = parseFloat($ligne.find('.prix-unitaire').data('prix') || 0);
                    if (prixUnitaire > 0) {
                        const sousTotal = prixUnitaire * qte;
                        $ligne.find('.sous-total-ligne').text(sousTotal.toFixed(2).replace('.', ',') + ' €');
                    }
                }
            },
            error: function () {
                alert('Erreur de communication avec le serveur.');
            }
        });
    }

    // Délégation : la ligne peut être retirée du DOM par updatePanier().
    $(document).on('change', '.panier-qte', function () {
        const $input = $(this);
        const qte = parseInt($input.val(), 10) || 0;
        updatePanier('update', $input.data('id-panier'), $input.data('id-variante'), qte, $input);
    });

    $(document).on('click', '.btn-remove-panier', function (e) {
        e.preventDefault();
        const $btn = $(this);
        if (confirm('Retirer cet article du panier ?')) {
            updatePanier('remove', $btn.data('id-panier'), $btn.data('id-variante'), 0, $btn);
        }
    });
});
