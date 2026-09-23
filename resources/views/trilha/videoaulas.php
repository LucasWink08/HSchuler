<?php
$turmaSelecionada = $turmaSelecionada ?? null;
$turmaQuery = $turmaSelecionada !== null ? '&amp;turma_id=' . (int) $turmaSelecionada['id'] : '';
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Videoaulas - Álgebra</title>
  <link rel="stylesheet" type="text/css" href="<?= app_asset('css/estilo_homepage.css') ?>?v=30">
  <style>
    body.video-page {
      position: relative;
      display: block;
      min-height: 100svh;
      overflow-x: hidden;
      overflow-y: auto;
      background: #02050a url("<?= app_asset('images/home/background.png') ?>") center top / cover fixed;
      color: #edf3ff;
      font-family: Arial, Helvetica, sans-serif;
    }

    body.video-page::before {
      content: "";
      position: fixed;
      inset: 0;
      z-index: 0;
      background:
        radial-gradient(circle at 50% 28%, rgba(28, 77, 202, .18), transparent 22%),
        linear-gradient(180deg, rgba(0, 2, 6, .15), rgba(0, 2, 7, .74) 72%, #010205 100%);
      pointer-events: none;
    }

    body.video-page::after {
      content: "";
      position: fixed;
      inset: 0;
      z-index: 0;
      pointer-events: none;
      background-image:
        radial-gradient(2px 2px at 20px 30px, rgba(255,255,255,.9), transparent),
        radial-gradient(2px 2px at 80px 65px, rgba(255,255,255,.8), transparent),
        radial-gradient(1.5px 1.5px at 150px 40px, rgba(255,255,255,.9), transparent),
        radial-gradient(1.5px 1.5px at 210px 100px, rgba(255,255,255,.8), transparent),
        radial-gradient(2px 2px at 270px 40px, rgba(255,255,255,.9), transparent),
        radial-gradient(2px 2px at 340px 190px, rgba(255,255,255,.75), transparent),
        radial-gradient(1px 1px at 420px 120px, rgba(255,255,255,.9), transparent),
        radial-gradient(2px 2px at 500px 80px, rgba(255,255,255,.7), transparent),
        radial-gradient(1.5px 1.5px at 700px 90px, rgba(255,255,255,.8), transparent),
        radial-gradient(1.5px 1.5px at 880px 160px, rgba(255,255,255,.8), transparent);
      background-repeat: repeat;
      background-size: 980px 360px;
      animation: video-stars 30s linear infinite;
      opacity: .8;
    }

    @keyframes video-stars {
      from { transform: translateY(0); }
      to { transform: translateY(-120px); }
    }

    .video-page .home-nav {
      position: relative;
      z-index: 2;
      display: grid;
      grid-template-columns: 120px minmax(0, 1fr) 120px;
      align-items: center;
      width: 100%;
      min-height: 64px;
      padding: 10px 28px 0;
      background: transparent;
    }

    .video-page .home-nav-links,
    .video-page .home-nav-actions {
      display: flex;
      align-items: center;
      justify-content: center;
      gap: 28px;
    }

    .video-page .home-nav-links a,
    .video-page .home-nav-actions a,
    .video-page .home-return {
      color: rgba(233, 239, 255, .85);
      text-decoration: none;
      font-size: .8rem;
      font-weight: 500;
      line-height: 1.2;
      letter-spacing: .01em;
      transition: color .18s ease, transform .18s ease;
    }

    .video-page .home-nav-links a:hover,
    .video-page .home-nav-links a:focus-visible,
    .video-page .home-return:hover,
    .video-page .home-return:focus-visible,
    .video-page .home-nav-actions a:hover,
    .video-page .home-nav-actions a:focus-visible {
      color: #fff;
      outline: none;
    }

    .video-page .home-nav-links a.is-active {
      color: #fff;
      text-shadow: 0 0 12px rgba(121, 151, 255, .8);
    }

    .video-page .home-logo {
      display: inline-flex;
      width: 118px;
      height: 36px;
      align-items: center;
      justify-content: flex-start;
      overflow: visible;
    }

    .video-page .home-logo img {
      width: 100%;
      height: 100%;
      object-fit: contain;
      filter: drop-shadow(0 0 18px rgba(103, 134, 255, .35));
    }

    .video-page .home-return {
      justify-self: start;
      text-align: left;
      margin-left: 8px;
    }

    .video-page .home-nav-actions {
      justify-content: flex-end;
    }

    .video-page .home-nav-actions a {
      padding: 10px 20px;
      border: 1px solid rgba(103, 134, 255, .7);
      border-radius: 10px;
      background: linear-gradient(135deg, #3d3ac7, #1f3b9f);
      box-shadow: inset 0 1px rgba(255,255,255,.25), 0 0 20px rgba(93, 110, 255, .22);
      color: #eff5ff;
    }

    .video-wrap {
      position: relative;
      z-index: 1;
      width: min(1140px, calc(100% - 48px));
      margin: 42px auto 60px;
      color: #fff;
    }

    .video-title {
      text-align: center;
      margin-bottom: 28px;
    }

    .video-title h1 {
      margin: 0;
      font-size: clamp(2rem, 2.6vw, 3.1rem);
      font-weight: 700;
      letter-spacing: -.045em;
      color: #f3f7ff;
      text-shadow: 0 0 18px rgba(124, 154, 255, .2);
    }

    .video-title p {
      margin: 12px auto 0;
      max-width: 540px;
      color: rgba(222, 232, 255, .78);
      font-size: 1rem;
      line-height: 1.6;
    }

    .video-grid {
      display: grid;
      grid-template-columns: repeat(3, minmax(0, 1fr));
      gap: 22px;
      margin-top: 18px;
    }

    .video-card {
      position: relative;
      display: flex;
      flex-direction: column;
      min-height: 210px;
      padding: 18px 18px 16px;
      border: 1px solid rgba(108, 141, 255, .48);
      border-radius: 16px;
      background: linear-gradient(160deg, rgba(10, 20, 40, .92), rgba(4, 8, 22, .8));
      box-shadow: inset 0 1px rgba(176, 205, 255, .08), 0 18px 34px rgba(8, 18, 42, .28);
      cursor: pointer;
      overflow: hidden;
      transition: transform .22s ease, box-shadow .22s ease, border-color .22s ease;
    }

    .video-card::before {
      content: "";
      position: absolute;
      inset: 0;
      background: radial-gradient(circle at 10% 20%, rgba(94, 124, 255, .2), transparent 28%);
      pointer-events: none;
    }

    .video-card:hover,
    .video-card:focus-visible {
      transform: translateY(-4px);
      border-color: rgba(134, 168, 255, .8);
      box-shadow: 0 20px 38px rgba(35, 74, 183, .28), inset 0 1px rgba(191, 212, 255, .12);
      outline: none;
    }

    .video-top {
      position: relative;
      z-index: 1;
      display: flex;
      align-items: center;
      justify-content: space-between;
      gap: 12px;
    }

    .video-badge {
      display: inline-flex;
      align-items: center;
      justify-content: center;
      width: 52px;
      height: 52px;
      border: 1px solid rgba(132, 171, 255, .5);
      border-radius: 14px;
      background: linear-gradient(135deg, rgba(70, 87, 255, .32), rgba(90, 138, 255, .12));
      color: #ebf4ff;
      font-size: 1.2rem;
      font-weight: 700;
      box-shadow: inset 0 1px rgba(255,255,255,.1);
    }

    .video-level {
      color: rgba(222, 232, 255, .82);
      font-size: .68rem;
      letter-spacing: .02em;
      white-space: nowrap;
    }

    .video-name {
      position: relative;
      z-index: 1;
      margin-top: 16px;
      color: #f4f8ff;
      font-size: clamp(1.2rem, 1.7vw, 1.55rem);
      font-weight: 700;
      letter-spacing: -.02em;
    }

    .video-desc {
      position: relative;
      z-index: 1;
      margin-top: 8px;
      color: rgba(204, 219, 255, .8);
      font-size: .92rem;
      line-height: 1.55;
      min-height: 58px;
    }

    .video-actions {
      position: relative;
      z-index: 1;
      display: flex;
      align-items: center;
      justify-content: space-between;
      gap: 12px;
      margin-top: auto;
      padding-top: 16px;
    }

    .video-pill {
      display: inline-flex;
      align-items: center;
      gap: 8px;
      padding: 7px 12px;
      border: 1px solid rgba(130, 169, 255, .52);
      border-radius: 999px;
      background: rgba(89, 118, 255, .12);
      color: #ebf4ff;
      font-size: .7rem;
      font-weight: 600;
      white-space: nowrap;
    }

    .video-link {
      display: inline-flex;
      align-items: center;
      justify-content: center;
      min-width: 90px;
      padding: 9px 16px;
      border: 1px solid rgba(125, 169, 255, .75);
      border-radius: 10px;
      background: linear-gradient(180deg, #4d6aff 0%, #2b4bd6 100%);
      color: #edf3ff;
      text-decoration: none;
      font-size: .75rem;
      font-weight: 700;
      letter-spacing: .01em;
      box-shadow: inset 0 1px rgba(255,255,255,.35), 0 6px 0 rgba(23, 39, 118, .95), 0 12px 22px rgba(53, 86, 219, .32);
      transition: transform .18s ease, box-shadow .18s ease, filter .18s ease;
    }

    .video-card:hover .video-link,
    .video-link:hover,
    .video-link:focus-visible {
      transform: translateY(-2px);
      box-shadow: inset 0 1px rgba(255,255,255,.38), 0 8px 0 rgba(23, 39, 118, .95), 0 16px 24px rgba(53, 86, 219, .34);
      filter: brightness(1.04);
      outline: none;
    }

    .video-link:active {
      transform: translateY(4px);
      box-shadow: inset 0 1px rgba(255,255,255,.2), 0 2px 0 rgba(23, 39, 118, .95), 0 8px 14px rgba(53, 86, 219, .26);
    }

    .video-back { margin: -4px 0 22px; text-align: center; }
    .video-back a { color: #abc8ff; font-size: .82rem; font-weight: 700; text-decoration: none; }
    .video-back a:hover, .video-back a:focus-visible { color: #fff; outline: none; }
    .video-empty { grid-column: 1 / -1; margin: 0; padding: 30px; border: 1px solid rgba(121, 151, 255, .38); border-radius: 15px; background: rgba(13, 27, 56, .64); color: #bfcee9; text-align: center; }

    @media (max-width: 980px) {
      .video-grid { grid-template-columns: repeat(2, minmax(0, 1fr)); }
    }

    @media (max-width: 760px) {
      .video-page .home-nav {
        grid-template-columns: 1fr auto;
        gap: 12px;
        padding: 12px 18px 0;
      }

      .video-page .home-nav-links {
        grid-column: 1 / -1;
        justify-content: flex-start;
        gap: 18px;
        overflow-x: auto;
      }

      .video-page .home-nav-links a,
      .video-page .home-return,
      .video-page .home-nav-actions a {
        font-size: .72rem;
      }

      .video-wrap { width: min(92vw, 620px); }
      .video-card { min-height: 190px; }
    }

    @media (max-width: 620px) {
      .video-grid { grid-template-columns: 1fr; }
      .video-wrap { margin-top: 28px; }
      .video-title p { max-width: 420px; }
    }
  </style>
</head>
<body class="video-page">
  <?php $navbarActive = 'videoaulas'; require APP_ROOT . '/resources/views/layouts/navbar.php'; ?>

  <main class="video-wrap">
    <header class="video-title">
      <?php if ($areaSelecionada !== null): ?>
        <h1>Videoaulas: <?= htmlspecialchars($areaSelecionada['titulo'], ENT_QUOTES, 'UTF-8') ?></h1>
        <p>Assista às aulas publicadas pelos professores para este conteúdo.</p>
      <?php else: ?>
        <h1>Videoaulas de Álgebra</h1>
        <p>Escolha uma área para acessar as aulas publicadas pelos professores.</p>
      <?php endif; ?>
    </header>

    <?php if ($areaSelecionada !== null): ?>
      <p class="video-back"><a href="<?= app_route('/videoaulas') ?><?= $turmaQuery ?>">← Ver todos os conteúdos</a></p>
      <section class="video-grid" aria-label="Videoaulas de <?= htmlspecialchars($areaSelecionada['titulo'], ENT_QUOTES, 'UTF-8') ?>">
        <?php if ($videos === []): ?>
          <p class="video-empty">Ainda não há videoaulas publicadas para este conteúdo.</p>
        <?php else: ?>
          <?php foreach ($videos as $video): ?>
            <article class="video-card">
              <div class="video-top">
                <div class="video-badge" aria-hidden="true"><?= htmlspecialchars($areaSelecionada['icone'], ENT_QUOTES, 'UTF-8') ?></div>
                <div class="video-level">Publicada pelo professor</div>
              </div>
              <h2 class="video-name"><?= htmlspecialchars($video['titulo'], ENT_QUOTES, 'UTF-8') ?></h2>
              <p class="video-desc"><?= htmlspecialchars((string) ($video['descricao'] ?: 'Assista à videoaula e avance nos estudos.'), ENT_QUOTES, 'UTF-8') ?></p>
              <div class="video-actions">
                <span class="video-pill">Professor <?= htmlspecialchars($video['professor_nome'], ENT_QUOTES, 'UTF-8') ?></span>
                <a class="video-link" href="<?= app_route('/videoaulas/assistir') ?>&amp;id=<?= (int) $video['id'] ?>&amp;area=<?= urlencode($areaSelecionada['slug']) ?><?= $turmaQuery ?>">Assistir</a>
              </div>
            </article>
          <?php endforeach; ?>
        <?php endif; ?>
      </section>
    <?php else: ?>
      <section class="video-grid" aria-label="Conteúdos de videoaulas">
        <?php foreach ($areas as $area): ?>
          <article class="video-card">
            <div class="video-top">
              <div class="video-badge" aria-hidden="true"><?= htmlspecialchars($area['icone'], ENT_QUOTES, 'UTF-8') ?></div>
              <div class="video-level">Nível: <?= htmlspecialchars($area['nivel'], ENT_QUOTES, 'UTF-8') ?></div>
            </div>
            <h2 class="video-name"><?= htmlspecialchars($area['titulo'], ENT_QUOTES, 'UTF-8') ?></h2>
            <p class="video-desc"><?= htmlspecialchars($area['descricao'], ENT_QUOTES, 'UTF-8') ?></p>
            <div class="video-actions">
              <span class="video-pill"><?= (int) $area['video_count'] ?> <?= (int) $area['video_count'] === 1 ? 'aula' : 'aulas' ?></span>
              <a class="video-link" href="<?= app_route('/videoaulas') ?>&amp;area=<?= urlencode($area['slug']) ?><?= $turmaQuery ?>">Ver aulas</a>
            </div>
          </article>
        <?php endforeach; ?>
      </section>
    <?php endif; ?>
  </main>
</body>
</html>

