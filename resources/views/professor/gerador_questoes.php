<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gerador de Questões</title>
    <link rel="stylesheet" href="<?= app_asset('css/estilo_homepage.css') ?>">
    <style>
        body { margin: 0; background: #00002e; color: #fff; font-family: Arial, sans-serif; }
        .container { max-width: 900px; margin: 60px auto; padding: 20px; }
        .card { border: 1px solid rgba(255,255,255,0.2); border-radius: 14px; background: rgba(255,255,255,0.04); padding: 20px; }
        .form-grid { display: grid; gap: 16px; }
        input, select, textarea, button { width: 100%; padding: 12px; border-radius: 8px; border: 1px solid rgba(126, 176, 255, .48); background: rgba(4, 16, 46, .78); color: #fff; box-shadow: inset 0 1px rgba(255,255,255,.08), 0 7px 15px rgba(0,0,0,.15); }
        input, select { color-scheme: dark; } input:focus, select:focus, textarea:focus { border-color: #a8d2ff; outline: none; box-shadow: 0 0 0 3px rgba(99, 151, 255, .2), inset 0 1px rgba(255,255,255,.1); } select { appearance: none; padding-right: 42px; cursor: pointer; background-image: linear-gradient(45deg, transparent 50%, #b9d9ff 50%), linear-gradient(135deg, #b9d9ff 50%, transparent 50%), linear-gradient(90deg, rgba(127, 172, 242, .34), rgba(127, 172, 242, .34)); background-position: calc(100% - 20px) 52%, calc(100% - 14px) 52%, calc(100% - 38px) 50%; background-size: 6px 6px, 6px 6px, 1px 52%; background-repeat: no-repeat; } select option { background: #091b40; color: #fff; }
        button { background: linear-gradient(45deg, #00002e, #5718cc); border: 1px solid rgba(255,255,255,.25); border-radius: 8px; box-shadow: 0 4px 0 #1b0754, 0 8px 16px rgba(0,0,0,.35); cursor: pointer; transition: transform .18s ease, box-shadow .18s ease, filter .18s ease; }
        button:hover { transform: translateY(-2px); box-shadow: 0 6px 0 #1b0754, 0 12px 20px rgba(87,24,204,.32); filter: brightness(1.08); }
    </style>
</head>
<body>
    <?php require APP_ROOT . '/resources/views/layouts/navbar.php'; ?>
    <div class="container">
        <h1>Gerador de Questões</h1>
        <div class="card">
            <form class="form-grid">
                <input type="text" placeholder="Enunciado da questão" />
                <select>
                    <option>Selecione o assunto</option>
                    <option>Álgebra</option>
                    <option>Geometria</option>
                    <option>Funções</option>
                </select>
                <select>
                    <option>Nível de dificuldade</option>
                    <option>Fácil</option>
                    <option>Médio</option>
                    <option>Difícil</option>
                </select>
                <textarea rows="4" placeholder="Alternativas e resposta correta"></textarea>
                <button type="submit">Gerar questão</button>
            </form>
        </div>
    </div>
</body>
</html>
