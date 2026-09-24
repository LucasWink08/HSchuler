<?php
$turma = $turma ?? [];
$atividades = $atividades ?? [];
$avisos = $avisos ?? [];
$alunos = $alunos ?? [];
$entregas = $entregas ?? [];
$aba = $aba ?? 'avisos';
$mensagem = $mensagem ?? '';
$status = $status ?? '';
$token = (string) ($_SESSION['atividade_turma_token'] ?? '');
$tokenNota = (string) ($_SESSION['nota_entrega_token'] ?? '');
$tokenAviso = (string) ($_SESSION['aviso_turma_token'] ?? '');
$formatarData = static function ($data): string {
    $timestamp = $data ? strtotime((string) $data) : false;
    return $timestamp === false ? 'Data indisponível' : date('d/m/Y \à\s H:i', $timestamp);
};
$rotaDaTurma = app_route('/professor/turma') . '&id=' . (int) ($turma['id'] ?? 0);
?>
<!doctype html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= htmlspecialchars((string) ($turma['nome'] ?? 'Turma'), ENT_QUOTES, 'UTF-8') ?> | HSchuler</title>
    <link rel="stylesheet" href="<?= app_asset('css/turmas.css') ?>?v=4">
</head>
<body class="classroom-page">
    <?php $navbarActive = 'turmas'; require APP_ROOT . '/resources/views/layouts/navbar.php'; ?>

    <main class="classroom-shell classroom-tab-shell">
        <a class="classroom-back-link" href="<?= app_route('/professor/turmas') ?>">← Minhas turmas</a>
        <section class="classroom-hero classroom-class-hero">
            <div class="classroom-hero-copy"><p class="classroom-eyebrow">Sala do professor</p><h1><?= htmlspecialchars((string) ($turma['nome'] ?? ''), ENT_QUOTES, 'UTF-8') ?></h1><p><?= htmlspecialchars((string) (($turma['descricao'] ?? '') ?: 'Publique avisos, atividades e videoaulas para os alunos da turma.'), ENT_QUOTES, 'UTF-8') ?></p></div>
            <div class="classroom-code-box"><small>Código de entrada</small><code><?= htmlspecialchars((string) ($turma['codigo'] ?? ''), ENT_QUOTES, 'UTF-8') ?></code></div>
        </section>

        <nav class="classroom-tabs" aria-label="Seções da turma">
            <a class="classroom-tab <?= $aba === 'avisos' ? 'is-active' : '' ?>" href="<?= $rotaDaTurma ?>&amp;aba=avisos">Avisos</a>
            <a class="classroom-tab <?= $aba === 'atividades' ? 'is-active' : '' ?>" href="<?= $rotaDaTurma ?>&amp;aba=atividades">Atividades</a>
            <a class="classroom-tab <?= $aba === 'videoaulas' ? 'is-active' : '' ?>" href="<?= $rotaDaTurma ?>&amp;aba=videoaulas">Videoaulas</a>
        </nav>

        <?php if ($mensagem !== ''): ?><p class="classroom-notice <?= $status === 'ok' ? 'is-ok' : 'is-error' ?>" role="status"><?= htmlspecialchars($mensagem, ENT_QUOTES, 'UTF-8') ?></p><?php endif; ?>

        <?php if ($aba === 'avisos'): ?>
            <section class="classroom-content-grid teacher-content-grid">
                <div class="classroom-main-column">
                    <section class="classroom-panel" aria-labelledby="announcement-form-title">
                        <div class="classroom-panel-heading"><div><p class="classroom-eyebrow">Mural da turma</p><h2 id="announcement-form-title">Publicar aviso</h2></div></div>
                        <form class="classroom-form" method="post" action="<?= app_route('/professor/turma/aviso') ?>">
                            <input type="hidden" name="token" value="<?= htmlspecialchars($tokenAviso, ENT_QUOTES, 'UTF-8') ?>"><input type="hidden" name="turma_id" value="<?= (int) $turma['id'] ?>">
                            <div class="classroom-field"><label for="mensagem">Mensagem para os alunos</label><textarea id="mensagem" name="mensagem" maxlength="5000" required placeholder="Ex.: A atividade de hoje terá revisão na próxima aula."></textarea><small>O aviso ficará visível apenas para os alunos desta turma.</small></div><button class="classroom-primary" type="submit">Publicar aviso</button>
                        </form>
                    </section>
                    <section class="classroom-panel" aria-labelledby="announcements-title">
                        <div class="classroom-panel-heading"><div><h2 id="announcements-title">Avisos publicados</h2></div><span class="classroom-count-pill"><?= count($avisos) ?> <?= count($avisos) === 1 ? 'aviso' : 'avisos' ?></span></div>
                        <?php if ($avisos === []): ?><p class="classroom-empty">Ainda não há avisos publicados para esta turma.</p><?php else: ?><div class="classroom-timeline"><?php foreach ($avisos as $aviso): ?><article class="classroom-post"><div class="classroom-post-avatar" aria-hidden="true">P</div><div><header><strong><?= htmlspecialchars((string) $aviso['professor_nome'], ENT_QUOTES, 'UTF-8') ?></strong><small><?= htmlspecialchars($formatarData($aviso['created_at'] ?? null), ENT_QUOTES, 'UTF-8') ?></small></header><p><?= nl2br(htmlspecialchars((string) $aviso['mensagem'], ENT_QUOTES, 'UTF-8')) ?></p></div></article><?php endforeach; ?></div><?php endif; ?>
                    </section>
                </div>
                <aside class="classroom-sidebar"><section class="classroom-panel"><p class="classroom-eyebrow">Participação</p><h2><?= count($alunos) ?> <?= count($alunos) === 1 ? 'aluno' : 'alunos' ?></h2><p>Compartilhe o código <strong><?= htmlspecialchars((string) ($turma['codigo'] ?? ''), ENT_QUOTES, 'UTF-8') ?></strong> para novos alunos entrarem.</p></section></aside>
            </section>
        <?php elseif ($aba === 'atividades'): ?>
            <section class="classroom-content-grid teacher-content-grid">
                <div class="classroom-main-column">
                    <section class="classroom-panel" aria-labelledby="activity-form-title">
                        <div class="classroom-panel-heading"><div><p class="classroom-eyebrow">Conteúdo da turma</p><h2 id="activity-form-title">Publicar atividade</h2></div></div>
                        <form class="classroom-form" method="post" action="<?= app_route('/professor/turma/atividade') ?>" enctype="multipart/form-data">
                            <input type="hidden" name="token" value="<?= htmlspecialchars($token, ENT_QUOTES, 'UTF-8') ?>"><input type="hidden" name="turma_id" value="<?= (int) $turma['id'] ?>">
                            <div class="classroom-field"><label for="titulo">Título</label><input id="titulo" name="titulo" type="text" maxlength="150" required placeholder="Ex.: Lista de exercícios — Potenciação"></div>
                            <div class="classroom-field"><label for="descricao">Descrição e orientações</label><textarea id="descricao" name="descricao" maxlength="5000" placeholder="Explique o que deve ser feito e quais conceitos devem ser revisados."></textarea></div>
                            <div class="classroom-field"><label for="periodo_entrega">Prazo de entrega</label><input id="periodo_entrega" name="periodo_entrega" type="datetime-local"><small>Se preenchido, deve ser uma data futura. Após o horário definido, o sistema bloqueia novas entregas.</small></div>
                            <div class="classroom-field classroom-upload"><label for="anexos">Anexos de apoio</label><input id="anexos" name="anexos[]" type="file" multiple accept=".pdf,.txt,.jpg,.jpeg,.png,.doc,.docx,.xls,.xlsx,.ppt,.pptx"><small>Até 5 arquivos de 10 MB cada: PDF, documentos, planilhas, apresentações ou imagens.</small></div><button class="classroom-primary" type="submit">Publicar atividade</button>
                        </form>
                    </section>
                    <section class="classroom-panel" aria-labelledby="activities-title">
                        <div class="classroom-panel-heading"><div><h2 id="activities-title">Atividades publicadas</h2></div><span class="classroom-count-pill"><?= count($atividades) ?> <?= count($atividades) === 1 ? 'atividade' : 'atividades' ?></span></div>
                        <?php if ($atividades === []): ?><p class="classroom-empty">Nenhuma atividade publicada ainda.</p><?php else: ?><div class="classroom-activity-list"><?php foreach ($atividades as $atividade): ?><article class="classroom-activity classroom-activity-card"><div class="classroom-activity-head"><div><h3><?= htmlspecialchars((string) $atividade['titulo'], ENT_QUOTES, 'UTF-8') ?></h3><span class="classroom-due is-<?= htmlspecialchars((string) ($atividade['prazo_status'] ?? 'sem-prazo'), ENT_QUOTES, 'UTF-8') ?>"><?= htmlspecialchars((string) ($atividade['prazo_texto'] ?? 'Sem prazo definido'), ENT_QUOTES, 'UTF-8') ?></span></div></div><?php if (trim((string) ($atividade['descricao'] ?? '')) !== ''): ?><p><?= htmlspecialchars((string) $atividade['descricao'], ENT_QUOTES, 'UTF-8') ?></p><?php endif; ?><?php if (($atividade['anexos'] ?? []) !== []): ?><div class="classroom-attachments"><?php foreach ($atividade['anexos'] as $anexo): ?><a class="classroom-attachment" href="<?= app_route('/turma/anexo') ?>&amp;id=<?= (int) $anexo['id'] ?>">📎 <span><?= htmlspecialchars((string) $anexo['nome_original'], ENT_QUOTES, 'UTF-8') ?></span></a><?php endforeach; ?></div><?php endif; ?></article><?php endforeach; ?></div><?php endif; ?>
                    </section>
                </div>
                <aside class="classroom-sidebar">
                    <section class="classroom-panel" aria-labelledby="submissions-title"><p class="classroom-eyebrow">Correção</p><h2 id="submissions-title">Entregas dos alunos</h2><p><?= count($entregas) ?> <?= count($entregas) === 1 ? 'arquivo enviado' : 'arquivos enviados' ?>.</p>
                        <?php if ($entregas === []): ?><p class="classroom-empty classroom-empty-compact">As entregas aparecerão aqui quando os alunos enviarem suas atividades.</p><?php else: ?><div class="submission-list"><?php foreach ($entregas as $entrega): ?><?php $inicial = function_exists('mb_substr') ? mb_strtoupper(mb_substr((string) $entrega['aluno_nome'], 0, 1, 'UTF-8') , 'UTF-8') : strtoupper(substr((string) $entrega['aluno_nome'], 0, 1)); $notaAtual = $entrega['nota'] !== null ? number_format((float) $entrega['nota'], 2, '.', '') : ''; ?><article class="submission-card"><div class="submission-student"><span class="classroom-list-avatar"><?= htmlspecialchars($inicial, ENT_QUOTES, 'UTF-8') ?></span><span><strong><?= htmlspecialchars((string) $entrega['aluno_nome'], ENT_QUOTES, 'UTF-8') ?></strong><small><?= htmlspecialchars((string) $entrega['atividade_titulo'], ENT_QUOTES, 'UTF-8') ?></small></span></div><a class="classroom-attachment submission-file" href="<?= app_route('/turma/entrega') ?>&amp;id=<?= (int) $entrega['id'] ?>">📄 <span><?= htmlspecialchars((string) $entrega['nome_original'], ENT_QUOTES, 'UTF-8') ?></span></a><small class="submission-date">Enviado em <?= htmlspecialchars($formatarData($entrega['created_at'] ?? null), ENT_QUOTES, 'UTF-8') ?></small><form class="grade-form" method="post" action="<?= app_route('/professor/turma/nota') ?>"><input type="hidden" name="token" value="<?= htmlspecialchars($tokenNota, ENT_QUOTES, 'UTF-8') ?>"><input type="hidden" name="turma_id" value="<?= (int) $turma['id'] ?>"><input type="hidden" name="entrega_id" value="<?= (int) $entrega['id'] ?>"><label for="nota-<?= (int) $entrega['id'] ?>">Nota (0–10)</label><div><input id="nota-<?= (int) $entrega['id'] ?>" name="nota" type="number" min="0" max="10" step="0.1" required value="<?= htmlspecialchars($notaAtual, ENT_QUOTES, 'UTF-8') ?>"><button class="classroom-secondary" type="submit">Salvar</button></div></form></article><?php endforeach; ?></div><?php endif; ?>
                    </section>
                </aside>
            </section>
        <?php else: ?>
            <section class="classroom-content-grid classroom-video-tab"><section class="classroom-panel"><p class="classroom-eyebrow">Conteúdo complementar</p><h2>Videoaulas desta turma</h2><p>Cadastre e gerencie as videoaulas destinadas a esta turma. Os vídeos ficam visíveis somente aos alunos matriculados nela.</p><a class="classroom-primary classroom-inline-action" href="<?= app_route('/professor/videos') ?>&amp;turma_id=<?= (int) $turma['id'] ?>">Cadastrar ou gerenciar videoaulas</a></section><aside class="classroom-sidebar"><section class="classroom-panel"><h2>Turma selecionada</h2><p>A publicação abrirá já filtrada para <strong><?= htmlspecialchars((string) ($turma['nome'] ?? ''), ENT_QUOTES, 'UTF-8') ?></strong>.</p></section></aside></section>
        <?php endif; ?>
    </main>
</body>
</html>
