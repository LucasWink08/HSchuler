<?php
$siteRoot = rtrim((string) preg_replace('#/public$#', '', APP_URL), '/');
$backgroundDois = $siteRoot . '/imgs/background2.png';
$totalQuestoes = count($questoesDisponiveis ?? []);
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Preparar simulado - HSchuler</title>
    <link rel="stylesheet" href="<?= app_asset('css/estilo_homepage.css') ?>">
    <style>
        body.pre-simulado {
            display: flex;
            flex-direction: column;
            min-height: 100svh;
            margin: 0;
            overflow-x: hidden;
            color: #fff;
        }

        body.pre-simulado .site-nav {
            position: relative;
            flex: 0 0 auto;
        }

        .pre-simulado-content {
            display: grid;
            width: 100%;
            flex: 1 1 auto;
            place-items: center;
            padding: 30px 24px 64px;
        }

        .prep-card {
            width: min(100%, 570px);
            padding: clamp(28px, 6vw, 48px);
            border: 1px solid rgba(255, 255, 255, .2);
            border-radius: 12px;
            background: linear-gradient(145deg, rgba(20, 10, 49, .92), rgba(5, 5, 12, .94));
            box-shadow: 0 22px 60px rgba(0, 0, 0, .42), 0 0 40px rgba(87, 24, 204, .16);
            text-align: center;
        }

        .prep-icon {
            display: grid;
            width: 58px;
            height: 58px;
            margin: 0 auto 20px;
            place-items: center;
            border: 1px solid #a45dff;
            border-radius: 50%;
            background: rgba(116, 37, 227, .16);
            color: #d7b8ff;
            font-size: 1.6rem;
        }

        .prep-card h1 {
            margin: 0;
            font-size: clamp(1.8rem, 5vw, 2.5rem);
            font-weight: 500;
        }

        .prep-card > p {
            max-width: 410px;
            margin: 14px auto 30px;
            color: rgba(255, 255, 255, .72);
            line-height: 1.6;
        }

        .start-button {
            width: 100%;
            margin-top: 28px;
            padding: 14px 20px;
            border: 1px solid #9444ff;
            border-radius: 9px;
            background: linear-gradient(135deg, #7920e8, #3c087f);
            box-shadow: 0 5px 0 #260654, 0 12px 25px rgba(39, 7, 94, .35);
            color: #fff;
            cursor: pointer;
            font: inherit;
            font-weight: 500;
            transition: transform .18s ease, box-shadow .18s ease, filter .18s ease;
        }

        .start-button:hover { transform: translateY(-2px); box-shadow: 0 7px 0 #260654, 0 16px 28px rgba(87, 24, 204, .38); filter: brightness(1.08); }
        .start-button:active { transform: translateY(2px); box-shadow: 0 2px 0 #260654, 0 5px 12px rgba(39, 7, 94, .35); }

        @media (max-width: 560px) {
            .pre-simulado-content { padding: 22px 16px 38px; }
            .prep-card { padding: 28px 22px; }
        }

        /* Identidade visual compartilhada com as páginas da área do aluno. */
        body.pre-simulado {
            position: relative;
            isolation: isolate;
            background:
                radial-gradient(circle at 75% 16%, rgba(82, 95, 255, .24), transparent 24%),
                radial-gradient(circle at 16% 78%, rgba(152, 65, 238, .16), transparent 30%),
                linear-gradient(rgba(1, 5, 16, .56), rgba(1, 4, 13, .92)),
                url("<?= htmlspecialchars($backgroundDois, ENT_QUOTES, 'UTF-8') ?>") center top / cover fixed,
                #02050c;
            font-family: Arial, Helvetica, sans-serif;
        }

        body.pre-simulado::before,
        body.pre-simulado::after { position: fixed; z-index: -1; inset: 0; content: ""; pointer-events: none; }
        body.pre-simulado::before { background: radial-gradient(ellipse 54% 45% at 50% 48%, rgba(37, 116, 237, .14), transparent 72%); }
        body.pre-simulado::after {
            opacity: .46;
            background-image:
                radial-gradient(1.5px 1.5px at 70px 90px, #d8edff, transparent),
                radial-gradient(1px 1px at 270px 170px, #fff, transparent),
                radial-gradient(1.5px 1.5px at 560px 72px, #cce5ff, transparent),
                radial-gradient(1px 1px at 840px 210px, #fff, transparent);
            background-size: 940px 340px;
        }

        body.pre-simulado .site-nav { z-index: 20; width: min(calc(100% - 48px), 1240px); min-height: 76px; padding: 16px 0 10px; }
        .pre-simulado-content { position: relative; z-index: 1; width: min(calc(100% - 48px), 1080px); margin: 0 auto; padding: clamp(38px, 8vh, 96px) 0 72px; place-items: center; }
        .prep-card {
            position: relative;
            overflow: hidden;
            width: min(100%, 650px);
            padding: clamp(32px, 6vw, 52px);
            border-color: rgba(118, 167, 255, .38);
            border-radius: 24px;
            background: linear-gradient(145deg, rgba(15, 35, 76, .93), rgba(5, 12, 31, .94));
            box-shadow: inset 0 1px rgba(225, 240, 255, .1), 0 26px 70px rgba(0, 0, 0, .38), 0 0 54px rgba(62, 113, 255, .11);
        }
        .prep-card::before { position: absolute; width: 260px; height: 260px; top: -150px; right: -130px; border-radius: 50%; background: radial-gradient(circle, rgba(70, 159, 255, .27), transparent 68%); content: ""; pointer-events: none; }
        .prep-card > * { position: relative; z-index: 1; }
        .prep-icon { width: 72px; height: 72px; margin-bottom: 23px; border-color: rgba(155, 214, 255, .82); border-radius: 20px; background: linear-gradient(145deg, #249fe9, #2841c0); box-shadow: inset 0 2px rgba(255, 255, 255, .35), 0 0 0 7px rgba(87, 144, 255, .1), 0 12px 25px rgba(12, 79, 206, .3); color: #fff; font-size: 2rem; }
        .prep-kicker { margin: 0 0 10px; color: #75caff; font-size: .73rem; font-weight: 700; letter-spacing: .13em; text-transform: uppercase; }
        .prep-card h1 { color: #f6f9ff; font-size: clamp(2rem, 5vw, 3rem); letter-spacing: -.05em; }
        .prep-card > p { margin-bottom: 28px; color: #becde5; font-size: .98rem; }
        .prep-info { display: grid; grid-template-columns: repeat(3, 1fr); gap: 10px; margin: 0; padding: 18px 0; border-top: 1px solid rgba(129, 172, 247, .18); border-bottom: 1px solid rgba(129, 172, 247, .18); }
        .prep-info div { display: grid; gap: 5px; }
        .prep-info div + div { border-left: 1px solid rgba(129, 172, 247, .16); }
        .prep-info dt { color: #93acd1; font-size: .69rem; }
        .prep-info dd { margin: 0; color: #f2f7ff; font-size: .9rem; font-weight: 700; }
        .start-button { min-height: 50px; margin-top: 26px; border-color: #579cff; border-radius: 11px; background: linear-gradient(100deg, #1ca6ef, #2465da 55%, #6333d9); box-shadow: inset 0 1px rgba(255, 255, 255, .28), 0 8px 22px rgba(36, 104, 228, .34); font-weight: 700; }
        .start-button:hover { box-shadow: inset 0 1px rgba(255, 255, 255, .34), 0 11px 27px rgba(36, 104, 228, .45); }
        @media (max-width: 620px) { body.pre-simulado .site-nav, .pre-simulado-content { width: min(calc(100% - 28px), 520px); } .pre-simulado-content { padding-top: 34px; } .prep-card { border-radius: 19px; } }
        @media (max-width: 400px) { .prep-info { grid-template-columns: 1fr; text-align: left; } .prep-info div + div { padding-top: 10px; border-top: 1px solid rgba(129, 172, 247, .16); border-left: 0; } }
    </style>
</head>
<body class="pre-simulado">
    <?php $navbarActive = 'simulados'; require APP_ROOT . '/resources/views/layouts/navbar.php'; ?>

    <main class="pre-simulado-content">
        <section class="prep-card" aria-labelledby="prep-title">
            <div class="prep-icon" aria-hidden="true">✦</div>
            <p class="prep-kicker">Desafio de conhecimentos</p>
            <h1 id="prep-title">Voc&ecirc; est&aacute; preparado?</h1>
            <p>Voc&ecirc; vai come&ccedil;ar um simulado para testar o que aprendeu nas trilhas.</p>
            <dl class="prep-info">
                <div><dt>Quest&otilde;es</dt><dd><?= $totalQuestoes > 0 ? $totalQuestoes : 20 ?></dd></div>
                <div><dt>Tempo</dt><dd>25 min</dd></div>
                <div><dt>Resultado</dt><dd>Ao final</dd></div>
            </dl>

            <form method="get" action="<?= APP_URL ?>/index.php">
                <input type="hidden" name="route" value="/aluno/simulados">
                <input type="hidden" name="iniciar" value="1">
                <button class="start-button" type="submit">Come&ccedil;ar simulado &rarr;</button>
            </form>
        </section>
    </main>
</body>
</html>
