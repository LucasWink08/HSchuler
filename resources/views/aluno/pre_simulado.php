<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Preparar simulado - HSchuler</title>
    <link rel="stylesheet" href="<?= app_asset('css/estilo_homepage.css') ?>">
    <style>
        body.pre-simulado {
            display: grid;
            min-height: 100svh;
            place-items: center;
            padding: 92px 16px 32px;
            overflow-y: auto;
            color: #fff;
        }

        body.pre-simulado .prep-nav {
            position: fixed;
            inset: 0 0 auto;
            display: flex;
            align-items: center;
            justify-content: space-between;
            width: 100%;
            min-height: 64px;
            padding: 14px 40px;
            background: transparent;
            z-index: 10;
        }

        .prep-nav a {
            color: rgba(255, 255, 255, .84);
            text-decoration: none;
            transition: color .2s ease;
        }

        .prep-nav a:hover { color: #b479ff; }

        .prep-card {
            width: min(100%, 570px);
            padding: clamp(28px, 6vw, 48px);
            border: 1px solid rgba(255, 255, 255, .2);
            border-radius: 18px;
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
            body.pre-simulado .prep-nav { padding: 14px 20px; }
            body.pre-simulado { padding: 86px 16px 24px; }
        }
    </style>
</head>
<body class="pre-simulado">
    <?php $navbarActive = 'simulados'; require APP_ROOT . '/resources/views/layouts/navbar.php'; ?>
    <nav class="prep-nav" aria-label="Navega&ccedil;&atilde;o do simulado" hidden>
        <a href="<?= app_route('/') ?>">&larr; Voltar para a home</a>
        <a href="<?= app_route('/ranking') ?>">Ranking</a>
    </nav>

    <main class="prep-card">
        <div class="prep-icon" aria-hidden="true">?</div>
        <h1>Voc&ecirc; est&aacute; preparado?</h1>
        <p>Voc&ecirc; vai come&ccedil;ar um simulado de 20 quest&otilde;es.</p>

        <form method="get" action="<?= app_route('/aluno/simulados') ?>">
            <input type="hidden" name="iniciar" value="1">
            <button class="start-button" type="submit">Come&ccedil;ar simulado</button>
        </form>
    </main>
</body>
</html>
