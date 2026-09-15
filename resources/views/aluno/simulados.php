<?php
$area = $area ?? 'potenciacao';
$questoes = $questoes ?? [];
$areaLabel = $this->questaoService->getAreaLabel($area);
$totalQuestoes = count($questoes);
$resultado = $resultado ?? null;
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
    </style>
</head>
<body class="exam-page">
<?php $navbarActive = 'simulados'; require APP_ROOT . '/resources/views/layouts/navbar.php'; ?>
<nav class="exam-nav" aria-label="Navegação do simulado" hidden>
    <a class="exam-nav-back" href="<?= app_route('/') ?>">&larr; Voltar para a home</a>
    <div class="exam-nav-links">
        <a href="<?= app_route('/videoaulas') ?>">Videoaulas</a>
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
