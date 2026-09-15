<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Perfil do aluno | HSchuler</title>
    <link rel="stylesheet" href="<?= app_asset('css/estilo_homepage.css') ?>">
    <style>
        body { margin: 0; min-height: 100vh; color: #fff; background: #030305; font-family: Arial, sans-serif; }
        .page { width: min(880px, calc(100% - 32px)); margin: 32px auto 48px; }
        .stats { display: grid; grid-template-columns: repeat(4, minmax(130px, 1fr)); gap: 14px; margin: 22px 0; }
        .card { padding: 18px; border: 1px solid rgba(255, 255, 255, .15); border-radius: 12px; background: #0b0b10; }
        .card small { color: #b9b9c2; }
        .card strong { display: block; margin-top: 8px; font-size: 1.5rem; }
        .links a { display: inline-block; margin: 6px 8px 0 0; padding: 10px 14px; border: 1px solid #4d99dc; border-radius: 8px; color: #fff; text-decoration: none; }
        @media (max-width: 650px) { .stats { grid-template-columns: repeat(2, 1fr); } }
    </style>
</head>
<body>
    <?php require APP_ROOT . '/resources/views/layouts/navbar.php'; ?>
    <main class="page">
        <h1>Seu perfil</h1>
        <section class="stats">
            <article class="card"><small>XP total</small><strong><?= (int) ($resumo['xp'] ?? 0) ?></strong></article>
            <article class="card"><small>Nível atual</small><strong><?= (int) ($resumo['nivel'] ?? 1) ?></strong></article>
            <article class="card"><small>Sequência diária</small><strong>🔥 <?= (int) ($resumo['streak_atual'] ?? 0) ?></strong></article>
            <article class="card"><small>Etapas concluídas</small><strong><?= (int) ($resumo['etapas_concluidas'] ?? 0) ?>/<?= (int) ($resumo['total_etapas'] ?? 0) ?></strong></article>
        </section>
        <nav class="links" aria-label="Atalhos do perfil">
            <a href="<?= app_route('/aluno/trilha') ?>">Minha trilha</a>
            <a href="<?= app_route('/aluno/simulados') ?>">Simulados</a>
            <a href="<?= app_route('/ranking') ?>">Ranking</a>
        </nav>
    </main>
</body>
</html>
