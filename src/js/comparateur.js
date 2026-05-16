$(document).ready(function () {

    const CSRF_TOKEN = $('body').data('csrf-token') || '';
    if (!CSRF_TOKEN) console.error('CSRF token missing on <body>');

    $(document).on('click', '.btn-compare', function (e) {
        e.preventDefault();
        e.stopPropagation();

        const $btn = $(this);
        const idVariante = $btn.data('id-variante');
        if (!idVariante) return;

        if ($btn.hasClass('is-loading')) return;
        $btn.addClass('is-loading');

        $.ajax({
            url: '/src/php/ajax/comparateur_ajax.php',
            method: 'POST',
            dataType: 'json',
            data: {
                action: 'toggle',
                id_variante: idVariante,
                csrf_token: CSRF_TOKEN
            }
        }).done(function (response) {
            if (!response.ok) {
                if (response.plein) {
                    alert(response.erreur);
                } else {
                    console.error('Erreur comparateur :', response.erreur);
                }
                return;
            }

            const $icon = $btn.find('i');
            if (response.in_compare) {
                $btn.addClass('is-active');
                $icon.removeClass('bi-bar-chart').addClass('bi-bar-chart-fill');
            } else {
                $btn.removeClass('is-active');
                $icon.removeClass('bi-bar-chart-fill').addClass('bi-bar-chart');
            }

            const $badge = $('#badge-compare');
            if (response.nb > 0) {
                $badge.text(response.nb).removeClass('d-none');
            } else {
                $badge.text('0').addClass('d-none');
            }

            // Sur la page compare, recharger pour mettre à jour les colonnes.
            if ($btn.data('reload-on-remove') && !response.in_compare) {
                window.location.reload();
            }
        }).fail(function () {
            console.error('Erreur réseau lors du toggle comparateur.');
        }).always(function () {
            $btn.removeClass('is-loading');
        });
    });

    // Bouton « Retirer » spécifique à la page compare : retire la variante puis recharge.
    $(document).on('click', '.ss-btn-retirer-compare', function () {
        const $btn = $(this);
        const idVariante = $btn.data('id-variante');
        if (!idVariante) return;

        $btn.prop('disabled', true);

        $.ajax({
            url: '/src/php/ajax/comparateur_ajax.php',
            method: 'POST',
            dataType: 'json',
            data: {
                action: 'toggle',
                id_variante: idVariante,
                csrf_token: CSRF_TOKEN
            }
        }).done(function (response) {
            if (response.ok) {
                window.location.reload();
            } else {
                console.error('Erreur retrait comparateur :', response.erreur);
                $btn.prop('disabled', false);
            }
        }).fail(function () {
            console.error('Erreur réseau lors du retrait.');
            $btn.prop('disabled', false);
        });
    });

    $(document).on('click', '#btn-vider-compare', function () {
        if (!confirm('Vider tout le comparateur ?')) return;

        $.ajax({
            url: '/src/php/ajax/comparateur_ajax.php',
            method: 'POST',
            dataType: 'json',
            data: {
                action: 'vider',
                csrf_token: CSRF_TOKEN
            }
        }).done(function (response) {
            if (response.ok) {
                window.location.reload();
            } else {
                console.error('Erreur vider comparateur :', response.erreur);
            }
        }).fail(function () {
            console.error('Erreur réseau lors du vidage du comparateur.');
        });
    });
});
