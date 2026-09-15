<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ranking</title>
    <link rel="stylesheet" href="<?= app_asset('css/estilo_homepage.css') ?>">
    <style>
        body { margin: 0; background: #00002e; color: #fff; font-family: Arial, sans-serif; }
        .container { max-width: 900px; margin: 60px auto; padding: 20px; }
        .board { border: 1px solid rgba(255,255,255,0.2); border-radius: 14px; background: rgba(255,255,255,0.04); padding: 20px; }
        .row { display: flex; justify-content: space-between; align-items: center; padding: 12px 0; border-bottom: 1px solid rgba(255,255,255,0.1); }
        .row:last-child { border-bottom: none; }
        .meta { display: flex; gap: 12px; align-items: center; }
        .pos { width: 30px; height: 30px; border-radius: 50%; background: rgba(255,255,255,0.07); display: flex; align-items: center; justify-content: center; }
        .actions { margin-top: 18px; }
        .actions a { display: inline-block; padding: 12px 18px; border: 1px solid rgba(255,255,255,.25); border-radius: 8px; background: linear-gradient(45deg, #00002e, #5718cc); color: white; box-shadow: 0 4px 0 #1b0754, 0 8px 16px rgba(0,0,0,.35); text-decoration: none; transition: transform .18s ease, box-shadow .18s ease, filter .18s ease; }
        .actions a:hover { transform: translateY(-2px); box-shadow: 0 6px 0 #1b0754, 0 12px 20px rgba(87,24,204,.32); filter: brightness(1.08); }
    </style>
</head>
<body>
    <?php $navbarActive = 'ranking'; require APP_ROOT . '/resources/views/layouts/navbar.php'; ?>
    <div class="container">
        <h1>Ranking</h1>
        <div class="board">
            <div class="row">
                <div class="meta">
                    <span class="pos">1</span>
                    <span>Maria</span>
                </div>
                <strong>980 XP</strong>
            </div>
            <div class="row">
                <div class="meta">
                    <span class="pos">2</span>
                    <span>João</span>
                </div>
                <strong>940 XP</strong>
            </div>
            <div class="row">
                <div class="meta">
                    <span class="pos">3</span>
                    <span>Lucas</span>
                </div>
                <strong>890 XP</strong>
            </div>
        </div>
        <div class="actions">
            <a href="<?= app_route('/aluno/dashboard') ?>">Voltar</a>
        </div>
    </div>
</body>
</html>
