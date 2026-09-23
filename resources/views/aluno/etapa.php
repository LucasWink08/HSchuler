<?php
$area = $area ?? 'potenciacao';
$areaLabel = $areaLabel ?? (new QuestaoService())->getAreaLabel($area);
$etapa = $etapa ?? [];
$questoes = $questoes ?? [];
$resumo = $resumo ?? [];
$resultado = $resultado ?? null;
$resultadosQuestoes = is_array($resultado['questoes'] ?? null) ? $resultado['questoes'] : [];
$total = count($questoes);
$etapasConcluidas = (int) ($resumo['etapas_concluidas'] ?? 0);
$totalEtapas = (int) ($resumo['total_etapas'] ?? 0);
$progressoGeral = $totalEtapas > 0 ? (int) round(($etapasConcluidas / $totalEtapas) * 100) : 0;
$progressoInicial = $total > 0 ? (int) round(100 / $total) : 0;
$etapaNome = (string) ($etapa['nome'] ?? 'Etapa da trilha');
$siteRoot = rtrim((string) preg_replace('#/public$#', '', APP_URL), '/');
$backgroundDois = $siteRoot . '/imgs/background2.png';
$streakImage = $siteRoot . '/imgs/streak.png';
?>
<!doctype html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= htmlspecialchars($etapaNome, ENT_QUOTES, 'UTF-8') ?> | HSchuler</title>
    <link rel="stylesheet" href="<?= app_asset('css/estilo_homepage.css') ?>?v=36">
    <style>
        :root { color-scheme: dark; }
        * { box-sizing: border-box; }
        body.stage-page {
            display: block !important;
            min-height: 100svh;
            margin: 0;
            padding: 0 20px 40px;
            background: #02050a url("<?= htmlspecialchars($backgroundDois, ENT_QUOTES, 'UTF-8') ?>") center top / cover fixed;
            color: #f4f7ff;
            font-family: Arial, Helvetica, sans-serif;
        }
        body.stage-page .site-nav { margin-bottom: 26px; }
        .stage-shell {
            display: grid;
            grid-template-columns: 230px minmax(0, 700px) 270px;
            gap: clamp(20px, 3vw, 48px);
            align-items: start;
            width: min(1400px, 100%);
            margin: 0 auto;
        }
        .stage-card {
            border: 1px solid rgba(106, 145, 206, .3);
            border-radius: 16px;
            background: rgba(5, 10, 21, .88);
            box-shadow: 0 18px 48px rgba(0, 0, 0, .34);
        }
        .stage-summary { padding: 20px; }
        .stage-summary h2, .stage-side h2 { margin: 0; font-size: 1rem; }
        .stage-ring {
            position: relative;
            display: grid;
            width: 118px;
            height: 118px;
            margin: 21px auto;
            place-items: center;
            border-radius: 50%;
            background: conic-gradient(#ba39ff calc(var(--progress) * 1%), #202838 0);
        }
        .stage-ring::before { position: absolute; inset: 10px; border-radius: 50%; background: #090e18; content: ""; }
        .stage-ring > div { position: relative; text-align: center; }
        .stage-ring strong { display: block; font-size: 1.7rem; }
        .stage-ring small { color: #c0cbe0; font-size: .68rem; }
        .stage-stat { padding: 15px 0; border-top: 1px solid rgba(141, 165, 207, .2); color: #bdc8da; font-size: .78rem; }
        .stage-stat b { display: block; margin-top: 7px; color: #c148ff; font-size: .94rem; }
        .stage-main { overflow: hidden; }
        .stage-head { padding: 24px 30px 22px; }
        .stage-eyebrow { margin: 0; color: #c24bff; font-size: .72rem; font-weight: 700; letter-spacing: .06em; text-transform: uppercase; }
        .stage-head h1 { margin: 7px 0; font-size: clamp(1.45rem, 3vw, 2.15rem); }
        .stage-count { margin: 0; color: #b9c4d7; }
        .stage-progress { height: 10px; overflow: hidden; margin: 13px 0 0; border-radius: 999px; background: #202a3a; }
        .stage-progress > i { display: block; width: var(--progress); height: 100%; border-radius: inherit; background: linear-gradient(90deg, #d130ff, #813cf5); transition: width .2s ease; }
        .stage-form { padding: 28px 30px 30px; border-top: 1px solid rgba(141, 165, 207, .2); }
        .stage-question { display: none; margin: 0; padding: 0; border: 0; }
        .stage-question.is-active { display: block; }
        .stage-question legend { width: 100%; margin: 0 0 24px; font-size: clamp(1.2rem, 2.5vw, 1.75rem); font-weight: 600; line-height: 1.35; text-align: center; }
        .stage-option { display: flex; min-height: 70px; align-items: center; gap: 17px; margin: 13px 0; padding: 12px 18px; border: 2px solid #273347; border-radius: 13px; background: rgba(10, 15, 25, .94); cursor: pointer; transition: border-color .18s ease, background .18s ease, box-shadow .18s ease; }
        .stage-option:hover, .stage-option:has(input:focus-visible), .stage-option.is-selected { border-color: #159ff2; background: linear-gradient(90deg, rgba(8, 137, 236, .2), rgba(56, 61, 174, .16)); box-shadow: 0 0 20px rgba(25, 147, 245, .16); }
        .stage-option input { position: absolute; opacity: 0; pointer-events: none; }
        .stage-option-letter { display: grid; width: 42px; height: 42px; flex: 0 0 42px; place-items: center; border-radius: 50%; background: #2a374a; font-weight: 700; }
        .stage-option.is-selected .stage-option-letter { background: linear-gradient(145deg, #1cc5ff, #0879e4); }
        .stage-option-answer { font-size: 1.05rem; line-height: 1.35; }
        .stage-form-error { display: none; margin: 17px 0 0; color: #ffb5b5; }
        .stage-form-error.is-visible { display: block; }
        .stage-actions { display: flex; justify-content: space-between; gap: 12px; margin-top: 28px; }
        .stage-button { display: inline-flex; min-height: 45px; align-items: center; justify-content: center; padding: 11px 18px; border: 1px solid #34445e; border-radius: 10px; background: #101827; color: #edf5ff; font: inherit; font-weight: 700; text-decoration: none; cursor: pointer; transition: filter .18s ease, transform .18s ease; }
        .stage-button:hover, .stage-button:focus-visible { filter: brightness(1.15); outline: none; transform: translateY(-1px); }
        .stage-button-primary { min-width: 225px; border: 0; background: linear-gradient(100deg, #09aaf4, #0b8eed 54%, #8730fc); box-shadow: 0 5px 20px rgba(48, 87, 255, .34); }
        .stage-button[hidden] { display: none; }
        .stage-result { padding: 42px 30px; text-align: center; }
        .stage-result h2 { margin: 0; font-size: 1.75rem; }
        .stage-result p { color: #c0cbe0; line-height: 1.5; }
        .stage-result-values { display: flex; justify-content: center; gap: 28px; flex-wrap: wrap; margin: 26px 0; }
        .stage-result-values strong { display: block; font-size: 1.65rem; }
        .stage-result-values .is-xp { color: #ffcf55; }
        .stage-result-details { display: grid; gap: 13px; margin: 30px 0; text-align: left; }
        .stage-result-details h3 { margin: 0 0 2px; font-size: 1.1rem; }
        .stage-result-question { padding: 16px; border: 1px solid rgba(141, 165, 207, .28); border-left: 5px solid #7f8ca2; border-radius: 12px; background: rgba(12, 20, 34, .7); }
        .stage-result-question.is-correct { border-left-color: #39d878; background: rgba(34, 151, 75, .12); }
        .stage-result-question.is-error { border-left-color: #ff6d79; background: rgba(178, 49, 65, .13); }
        .stage-result-question-header { display: flex; align-items: center; justify-content: space-between; gap: 12px; }
        .stage-result-question-header strong { font-size: .92rem; }
        .stage-result-status { padding: 5px 9px; border-radius: 999px; font-size: .72rem; font-weight: 700; }
        .is-correct .stage-result-status { background: rgba(57, 216, 120, .2); color: #9af5bb; }
        .is-error .stage-result-status { background: rgba(255, 109, 121, .2); color: #ffb4bb; }
        .stage-result-enunciado { margin: 12px 0 10px !important; color: #f4f7ff !important; font-size: .93rem; }
        .stage-result-answer { display: grid; grid-template-columns: minmax(112px, auto) 1fr; gap: 8px 12px; margin: 7px 0; color: #c0cbe0; font-size: .84rem; line-height: 1.4; }
        .stage-result-answer b { color: #e3eafd; }
        .stage-result-answer.is-right b { color: #aaf4c5; }
        .stage-result-explanation { margin: 12px 0 0 !important; color: #b8c7de !important; font-size: .8rem; }
        .stage-notice { margin: 0 30px 28px; padding: 13px; border: 1px solid #ef7171; border-radius: 10px; background: rgba(201, 54, 54, .13); color: #ffd0d0; }
        .stage-side { padding: 20px; }
        .stage-side-row { display: flex; justify-content: space-between; gap: 10px; margin: 17px 0; color: #c0cbe0; font-size: .86rem; }
        .stage-side-row b { color: #c448ff; white-space: nowrap; }
        .stage-side .stage-progress { margin: 0; }
        .stage-streak { margin: 20px 0 0; padding-top: 20px; border-top: 1px solid rgba(141, 165, 207, .2); color: #d6deec; }
        .stage-streak-icon { width: 1.15em; height: 1.15em; margin-right: .18em; object-fit: contain; vertical-align: -.24em; filter: drop-shadow(0 0 6px rgba(255, 132, 35, .72)); }
        .stage-empty { padding: 34px 30px; color: #c0cbe0; line-height: 1.5; text-align: center; }
        @media (max-width: 1180px) { .stage-shell { grid-template-columns: minmax(0, 700px) 270px; } .stage-overview { display: none; } }
        @media (max-width: 820px) {
            body.stage-page { padding: 0 12px 28px; background-attachment: scroll; }
            body.stage-page .site-nav { margin-bottom: 18px; }
            .stage-shell { display: block; width: min(700px, 100%); }
            .stage-side { display: none; }
            .stage-head, .stage-form { padding-right: 20px; padding-left: 20px; }
            .stage-question legend { text-align: left; }
            .stage-option { min-height: 61px; gap: 12px; padding: 10px 12px; }
            .stage-option-letter { width: 38px; height: 38px; flex-basis: 38px; }
            .stage-option-answer { font-size: .98rem; }
            .stage-button-primary { min-width: 0; flex: 1; }
        }
        @media (prefers-reduced-motion: reduce) { .stage-progress > i, .stage-option, .stage-button { transition: none; } }
    </style>
</head>
<body class="stage-page">
    <?php $navbarActive = 'trilhas'; require APP_ROOT . '/resources/views/layouts/navbar.php'; ?>

    <main class="stage-shell">
        <aside class="stage-card stage-summary stage-overview" aria-label="Seu progresso geral">
            <h2>Seu progresso geral</h2>
            <div class="stage-ring" style="--progress:<?= $progressoGeral ?>">
                <div><strong><?= $progressoGeral ?>%</strong><small>concluído</small></div>
            </div>
            <div class="stage-stat">Nível atual<b>Nível <?= (int) ($resumo['nivel'] ?? 1) ?></b></div>
            <div class="stage-stat">Sequência atual<b><img class="stage-streak-icon" src="<?= htmlspecialchars($streakImage, ENT_QUOTES, 'UTF-8') ?>" alt="" aria-hidden="true"><?= (int) ($resumo['streak_atual'] ?? 0) ?> dias</b></div>
            <div class="stage-stat">Etapas concluídas<b><?= $etapasConcluidas ?> de <?= $totalEtapas ?></b></div>
        </aside>

        <section class="stage-card stage-main" aria-labelledby="stage-title">
            <header class="stage-head">
                <p class="stage-eyebrow">Trilha de <?= htmlspecialchars($areaLabel, ENT_QUOTES, 'UTF-8') ?></p>
                <h1 id="stage-title"><?= htmlspecialchars($etapaNome, ENT_QUOTES, 'UTF-8') ?></h1>
                <p class="stage-count" id="stage-count">Questão 1 de <?= $total ?></p>
                <div class="stage-progress" aria-label="Progresso da atividade"><i id="stage-progress" style="--progress:<?= $progressoInicial ?>%"></i></div>
            </header>

            <?php if ($resultado !== null): ?>
                <div class="stage-result">
                    <h2>Etapa concluída!</h2>
                    <div class="stage-result-values">
                        <span><strong><?= (int) $resultado['acertos'] ?></strong>acertos</span>
                        <span><strong><?= (int) $resultado['erros'] ?></strong>erros</span>
                        <span class="is-xp"><strong>+<?= (int) $resultado['xp_recebido'] ?></strong>XP recebido</span>
                    </div>
                    <p><?= $resultado['primeira_conclusao'] ? 'Cada acerto vale 5 XP. A próxima etapa foi liberada e seu progresso foi atualizado.' : 'Resultado registrado. Esta etapa já concedeu a recompensa na primeira conclusão.' ?></p>
                    <?php if ($resultadosQuestoes !== []): ?>
                        <section class="stage-result-details" aria-label="Resultado por questão">
                            <h3>Confira suas respostas</h3>
                            <?php foreach ($resultadosQuestoes as $resultadoQuestao): ?>
                                <?php
                                $acertou = (bool) ($resultadoQuestao['acertou'] ?? false);
                                $indiceEscolhido = (int) ($resultadoQuestao['indice_escolhido'] ?? 0);
                                $indiceCorreto = (int) ($resultadoQuestao['indice_correto'] ?? 0);
                                ?>
                                <article class="stage-result-question <?= $acertou ? 'is-correct' : 'is-error' ?>">
                                    <header class="stage-result-question-header">
                                        <strong>Questão <?= (int) ($resultadoQuestao['numero'] ?? 0) ?></strong>
                                        <span class="stage-result-status"><?= $acertou ? 'Acertou' : 'Errou' ?></span>
                                    </header>
                                    <p class="stage-result-enunciado"><?= htmlspecialchars((string) ($resultadoQuestao['enunciado'] ?? ''), ENT_QUOTES, 'UTF-8') ?></p>
                                    <div class="stage-result-answer">
                                        <b>Sua resposta</b>
                                        <span><?= chr(65 + $indiceEscolhido) ?>. <?= htmlspecialchars((string) ($resultadoQuestao['resposta_escolhida'] ?? ''), ENT_QUOTES, 'UTF-8') ?></span>
                                    </div>
                                    <?php if (!$acertou): ?>
                                        <div class="stage-result-answer is-right">
                                            <b>Resposta correta</b>
                                            <span><?= chr(65 + $indiceCorreto) ?>. <?= htmlspecialchars((string) ($resultadoQuestao['resposta_correta'] ?? ''), ENT_QUOTES, 'UTF-8') ?></span>
                                        </div>
                                    <?php endif; ?>
                                    <?php if (($resultadoQuestao['explicacao'] ?? '') !== ''): ?>
                                        <p class="stage-result-explanation"><?= htmlspecialchars((string) $resultadoQuestao['explicacao'], ENT_QUOTES, 'UTF-8') ?></p>
                                    <?php endif; ?>
                                </article>
                            <?php endforeach; ?>
                        </section>
                    <?php endif; ?>
                    <a class="stage-button stage-button-primary" href="<?= app_route('/aluno/trilha') ?>&area=<?= urlencode($area) ?>">Ver trilha atualizada</a>
                </div>
            <?php elseif ($total === 0): ?>
                <div class="stage-empty">Ainda não há questões disponíveis para esta etapa.</div>
            <?php else: ?>
                <?php if ($erroFormulario !== null): ?><p class="stage-notice"><?= htmlspecialchars($erroFormulario, ENT_QUOTES, 'UTF-8') ?></p><?php endif; ?>
                <form class="stage-form" method="post" id="stage-form" novalidate>
                    <?php foreach ($questoes as $indice => $questao): ?>
                        <fieldset class="stage-question<?= $indice === 0 ? ' is-active' : '' ?>">
                            <legend><?= htmlspecialchars((string) $questao['enunciado'], ENT_QUOTES, 'UTF-8') ?></legend>
                            <?php foreach ($questao['alternativas'] as $alternativaIndice => $alternativa): ?>
                                <label class="stage-option">
                                    <input type="radio" name="respostas[<?= $indice ?>]" value="<?= $alternativaIndice ?>" required>
                                    <span class="stage-option-letter"><?= chr(65 + $alternativaIndice) ?></span>
                                    <span class="stage-option-answer"><?= htmlspecialchars((string) $alternativa, ENT_QUOTES, 'UTF-8') ?></span>
                                </label>
                            <?php endforeach; ?>
                        </fieldset>
                    <?php endforeach; ?>
                    <p class="stage-form-error" id="stage-form-error">Escolha uma alternativa para continuar.</p>
                    <div class="stage-actions">
                        <a class="stage-button" href="<?= app_route('/aluno/trilha') ?>&area=<?= urlencode($area) ?>" id="stage-back">← Voltar</a>
                        <button class="stage-button" type="button" id="stage-prev" hidden>← Anterior</button>
                        <button class="stage-button stage-button-primary" type="button" id="stage-next">Próxima questão</button>
                        <button class="stage-button stage-button-primary" type="submit" id="stage-finish" hidden>Finalizar etapa · até 25 XP</button>
                    </div>
                </form>
            <?php endif; ?>
        </section>

        <aside class="stage-card stage-side" aria-label="Progresso da atividade">
            <h2>Seu progresso</h2>
            <div class="stage-side-row"><span id="stage-side-count">Questão 1 de <?= $total ?></span><b>5 XP por acerto</b></div>
            <div class="stage-progress"><i id="stage-side-progress" style="--progress:<?= $progressoInicial ?>%"></i></div>
            <p class="stage-streak"><img class="stage-streak-icon" src="<?= htmlspecialchars($streakImage, ENT_QUOTES, 'UTF-8') ?>" alt="" aria-hidden="true">Sequência de <?= (int) ($resumo['streak_atual'] ?? 0) ?> dias</p>
        </aside>
    </main>

    <?php if ($resultado === null && $total > 0): ?>
        <script>
            (() => {
                const questions = [...document.querySelectorAll('.stage-question')];
                const total = questions.length;
                const next = document.querySelector('#stage-next');
                const previous = document.querySelector('#stage-prev');
                const back = document.querySelector('#stage-back');
                const finish = document.querySelector('#stage-finish');
                const error = document.querySelector('#stage-form-error');
                const form = document.querySelector('#stage-form');
                let active = 0;

                const update = () => {
                    questions.forEach((question, index) => question.classList.toggle('is-active', index === active));
                    const progress = Math.round(((active + 1) / total) * 100);
                    document.querySelector('#stage-count').textContent = `Questão ${active + 1} de ${total}`;
                    document.querySelector('#stage-side-count').textContent = `Questão ${active + 1} de ${total}`;
                    document.querySelector('#stage-progress').style.setProperty('--progress', `${progress}%`);
                    document.querySelector('#stage-side-progress').style.setProperty('--progress', `${progress}%`);
                    previous.hidden = active === 0;
                    back.hidden = active !== 0;
                    next.hidden = active === total - 1;
                    finish.hidden = active !== total - 1;
                    error.classList.remove('is-visible');
                };

                document.querySelectorAll('.stage-option input').forEach((input) => {
                    input.addEventListener('change', () => {
                        document.querySelectorAll(`input[name="${input.name}"]`).forEach((choice) => {
                            choice.closest('.stage-option').classList.toggle('is-selected', choice.checked);
                        });
                        error.classList.remove('is-visible');
                    });
                });

                next.addEventListener('click', () => {
                    if (!questions[active].querySelector(':checked')) {
                        error.classList.add('is-visible');
                        return;
                    }
                    active += 1;
                    update();
                });

                previous.addEventListener('click', () => {
                    active -= 1;
                    update();
                });

                form.addEventListener('submit', (event) => {
                    const unanswered = questions.findIndex((question) => !question.querySelector(':checked'));
                    if (unanswered >= 0) {
                        event.preventDefault();
                        active = unanswered;
                        update();
                        error.classList.add('is-visible');
                    }
                });

                update();
            })();
        </script>
    <?php endif; ?>
</body>
</html>
