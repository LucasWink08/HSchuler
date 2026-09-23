<?php
$turmas = $turmas ?? [];
$mensagem = $mensagem ?? '';
$status = $status ?? '';
$token = (string) ($_SESSION['turma_professor_token'] ?? '');
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
    <?php $navbarActive = 'turmas'; require APP_ROOT . '/resources/views/layouts/navbar.php'; ?>

    <main class="classroom-shell">
        <section class="classroom-hero">
            <div class="classroom-hero-copy">
                <p class="classroom-eyebrow">Espaço do professor</p>
                <h1>Minhas turmas</h1>
                <p>Crie uma turma, compartilhe o código de entrada com os alunos e publique atividades para cada grupo.</p>
            </div>
            <div class="classroom-code-box"><small>Código de entrada</small><code>GERADO AO CRIAR</code></div>
        </section>

        <?php if ($mensagem !== ''): ?><p class="classroom-notice <?= $status === 'ok' ? 'is-ok' : 'is-error' ?>" role="status"><?= htmlspecialchars($mensagem, ENT_QUOTES, 'UTF-8') ?></p><?php endif; ?>

        <section class="classroom-layout" style="margin-top:28px">
            <div>
                <header class="classroom-section-head" style="margin-top:0"><h2>Suas turmas</h2><p><?= count($turmas) ?> <?= count($turmas) === 1 ? 'turma criada' : 'turmas criadas' ?></p></header>
                <?php if ($turmas === []): ?>
                    <p class="classroom-empty">Ainda não há turmas criadas. Use o formulário ao lado para montar a primeira.</p>
                <?php else: ?>
                    <section class="classroom-grid" aria-label="Turmas do professor">
                        <?php foreach ($turmas as $turma): ?>
                            <article class="classroom-card">
                                <div class="classroom-card-mark" aria-hidden="true">⌘</div>
                                <h3><?= htmlspecialchars($turma['nome'], ENT_QUOTES, 'UTF-8') ?></h3>
                                <p><?= htmlspecialchars((string) ($turma['descricao'] ?: 'Turma sem descrição.'), ENT_QUOTES, 'UTF-8') ?></p>
                                <div class="classroom-card-meta"><span class="classroom-chip">Código <?= htmlspecialchars($turma['codigo'], ENT_QUOTES, 'UTF-8') ?></span><span class="classroom-chip"><?= (int) $turma['total_alunos'] ?> alunos</span><span class="classroom-chip"><?= (int) $turma['total_atividades'] ?> atividades</span></div>
                                <a class="classroom-card-action" href="<?= app_route('/professor/turma') ?>&amp;id=<?= (int) $turma['id'] ?>">Abrir turma</a>
                            </article>
                        <?php endforeach; ?>
                    </section>
                <?php endif; ?>
            </div>

            <aside class="classroom-panel">
                <h2>Criar turma</h2>
                <p>O sistema gera um código exclusivo para seus alunos entrarem na turma.</p>
                <form class="classroom-form" method="post" action="<?= app_route('/professor/turmas/criar') ?>">
                    <input type="hidden" name="token" value="<?= htmlspecialchars($token, ENT_QUOTES, 'UTF-8') ?>">
                    <div class="classroom-field"><label for="nome">Nome da turma</label><input id="nome" name="nome" type="text" maxlength="100" required placeholder="Ex.: 1º ano A — Álgebra"></div>
                    <div class="classroom-field"><label for="descricao">Descrição</label><textarea id="descricao" name="descricao" maxlength="4000" placeholder="Objetivos, recados e informações para a turma."></textarea></div>
                    <button class="classroom-primary" type="submit">Criar turma e gerar código</button>
                </form>
            </aside>
        </section>
    </main>
</body>
</html>
