$(document).ready(function () {

    const CSRF_TOKEN = $('body').data('csrf-token') || '';
    if (!CSRF_TOKEN) console.error('CSRF token missing on <body>');

    $(document).on('click', '.btn-wishlist', function (e) {
        e.preventDefault();
        e.stopPropagation();

        const $btn = $(this);
        const idVariante = $btn.data('id-variante');
        if (!idVariante) return;

        if ($btn.hasClass('is-loading')) return;
        $btn.addClass('is-loading');

        $.ajax({
            url: '/src/php/ajax/toggle_liste_envie.php',
            method: 'POST',
            dataType: 'json',
            data: {
                id_variante: idVariante,
                csrf_token: CSRF_TOKEN
            }
        }).done(function (response) {
            if (response.need_login) {
                window.location.href = '/index_.php?page=compte/login';
                return;
            }
            if (!response.ok) {
                console.error('Erreur liste envie :', response.erreur);
                return;
            }

            const $icon = $btn.find('i');
            if (response.in_list) {
                $btn.addClass('is-active');
                $icon.removeClass('bi-heart').addClass('bi-heart-fill');
            } else {
                $btn.removeClass('is-active');
                $icon.removeClass('bi-heart-fill').addClass('bi-heart');
            }
        }).fail(function () {
            console.error('Erreur réseau lors du toggle liste d\'envie.');
        }).always(function () {
            $btn.removeClass('is-loading');
        });
    });
});
