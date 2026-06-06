$(document).ready(function () {
    const $root = $('#chat-root');
    const convId = parseInt($root.data('conv-id'), 10) || 0;
    if (convId <= 0) {
        return;
    }

    const $messages = $('#chat-messages');
    const viewerType = $root.data('viewer-type') || 'client';
    const intervalMs = 4000;
    let dernierId = parseInt($root.data('last-id'), 10) || 0;
    let enCours = false;

    $messages.scrollTop($messages.prop('scrollHeight'));

    function ajouterMessage(msg) {
        const estMien = msg.expediteur_type === viewerType;
        const labelMien = viewerType === 'client' ? 'Moi' : 'Support';
        const labelAutre = viewerType === 'client' ? 'Support' : 'Client';
        const label = estMien ? labelMien : labelAutre;
        const align = estMien ? 'justify-content-end' : 'justify-content-start';
        const bubble = estMien ? 'ss-chat-bubble-mine' : 'ss-chat-bubble-other';

        // Échappement HTML via .text() puis .html() pour neutraliser le contenu utilisateur.
        const contenuHtml = $('<div>').text(msg.contenu).html().replace(/\n/g, '<br>');

        const $bloc = $(`
            <div class="mb-3 d-flex ${align}">
                <div class="rounded p-2 px-3 ss-chat-bubble ${bubble}">
                    <div class="small fw-semibold mb-1">${label}</div>
                    ${contenuHtml}
                </div>
            </div>
        `);
        $messages.append($bloc);
    }

    function poll() {
        if (enCours || document.hidden) {
            return;
        }
        enCours = true;

        $.ajax({
            url: '/src/php/ajax/messages_chat.php',
            method: 'GET',
            dataType: 'json',
            data: { id_conv: convId, dernier_id: dernierId }
        }).done(function (response) {
            if (response.ok && response.messages.length > 0) {
                response.messages.forEach(function (msg) {
                    ajouterMessage(msg);
                    if (msg.id_message > dernierId) {
                        dernierId = msg.id_message;
                    }
                });
                $messages.scrollTop($messages.prop('scrollHeight'));
            }
        }).always(function () {
            enCours = false;
        });
    }

    setInterval(poll, intervalMs);
});
