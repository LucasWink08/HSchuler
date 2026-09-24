<?php
$area = $area ?? 'potenciacao';
$questoes = $questoes ?? [];
$areaLabel = $this->questaoService->getAreaLabel($area);
$siteRoot = rtrim((string) preg_replace('#/public$#', '', APP_URL), '/');
$planetaUm = $siteRoot . '/imgs/planeta.png';
$planetaDois = $siteRoot . '/imgs/planeta2.png';
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Questões de <?= htmlspecialchars($areaLabel) ?></title>
    <link rel="stylesheet" href="<?= app_asset('css/estilo_homepage.css') ?>">
    <style>
        body.activity-questions-page { position: relative; min-height: 100vh; margin: 0; overflow-x: hidden; background: radial-gradient(circle at 50% 10%, rgba(69, 119, 255, .2), transparent 32%), #080824; color: #fff; font-family: Arial, sans-serif; }
        .questions-planets { position: fixed; z-index: 0; inset: 0; overflow: hidden; pointer-events: none; }
        .questions-planet { position: absolute; height: auto; user-select: none; }
        .questions-planet-one { top: 11%; right: -12vw; width: min(355px, 29vw); opacity: .44; filter: drop-shadow(0 0 26px rgba(212, 98, 255, .27)); animation: questions-planet-drift 24s ease-in-out infinite alternate; }
        .questions-planet-two { bottom: -11%; left: -10vw; width: min(385px, 31vw); opacity: .5; filter: drop-shadow(0 0 30px rgba(37, 213, 255, .25)); animation: questions-planet-drift-reverse 28s ease-in-out infinite alternate; }
        @keyframes questions-planet-drift { to { transform: translate(-28px, 25px) rotate(6deg); } }
        @keyframes questions-planet-drift-reverse { to { transform: translate(30px, -23px) rotate(-6deg); } }
        .page { position: relative; z-index: 1; width: min(980px, calc(100% - 32px)); margin: 92px auto 48px; }
        .header { display: flex; justify-content: space-between; align-items: end; gap: 20px; margin-bottom: 24px; }
        .header h1 { margin: 0 0 8px; }
        .header p { margin: 0; color: rgba(255,255,255,.7); }
        .question { margin-bottom: 18px; padding: 22px; border: 1px solid rgba(255,255,255,.18); border-radius: 14px; background: rgba(255,255,255,.06); }
        .question h2 { margin: 0 0 18px; font-size: 1.15rem; }
        .option { display: flex; gap: 10px; align-items: center; padding: 12px; margin-top: 10px; border: 1px solid rgba(255,255,255,.16); border-radius: 8px; cursor: pointer; }
        .option:hover { border-color: #63d9ff; background: rgba(99,217,255,.08); }
        .option input { accent-color: #63d9ff; }
        .actions { display: flex; gap: 12px; flex-wrap: wrap; margin-top: 22px; }
        .button { display: inline-block; padding: 12px 18px; border: 1px solid rgba(255,255,255,.25); border-radius: 8px; color: #fff; background: linear-gradient(45deg, #17104d, #6934d4); box-shadow: 0 4px 0 #1b0754, 0 8px 16px rgba(0,0,0,.35); text-decoration: none; cursor: pointer; transition: transform .18s ease, box-shadow .18s ease, filter .18s ease; }
        .button:hover { transform: translateY(-2px); box-shadow: 0 6px 0 #1b0754, 0 12px 20px rgba(87,24,204,.32); filter: brightness(1.08); }
        .feedback { padding: 14px 16px; border-radius: 10px; margin-bottom: 18px; background: rgba(99,255,154,.12); border: 1px solid #63ff9a; }
        .feedback.error { background: rgba(255,107,107,.12); border-color: #ff6b6b; }
        @media (max-width: 640px) { .header { display: block; } .page { margin-top: 78px; } .questions-planet-one { top: 12%; right: -36vw; width: 250px; opacity: .25; } .questions-planet-two { bottom: -4%; left: -36vw; width: 260px; opacity: .3; } }
        @media (prefers-reduced-motion: reduce) { .questions-planet { animation: none; } }
    </style>
</head>
<body class="activity-questions-page">
    <?php $navbarActive = 'trilhas'; require APP_ROOT . '/resources/views/layouts/navbar.php'; ?>
    <div class="questions-planets" aria-hidden="true">
        <img class="questions-planet questions-planet-one" src="<?= htmlspecialchars($planetaUm, ENT_QUOTES, 'UTF-8') ?>" alt="">
        <img class="questions-planet questions-planet-two" src="<?= htmlspecialchars($planetaDois, ENT_QUOTES, 'UTF-8') ?>" alt="">
    </div>
    <main class="page">
        <header class="header">
            <div>
                <h1><?= htmlspecialchars($areaLabel) ?></h1>
                <p>Responda uma questão por vez e veja a explicação.</p>
            </div>
            <a class="button" href="<?= app_route('/') ?>">Voltar para a home</a>
        </header>

        <?php if ($resultado !== null): ?>
            <div class="feedback <?= $resultado['correta'] ? '' : 'error' ?>">
                <strong><?= $resultado['correta'] ? 'Resposta correta!' : 'Resposta incorreta.' ?></strong>
                <div><?= htmlspecialchars($resultado['explicacao']) ?></div>
            </div>
        <?php endif; ?>

        <?php foreach ($questoes as $indice => $questao): ?>
            <form class="question" method="post" action="<?= app_route('/aluno/questoes') ?>&area=<?= urlencode($area) ?>">
                <h2><?= ($indice + 1) ?>. <?= htmlspecialchars($questao['enunciado']) ?></h2>
                <?php foreach ($questao['alternativas'] as $alternativaIndice => $alternativa): ?>
                    <label class="option">
                        <input type="radio" name="resposta" value="<?= $alternativaIndice ?>" required>
                        <span><?= htmlspecialchars($alternativa) ?></span>
                    </label>
                <?php endforeach; ?>
                <input type="hidden" name="questao" value="<?= $indice ?>">
                <div class="actions">
                    <button class="button" type="submit">Corrigir resposta</button>
                </div>
            </form>
        <?php endforeach; ?>
    </main>
</body>
</html>
