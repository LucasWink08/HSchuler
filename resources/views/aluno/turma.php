<?php
$turmas = $turmas ?? [];
$mensagem = $mensagem ?? '';
$status = $status ?? '';
$token = (string) ($_SESSION['turma_aluno_token'] ?? '');
?>
<!doctype html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Minhas turmas | HSchuler</title>
    <link rel="stylesheet" href="<?= app_asset('css/turmas.css') ?>?v=3">
</head>
<body class="classroom-page">
    <?php $navbarActive = 'turma'; require APP_ROOT . '/resources/views/layouts/navbar.php'; ?>

    <main class="classroom-shell">
        <section class="classroom-hero">
            <div class="classroom-hero-copy"><p class="classroom-eyebrow">Espaço do aluno</p><h1>Minhas turmas</h1><p>Entre em uma turma com o código enviado pelo seu professor e acompanhe as atividades e os materiais publicados para você.</p></div>
            <div class="classroom-code-box"><small>Tem um código?</small><code>ENTRE AGORA</code></div>
        </section>

        <?php if ($mensagem !== ''): ?><p class="classroom-notice <?= $status === 'ok' ? 'is-ok' : 'is-error' ?>" role="status"><?= htmlspecialchars($mensagem, ENT_QUOTES, 'UTF-8') ?></p><?php endif; ?>

        <section class="classroom-panel" style="margin-top:28px" aria-labelledby="join-title">
            <h2 id="join-title">Entrar em uma turma</h2>
            <p>Digite o código de oito caracteres compartilhado pelo professor.</p>
            <form class="classroom-join" method="post" action="<?= app_route('/aluno/turma/entrar') ?>">
                <input type="hidden" name="token" value="<?= htmlspecialchars($token, ENT_QUOTES, 'UTF-8') ?>">
                <div class="classroom-field"><label for="codigo">Código da turma</label><input class="classroom-code-input" id="codigo" name="codigo" type="text" maxlength="12" required autocomplete="off" placeholder="EX.: A7K2M9PX"></div>
                <button class="classroom-primary" type="submit">Entrar na turma</button>
            </form>
        </section>

        <header class="classroom-section-head"><h2>Turmas em que você participa</h2><p><?= count($turmas) ?> <?= count($turmas) === 1 ? 'turma ativa' : 'turmas ativas' ?></p></header>
        <?php if ($turmas === []): ?>
            <p class="classroom-empty">Você ainda não participa de uma turma. Digite o código enviado pelo professor para começar.</p>
        <?php else: ?>
            <section class="classroom-grid" aria-label="Turmas do aluno">
                <?php foreach ($turmas as $turma): ?>
                    <article class="classroom-card">
                        <div class="classroom-card-mark" aria-hidden="true">⌘</div>
                        <h3><?= htmlspecialchars($turma['nome'], ENT_QUOTES, 'UTF-8') ?></h3>
                        <p><?= htmlspecialchars((string) ($turma['descricao'] ?: 'Turma sem descrição.'), ENT_QUOTES, 'UTF-8') ?></p>
                        <div class="classroom-card-meta"><span class="classroom-chip">Prof. <?= htmlspecialchars($turma['professor_nome'], ENT_QUOTES, 'UTF-8') ?></span><span class="classroom-chip"><?= (int) $turma['total_atividades'] ?> atividades</span></div>
                        <a class="classroom-card-action" href="<?= app_route('/aluno/turma/detalhe') ?>&amp;id=<?= (int) $turma['id'] ?>">Ver turma</a>
                    </article>
                <?php endforeach; ?>
            </section>
        <?php endif; ?>
    </main>
</body>
</html>
