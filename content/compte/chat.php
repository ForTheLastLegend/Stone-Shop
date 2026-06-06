<?php

declare(strict_types=1);

require_once __DIR__ . '/../../src/php/utils/check_connexion.php';

$_idClient  = (int) $_SESSION['client']['id_client'];
$_convDAO   = new ConversationDAO($cnx);
$_msgDAO2   = new MessageChatDAO($cnx);

$_erreurs = [];
$_succes  = '';

// Un seul appel couvre les deux branches POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    Csrf::verifier();
}

// Nouvelle conversation
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['nouvelle_conversation'])) {
    $_sujet = trim($_POST['sujet'] ?? '');
    if ($_sujet === '') {
        $_erreurs[] = 'Le sujet est requis.';
    } else {
        $_ret = $_convDAO->ajouterConversation($_idClient, $_sujet);
        if ($_ret > 0) {
            $_succes = 'Conversation ouverte.';
        }
    }
}

// Envoi message
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['envoyer_message'])) {
    $_idConv = (int) ($_POST['id_conversation'] ?? 0);
    $_contenu = trim($_POST['contenu'] ?? '');
    if ($_contenu !== '' && $_idConv > 0) {
        $_msgDAO2->envoyerMessage($_idConv, 'client', $_contenu);
    }
}

// Liste des conversations du client
$_conversations = $_convDAO->getConversationsParClient($_idClient) ?? [];

// Conversation active
$_idConvActive = isset($_GET['conv']) ? (int) $_GET['conv'] : 0;
$_messages     = [];
if ($_idConvActive > 0) {
    $_messages = $_msgDAO2->getMessagesByConversation($_idConvActive) ?? [];
}
?>

<section class="py-5">
    <div class="container-xl">
        <div class="row">
            <nav class="col-12 col-md-3 mb-4">
                <?php require_once __DIR__ . '/../../src/php/utils/menu_compte.php'; ?>
            </nav>

            <div class="col-12 col-md-9">
                <h3 class="fw-bold mb-4">
                    <i class="bi bi-chat-dots me-2"></i>Support — Chat
                </h3>

                <?php if ($_succes): ?>
                    <div class="alert alert-success"><?= htmlspecialchars($_succes) ?></div>
                <?php endif; ?>
                <?php foreach ($_erreurs as $_e): ?>
                    <div class="alert alert-danger"><?= htmlspecialchars($_e) ?></div>
                <?php endforeach; ?>

                <div class="row g-3">

                    <!-- Liste conversations -->
                    <div class="col-12 col-md-4">
                        <div class="card border-0 shadow-sm h-100">
                            <div class="card-header bg-white fw-semibold d-flex justify-content-between align-items-center">
                                Conversations
                                <button class="btn btn-primary btn-sm" data-bs-toggle="modal"
                                        data-bs-target="#modalNouvConv">
                                    <i class="bi bi-plus"></i>
                                </button>
                            </div>
                            <div class="list-group list-group-flush">
                                <?php if (empty($_conversations)): ?>
                                    <span class="list-group-item text-muted small">
                                        Aucune conversation.
                                    </span>
                                <?php else: ?>
                                    <?php foreach ($_conversations as $_conv): ?>
                                        <a href="/index_.php?page=compte/chat&conv=<?= (int) $_conv['id_conversation'] ?>"
                                           class="list-group-item list-group-item-action
                                                  <?= $_idConvActive === (int) $_conv['id_conversation'] ? 'active' : '' ?>">
                                            <div class="fw-semibold small">
                                                <?= htmlspecialchars($_conv['sujet']) ?>
                                            </div>
                                            <small class="<?= $_idConvActive === (int) $_conv['id_conversation'] ? '' : 'text-muted' ?>">
                                                <?= htmlspecialchars($_conv['statut'] ?? 'ouverte') ?>
                                            </small>
                                        </a>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>

                    <!-- Messages -->
                    <div class="col-12 col-md-8">
                        <?php if ($_idConvActive === 0): ?>
                            <div class="card border-0 shadow-sm p-4 text-center text-muted h-100
                                        d-flex align-items-center justify-content-center">
                                <i class="bi bi-chat fs-1 mb-2"></i>
                                <p>Sélectionnez une conversation</p>
                            </div>
                        <?php else: ?>
                            <div class="card border-0 shadow-sm d-flex flex-column ss-chat-pane">
                                <div id="chat-messages"
                                     class="card-body overflow-auto flex-grow-1 p-3 ss-chat-msgs">
                                    <?php if (empty($_messages)): ?>
                                        <p class="text-muted small text-center">
                                            Aucun message.
                                        </p>
                                    <?php else: ?>
                                        <?php foreach ($_messages as $_m): ?>
                                            <?php $_estClient = $_m->expediteur_type === 'client'; ?>
                                            <div class="mb-3 d-flex
                                                        <?= $_estClient ? 'justify-content-end' : 'justify-content-start' ?>">
                                                <div class="rounded p-2 px-3 ss-chat-bubble <?= $_estClient ? 'ss-chat-bubble-mine' : 'ss-chat-bubble-other' ?>">
                                                    <div class="small fw-semibold mb-1">
                                                        <?= $_estClient ? 'Moi' : 'Support' ?>
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
                                               placeholder="Votre message…" required>
                                        <button type="submit" name="envoyer_message"
                                                class="btn btn-primary btn-sm">
                                            <i class="bi bi-send"></i>
                                        </button>
                                    </form>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>

                </div><!-- /.row -->
            </div>
        </div>
    </div>
</section>

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
         data-viewer-type="client"></div>
<?php endif; ?>

<!-- Modal nouvelle conversation -->
<div class="modal fade" id="modalNouvConv" tabindex="-1">
    <div class="modal-dialog">
        <form method="post" class="modal-content">
            <input type="hidden" name="csrf_token"
                   value="<?= $_SESSION['csrf_token'] ?>">
            <div class="modal-header">
                <h5 class="modal-title">Nouvelle conversation</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <label class="form-label fw-semibold">Sujet</label>
                <input type="text" name="sujet" class="form-control" required>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    Annuler
                </button>
                <button type="submit" name="nouvelle_conversation" class="btn btn-primary">
                    Créer
                </button>
            </div>
        </form>
    </div>
</div>
