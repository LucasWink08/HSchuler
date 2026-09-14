<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Vídeos</title>
    <link rel="stylesheet" href="<?= ASSET_URL ?>/css/estilo_homepage.css">
    <style>
        body { margin: 0; background: #00002e; color: #fff; font-family: Arial, sans-serif; }
        .container { max-width: 900px; margin: 60px auto; padding: 20px; }
        .card { border: 1px solid rgba(255,255,255,0.2); border-radius: 14px; background: rgba(255,255,255,0.04); padding: 20px; margin-bottom: 18px; }
        .actions a { display: inline-block; margin-top: 14px; padding: 10px 16px; border: 1px solid rgba(255,255,255,.25); border-radius: 8px; background: linear-gradient(45deg, #00002e, #5718cc); color: white; box-shadow: 0 4px 0 #1b0754, 0 8px 16px rgba(0,0,0,.35); text-decoration: none; transition: transform .18s ease, box-shadow .18s ease, filter .18s ease; }
        .actions a:hover { transform: translateY(-2px); box-shadow: 0 6px 0 #1b0754, 0 12px 20px rgba(87,24,204,.32); filter: brightness(1.08); }
    </style>
</head>
<body>
    <div class="container">
        <h1>Vídeos</h1>
        <div class="card">
            <h3>Fatoração por agrupamento</h3>
            <p>Videoaula introdutória com passo a passo de aplicação.</p>
            <div class="actions"><a href="<?= app_route('/professor/videos') ?>">Assistir</a></div>
        </div>
        <div class="card">
            <h3>Produto notável: (a+b)^2</h3>
            <p>Explicação visual para o desenvolvimento do quadrado da soma.</p>
            <div class="actions"><a href="<?= app_route('/professor/videos') ?>">Assistir</a></div>
        </div>
    </div>
</body>
</html>
