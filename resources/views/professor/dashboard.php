<?php
$perfil = $perfil ?? [];
$resumo = $resumo ?? ['videos' => 0, 'atividades' => 0, 'questoes' => 0];
$videos = $videos ?? [];
$atividades = $atividades ?? [];
$nome = trim((string) ($perfil['nome'] ?? $_SESSION['usuario'] ?? 'Professor(a)'));
$email = trim((string) ($perfil['email'] ?? ''));
$siape = trim((string) ($perfil['siape'] ?? ''));
$inicial = function_exists('mb_substr') ? mb_strtoupper(mb_substr($nome, 0, 1, 'UTF-8'), 'UTF-8') : strtoupper(substr($nome, 0, 1));
$siteRoot = rtrim((string) preg_replace('#/public$#', '', APP_URL), '/');
$backgroundDois = $siteRoot . '/imgs/background2.png';
$formatarData = static function ($data): string {
    $timestamp = $data ? strtotime((string) $data) : false;
    return $timestamp === false ? 'Data não informada' : date('d/m/Y', $timestamp);
};
?>
<!doctype html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Perfil do professor | HSchuler</title>
    <link rel="stylesheet" href="<?= app_asset('css/estilo_homepage.css') ?>?v=41">
    <style>
        :root { color-scheme: dark; }
        * { box-sizing: border-box; }
        body.teacher-profile-page { display: block; min-height: 100svh; margin: 0; overflow-x: hidden; background: #02050a url("<?= htmlspecialchars($backgroundDois, ENT_QUOTES, 'UTF-8') ?>") center top / cover fixed; color: #edf4ff; font-family: Arial, Helvetica, sans-serif; }
        body.teacher-profile-page::before { position: fixed; z-index: 0; inset: 0; content: ""; pointer-events: none; background: radial-gradient(circle at 18% 8%, rgba(37, 130, 255, .2), transparent 28%), linear-gradient(180deg, rgba(1, 5, 15, .3), rgba(1, 4, 12, .9)); }
        body.teacher-profile-page > * { position: relative; z-index: 1; }
        body.teacher-profile-page .site-nav { z-index: 100; }
        .teacher-profile-shell { width: min(1200px, calc(100% - 48px)); margin: 28px auto 72px; }
        .teacher-profile-hero { display: grid; grid-template-columns: minmax(0, 1fr) auto; gap: 32px; align-items: center; padding: clamp(25px, 4vw, 42px); border: 1px solid rgba(121, 176, 255, .38); border-radius: 23px; background: linear-gradient(135deg, rgba(12, 35, 75, .94), rgba(3, 12, 31, .93)); box-shadow: inset 0 1px rgba(235, 246, 255, .1), 0 26px 55px rgba(0, 0, 0, .31); }
        .teacher-profile-intro { display: flex; align-items: center; gap: clamp(18px, 3vw, 28px); min-width: 0; }
        .teacher-avatar { display: grid; width: 92px; height: 92px; flex: 0 0 92px; place-items: center; border: 2px solid rgba(166, 211, 255, .9); border-radius: 50%; background: linear-gradient(145deg, #4d86ec, #3237a9 58%, #622ca7); box-shadow: 0 0 0 7px rgba(79, 140, 255, .12), 0 15px 30px rgba(3, 23, 76, .45); color: #fff; font-size: 2.4rem; font-weight: 700; }
        .teacher-profile-kicker { margin: 0 0 8px; color: #8fc7ff; font-size: .72rem; font-weight: 700; letter-spacing: .12em; text-transform: uppercase; }
        .teacher-profile-intro h1 { margin: 0; color: #fff; font-size: clamp(1.8rem, 4vw, 2.8rem); letter-spacing: -.045em; }
        .teacher-profile-intro p:not(.teacher-profile-kicker) { margin: 9px 0 0; color: #bdcce4; line-height: 1.5; }
        .teacher-contact { display: inline-flex; flex-wrap: wrap; gap: 8px 17px; margin-top: 14px; color: #dceaff; font-size: .79rem; }
        .teacher-contact span::before { margin-right: 6px; color: #75b6ff; content: "•"; }
        .teacher-hero-action { display: inline-flex; min-height: 43px; align-items: center; justify-content: center; padding: 0 16px; border: 1px solid rgba(156, 205, 255, .82); border-radius: 10px; background: linear-gradient(135deg, #258edc, #3445bb); box-shadow: 0 8px 20px rgba(35, 97, 213, .31); color: #fff; font-size: .82rem; font-weight: 700; text-align: center; text-decoration: none; transition: filter .18s ease, transform .18s ease; }
        .teacher-hero-action:hover, .teacher-hero-action:focus-visible { filter: brightness(1.12); outline: 2px solid #b3dcff; outline-offset: 3px; transform: translateY(-2px); }
        .teacher-section-head { display: flex; align-items: end; justify-content: space-between; gap: 20px; margin: 37px 0 16px; }
        .teacher-section-head h2 { margin: 0; color: #fff; font-size: 1.26rem; }
        .teacher-section-head p { margin: 0; color: #aebfda; font-size: .84rem; }
        .teacher-metrics { display: grid; grid-template-columns: repeat(3, minmax(0, 1fr)); gap: 15px; }
        .teacher-metric { min-height: 142px; padding: 20px; border: 1px solid rgba(112, 164, 243, .29); border-radius: 17px; background: linear-gradient(145deg, rgba(13, 34, 71, .88), rgba(5, 13, 31, .92)); box-shadow: inset 0 1px rgba(231, 243, 255, .06); }
        .teacher-metric small { color: #a9bddc; font-size: .76rem; }
        .teacher-metric strong { display: block; margin-top: 15px; color: #fff; font-size: clamp(1.8rem, 3.8vw, 2.5rem); letter-spacing: -.055em; }
        .teacher-metric span { display: block; margin-top: 5px; color: #7ca8e5; font-size: .73rem; }
        .teacher-content-grid { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 18px; }
        .teacher-panel { overflow: hidden; border: 1px solid rgba(112, 164, 243, .31); border-radius: 18px; background: linear-gradient(145deg, rgba(11, 31, 65, .92), rgba(4, 12, 29, .93)); box-shadow: inset 0 1px rgba(237, 247, 255, .07), 0 15px 34px rgba(0, 0, 0, .22); }
        .teacher-panel-head { display: flex; align-items: center; justify-content: space-between; gap: 14px; padding: 19px 20px; border-bottom: 1px solid rgba(121, 167, 232, .19); }
        .teacher-panel-head h3 { margin: 0; color: #fff; font-size: 1rem; }
        .teacher-panel-head a { color: #9dcbff; font-size: .76rem; font-weight: 700; text-decoration: none; }
        .teacher-panel-head a:hover, .teacher-panel-head a:focus-visible { color: #fff; outline: none; }
        .teacher-entry { padding: 17px 20px; border-bottom: 1px solid rgba(121, 167, 232, .14); }
        .teacher-entry:last-child { border-bottom: 0; }
        .teacher-entry-top { display: flex; align-items: start; justify-content: space-between; gap: 12px; }
        .teacher-entry h4 { margin: 0; color: #f2f7ff; font-size: .93rem; line-height: 1.35; }
        .teacher-entry p { display: -webkit-box; overflow: hidden; margin: 8px 0 0; color: #aebfda; font-size: .79rem; line-height: 1.45; -webkit-box-orient: vertical; -webkit-line-clamp: 2; }
        .teacher-entry-meta { display: inline-flex; flex-wrap: wrap; gap: 7px; margin-top: 12px; color: #94b4df; font-size: .69rem; }
        .teacher-tag { padding: 5px 7px; border: 1px solid rgba(105, 167, 245, .29); border-radius: 6px; background: rgba(49, 108, 202, .14); color: #bfdcff; }
        .teacher-entry-link { flex: 0 0 auto; color: #9eceff; font-size: .73rem; font-weight: 700; text-decoration: none; }
        .teacher-entry-link:hover, .teacher-entry-link:focus-visible { color: #fff; outline: none; }
        .teacher-empty { margin: 0; padding: 31px 22px; color: #aebfda; font-size: .84rem; line-height: 1.55; text-align: center; }
        .teacher-empty a { color: #9dcbff; font-weight: 700; text-decoration: none; }
        .teacher-empty a:hover, .teacher-empty a:focus-visible { color: #fff; outline: none; }
        @media (max-width: 800px) { .teacher-profile-hero { grid-template-columns: 1fr; } .teacher-hero-action { justify-self: start; } .teacher-content-grid { grid-template-columns: 1fr; } }
        @media (max-width: 590px) { body.teacher-profile-page .site-nav, .teacher-profile-shell { width: min(calc(100% - 28px), 560px); } .teacher-profile-shell { margin-top: 16px; margin-bottom: 42px; } .teacher-profile-hero { padding: 23px 18px; border-radius: 18px; } .teacher-profile-intro { align-items: flex-start; flex-direction: column; } .teacher-avatar { width: 76px; height: 76px; flex-basis: 76px; font-size: 2rem; } .teacher-metrics { grid-template-columns: 1fr; } .teacher-metric { min-height: 115px; } .teacher-section-head { align-items: start; flex-direction: column; margin-top: 30px; } }
    </style>
</head>
<body class="teacher-profile-page">
    <?php $navbarActive = ''; require APP_ROOT . '/resources/views/layouts/navbar.php'; ?>

    <main class="teacher-profile-shell">
        <section class="teacher-profile-hero" aria-labelledby="teacher-profile-title">
            <div class="teacher-profile-intro">
                <div class="teacher-avatar" aria-hidden="true"><?= htmlspecialchars($inicial, ENT_QUOTES, 'UTF-8') ?></div>
                <div>
                    <p class="teacher-profile-kicker">Perfil do professor</p>
                    <h1 id="teacher-profile-title"><?= htmlspecialchars($nome, ENT_QUOTES, 'UTF-8') ?></h1>
                    <p>Organize os conteúdos que você publica e acompanhe o material disponível para os alunos.</p>
                    <div class="teacher-contact">
                        <?php if ($email !== ''): ?><span><?= htmlspecialchars($email, ENT_QUOTES, 'UTF-8') ?></span><?php endif; ?>
                        <?php if ($siape !== ''): ?><span>SIAPE <?= htmlspecialchars($siape, ENT_QUOTES, 'UTF-8') ?></span><?php endif; ?>
                    </div>
                </div>
            </div>
            <a class="teacher-hero-action" href="<?= app_route('/professor/videos') ?>">Gerenciar videoaulas</a>
        </section>

        <header class="teacher-section-head"><h2>Seu conteúdo</h2><p>Resumo do material cadastrado por você.</p></header>
        <section class="teacher-metrics" aria-label="Resumo de conteúdos cadastrados">
            <article class="teacher-metric"><small>Videoaulas cadastradas</small><strong><?= (int) $resumo['videos'] ?></strong><span>Disponíveis para os alunos</span></article>
            <article class="teacher-metric"><small>Atividades cadastradas</small><strong><?= (int) $resumo['atividades'] ?></strong><span>Materiais de prática criados</span></article>
            <article class="teacher-metric"><small>Questões cadastradas</small><strong><?= (int) $resumo['questoes'] ?></strong><span>Itens vinculados às atividades</span></article>
        </section>

        <header class="teacher-section-head"><h2>Publicações recentes</h2><p>Os últimos materiais que você cadastrou.</p></header>
        <section class="teacher-content-grid" aria-label="Conteúdos recentes do professor">
            <article class="teacher-panel">
                <header class="teacher-panel-head"><h3>Videoaulas</h3><a href="<?= app_route('/professor/videos') ?>">Gerenciar videoaulas →</a></header>
                <?php if ($videos === []): ?>
                    <p class="teacher-empty">Você ainda não cadastrou videoaulas. <a href="<?= app_route('/professor/videos') ?>">Publicar a primeira</a></p>
                <?php else: ?>
                    <?php foreach ($videos as $video): ?>
                        <article class="teacher-entry">
                            <div class="teacher-entry-top"><h4><?= htmlspecialchars($video['titulo'], ENT_QUOTES, 'UTF-8') ?></h4><span class="teacher-entry-link">Publicada</span></div>
                            <?php if (trim((string) ($video['descricao'] ?? '')) !== ''): ?><p><?= htmlspecialchars((string) $video['descricao'], ENT_QUOTES, 'UTF-8') ?></p><?php endif; ?>
                            <div class="teacher-entry-meta"><span class="teacher-tag"><?= htmlspecialchars((string) ($video['modulo_nome'] ?? 'Sem módulo'), ENT_QUOTES, 'UTF-8') ?></span><span><?= htmlspecialchars($formatarData($video['created_at'] ?? null), ENT_QUOTES, 'UTF-8') ?></span></div>
                        </article>
                    <?php endforeach; ?>
                <?php endif; ?>
            </article>

            <article class="teacher-panel">
                <header class="teacher-panel-head"><h3>Atividades</h3><a href="<?= app_route('/professor/atividades') ?>">Ver atividades →</a></header>
                <?php if ($atividades === []): ?>
                    <p class="teacher-empty">Nenhuma atividade cadastrada até o momento.</p>
                <?php else: ?>
                    <?php foreach ($atividades as $atividade): ?>
                        <article class="teacher-entry">
                            <div class="teacher-entry-top"><h4><?= htmlspecialchars($atividade['titulo'], ENT_QUOTES, 'UTF-8') ?></h4><span class="teacher-entry-link"><?= (int) $atividade['total_questoes'] ?> questões</span></div>
                            <?php if (trim((string) ($atividade['descricao'] ?? '')) !== ''): ?><p><?= htmlspecialchars((string) $atividade['descricao'], ENT_QUOTES, 'UTF-8') ?></p><?php endif; ?>
                            <div class="teacher-entry-meta"><span class="teacher-tag"><?= htmlspecialchars((string) ($atividade['modulo_nome'] ?? 'Sem módulo'), ENT_QUOTES, 'UTF-8') ?></span><span class="teacher-tag"><?= htmlspecialchars(ucfirst((string) ($atividade['dificuldade'] ?? 'médio')), ENT_QUOTES, 'UTF-8') ?></span><span><?= htmlspecialchars($formatarData($atividade['created_at'] ?? null), ENT_QUOTES, 'UTF-8') ?></span></div>
                        </article>
                    <?php endforeach; ?>
                <?php endif; ?>
            </article>
        </section>
    </main>
</body>
</html>
