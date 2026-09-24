<?php
$turma = $turma ?? [];
$atividades = $atividades ?? [];
$avisos = $avisos ?? [];
$aba = $aba ?? 'avisos';
$mensagem = $mensagem ?? '';
$status = $status ?? '';
$tokenEntrega = (string) ($_SESSION['entrega_turma_token'] ?? '');
$formatarData = static function ($data): string {
    $timestamp = $data ? strtotime((string) $data) : false;
    return $timestamp === false ? 'Data indisponível' : date('d/m/Y \à\s H:i', $timestamp);
};
$proximasAtividades = array_values(array_filter($atividades, static fn (array $atividade): bool => in_array((string) ($atividade['prazo_status'] ?? ''), ['hoje', 'proximo'], true)));
$rotaDaTurma = app_route('/aluno/turma/detalhe') . '&id=' . (int) ($turma['id'] ?? 0);
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
    <?php $navbarActive = 'turma'; require APP_ROOT . '/resources/views/layouts/navbar.php'; ?>

    <main class="classroom-shell classroom-tab-shell">
        <a class="classroom-back-link" href="<?= app_route('/aluno/turma') ?>">← Minhas turmas</a>
        <section class="classroom-hero classroom-class-hero">
            <div class="classroom-hero-copy">
                <p class="classroom-eyebrow">Sala da turma</p>
                <h1><?= htmlspecialchars((string) ($turma['nome'] ?? ''), ENT_QUOTES, 'UTF-8') ?></h1>
                <p><?= htmlspecialchars((string) (($turma['descricao'] ?? '') ?: 'Acompanhe os avisos, as atividades e as videoaulas desta turma.'), ENT_QUOTES, 'UTF-8') ?></p>
            </div>
            <div class="classroom-code-box"><small>Professor(a)</small><code class="classroom-teacher-name"><?= htmlspecialchars((string) ($turma['professor_nome'] ?? ''), ENT_QUOTES, 'UTF-8') ?></code></div>
        </section>

        <nav class="classroom-tabs" aria-label="Seções da turma">
            <a class="classroom-tab <?= $aba === 'avisos' ? 'is-active' : '' ?>" href="<?= $rotaDaTurma ?>&amp;aba=avisos">Avisos</a>
            <a class="classroom-tab <?= $aba === 'atividades' ? 'is-active' : '' ?>" href="<?= $rotaDaTurma ?>&amp;aba=atividades">Atividades</a>
            <a class="classroom-tab <?= $aba === 'videoaulas' ? 'is-active' : '' ?>" href="<?= $rotaDaTurma ?>&amp;aba=videoaulas">Videoaulas</a>
        </nav>

        <?php if ($mensagem !== ''): ?><p class="classroom-notice <?= $status === 'ok' ? 'is-ok' : 'is-error' ?>" role="status"><?= htmlspecialchars($mensagem, ENT_QUOTES, 'UTF-8') ?></p><?php endif; ?>

        <?php if ($aba === 'avisos'): ?>
            <section class="classroom-content-grid">
                <section class="classroom-panel" aria-labelledby="student-announcements-title">
                    <div class="classroom-panel-heading"><div><p class="classroom-eyebrow">Mural</p><h2 id="student-announcements-title">Avisos da turma</h2></div><span class="classroom-count-pill"><?= count($avisos) ?> <?= count($avisos) === 1 ? 'aviso' : 'avisos' ?></span></div>
                    <?php if ($avisos === []): ?>
                        <p class="classroom-empty">Ainda não há avisos publicados. Quando houver novidades, elas aparecerão neste mural.</p>
                    <?php else: ?>
                        <div class="classroom-timeline">
                            <?php foreach ($avisos as $aviso): ?>
                                <article class="classroom-post">
                                    <div class="classroom-post-avatar" aria-hidden="true"><?= htmlspecialchars(function_exists('mb_substr') ? mb_strtoupper(mb_substr((string) $aviso['professor_nome'], 0, 1, 'UTF-8'), 'UTF-8') : strtoupper(substr((string) $aviso['professor_nome'], 0, 1)), ENT_QUOTES, 'UTF-8') ?></div>
                                    <div><header><strong><?= htmlspecialchars((string) $aviso['professor_nome'], ENT_QUOTES, 'UTF-8') ?></strong><small><?= htmlspecialchars($formatarData($aviso['created_at'] ?? null), ENT_QUOTES, 'UTF-8') ?></small></header><p><?= nl2br(htmlspecialchars((string) $aviso['mensagem'], ENT_QUOTES, 'UTF-8')) ?></p></div>
                                </article>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </section>

                <aside class="classroom-sidebar">
                    <section class="classroom-panel classroom-deadline-summary"><p class="classroom-eyebrow">Prazos</p><h2>Próximas atividades</h2>
                        <?php if ($proximasAtividades === []): ?><p>Não há prazos próximos neste momento.</p><?php else: ?><div class="classroom-mini-list"><?php foreach ($proximasAtividades as $atividade): ?><a href="<?= $rotaDaTurma ?>&amp;aba=atividades"><strong><?= htmlspecialchars((string) $atividade['titulo'], ENT_QUOTES, 'UTF-8') ?></strong><small><?= htmlspecialchars((string) $atividade['prazo_texto'], ENT_QUOTES, 'UTF-8') ?></small></a><?php endforeach; ?></div><?php endif; ?>
                    </section>
                    <section class="classroom-panel"><h2>Contato</h2><p>Professor(a): <strong><?= htmlspecialchars((string) ($turma['professor_nome'] ?? ''), ENT_QUOTES, 'UTF-8') ?></strong></p><?php if (trim((string) ($turma['professor_email'] ?? '')) !== ''): ?><p><?= htmlspecialchars((string) $turma['professor_email'], ENT_QUOTES, 'UTF-8') ?></p><?php endif; ?></section>
                </aside>
            </section>
        <?php elseif ($aba === 'atividades'): ?>
            <section class="classroom-content-grid">
                <section class="classroom-panel" aria-labelledby="student-activities-title">
                    <div class="classroom-panel-heading"><div><p class="classroom-eyebrow">Conteúdo da turma</p><h2 id="student-activities-title">Atividades</h2></div><span class="classroom-count-pill"><?= count($atividades) ?> <?= count($atividades) === 1 ? 'atividade' : 'atividades' ?></span></div>
                    <?php if ($atividades === []): ?>
                        <p class="classroom-empty">Ainda não há atividades publicadas nesta turma.</p>
                    <?php else: ?>
                        <div class="classroom-activity-list">
                            <?php foreach ($atividades as $atividade): ?>
                                <article class="classroom-activity classroom-activity-card">
                                    <div class="classroom-activity-head"><div><h3><?= htmlspecialchars((string) $atividade['titulo'], ENT_QUOTES, 'UTF-8') ?></h3><span class="classroom-due is-<?= htmlspecialchars((string) ($atividade['prazo_status'] ?? 'sem-prazo'), ENT_QUOTES, 'UTF-8') ?>"><?= htmlspecialchars((string) ($atividade['prazo_texto'] ?? 'Sem prazo definido'), ENT_QUOTES, 'UTF-8') ?></span></div></div>
                                    <?php if (trim((string) ($atividade['descricao'] ?? '')) !== ''): ?><p><?= htmlspecialchars((string) $atividade['descricao'], ENT_QUOTES, 'UTF-8') ?></p><?php endif; ?>
                                    <?php if (($atividade['anexos'] ?? []) !== []): ?><div class="classroom-attachments" aria-label="Anexos da atividade"><?php foreach ($atividade['anexos'] as $anexo): ?><a class="classroom-attachment" href="<?= app_route('/turma/anexo') ?>&amp;id=<?= (int) $anexo['id'] ?>">📎 <span><?= htmlspecialchars((string) $anexo['nome_original'], ENT_QUOTES, 'UTF-8') ?></span></a><?php endforeach; ?></div><?php endif; ?>
                                    <section class="classroom-submission-box" aria-label="Entrega da atividade">
                                        <div><strong>Sua entrega</strong>
                                            <?php if (($atividade['entrega_id'] ?? null) !== null): ?>
                                                <p>Enviada em <?= htmlspecialchars($formatarData($atividade['entrega_enviada_em'] ?? null), ENT_QUOTES, 'UTF-8') ?><?= !empty($atividade['entrega_apos_prazo']) ? ' (após o prazo)' : '' ?>.</p>
                                                <a class="classroom-attachment submission-file" href="<?= app_route('/turma/entrega') ?>&amp;id=<?= (int) $atividade['entrega_id'] ?>">📄 <span><?= htmlspecialchars((string) $atividade['entrega_nome_original'], ENT_QUOTES, 'UTF-8') ?></span></a>
                                                <?php if ($atividade['entrega_nota'] !== null): ?><p class="submission-grade">Nota: <strong><?= htmlspecialchars(number_format((float) $atividade['entrega_nota'], 1, ',', '.'), ENT_QUOTES, 'UTF-8') ?> / 10</strong></p><?php else: ?><p class="submission-pending">Aguardando correção do professor.</p><?php endif; ?>
                                            <?php elseif (empty($atividade['pode_entregar'])): ?><p class="submission-expired">O prazo foi encerrado; esta atividade não aceita novas entregas.</p>
                                            <?php else: ?><p>Envie um único arquivo para o professor corrigir.</p><?php endif; ?>
                                        </div>
                                        <?php if (!empty($atividade['pode_entregar'])): ?>
                                            <form class="submission-upload-form" method="post" action="<?= app_route('/aluno/turma/entrega') ?>" enctype="multipart/form-data">
                                                <input type="hidden" name="token" value="<?= htmlspecialchars($tokenEntrega, ENT_QUOTES, 'UTF-8') ?>"><input type="hidden" name="turma_id" value="<?= (int) $turma['id'] ?>"><input type="hidden" name="atividade_id" value="<?= (int) $atividade['id'] ?>">
                                                <label for="entrega-<?= (int) $atividade['id'] ?>"><?= ($atividade['entrega_id'] ?? null) !== null ? 'Substituir arquivo' : 'Arquivo da atividade' ?></label>
                                                <input id="entrega-<?= (int) $atividade['id'] ?>" name="entrega" type="file" required accept=".pdf,.txt,.jpg,.jpeg,.png,.doc,.docx,.xls,.xlsx,.ppt,.pptx"><small>Até 10 MB: PDF, documentos, planilhas, apresentações ou imagens.</small><button class="classroom-secondary" type="submit"><?= ($atividade['entrega_id'] ?? null) !== null ? 'Substituir entrega' : 'Enviar atividade' ?></button>
                                            </form>
                                        <?php endif; ?>
                                    </section>
                                </article>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </section>
                <aside class="classroom-sidebar"><section class="classroom-panel"><p class="classroom-eyebrow">Organização</p><h2>Como funcionam os prazos</h2><p>As entregas ficam indisponíveis assim que o prazo encerra. Confira a data e a hora antes de enviar ou substituir um arquivo.</p></section></aside>
            </section>
        <?php else: ?>
            <section class="classroom-content-grid classroom-video-tab">
                <section class="classroom-panel"><p class="classroom-eyebrow">Conteúdo complementar</p><h2>Videoaulas desta turma</h2><p>Assista somente às videoaulas publicadas para esta turma pelo seu professor.</p><a class="classroom-primary classroom-inline-action" href="<?= app_route('/videoaulas') ?>&amp;turma_id=<?= (int) $turma['id'] ?>">Abrir videoaulas da turma</a></section>
                <aside class="classroom-sidebar"><section class="classroom-panel"><h2>Acesso da turma</h2><p>Este conteúdo é exclusivo para alunos matriculados nesta turma.</p></section></aside>
            </section>
        <?php endif; ?>
    </main>
</body>
</html>
