<?php
$resumo = $resumo ?? [];
$perfil = $perfil ?? [];
$fotoStatus = $fotoStatus ?? '';
$fotoMensagem = $fotoMensagem ?? '';
$fotoToken = $fotoToken ?? '';
$nomePerfil = trim((string) ($perfil['usuario'] ?? $_SESSION['usuario'] ?? 'Aluno'));
$emailPerfil = trim((string) ($perfil['email'] ?? ''));
$fotoPerfil = basename(trim((string) ($perfil['foto_perfil'] ?? '')));
$fotoUrl = $fotoPerfil === '' ? '' : APP_URL . '/uploads/perfis/' . rawurlencode($fotoPerfil);
$inicialPerfil = function_exists('mb_substr') ? mb_strtoupper(mb_substr($nomePerfil, 0, 1, 'UTF-8'), 'UTF-8') : strtoupper(substr($nomePerfil, 0, 1));
$etapasConcluidas = (int) ($resumo['etapas_concluidas'] ?? 0);
$totalEtapas = (int) ($resumo['total_etapas'] ?? 0);
$progresso = $totalEtapas > 0 ? (int) round(($etapasConcluidas / $totalEtapas) * 100) : 0;
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Meu perfil | HSchuler</title>
    <link rel="stylesheet" href="<?= app_asset('css/estilo_homepage.css') ?>">
    <style>
        :root { color-scheme: dark; }
        * { box-sizing: border-box; }
        body.profile-page { display: block !important; min-height: 100svh; margin: 0; overflow-x: hidden; color: #edf4ff; background: radial-gradient(circle at 76% 14%, rgba(71, 87, 222, .24), transparent 26%), radial-gradient(circle at 13% 84%, rgba(136, 50, 224, .18), transparent 31%), #03050c; font-family: Arial, Helvetica, sans-serif; isolation: isolate; }
        body.profile-page::before { position: fixed; z-index: -1; inset: 0; content: ""; opacity: .38; background-image: radial-gradient(circle at 12% 15%, #d8ecff 0 1px, transparent 1.7px), radial-gradient(circle at 68% 11%, #d8ecff 0 1px, transparent 1.6px), radial-gradient(circle at 89% 39%, #d8ecff 0 1px, transparent 1.5px), radial-gradient(circle at 31% 71%, #d8ecff 0 1px, transparent 1.5px), radial-gradient(circle at 60% 87%, #d8ecff 0 1px, transparent 1.6px); background-size: 270px 246px, 344px 318px, 304px 280px, 241px 259px, 389px 332px; pointer-events: none; }
        body.profile-page .site-nav { position: relative; z-index: 20; display: grid; width: min(calc(100% - 48px), 1240px); min-height: 76px; margin: 0 auto; padding: 16px 0 10px; }
        .profile-main { width: min(calc(100% - 48px), 1100px); margin: 22px auto 64px; }
        .profile-hero { display: grid; grid-template-columns: minmax(0, 1.2fr) minmax(250px, .8fr); gap: 28px; padding: clamp(24px, 4vw, 44px); border: 1px solid rgba(115, 151, 255, .28); border-radius: 24px; background: linear-gradient(135deg, rgba(13, 27, 61, .94), rgba(9, 13, 31, .91)); box-shadow: inset 0 1px rgba(255, 255, 255, .08), 0 25px 60px rgba(0, 0, 0, .32); }
        .profile-identity { display: flex; align-items: center; gap: clamp(20px, 3vw, 30px); min-width: 0; }
        .profile-photo-form { display: grid; flex: 0 0 144px; gap: 11px; text-align: center; }
        .avatar-display { position: relative; display: grid; width: 144px; height: 144px; overflow: hidden; place-items: center; border: 3px solid rgba(160, 204, 255, .86); border-radius: 50%; background: linear-gradient(145deg, #6d47e4, #216dc4 65%, #123a7d); box-shadow: 0 0 0 7px rgba(107, 102, 255, .12), 0 16px 32px rgba(2, 9, 34, .44); color: #fff; cursor: pointer; }
        .avatar-display img { display: none; width: 100%; height: 100%; object-fit: cover; }
        .avatar-display img.is-visible { display: block; }
        .avatar-initial { font-size: 3.35rem; font-weight: 700; text-shadow: 0 2px 10px rgba(0, 0, 0, .3); }
        .avatar-initial[hidden] { display: none; }
        .avatar-edit { position: absolute; right: 4px; bottom: 5px; display: grid; width: 37px; height: 37px; place-items: center; border: 2px solid #e9f6ff; border-radius: 50%; background: #235ee5; box-shadow: 0 4px 13px rgba(6, 20, 73, .55); font-size: 1rem; }
        .photo-input { position: absolute; width: 1px; height: 1px; overflow: hidden; clip: rect(0, 0, 0, 0); white-space: nowrap; clip-path: inset(50%); }
        .photo-help { color: #aebdda; font-size: .7rem; line-height: 1.35; }
        .photo-submit { min-height: 34px; padding: 7px 10px; border: 1px solid #538aff; border-radius: 8px; background: rgba(36, 91, 200, .24); color: #edf5ff; font: inherit; font-size: .75rem; font-weight: 700; cursor: pointer; }
        .photo-submit:hover, .photo-submit:focus-visible { background: rgba(56, 119, 244, .42); outline: 2px solid #9fc5ff; outline-offset: 2px; }
        .profile-copy { min-width: 0; }
        .profile-kicker { margin: 0 0 8px; color: #9bbfff; font-size: .72rem; font-weight: 700; letter-spacing: .1em; text-transform: uppercase; }
        .profile-copy h1 { margin: 0; color: #fff; font-size: clamp(1.9rem, 4vw, 2.8rem); letter-spacing: -.045em; }
        .profile-copy > p:not(.profile-kicker) { max-width: 430px; margin: 10px 0 0; color: #b9c8e5; line-height: 1.55; }
        .profile-email { display: inline-flex; align-items: center; gap: 8px; margin-top: 15px; color: #d7e4fa; font-size: .84rem; }
        .profile-email::before { content: "✦"; color: #8fb7ff; }
        .profile-notice { margin: 18px 0 0; padding: 11px 13px; border: 1px solid; border-radius: 10px; font-size: .8rem; line-height: 1.4; }
        .profile-notice.is-ok { border-color: rgba(85, 220, 144, .55); background: rgba(41, 157, 91, .17); color: #c2f8d7; }
        .profile-notice.is-error { border-color: rgba(255, 128, 140, .58); background: rgba(184, 49, 65, .17); color: #ffd0d4; }
        .profile-level-card { display: grid; align-content: center; gap: 14px; padding: 21px; border: 1px solid rgba(171, 131, 255, .33); border-radius: 18px; background: rgba(22, 17, 56, .5); }
        .profile-level-card small { color: #c3b9f6; font-weight: 700; letter-spacing: .06em; text-transform: uppercase; }
        .profile-level-card strong { color: #fff; font-size: 1.45rem; }
        .level-bar { height: 9px; overflow: hidden; border-radius: 999px; background: rgba(235, 235, 255, .13); }
        .level-bar i { display: block; width: var(--progress); height: 100%; border-radius: inherit; background: linear-gradient(90deg, #6d67ff, #c25aff); box-shadow: 0 0 14px rgba(158, 91, 255, .85); }
        .level-caption { display: flex; justify-content: space-between; gap: 10px; color: #c9c5e8; font-size: .77rem; }
        .profile-section-head { display: flex; align-items: end; justify-content: space-between; gap: 20px; margin: 39px 0 16px; }
        .profile-section-head h2 { margin: 0; color: #fff; font-size: 1.25rem; }
        .profile-section-head p { margin: 0; color: #aebdda; font-size: .83rem; }
        .stats { display: grid; grid-template-columns: repeat(4, minmax(0, 1fr)); gap: 14px; }
        .card { min-height: 148px; padding: 20px; border: 1px solid rgba(120, 147, 203, .25); border-radius: 17px; background: linear-gradient(150deg, rgba(18, 27, 49, .9), rgba(8, 13, 27, .9)); box-shadow: inset 0 1px rgba(255, 255, 255, .055); }
        .card small { color: #aebdda; font-size: .76rem; }
        .card strong { display: block; margin-top: 18px; color: #fff; font-size: clamp(1.65rem, 3vw, 2.2rem); letter-spacing: -.04em; }
        .card .stat-detail { display: block; margin-top: 7px; color: #8fa7cc; font-size: .72rem; }
        .card.is-xp strong { color: #ffcf6b; }
        .card.is-streak strong { color: #ffbc6b; }
        .quick-links { position: static; z-index: auto; display: grid; width: auto; margin: 0; padding: 0; grid-template-columns: repeat(3, minmax(0, 1fr)); gap: 14px; background: transparent; }
        .quick-link { display: grid; gap: 9px; min-height: 130px; padding: 20px; border: 1px solid rgba(106, 148, 230, .35); border-radius: 16px; background: rgba(11, 20, 42, .75); color: #ecf3ff; text-decoration: none; transition: transform .18s ease, border-color .18s ease, background .18s ease; }
        .quick-link:hover, .quick-link:focus-visible { border-color: #8fc3ff; background: rgba(28, 59, 118, .66); outline: none; transform: translateY(-3px); }
        .quick-link span { color: #9dc2ff; font-size: 1.25rem; }
        .quick-link strong { font-size: 1rem; }
        .quick-link small { color: #adbedb; font-size: .76rem; line-height: 1.4; }
        @media (max-width: 920px) { .profile-hero { grid-template-columns: 1fr; } .profile-level-card { grid-template-columns: auto 1fr; align-items: center; } .profile-level-card small { grid-column: 1 / -1; } .stats { grid-template-columns: repeat(2, minmax(0, 1fr)); } }
        @media (max-width: 620px) { body.profile-page .site-nav, .profile-main { width: min(calc(100% - 28px), 520px); } .profile-main { margin-top: 12px; } .profile-hero { padding: 24px 18px; border-radius: 19px; } .profile-identity { flex-direction: column; align-items: flex-start; } .profile-photo-form { flex-basis: auto; } .avatar-display { width: 118px; height: 118px; } .profile-section-head { align-items: start; flex-direction: column; margin-top: 30px; } .stats, .quick-links { grid-template-columns: 1fr; } .card { min-height: auto; } .profile-level-card { grid-template-columns: 1fr; } }
    </style>
</head>
<body class="profile-page">
    <?php $navbarActive = ''; require APP_ROOT . '/resources/views/layouts/navbar.php'; ?>

    <main class="profile-main">
        <section class="profile-hero" aria-labelledby="profile-title">
            <div class="profile-identity">
                <form class="profile-photo-form" method="post" enctype="multipart/form-data">
                    <input type="hidden" name="foto_perfil_token" value="<?= htmlspecialchars($fotoToken, ENT_QUOTES, 'UTF-8') ?>">
                    <label class="avatar-display" for="foto_perfil" title="Selecionar foto de perfil">
                        <img id="avatar-preview" class="<?= $fotoUrl !== '' ? 'is-visible' : '' ?>" src="<?= htmlspecialchars($fotoUrl, ENT_QUOTES, 'UTF-8') ?>" alt="Foto de perfil de <?= htmlspecialchars($nomePerfil, ENT_QUOTES, 'UTF-8') ?>">
                        <span id="avatar-initial" class="avatar-initial"<?= $fotoUrl !== '' ? ' hidden' : '' ?>><?= htmlspecialchars($inicialPerfil, ENT_QUOTES, 'UTF-8') ?></span>
                        <span class="avatar-edit" aria-hidden="true">✎</span>
                    </label>
                    <input class="photo-input" id="foto_perfil" name="foto_perfil" type="file" accept="image/jpeg,image/png,image/webp">
                    <span class="photo-help" id="photo-help">JPG, PNG ou WEBP<br>até 3 MB</span>
                    <button class="photo-submit" type="submit">Salvar foto</button>
                </form>

                <div class="profile-copy">
                    <p class="profile-kicker">Área do aluno</p>
                    <h1 id="profile-title">Olá, <?= htmlspecialchars($nomePerfil, ENT_QUOTES, 'UTF-8') ?></h1>
                    <p>Acompanhe sua evolução, mantenha sua sequência e continue avançando pelos desafios.</p>
                    <?php if ($emailPerfil !== ''): ?><span class="profile-email"><?= htmlspecialchars($emailPerfil, ENT_QUOTES, 'UTF-8') ?></span><?php endif; ?>
                    <?php if ($fotoMensagem !== ''): ?><p class="profile-notice <?= $fotoStatus === 'ok' ? 'is-ok' : 'is-error' ?>" role="status"><?= htmlspecialchars($fotoMensagem, ENT_QUOTES, 'UTF-8') ?></p><?php endif; ?>
                </div>
            </div>

            <aside class="profile-level-card" aria-label="Progresso da trilha">
                <small>Progresso geral</small>
                <strong><?= $etapasConcluidas ?> de <?= $totalEtapas ?> etapas</strong>
                <div class="level-bar" aria-label="<?= $progresso ?>% concluído"><i style="--progress:<?= $progresso ?>%"></i></div>
                <div class="level-caption"><span><?= $progresso ?>% concluído</span><span>Nível <?= (int) ($resumo['nivel'] ?? 1) ?></span></div>
            </aside>
        </section>

        <div class="profile-section-head"><h2>Seu desempenho</h2><p>Seu progresso é atualizado a cada atividade concluída.</p></div>
        <section class="stats" aria-label="Indicadores de desempenho">
            <article class="card is-xp"><small>XP total</small><strong><?= (int) ($resumo['xp'] ?? 0) ?></strong><span class="stat-detail">Pontos acumulados</span></article>
            <article class="card"><small>Nível atual</small><strong><?= (int) ($resumo['nivel'] ?? 1) ?></strong><span class="stat-detail">Continue praticando</span></article>
            <article class="card is-streak"><small>Sequência diária</small><strong>🔥 <?= (int) ($resumo['streak_atual'] ?? 0) ?></strong><span class="stat-detail">Maior: <?= (int) ($resumo['maior_streak'] ?? 0) ?> dias</span></article>
            <article class="card"><small>Etapas concluídas</small><strong><?= $etapasConcluidas ?>/<?= $totalEtapas ?></strong><span class="stat-detail">Trilhas de álgebra</span></article>
        </section>

        <div class="profile-section-head"><h2>Continue aprendendo</h2><p>Escolha uma atividade para retomar sua jornada.</p></div>
        <nav class="quick-links" aria-label="Atalhos do perfil">
            <a class="quick-link" href="<?= app_route('/aluno/trilha') ?>"><span aria-hidden="true">✦</span><strong>Minha trilha</strong><small>Avance pelas etapas liberadas.</small></a>
            <a class="quick-link" href="<?= app_route('/aluno/simulados') ?>"><span aria-hidden="true">◌</span><strong>Simulados</strong><small>Teste seus conhecimentos.</small></a>
            <a class="quick-link" href="<?= app_route('/ranking') ?>"><span aria-hidden="true">♕</span><strong>Ranking</strong><small>Veja sua posição entre os alunos.</small></a>
        </nav>
    </main>

    <script>
        (() => {
            const input = document.querySelector('#foto_perfil');
            const preview = document.querySelector('#avatar-preview');
            const initial = document.querySelector('#avatar-initial');
            const help = document.querySelector('#photo-help');
            if (!input || !preview || !initial || !help) return;

            input.addEventListener('change', () => {
                const [file] = input.files;
                if (!file) return;
                if (!['image/jpeg', 'image/png', 'image/webp'].includes(file.type) || file.size > 3 * 1024 * 1024) {
                    input.value = '';
                    help.textContent = 'Use JPG, PNG ou WEBP de até 3 MB.';
                    return;
                }
                preview.src = URL.createObjectURL(file);
                preview.classList.add('is-visible');
                initial.hidden = true;
                help.textContent = `${file.name} selecionada`;
            });
        })();
    </script>
</body>
</html>
