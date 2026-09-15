<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Atividades</title>
    <link rel="stylesheet" href="<?= app_asset('css/estilo_homepage.css') ?>">
    <style>
        body { margin: 0; background: #00002e; color: #fff; font-family: Arial, sans-serif; }
        .container { max-width: 900px; margin: 60px auto; padding: 20px; }
        .card { border: 1px solid rgba(255,255,255,0.2); border-radius: 14px; background: rgba(255,255,255,0.04); padding: 20px; margin-bottom: 18px; }
        .pill { display: inline-block; padding: 6px 10px; border-radius: 999px; background: rgba(255,255,255,0.08); font-size: 12px; }
        .actions a { display: inline-block; margin-top: 14px; padding: 10px 16px; border: 1px solid rgba(255,255,255,.25); border-radius: 8px; background: linear-gradient(45deg, #00002e, #5718cc); color: white; box-shadow: 0 4px 0 #1b0754, 0 8px 16px rgba(0,0,0,.35); text-decoration: none; transition: transform .18s ease, box-shadow .18s ease, filter .18s ease; }
        .actions a:hover { transform: translateY(-2px); box-shadow: 0 6px 0 #1b0754, 0 12px 20px rgba(87,24,204,.32); filter: brightness(1.08); }
    </style>
</head>
<body>
    <?php require APP_ROOT . '/resources/views/layouts/navbar.php'; ?>
    <div class="container">
        <h1>Atividades</h1>
        <div class="card">
            <span class="pill">Módulo 1</span>
            <h3>Expressões algébricas</h3>
            <p>Atividade de fixação com 5 exercícios de simplificação.</p>
            <div class="actions"><a href="<?= app_route('/professor/atividades') ?>">Visualizar</a></div>
        </div>
        <div class="card">
            <span class="pill">Módulo 2</span>
            <h3>Produtos notáveis</h3>
            <p>Lista de exercícios focada em quadrado da soma e da diferença.</p>
            <div class="actions"><a href="<?= app_route('/professor/atividades') ?>">Visualizar</a></div>
        </div>
    </div>
</body>
</html>
