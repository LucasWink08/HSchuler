<?php
$turma = $turma ?? [];
$atividades = $atividades ?? [];
$mensagem = $mensagem ?? '';
$status = $status ?? '';
$tokenEntrega = (string) ($_SESSION['entrega_turma_token'] ?? '');
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
    <?php $navbarActive = 'turma'; require APP_ROOT . '/resources/views/layouts/navbar.php'; ?>

    <main class="classroom-shell">
        <a class="classroom-video-link" href="<?= app_route('/aluno/turma') ?>">← Voltar para minhas turmas</a>
        <section class="classroom-hero" style="margin-top:14px">
            <div class="classroom-hero-copy"><p class="classroom-eyebrow">Turma</p><h1><?= htmlspecialchars($turma['nome'], ENT_QUOTES, 'UTF-8') ?></h1><p><?= htmlspecialchars((string) ($turma['descricao'] ?: 'Acompanhe abaixo as atividades e materiais enviados pelo professor.'), ENT_QUOTES, 'UTF-8') ?></p></div>
            <div class="classroom-code-box"><small>Seu professor</small><code style="font-size:.9rem;letter-spacing:.04em"><?= htmlspecialchars($turma['professor_nome'], ENT_QUOTES, 'UTF-8') ?></code></div>
        </section>

        <?php if ($mensagem !== ''): ?><p class="classroom-notice <?= $status === 'ok' ? 'is-ok' : 'is-error' ?>" role="status"><?= htmlspecialchars($mensagem, ENT_QUOTES, 'UTF-8') ?></p><?php endif; ?>

        <section class="classroom-layout" style="margin-top:28px">
            <section class="classroom-panel" aria-labelledby="student-activities-title">
                <h2 id="student-activities-title">Atividades da turma</h2>
                <p>Leia as orientações e baixe os materiais de apoio enviados pelo professor.</p>
                <?php if ($atividades === []): ?>
                    <p class="classroom-empty" style="margin-top:18px">Ainda não há atividades publicadas nesta turma.</p>
                <?php else: ?>
                    <div style="margin-top:20px">
                        <?php foreach ($atividades as $atividade): ?>
                            <article class="classroom-activity">
                                <div class="classroom-activity-head"><h3><?= htmlspecialchars($atividade['titulo'], ENT_QUOTES, 'UTF-8') ?></h3><span class="classroom-due"><?= htmlspecialchars($formatarData($atividade['periodo_entrega'] ?? null), ENT_QUOTES, 'UTF-8') ?></span></div>
                                <?php if (trim((string) ($atividade['descricao'] ?? '')) !== ''): ?><p><?= htmlspecialchars((string) $atividade['descricao'], ENT_QUOTES, 'UTF-8') ?></p><?php endif; ?>
                                <?php if (($atividade['anexos'] ?? []) !== []): ?>
                                    <div class="classroom-attachments" aria-label="Anexos da atividade">
                                        <?php foreach ($atividade['anexos'] as $anexo): ?>
                                            <a class="classroom-attachment" href="<?= app_route('/turma/anexo') ?>&amp;id=<?= (int) $anexo['id'] ?>"><span aria-hidden="true">📎</span><span><?= htmlspecialchars($anexo['nome_original'], ENT_QUOTES, 'UTF-8') ?></span></a>
                                        <?php endforeach; ?>
                                    </div>
                                <?php endif; ?>
                                <section class="classroom-submission-box" aria-label="Entrega da atividade">
                                    <div>
                                        <strong>Sua entrega</strong>
                                        <?php if (($atividade['entrega_id'] ?? null) !== null): ?>
                                            <p>Enviada em <?= htmlspecialchars($formatarData($atividade['entrega_enviada_em'] ?? null), ENT_QUOTES, 'UTF-8') ?>.</p>
                                            <a class="classroom-attachment submission-file" href="<?= app_route('/turma/entrega') ?>&amp;id=<?= (int) $atividade['entrega_id'] ?>"><span aria-hidden="true">📄</span><span><?= htmlspecialchars((string) $atividade['entrega_nome_original'], ENT_QUOTES, 'UTF-8') ?></span></a>
                                            <?php if ($atividade['entrega_nota'] !== null): ?><p class="submission-grade">Nota: <strong><?= htmlspecialchars(number_format((float) $atividade['entrega_nota'], 1, ',', '.'), ENT_QUOTES, 'UTF-8') ?> / 10</strong></p><?php else: ?><p class="submission-pending">Aguardando correção do professor.</p><?php endif; ?>
                                        <?php else: ?>
                                            <p>Envie um único arquivo para o professor corrigir.</p>
                                        <?php endif; ?>
                                    </div>
                                    <form class="submission-upload-form" method="post" action="<?= app_route('/aluno/turma/entrega') ?>" enctype="multipart/form-data">
                                        <input type="hidden" name="token" value="<?= htmlspecialchars($tokenEntrega, ENT_QUOTES, 'UTF-8') ?>"><input type="hidden" name="turma_id" value="<?= (int) $turma['id'] ?>"><input type="hidden" name="atividade_id" value="<?= (int) $atividade['id'] ?>">
                                        <label for="entrega-<?= (int) $atividade['id'] ?>"><?= ($atividade['entrega_id'] ?? null) !== null ? 'Reenviar arquivo' : 'Arquivo da atividade' ?></label>
                                        <input id="entrega-<?= (int) $atividade['id'] ?>" name="entrega" type="file" required accept=".pdf,.txt,.jpg,.jpeg,.png,.doc,.docx,.xls,.xlsx,.ppt,.pptx">
                                        <small>Até 10 MB: PDF, documentos, planilhas, apresentações ou imagens.</small>
                                        <button class="classroom-secondary" type="submit"><?= ($atividade['entrega_id'] ?? null) !== null ? 'Reenviar atividade' : 'Enviar atividade' ?></button>
                                    </form>
                                </section>
                            </article>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </section>

            <aside class="classroom-sidebar">
                <section class="classroom-panel"><h2>Contato</h2><p>Professor(a): <strong><?= htmlspecialchars($turma['professor_nome'], ENT_QUOTES, 'UTF-8') ?></strong></p><?php if (trim((string) ($turma['professor_email'] ?? '')) !== ''): ?><p><?= htmlspecialchars($turma['professor_email'], ENT_QUOTES, 'UTF-8') ?></p><?php endif; ?></section>
                <section class="classroom-panel"><h2>Videoaulas</h2><p>Assista às videoaulas que seu professor publicou para esta turma.</p><a class="classroom-video-link" href="<?= app_route('/videoaulas') ?>&amp;turma_id=<?= (int) $turma['id'] ?>">Abrir videoaulas →</a></section>
            </aside>
        </section>
    </main>
</body>
</html>
