<?php
$video = $video ?? [];
$player = $player ?? ['tipo' => 'externo', 'url' => '', 'provedor' => 'Site externo'];
$areaSelecionada = $areaSelecionada ?? null;
$turmaSelecionada = $turmaSelecionada ?? null;
$siteRoot = rtrim((string) preg_replace('#/public$#', '', APP_URL), '/');
$backgroundDois = $siteRoot . '/imgs/background2.png';
$titulo = (string) ($video['titulo'] ?? 'Videoaula');
$descricao = trim((string) ($video['descricao'] ?? ''));
$professor = trim((string) ($video['professor_nome'] ?? 'Professor(a)'));
$conteudo = $areaSelecionada['titulo'] ?? ($video['modulo_nome'] ?? 'Álgebra');
$voltarUrl = app_route('/videoaulas')
    . ($areaSelecionada !== null ? '&area=' . urlencode((string) $areaSelecionada['slug']) : '')
    . ($turmaSelecionada !== null ? '&turma_id=' . (int) $turmaSelecionada['id'] : '');
$dataPublicacao = !empty($video['created_at']) ? strtotime((string) $video['created_at']) : false;
$dataFormatada = $dataPublicacao !== false ? date('d/m/Y', $dataPublicacao) : null;
?>
<!doctype html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= htmlspecialchars($titulo, ENT_QUOTES, 'UTF-8') ?> | HSchuler</title>
    <link rel="stylesheet" href="<?= app_asset('css/estilo_homepage.css') ?>?v=41">
    <style>
        :root { color-scheme: dark; }
        * { box-sizing: border-box; }
        body.watch-page { display: block; min-height: 100svh; margin: 0; overflow-x: hidden; background: #02050a url("<?= htmlspecialchars($backgroundDois, ENT_QUOTES, 'UTF-8') ?>") center top / cover fixed; color: #edf4ff; font-family: Arial, Helvetica, sans-serif; }
        body.watch-page::before { position: fixed; z-index: 0; inset: 0; content: ""; pointer-events: none; background: radial-gradient(circle at 50% 10%, rgba(55, 123, 255, .21), transparent 30%), linear-gradient(180deg, rgba(1, 5, 16, .38), rgba(1, 4, 12, .88)); }
        body.watch-page > * { position: relative; z-index: 1; }
        body.watch-page .site-nav { z-index: 100; }
        .watch-shell { width: min(1320px, calc(100% - 48px)); margin: 22px auto 72px; }
        .watch-back { display: inline-flex; align-items: center; gap: 8px; margin-bottom: 25px; color: #a9c9ff; font-size: .83rem; font-weight: 700; text-decoration: none; transition: color .18s ease, transform .18s ease; }
        .watch-back:hover, .watch-back:focus-visible { color: #fff; outline: none; transform: translateX(-3px); }
        .watch-heading { max-width: 890px; margin-bottom: 26px; }
        .watch-heading p { margin: 0 0 9px; color: #7db8ff; font-size: .74rem; font-weight: 700; letter-spacing: .12em; text-transform: uppercase; }
        .watch-heading h1 { max-width: 940px; margin: 0; color: #f7fbff; font-size: clamp(1.75rem, 3.4vw, 3rem); letter-spacing: -.045em; line-height: 1.1; }
        .watch-layout { display: grid; grid-template-columns: minmax(0, 1fr) 285px; gap: clamp(20px, 3vw, 36px); align-items: start; }
        .watch-main { min-width: 0; }
        .watch-player { position: relative; overflow: hidden; aspect-ratio: 16 / 9; border: 1px solid rgba(126, 180, 255, .45); border-radius: 20px; background: #01040b; box-shadow: 0 26px 62px rgba(0, 0, 0, .43), inset 0 1px rgba(255, 255, 255, .1); }
        .watch-player iframe, .watch-player video { display: block; width: 100%; height: 100%; border: 0; background: #000; }
        .watch-external { display: grid; width: 100%; height: 100%; padding: clamp(24px, 6vw, 70px); place-items: center; text-align: center; background: radial-gradient(circle at 50% 42%, rgba(35, 102, 233, .27), transparent 30%), linear-gradient(140deg, #071731, #020711); }
        .watch-external-inner { max-width: 440px; }
        .watch-external-mark { display: grid; width: 76px; height: 76px; margin: 0 auto 20px; place-items: center; border: 1px solid rgba(138, 196, 255, .8); border-radius: 50%; background: linear-gradient(135deg, #209ce8, #304bd2); box-shadow: 0 0 0 9px rgba(73, 146, 255, .12), 0 12px 28px rgba(16, 84, 216, .35); color: #fff; font-size: 1.8rem; }
        .watch-external h2 { margin: 0; color: #f5f9ff; font-size: clamp(1.1rem, 2vw, 1.45rem); }
        .watch-external p { margin: 12px auto 22px; color: #b9cbe5; font-size: .9rem; line-height: 1.55; }
        .watch-external a { display: inline-flex; min-height: 42px; align-items: center; padding: 0 17px; border: 1px solid rgba(151, 204, 255, .84); border-radius: 10px; background: linear-gradient(135deg, #1c91de, #3148c9); box-shadow: 0 7px 17px rgba(31, 93, 218, .31); color: #fff; font-size: .85rem; font-weight: 700; text-decoration: none; }
        .watch-external a:hover, .watch-external a:focus-visible { filter: brightness(1.13); outline: 2px solid #b6dcff; outline-offset: 3px; }
        .watch-details, .watch-sidebar { border: 1px solid rgba(116, 168, 246, .27); border-radius: 17px; background: linear-gradient(145deg, rgba(10, 29, 62, .91), rgba(3, 11, 28, .92)); box-shadow: inset 0 1px rgba(230, 241, 255, .08), 0 17px 36px rgba(0, 0, 0, .24); }
        .watch-details { margin-top: 20px; padding: clamp(20px, 3vw, 30px); }
        .watch-details h2, .watch-sidebar h2 { margin: 0; color: #fff; font-size: 1.05rem; }
        .watch-details p { margin: 13px 0 0; color: #c3d0e5; line-height: 1.68; white-space: pre-line; }
        .watch-sidebar { position: sticky; top: 18px; padding: 22px; }
        .watch-sidebar > p { margin: 7px 0 20px; color: #92a9cd; font-size: .8rem; line-height: 1.45; }
        .watch-meta { display: grid; gap: 14px; margin: 0; }
        .watch-meta div { padding-bottom: 14px; border-bottom: 1px solid rgba(129, 164, 221, .19); }
        .watch-meta div:last-child { padding-bottom: 0; border-bottom: 0; }
        .watch-meta dt { margin-bottom: 5px; color: #88bfff; font-size: .68rem; font-weight: 700; letter-spacing: .08em; text-transform: uppercase; }
        .watch-meta dd { margin: 0; color: #edf4ff; font-size: .87rem; line-height: 1.4; }
        .watch-provider { display: inline-flex; align-items: center; gap: 7px; margin-top: 20px; padding: 8px 10px; border: 1px solid rgba(101, 160, 245, .38); border-radius: 8px; background: rgba(55, 109, 207, .16); color: #bdd6ff; font-size: .75rem; }
        .watch-provider::before { width: 7px; height: 7px; border-radius: 50%; background: #62b8ff; box-shadow: 0 0 9px #3b9fff; content: ""; }
        .watch-source { display: inline-flex; margin-top: 16px; color: #9dc9ff; font-size: .78rem; font-weight: 700; text-decoration: none; }
        .watch-source:hover, .watch-source:focus-visible { color: #fff; outline: none; }
        @media (max-width: 920px) { .watch-layout { grid-template-columns: 1fr; } .watch-sidebar { position: static; display: grid; grid-template-columns: minmax(180px, .8fr) 1.2fr; column-gap: 26px; } .watch-sidebar > p, .watch-provider, .watch-source { grid-column: 1; } .watch-meta { grid-column: 2; grid-row: 1 / span 4; } }
        @media (max-width: 620px) { body.watch-page .site-nav, .watch-shell { width: min(calc(100% - 28px), 600px); } .watch-shell { margin-top: 16px; margin-bottom: 42px; } .watch-back { margin-bottom: 18px; } .watch-heading { margin-bottom: 20px; } .watch-player { border-radius: 13px; } .watch-details, .watch-sidebar { border-radius: 14px; } .watch-sidebar { display: block; } .watch-meta { margin-top: 19px; } }
    </style>
</head>
<body class="watch-page">
    <?php $navbarActive = 'videoaulas'; require APP_ROOT . '/resources/views/layouts/navbar.php'; ?>

    <main class="watch-shell">
        <a class="watch-back" href="<?= htmlspecialchars($voltarUrl, ENT_QUOTES, 'UTF-8') ?>" aria-label="Voltar para as videoaulas">← Voltar para as videoaulas</a>
        <header class="watch-heading">
            <p><?= htmlspecialchars((string) $conteudo, ENT_QUOTES, 'UTF-8') ?> · Videoaula</p>
            <h1><?= htmlspecialchars($titulo, ENT_QUOTES, 'UTF-8') ?></h1>
        </header>

        <section class="watch-layout" aria-label="Reprodução da videoaula">
            <div class="watch-main">
                <div class="watch-player">
                    <?php if ($player['tipo'] === 'embed'): ?>
                        <iframe src="<?= htmlspecialchars($player['url'], ENT_QUOTES, 'UTF-8') ?>" title="<?= htmlspecialchars($titulo, ENT_QUOTES, 'UTF-8') ?>" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" allowfullscreen referrerpolicy="strict-origin-when-cross-origin"></iframe>
                    <?php elseif ($player['tipo'] === 'arquivo'): ?>
                        <video controls playsinline preload="metadata" aria-label="<?= htmlspecialchars($titulo, ENT_QUOTES, 'UTF-8') ?>">
                            <source src="<?= htmlspecialchars($player['url'], ENT_QUOTES, 'UTF-8') ?>">
                            Seu navegador não oferece suporte à reprodução deste vídeo.
                        </video>
                    <?php else: ?>
                        <div class="watch-external">
                            <div class="watch-external-inner">
                                <div class="watch-external-mark" aria-hidden="true">▶</div>
                                <h2>Este vídeo é reproduzido em <?= htmlspecialchars($player['provedor'], ENT_QUOTES, 'UTF-8') ?></h2>
                                <p>Abra o conteúdo em uma nova aba para assistir à videoaula completa.</p>
                                <a href="<?= htmlspecialchars($player['url'], ENT_QUOTES, 'UTF-8') ?>" target="_blank" rel="noopener noreferrer">Abrir vídeo</a>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>

                <article class="watch-details">
                    <h2>Sobre esta aula</h2>
                    <p><?= htmlspecialchars($descricao !== '' ? $descricao : 'Assista à explicação e retome a trilha quando estiver pronto para praticar.', ENT_QUOTES, 'UTF-8') ?></p>
                </article>
            </div>

            <aside class="watch-sidebar" aria-label="Detalhes da videoaula">
                <h2>Detalhes da aula</h2>
                <p>Use os controles do player para pausar, avançar ou assistir em tela cheia.</p>
                <dl class="watch-meta">
                    <div><dt>Conteúdo</dt><dd><?= htmlspecialchars((string) $conteudo, ENT_QUOTES, 'UTF-8') ?></dd></div>
                    <div><dt>Professor(a)</dt><dd><?= htmlspecialchars($professor, ENT_QUOTES, 'UTF-8') ?></dd></div>
                    <?php if ($dataFormatada !== null): ?><div><dt>Publicada em</dt><dd><?= htmlspecialchars($dataFormatada, ENT_QUOTES, 'UTF-8') ?></dd></div><?php endif; ?>
                </dl>
                <span class="watch-provider">Reprodução: <?= htmlspecialchars($player['provedor'], ENT_QUOTES, 'UTF-8') ?></span>
                <a class="watch-source" href="<?= htmlspecialchars((string) $video['url_video'], ENT_QUOTES, 'UTF-8') ?>" target="_blank" rel="noopener noreferrer">Abrir no site de origem ↗</a>
            </aside>
        </section>
    </main>
</body>
</html>
