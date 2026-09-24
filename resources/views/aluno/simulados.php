<?php
$area = $area ?? 'potenciacao';
$questoes = $questoes ?? [];
$areaLabel = $this->questaoService->getAreaLabel($area);
$totalQuestoes = count($questoes);
$resultado = $resultado ?? null;
$siteRoot = rtrim((string) preg_replace('#/public$#', '', APP_URL), '/');
$backgroundDois = $siteRoot . '/imgs/background2.png';
$formatarExpressao = static function (string $texto): string {
    $textoSeguro = htmlspecialchars($texto, ENT_QUOTES, 'UTF-8');
    $textoSeguro = preg_replace('/\^(-?\d+)/', '<sup>$1</sup>', $textoSeguro);

    return str_replace(
        [' * ', ' / ', '-infinito', 'infinito'],
        [' &times; ', ' &divide; ', '-&infin;', '&infin;'],
        $textoSeguro
    );
};
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Simulado de <?= htmlspecialchars($areaLabel) ?></title>
    <link rel="stylesheet" href="<?= app_asset('css/estilo_homepage.css') ?>">
    <style>
        :root { --exam-bg:#050509; --panel:rgba(10,10,17,.88); --line:rgba(255,255,255,.24); --soft:rgba(255,255,255,.72); --purple:#7b22ed; --green:#63d63b; --orange:#ff9800; }
        body.exam-page { display:block; min-height:100vh; overflow-x:hidden; overflow-y:auto; background:var(--exam-bg); color:#fff; }
        .exam-nav { position:static; left:auto; right:auto; top:auto; display:flex; align-items:center; justify-content:space-between; gap:20px; width:min(1440px,calc(100% - 32px)); margin:0 auto; padding:18px 0 0; z-index:1; }
        .exam-nav a { color:var(--soft); text-decoration:none; font-size:.84rem; transition:color .18s ease; }
        .exam-nav a:hover { color:#b479ff; }
        .exam-nav .exam-nav-back { color:#fff; }
        .exam-nav-links { display:flex; align-items:center; gap:22px; }
        .exam-shell { width:min(1440px,calc(100% - 32px)); margin:auto; padding:18px 0 34px; }
        .exam-header { display:grid; grid-template-columns:minmax(220px,1fr) 180px minmax(360px,1.5fr); gap:14px; margin-bottom:20px; }
        .exam-title h1 { margin:0; font-size:clamp(1.8rem,3vw,2.5rem); font-weight:500; }
        .exam-title p { margin:10px 0 0; color:var(--soft); font-size:.86rem; }
        .exam-stat,.progress-panel,.question-panel,.exam-sidebar,.exam-summary { border:1px solid var(--line); border-radius:8px; background:var(--panel); box-shadow:0 12px 30px rgba(0,0,0,.22); }
        .exam-stat { display:grid; grid-template-columns:36px auto; align-items:center; justify-content:center; gap:10px; text-align:left; padding:12px; }
        .exam-stat > div { display:grid; grid-template-columns:52px auto; align-items:center; gap:2px; }
        .exam-stat small { max-width:52px; line-height:1.45; }
        .clock-icon { position:relative; width:31px; height:31px; border:2px solid #8f2cff; border-radius:50%; box-shadow:0 0 12px rgba(123,34,237,.3); }
        .clock-icon::before { content:""; position:absolute; left:14px; top:6px; width:2px; height:9px; background:#8f2cff; transform-origin:bottom; }
        .clock-icon::after { content:""; position:absolute; left:14px; top:14px; width:8px; height:2px; background:#8f2cff; transform:rotate(28deg); transform-origin:left center; }
        .exam-stat small,.progress-label,.sidebar-legend,.summary-label { color:var(--soft); font-size:.74rem; }
        .exam-clock { color:#9b4dff; font-size:1.42rem; font-variant-numeric:tabular-nums; white-space:nowrap; }
        .progress-panel { padding:14px 18px; }
        .progress-top,.progress-bottom { display:flex; align-items:center; justify-content:space-between; gap:14px; }
        .progress-top strong,.progress-bottom span:first-child { color:var(--green); font-size:.74rem; font-weight:400; }
        .progress-track { height:7px; margin:12px 0 8px; overflow:hidden; border-radius:5px; background:rgba(255,255,255,.12); }
        .progress-fill { width:0; height:100%; border-radius:inherit; background:linear-gradient(90deg,#42be25,#a4ec6e); transition:width .25s ease; }
        .exam-layout { display:grid; grid-template-columns:minmax(0,1fr) 330px; gap:18px; }
        .question-panel { padding:22px; }
        .question-step { display:none; min-height:430px; }
        .question-step.is-active { display:block; animation:question-in .24s ease both; }
        @keyframes question-in { from{opacity:0;transform:translateX(8px)} to{opacity:1;transform:translateX(0)} }
        .question-meta { display:flex; align-items:center; justify-content:space-between; gap:16px; margin-bottom:28px; color:var(--soft); font-size:.86rem; }
        .question-number strong { color:#9b4dff; font-size:1.05rem; }
        .subject-tag { padding:7px 12px; border:1px solid var(--purple); border-radius:6px; color:#c68bff; font-size:.76rem; }
        .question-text { max-width:800px; margin:0 0 22px; font-size:1rem; font-weight:400; line-height:1.65; }
        .question-text sup,.answer-option sup { font-size:.72em; line-height:0; }
        .answer-list { display:grid; gap:9px; }
        .answer-option { position:relative; display:flex; align-items:center; gap:14px; min-height:44px; padding:9px 14px; border:1px solid rgba(255,255,255,.2); border-radius:7px; cursor:pointer; color:var(--soft); transition:border-color .18s,background .18s,transform .18s; }
        .answer-option:hover { border-color:#8e3fff; background:rgba(123,34,237,.1); transform:translateX(2px); }
        .answer-option input { position:absolute; opacity:0; pointer-events:none; }
        .answer-letter { display:grid; width:27px; height:27px; flex:0 0 27px; place-items:center; border:1px solid rgba(255,255,255,.5); border-radius:50%; color:#fff; font-size:.78rem; }
        .answer-option.is-selected { border-color:#953dff; background:linear-gradient(90deg,rgba(92,16,190,.7),rgba(48,9,112,.52)); color:#fff; }
        .answer-option.is-selected .answer-letter { border-color:#c287ff; background:var(--purple); }
        .question-actions { display:flex; align-items:center; justify-content:space-between; gap:16px; margin-top:28px; }
        .exam-button { min-height:40px; padding:9px 16px; border:1px solid rgba(255,255,255,.38); border-radius:7px; background:transparent; color:#fff; cursor:pointer; font:inherit; transition:transform .16s,box-shadow .16s,background .16s; }
        .exam-button:hover { transform:translateY(-2px); box-shadow:0 6px 12px rgba(0,0,0,.28); }
        .exam-button:active { transform:translateY(2px); box-shadow:none; }
        .exam-button.primary { border-color:var(--purple); background:linear-gradient(135deg,#6d16d7,#3d0b89); }
        .review-toggle { border:0; background:transparent; color:#b479ff; cursor:pointer; font:inherit; }
        .review-toggle.is-marked { color:#ffad1f; }
        .exam-sidebar { padding:18px; }
        .exam-sidebar h2 { margin:2px 0 16px; font-size:.98rem; font-weight:400; }
        .sidebar-legend { display:flex; flex-wrap:wrap; gap:12px; margin-bottom:16px; font-size:.7rem; }
        .legend-item { display:inline-flex; align-items:center; gap:5px; }
        .legend-dot { width:9px; height:9px; border-radius:50%; background:#65656d; }
        .legend-dot.answered { background:var(--green); }.legend-dot.current { background:var(--purple); }.legend-dot.review { background:var(--orange); }
        .question-nav { display:grid; grid-template-columns:repeat(6,1fr); gap:9px; }
        .question-nav button { min-height:37px; border:1px solid rgba(255,255,255,.3); border-radius:7px; background:transparent; color:#fff; cursor:pointer; font:inherit; transition:transform .16s,background .16s,border-color .16s; }
        .question-nav button:hover { transform:translateY(-2px); border-color:#a75bff; }
        .question-nav button.is-current { border-color:#822bff; background:#6416c8; }.question-nav button.is-answered { border-color:var(--green); color:#baf3a4; }.question-nav button.is-review { border-color:var(--orange); color:#ffc15e; }
        .exam-summary { display:grid; grid-template-columns:repeat(3,1fr); gap:10px; margin-top:24px; padding:16px 10px; text-align:center; }
        .summary-item + .summary-item { border-left:1px solid rgba(255,255,255,.15); }.summary-value { display:block; margin-top:6px; font-size:1.25rem; }.summary-value.green{color:var(--green)}.summary-value.orange{color:var(--orange)}
        .summary-icon { display:block; height:22px; color:#a8a8b2; font-size:1.15rem; line-height:22px; }
        .summary-icon.check { color:var(--green); }.summary-icon.flag { color:var(--orange); }.summary-icon.clock { color:#a8a8b2; }
        .result-banner { margin-bottom:18px; padding:13px 16px; border:1px solid var(--green); border-radius:7px; background:rgba(99,214,59,.1); color:#c8f8b6; }
        @media(max-width:900px){.exam-header{grid-template-columns:1fr 150px}.progress-panel{grid-column:1/-1}.exam-layout{grid-template-columns:1fr}.exam-sidebar{order:-1}}
        @media(max-width:560px){.exam-shell{width:min(100% - 20px,480px);padding-top:16px}.exam-header{grid-template-columns:1fr}.exam-stat{min-height:76px}.question-panel{padding:15px}.question-step{min-height:0}.question-meta{align-items:flex-start;flex-direction:column;margin-bottom:18px}.question-actions{align-items:stretch;flex-wrap:wrap}.question-actions .exam-button{flex:1}.review-toggle{order:-1;width:100%;text-align:left}}

        /* Visual espacial da área do aluno. */
        :root { --exam-bg:#02050c; --panel:linear-gradient(145deg, rgba(13, 33, 71, .93), rgba(4, 12, 30, .94)); --line:rgba(105, 163, 255, .31); --soft:#b9c9e4; --purple:#7d5cff; --green:#62d98e; --orange:#ffbf56; }
        body.exam-page { position:relative; isolation:isolate; font-family:Arial,Helvetica,sans-serif; background:radial-gradient(circle at 76% 13%, rgba(76, 85, 229, .22), transparent 25%),radial-gradient(circle at 16% 77%, rgba(128, 56, 222, .15), transparent 30%),linear-gradient(rgba(1, 5, 16, .58), rgba(1, 4, 13, .94)),url("<?= htmlspecialchars($backgroundDois, ENT_QUOTES, 'UTF-8') ?>") center top / cover fixed,#02050c; }
        body.exam-page::before,body.exam-page::after { position:fixed;z-index:-1;inset:0;content:"";pointer-events:none; }
        body.exam-page::before { background:radial-gradient(ellipse 53% 48% at 48% 46%,rgba(33,108,226,.13),transparent 72%); }
        body.exam-page::after { opacity:.43;background-image:radial-gradient(1.5px 1.5px at 70px 90px,#d8edff,transparent),radial-gradient(1px 1px at 270px 170px,#fff,transparent),radial-gradient(1.5px 1.5px at 560px 72px,#cce5ff,transparent),radial-gradient(1px 1px at 840px 210px,#fff,transparent);background-size:940px 340px; }
        body.exam-page .site-nav { position:relative;z-index:20;width:min(calc(100% - 48px),1240px);min-height:76px;margin:0 auto;padding:16px 0 10px; }
        .exam-shell { position:relative;z-index:1;width:min(1160px,calc(100% - 48px));margin:14px auto 70px;padding:0; }
        .exam-header { grid-template-columns:minmax(240px,1.05fr) minmax(175px,.76fr) minmax(330px,1.45fr);gap:16px;margin-bottom:18px; }
        .exam-title,.exam-stat,.progress-panel,.question-panel,.exam-sidebar,.exam-summary { border-color:var(--line);border-radius:18px;background:var(--panel);box-shadow:inset 0 1px rgba(220,237,255,.08),0 18px 38px rgba(0,0,0,.26); }
        .exam-title { padding:20px 23px; }
        .exam-title h1 { color:#f5f8ff;font-size:clamp(1.65rem,3vw,2.25rem);letter-spacing:-.045em;text-shadow:0 0 24px rgba(91,145,255,.22); }
        .exam-title p { color:#a8bfdf;font-size:.79rem; }
        .exam-stat { grid-template-columns:36px 1fr;padding:18px; }
        .exam-stat > div { grid-template-columns:1fr;gap:4px; }
        .clock-icon { border-color:#8fb7ff;box-shadow:0 0 14px rgba(93,157,255,.42); }
        .clock-icon::before,.clock-icon::after { background:#8fb7ff; }
        .exam-clock { color:#e6f4ff;font-size:1.52rem;text-shadow:0 0 16px rgba(107,165,255,.5); }
        .progress-panel { padding:18px 20px; }
        .progress-top strong,.progress-bottom span:first-child { color:#94d7ff; }
        .progress-track { height:8px;background:rgba(107,145,207,.22);box-shadow:inset 0 1px 2px rgba(0,0,0,.42); }
        .progress-fill { background:linear-gradient(90deg,#1ba6ed,#4b83ff 58%,#a55bff);box-shadow:0 0 12px rgba(67,141,255,.58); }
        .exam-layout { grid-template-columns:minmax(0,1fr) 300px;gap:20px; }
        .question-panel { padding:clamp(22px,3vw,32px); }
        .question-step { min-height:440px; }
        .question-meta { margin-bottom:30px;color:#aabedc; }
        .question-number strong { color:#8bcaff;font-size:1.12rem; }
        .subject-tag { border-color:rgba(116,184,255,.55);border-radius:999px;background:rgba(62,125,230,.13);color:#a9d8ff; }
        .question-text { color:#f2f6ff;font-size:clamp(1rem,1.8vw,1.15rem);line-height:1.7; }
        .answer-list { gap:11px; }
        .answer-option { min-height:60px;padding:11px 15px;border-color:rgba(123,163,225,.28);border-radius:12px;background:rgba(7,17,38,.5);color:#c1d0e6; }
        .answer-option:hover { border-color:#77c5ff;background:linear-gradient(90deg,rgba(23,135,230,.18),rgba(89,74,214,.14));box-shadow:0 0 20px rgba(34,124,236,.12); }
        .answer-letter { width:31px;height:31px;flex-basis:31px;border-color:rgba(159,206,255,.6);background:rgba(56,104,187,.2);color:#eef7ff; }
        .answer-option.is-selected { border-color:#71c7ff;background:linear-gradient(90deg,rgba(22,144,232,.42),rgba(82,61,203,.38));box-shadow:inset 0 1px rgba(255,255,255,.09),0 0 18px rgba(42,127,243,.17); }
        .answer-option.is-selected .answer-letter { border-color:#c7edff;background:linear-gradient(145deg,#29afea,#3769df); }
        .question-actions { margin-top:32px; }
        .exam-button { min-height:44px;border-color:rgba(142,190,255,.54);border-radius:10px;background:rgba(34,69,134,.26);color:#eaf3ff;font-weight:700; }
        .exam-button:hover { border-color:#a8d7ff;background:rgba(50,106,204,.45);box-shadow:0 8px 17px rgba(3,22,67,.3); }
        .exam-button.primary { border-color:#5ca9ff;background:linear-gradient(100deg,#1ca6ef,#2465da 55%,#6333d9);box-shadow:0 6px 18px rgba(36,104,228,.27); }
        .review-toggle { padding:8px;color:#aec8ff;font-weight:700; }.review-toggle.is-marked { color:#ffd077; }
        .exam-sidebar { padding:21px; }
        .exam-sidebar h2 { color:#f4f8ff;font-size:1rem;font-weight:700; }
        .sidebar-legend { color:#aebfdb;line-height:1.5; }.legend-dot { background:#7888a3;box-shadow:0 0 7px rgba(143,176,221,.24); }.legend-dot.answered { background:var(--green); }.legend-dot.current { background:#67baff; }.legend-dot.review { background:var(--orange); }
        .question-nav { gap:8px; }.question-nav button { border-color:rgba(127,169,227,.35);border-radius:9px;background:rgba(8,19,41,.45); }.question-nav button:hover { border-color:#8bcaff;background:rgba(35,108,206,.25); }.question-nav button.is-current { border-color:#98ddff;background:linear-gradient(145deg,#209ce4,#3555c8);box-shadow:0 0 14px rgba(53,131,255,.3); }.question-nav button.is-answered { border-color:#62d98e;color:#bff6d4; }.question-nav button.is-review { border-color:#ffbf56;color:#ffe0a4; }
        .exam-summary { margin-top:20px;padding:16px 10px;border-radius:13px;background:rgba(4,14,33,.49);box-shadow:inset 0 1px rgba(215,237,255,.05); }.summary-value.green { color:#73eb9f; }.summary-value.orange { color:#ffd073; }
        .result-banner { border-color:rgba(104,224,156,.58);border-radius:13px;background:rgba(33,150,84,.16);box-shadow:0 10px 22px rgba(0,0,0,.16);color:#ccf9d9; }
        @media(max-width:900px){.exam-header{grid-template-columns:1fr minmax(175px,.7fr)}.progress-panel{grid-column:1/-1}.exam-layout{grid-template-columns:1fr}.exam-sidebar{order:0;display:grid;grid-template-columns:1fr 1fr;gap:18px}.exam-sidebar h2,.sidebar-legend{grid-column:1}.question-nav{grid-column:2;grid-row:1 / span 2}.exam-summary{grid-column:1;margin-top:0}}
        @media(max-width:620px){body.exam-page .site-nav,.exam-shell{width:min(calc(100% - 28px),520px)}.exam-shell{margin-top:12px}.exam-header{grid-template-columns:1fr}.progress-panel{grid-column:auto}.exam-title,.exam-stat,.progress-panel,.question-panel,.exam-sidebar{border-radius:15px}.exam-sidebar{display:block}.question-nav{margin-top:16px}.exam-summary{margin-top:18px}.question-panel{padding:20px 16px}.question-step{min-height:0}.question-actions{gap:9px}.question-actions .exam-button{flex:1}.review-toggle{order:-1;width:100%;text-align:left}}
        @media(prefers-reduced-motion:reduce){.progress-fill,.question-step,.answer-option,.exam-button,.question-nav button{transition:none;animation:none}}
    </style>
</head>
<body class="exam-page">
<?php $navbarActive = 'simulados'; require APP_ROOT . '/resources/views/layouts/navbar.php'; ?>
<nav class="exam-nav" aria-label="Navegação do simulado" hidden>
    <a class="exam-nav-back" href="<?= app_route('/') ?>">&larr; Voltar para a home</a>
    <div class="exam-nav-links">
        <a href="<?= app_route('/ranking') ?>">Ranking</a>
    </div>
</nav>
<main class="exam-shell">
    <header class="exam-header">
        <div class="exam-title"><h1>Simulado</h1><p>Álgebra &bull; <?= htmlspecialchars($areaLabel) ?> &bull; Prova 1</p></div>
        <div class="exam-stat"><span class="clock-icon" aria-hidden="true"></span><div><small>Tempo restante</small><strong class="exam-clock" id="exam-clock">25:00</strong></div></div>
        <div class="progress-panel"><div class="progress-top"><span class="progress-label">Progresso da prova</span><strong><span id="answered-count">0</span> de <?= $totalQuestoes ?> respondidas</strong></div><div class="progress-track"><div class="progress-fill" id="progress-fill"></div></div><div class="progress-bottom"><span id="progress-percent">0% concluído</span><span><?= $totalQuestoes ?> questões</span></div></div>
    </header>
    <?php if ($resultado !== null): ?><div class="result-banner">Simulado finalizado: <?= (int) $resultado['acertos'] ?> de <?= (int) $resultado['total'] ?> questões corretas (<?= (int) $resultado['percentual'] ?>%).</div><?php endif; ?>
    <form id="exam-form" method="post" action="<?= app_route('/aluno/simulados') ?>&amp;area=<?= urlencode($area) ?>&amp;iniciar=1">
        <div class="exam-layout">
            <section class="question-panel" aria-label="Questões do simulado">
                <?php foreach ($questoes as $indice => $questao): ?>
                    <article class="question-step<?= $indice === 0 ? ' is-active' : '' ?>" data-question="<?= $indice ?>">
                        <div class="question-meta"><span class="question-number">Questão <strong><?= str_pad((string) ($indice + 1), 2, '0', STR_PAD_LEFT) ?></strong> de <?= $totalQuestoes ?></span><span class="subject-tag">Álgebra</span></div>
                        <h2 class="question-text"><?= $formatarExpressao($questao['enunciado']) ?></h2>
                        <div class="answer-list">
                            <?php foreach ($questao['alternativas'] as $alternativaIndice => $alternativa): ?>
                                <label class="answer-option" data-option="<?= $alternativaIndice ?>"><input type="radio" name="respostas[<?= $indice ?>]" value="<?= $alternativaIndice ?>"><span class="answer-letter"><?= chr(65 + $alternativaIndice) ?></span><span><?= $formatarExpressao($alternativa) ?></span></label>
                            <?php endforeach; ?>
                        </div>
                        <div class="question-actions"><button class="exam-button" type="button" data-action="previous">&larr; Anterior</button><button class="review-toggle" type="button" data-action="review">&#9873; Marcar para revisão</button><?php if ($indice < $totalQuestoes - 1): ?><button class="exam-button primary" type="button" data-action="next">Próxima &rarr;</button><?php else: ?><button class="exam-button primary" type="submit">Finalizar simulado</button><?php endif; ?></div>
                    </article>
                <?php endforeach; ?>
            </section>
            <aside class="exam-sidebar"><h2>Navegação da prova</h2><div class="sidebar-legend"><span class="legend-item"><i class="legend-dot answered"></i> Respondida</span><span class="legend-item"><i class="legend-dot current"></i> Atual</span><span class="legend-item"><i class="legend-dot review"></i> Revisão</span><span class="legend-item"><i class="legend-dot"></i> Não respondida</span></div><div class="question-nav" id="question-nav"><?php foreach ($questoes as $indice => $questao): ?><button type="button" data-jump="<?= $indice ?>"><?= str_pad((string) ($indice + 1), 2, '0', STR_PAD_LEFT) ?></button><?php endforeach; ?></div><div class="exam-summary"><div class="summary-item"><span class="summary-icon check" aria-hidden="true">&#10003;</span><span class="summary-label">Respondidas</span><strong class="summary-value green" id="summary-answered">0</strong></div><div class="summary-item"><span class="summary-icon flag" aria-hidden="true">&#9873;</span><span class="summary-label">Marcadas</span><strong class="summary-value orange" id="summary-review">0</strong></div><div class="summary-item"><span class="summary-icon clock" aria-hidden="true">&#9711;</span><span class="summary-label">Restantes</span><strong class="summary-value" id="summary-remaining"><?= $totalQuestoes ?></strong></div></div></aside>
        </div>
    </form>
</main>
<script>
(() => {
    const steps = [...document.querySelectorAll('.question-step')];
    const navButtons = [...document.querySelectorAll('[data-jump]')];
    const total = steps.length;
    let current = 0;
    const review = new Set();
    const clock = document.getElementById('exam-clock');
    let secondsLeft = 25 * 60;
    const getAnswered = () => steps.filter(step => step.querySelector('input[type="radio"]:checked')).length;
    function updateState() {
        const answered = getAnswered();
        const percent = total ? Math.round((answered / total) * 100) : 0;
        document.getElementById('answered-count').textContent = answered;
        document.getElementById('progress-percent').textContent = percent + '% concluído';
        document.getElementById('progress-fill').style.width = percent + '%';
        document.getElementById('summary-answered').textContent = answered;
        document.getElementById('summary-review').textContent = review.size;
        document.getElementById('summary-remaining').textContent = Math.max(total - answered, 0);
        navButtons.forEach((button, index) => { const answeredHere = Boolean(steps[index].querySelector('input[type="radio"]:checked')); button.classList.toggle('is-current', index === current); button.classList.toggle('is-answered', answeredHere); button.classList.toggle('is-review', review.has(index)); });
    }
    function showQuestion(index) { current = Math.max(0, Math.min(index, total - 1)); steps.forEach((step, stepIndex) => step.classList.toggle('is-active', stepIndex === current)); document.querySelectorAll('[data-action="review"]').forEach((button, index) => button.classList.toggle('is-marked', index === current && review.has(current))); updateState(); }
    document.querySelectorAll('.answer-option').forEach(option => option.addEventListener('click', () => { const step = option.closest('.question-step'); step.querySelectorAll('.answer-option').forEach(item => item.classList.remove('is-selected')); option.classList.add('is-selected'); option.querySelector('input').checked = true; updateState(); }));
    document.querySelectorAll('[data-action="next"]').forEach(button => button.addEventListener('click', () => showQuestion(current + 1)));
    document.querySelectorAll('[data-action="previous"]').forEach(button => button.addEventListener('click', () => showQuestion(current - 1)));
    document.querySelectorAll('[data-action="review"]').forEach(button => button.addEventListener('click', () => { review.has(current) ? review.delete(current) : review.add(current); button.classList.toggle('is-marked', review.has(current)); updateState(); }));
    navButtons.forEach(button => button.addEventListener('click', () => showQuestion(Number(button.dataset.jump))));
    function updateClock() { const minutes = String(Math.floor(secondsLeft / 60)).padStart(2, '0'); const seconds = String(secondsLeft % 60).padStart(2, '0'); clock.textContent = minutes + ':' + seconds; if (secondsLeft > 0) secondsLeft -= 1; }
    updateClock(); setInterval(updateClock, 1000); updateState();
})();
</script>
</body>
</html>
