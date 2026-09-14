<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard do Professor</title>
    <link rel="stylesheet" href="<?= ASSET_URL ?>/css/estilo_homepage.css">
</head>
<body>
    <h1>Painel do Professor</h1>
    <ul>
        <li><a href="<?= APP_URL ?>/index.php?route=/professor/videos">Vídeos</a></li>
        <li><a href="<?= APP_URL ?>/index.php?route=/professor/atividades">Atividades</a></li>
        <li><a href="<?= APP_URL ?>/index.php?route=/professor/gerador-questoes">Gerador de Questões</a></li>
        <li><a href="<?= APP_URL ?>/index.php?route=/ranking">Ranking</a></li>
    </ul>
</body>
</html>
