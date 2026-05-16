<?php

declare(strict_types=1);

require_once __DIR__ . '/../../src/php/utils/check_support.php';

$_convDAO = new ConversationDAO($cnx);
$_msgDAO2 = new MessageChatDAO($cnx);
$_succes  = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verifier_csrf();
}

// Envoi réponse support
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['envoyer_reponse'])) {
    $_idConv  = (int) ($_POST['id_conversation'] ?? 0);
    $_contenu = trim($_POST['contenu'] ?? '');
    if ($_idConv > 0 && $_contenu !== '') {
        $_msgDAO2->envoyerMessage($_idConv, 'support', $_contenu);
    }
}

// Fermer conversation
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['fermer_conversation'])) {
    $_idConv = (int) ($_POST['id_conversation'] ?? 0);
    if ($_idConv > 0) {
        $_convDAO->fermerConversation($_idConv);
        $_succes = 'Conversation fermée.';
    }
}

$_conversations = $_convDAO->getConversationsOuvertes() ?? [];
$_idConvActive  = isset($_GET['conv']) ? (int) $_GET['conv'] : 0;
$_messages      = $_idConvActive > 0
    ? ($_msgDAO2->getMessagesByConversation($_idConvActive) ?? [])
    : [];
?>

<div class="container-fluid p-4">
    <h3 class="fw-bold mb-4">
        <i class="bi bi-chat-dots me-2"></i>Support — Conversations
    </h3>

    <?php if ($_succes): ?>
        <div class="alert alert-success"><?= htmlspecialchars($_succes) ?></div>
    <?php endif; ?>

    <div class="row g-3">

        <!-- Liste conversations -->
        <div class="col-12 col-md-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white fw-semibold">
                    Conversations ouvertes
                    <span class="badge bg-primary ms-2"><?= count($_conversations) ?></span>
                </div>
                <div class="list-group list-group-flush ss-list-scroll-500">
                    <?php if (empty($_conversations)): ?>
                        <span class="list-group-item text-muted small">
                            Aucune conversation ouverte.
                        </span>
                    <?php else: ?>
                        <?php foreach ($_conversations as $_conv): ?>
                            <a href="/admin/index_.php?page=support_chat&conv=<?= (int) $_conv['id_conversation'] ?>"
                               class="list-group-item list-group-item-action
                                      <?= $_idConvActive === (int) $_conv['id_conversation'] ? 'active' : '' ?>">
                                <div class="fw-semibold small">
                                    <?= htmlspecialchars($_conv['sujet']) ?>
                                </div>
                                <small class="<?= $_idConvActive === (int) $_conv['id_conversation'] ? '' : 'text-muted' ?>">
                                    Client #<?= (int) $_conv['id_client'] ?>
                                    — <?= htmlspecialchars($_conv['statut'] ?? 'ouverte') ?>
                                </small>
                            </a>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Zone messages -->
        <div class="col-12 col-md-8">
            <?php if ($_idConvActive === 0): ?>
                <div class="card border-0 shadow-sm p-4 text-center text-muted ss-chat-empty">
                    <i class="bi bi-chat fs-1 mb-2"></i>
                    <p>Sélectionnez une conversation</p>
                </div>
            <?php else: ?>
                <div class="card border-0 shadow-sm d-flex flex-column ss-chat-pane">
                    <div class="card-header bg-white d-flex justify-content-between align-items-center">
                        <span class="fw-semibold">Conversation #<?= $_idConvActive ?></span>
                        <form method="post" data-confirm="Fermer cette conversation ?">
                            <input type="hidden" name="csrf_token"
                                   value="<?= $_SESSION['csrf_token'] ?>">
                            <input type="hidden" name="id_conversation"
                                   value="<?= $_idConvActive ?>">
                            <button type="submit" name="fermer_conversation"
                                    class="btn btn-outline-danger btn-sm">
                                <i class="bi bi-x-circle me-1"></i>Fermer
                            </button>
                        </form>
                    </div>
                    <div id="chat-messages"
                         class="card-body overflow-auto flex-grow-1 p-3 ss-chat-msgs">
                        <?php if (empty($_messages)): ?>
                            <p class="text-muted small text-center">Aucun message.</p>
                        <?php else: ?>
                            <?php foreach ($_messages as $_m): ?>
                                <?php $_estSupport = $_m->expediteur_type === 'support'; ?>
                                <div class="mb-3 d-flex
                                            <?= $_estSupport ? 'justify-content-end' : 'justify-content-start' ?>">
                                    <div class="rounded p-2 px-3 ss-chat-bubble <?= $_estSupport ? 'ss-chat-bubble-mine' : 'ss-chat-bubble-other' ?>">
                                        <div class="small fw-semibold mb-1">
                                            <?= $_estSupport ? 'Support' : 'Client' ?>
                                        </div>
                                        <?= nl2br(htmlspecialchars($_m->contenu)) ?>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                    <div class="card-footer bg-white">
                        <form method="post" class="d-flex gap-2">
                            <input type="hidden" name="csrf_token"
                                   value="<?= $_SESSION['csrf_token'] ?>">
                            <input type="hidden" name="id_conversation"
                                   value="<?= $_idConvActive ?>">
                            <input type="text" name="contenu"
                                   class="form-control form-control-sm"
                                   placeholder="Votre réponse…" required>
                            <button type="submit" name="envoyer_reponse"
                                    class="btn btn-primary btn-sm">
                                <i class="bi bi-send"></i>
                            </button>
                        </form>
                    </div>
                </div>
            <?php endif; ?>
        </div>

    </div>
</div>

<?php if ($_idConvActive > 0): ?>
    <?php
        $_lastId = 0;
        foreach ($_messages as $_m) {
            if ($_m->id_message > $_lastId) $_lastId = $_m->id_message;
        }
    ?>
    <div id="chat-root" hidden
         data-conv-id="<?= (int) $_idConvActive ?>"
         data-last-id="<?= (int) $_lastId ?>"
         data-viewer-type="support"></div>
<?php endif; ?>
