<?php
$turma = $turma ?? [];
$atividades = $atividades ?? [];
$alunos = $alunos ?? [];
$entregas = $entregas ?? [];
$mensagem = $mensagem ?? '';
$status = $status ?? '';
$token = (string) ($_SESSION['atividade_turma_token'] ?? '');
$tokenNota = (string) ($_SESSION['nota_entrega_token'] ?? '');
$formatarData = static function ($data): string {
    $timestamp = $data ? strtotime((string) $data) : false;
    return $timestamp === false ? 'Sem prazo definido' : date('d/m/Y \à\s H:i', $timestamp);
};
?>
<!doctype html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= htmlspecialchars((string) ($turma['nome'] ?? 'Turma'), ENT_QUOTES, 'UTF-8') ?> | HSchuler</title>
    <link rel="stylesheet" href="<?= app_asset('css/turmas.css') ?>?v=3">
</head>
<body class="classroom-page">
    <?php $navbarActive = 'turmas'; require APP_ROOT . '/resources/views/layouts/navbar.php'; ?>

    <main class="classroom-shell">
        <a class="classroom-video-link" href="<?= app_route('/professor/turmas') ?>">← Voltar para minhas turmas</a>
        <section class="classroom-hero" style="margin-top:14px">
            <div class="classroom-hero-copy"><p class="classroom-eyebrow">Turma do professor</p><h1><?= htmlspecialchars($turma['nome'], ENT_QUOTES, 'UTF-8') ?></h1><p><?= htmlspecialchars((string) ($turma['descricao'] ?: 'Publique atividades e acompanhe os alunos da turma.'), ENT_QUOTES, 'UTF-8') ?></p></div>
            <div class="classroom-code-box"><small>Compartilhe com os alunos</small><code><?= htmlspecialchars($turma['codigo'], ENT_QUOTES, 'UTF-8') ?></code></div>
        </section>

        <?php if ($mensagem !== ''): ?><p class="classroom-notice <?= $status === 'ok' ? 'is-ok' : 'is-error' ?>" role="status"><?= htmlspecialchars($mensagem, ENT_QUOTES, 'UTF-8') ?></p><?php endif; ?>

        <section class="classroom-layout teacher-classroom-layout" style="margin-top:28px">
            <aside class="classroom-submissions">
                <section class="classroom-panel" aria-labelledby="submissions-title">
                    <h2 id="submissions-title">Entregas dos alunos</h2>
                    <p><?= count($entregas) ?> <?= count($entregas) === 1 ? 'arquivo enviado' : 'arquivos enviados' ?> para correção.</p>
                    <?php if ($entregas === []): ?>
                        <p class="classroom-empty classroom-empty-compact" style="margin-top:16px">As entregas aparecerão aqui quando os alunos enviarem suas atividades.</p>
                    <?php else: ?>
                        <div class="submission-list">
                            <?php foreach ($entregas as $entrega): ?>
                                <?php
                                $inicial = function_exists('mb_substr')
                                    ? mb_strtoupper(mb_substr((string) $entrega['aluno_nome'], 0, 1, 'UTF-8'), 'UTF-8')
                                    : strtoupper(substr((string) $entrega['aluno_nome'], 0, 1));
                                $notaAtual = $entrega['nota'] !== null ? number_format((float) $entrega['nota'], 2, '.', '') : '';
                                ?>
                                <article class="submission-card">
                                    <div class="submission-student"><span class="classroom-list-avatar"><?= htmlspecialchars($inicial, ENT_QUOTES, 'UTF-8') ?></span><span><strong><?= htmlspecialchars($entrega['aluno_nome'], ENT_QUOTES, 'UTF-8') ?></strong><small><?= htmlspecialchars($entrega['atividade_titulo'], ENT_QUOTES, 'UTF-8') ?></small></span></div>
                                    <a class="classroom-attachment submission-file" href="<?= app_route('/turma/entrega') ?>&amp;id=<?= (int) $entrega['id'] ?>"><span aria-hidden="true">📄</span><span><?= htmlspecialchars($entrega['nome_original'], ENT_QUOTES, 'UTF-8') ?></span></a>
                                    <small class="submission-date">Enviado em <?= htmlspecialchars($formatarData($entrega['created_at']), ENT_QUOTES, 'UTF-8') ?></small>
                                    <form class="grade-form" method="post" action="<?= app_route('/professor/turma/nota') ?>">
                                        <input type="hidden" name="token" value="<?= htmlspecialchars($tokenNota, ENT_QUOTES, 'UTF-8') ?>"><input type="hidden" name="turma_id" value="<?= (int) $turma['id'] ?>"><input type="hidden" name="entrega_id" value="<?= (int) $entrega['id'] ?>">
                                        <label for="nota-<?= (int) $entrega['id'] ?>">Nota (0–10)</label>
                                        <div><input id="nota-<?= (int) $entrega['id'] ?>" name="nota" type="number" min="0" max="10" step="0.1" required value="<?= htmlspecialchars($notaAtual, ENT_QUOTES, 'UTF-8') ?>"><button class="classroom-secondary" type="submit">Salvar</button></div>
                                    </form>
                                </article>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </section>
            </aside>
            <div class="classroom-main-column">
                <section class="classroom-panel" aria-labelledby="activity-form-title">
                    <h2 id="activity-form-title">Publicar atividade</h2>
                    <p>Cadastre uma atividade com orientações, prazo de entrega e materiais de apoio para os alunos.</p>
                    <form class="classroom-form" method="post" action="<?= app_route('/professor/turma/atividade') ?>" enctype="multipart/form-data">
                        <input type="hidden" name="token" value="<?= htmlspecialchars($token, ENT_QUOTES, 'UTF-8') ?>"><input type="hidden" name="turma_id" value="<?= (int) $turma['id'] ?>">
                        <div class="classroom-field"><label for="titulo">Título</label><input id="titulo" name="titulo" type="text" maxlength="150" required placeholder="Ex.: Lista de exercícios — Potenciação"></div>
                        <div class="classroom-field"><label for="descricao">Descrição e orientações</label><textarea id="descricao" name="descricao" maxlength="5000" placeholder="Explique o que deve ser feito e quais conceitos devem ser revisados."></textarea></div>
                        <div class="classroom-field"><label for="periodo_entrega">Período de entrega</label><input id="periodo_entrega" name="periodo_entrega" type="datetime-local"><small>Deixe em branco caso a atividade não tenha prazo.</small></div>
                        <div class="classroom-field classroom-upload"><label for="anexos">Anexos de apoio</label><input id="anexos" name="anexos[]" type="file" multiple accept=".pdf,.txt,.jpg,.jpeg,.png,.doc,.docx,.xls,.xlsx,.ppt,.pptx"><small>Até 5 arquivos de 10 MB cada: PDF, documentos, planilhas, apresentações ou imagens.</small></div>
                        <button class="classroom-primary" type="submit">Publicar atividade</button>
                    </form>
                </section>

                <section class="classroom-panel" aria-labelledby="activities-title">
                    <h2 id="activities-title">Atividades publicadas</h2>
                    <?php if ($atividades === []): ?>
                        <p class="classroom-empty" style="margin-top:16px">Nenhuma atividade publicada ainda.</p>
                    <?php else: ?>
                        <div style="margin-top:18px">
                            <?php foreach ($atividades as $atividade): ?>
                                <article class="classroom-activity">
                                    <div class="classroom-activity-head"><h3><?= htmlspecialchars($atividade['titulo'], ENT_QUOTES, 'UTF-8') ?></h3><span class="classroom-due"><?= htmlspecialchars($formatarData($atividade['periodo_entrega'] ?? null), ENT_QUOTES, 'UTF-8') ?></span></div>
                                    <?php if (trim((string) ($atividade['descricao'] ?? '')) !== ''): ?><p><?= htmlspecialchars((string) $atividade['descricao'], ENT_QUOTES, 'UTF-8') ?></p><?php endif; ?>
                                    <?php if (($atividade['anexos'] ?? []) !== []): ?><div class="classroom-attachments"><?php foreach ($atividade['anexos'] as $anexo): ?><a class="classroom-attachment" href="<?= app_route('/turma/anexo') ?>&amp;id=<?= (int) $anexo['id'] ?>"><span aria-hidden="true">📎</span><span><?= htmlspecialchars($anexo['nome_original'], ENT_QUOTES, 'UTF-8') ?></span></a><?php endforeach; ?></div><?php endif; ?>
                                </article>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </section>
            </div>

            <aside class="classroom-sidebar">
                <section class="classroom-panel"><h2>Alunos</h2><p><?= count($alunos) ?> <?= count($alunos) === 1 ? 'aluno participa' : 'alunos participam' ?> desta turma.</p>
                    <?php if ($alunos === []): ?><p class="classroom-empty" style="margin-top:16px">Compartilhe o código para os alunos entrarem.</p><?php else: ?><div class="classroom-list"><?php foreach ($alunos as $aluno): ?><?php $inicial = function_exists('mb_substr') ? mb_strtoupper(mb_substr($aluno['usuario'], 0, 1, 'UTF-8'), 'UTF-8') : strtoupper(substr($aluno['usuario'], 0, 1)); ?><div class="classroom-list-item"><span class="classroom-list-avatar"><?= htmlspecialchars($inicial, ENT_QUOTES, 'UTF-8') ?></span><span><strong><?= htmlspecialchars($aluno['usuario'], ENT_QUOTES, 'UTF-8') ?></strong><small><?= htmlspecialchars($aluno['email'], ENT_QUOTES, 'UTF-8') ?></small></span></div><?php endforeach; ?></div><?php endif; ?>
                </section>
                <section class="classroom-panel"><h2>Videoaulas</h2><p>Use a área de videoaulas já disponível para publicar explicações complementares.</p><a class="classroom-video-link" href="<?= app_route('/professor/videos') ?>">Gerenciar videoaulas →</a></section>
            </aside>
        </section>
    </main>
</body>
</html>
