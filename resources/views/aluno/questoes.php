<?php
$area = $area ?? 'potenciacao';
$questoes = $questoes ?? [];
$areaLabel = $this->questaoService->getAreaLabel($area);
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Questões de <?= htmlspecialchars($areaLabel) ?></title>
    <link rel="stylesheet" href="<?= app_asset('css/estilo_homepage.css') ?>">
    <style>
        body { margin: 0; min-height: 100vh; background: #080824; color: #fff; font-family: Arial, sans-serif; }
        .page { width: min(980px, calc(100% - 32px)); margin: 92px auto 48px; }
        .header { display: flex; justify-content: space-between; align-items: end; gap: 20px; margin-bottom: 24px; }
        .header h1 { margin: 0 0 8px; }
        .header p { margin: 0; color: rgba(255,255,255,.7); }
        .question { margin-bottom: 18px; padding: 22px; border: 1px solid rgba(255,255,255,.18); border-radius: 14px; background: rgba(255,255,255,.06); }
        .question h2 { margin: 0 0 18px; font-size: 1.15rem; }
        .option { display: flex; gap: 10px; align-items: center; padding: 12px; margin-top: 10px; border: 1px solid rgba(255,255,255,.16); border-radius: 8px; cursor: pointer; }
        .option:hover { border-color: #63d9ff; background: rgba(99,217,255,.08); }
        .option input { accent-color: #63d9ff; }
        .actions { display: flex; gap: 12px; flex-wrap: wrap; margin-top: 22px; }
        .button { display: inline-block; padding: 12px 18px; border: 1px solid rgba(255,255,255,.25); border-radius: 8px; color: #fff; background: linear-gradient(45deg, #17104d, #6934d4); box-shadow: 0 4px 0 #1b0754, 0 8px 16px rgba(0,0,0,.35); text-decoration: none; cursor: pointer; transition: transform .18s ease, box-shadow .18s ease, filter .18s ease; }
        .button:hover { transform: translateY(-2px); box-shadow: 0 6px 0 #1b0754, 0 12px 20px rgba(87,24,204,.32); filter: brightness(1.08); }
        .feedback { padding: 14px 16px; border-radius: 10px; margin-bottom: 18px; background: rgba(99,255,154,.12); border: 1px solid #63ff9a; }
        .feedback.error { background: rgba(255,107,107,.12); border-color: #ff6b6b; }
        @media (max-width: 640px) { .header { display: block; } .page { margin-top: 78px; } }
    </style>
</head>
<body>
    <?php $navbarActive = 'trilhas'; require APP_ROOT . '/resources/views/layouts/navbar.php'; ?>
    <main class="page">
        <header class="header">
            <div>
                <h1><?= htmlspecialchars($areaLabel) ?></h1>
                <p>Responda uma questão por vez e veja a explicação.</p>
            </div>
            <a class="button" href="<?= app_route('/') ?>">Voltar para a home</a>
        </header>

        <?php if ($resultado !== null): ?>
            <div class="feedback <?= $resultado['correta'] ? '' : 'error' ?>">
                <strong><?= $resultado['correta'] ? 'Resposta correta!' : 'Resposta incorreta.' ?></strong>
                <div><?= htmlspecialchars($resultado['explicacao']) ?></div>
            </div>
        <?php endif; ?>

        <?php foreach ($questoes as $indice => $questao): ?>
            <form class="question" method="post" action="<?= app_route('/aluno/questoes') ?>&area=<?= urlencode($area) ?>">
                <h2><?= ($indice + 1) ?>. <?= htmlspecialchars($questao['enunciado']) ?></h2>
                <?php foreach ($questao['alternativas'] as $alternativaIndice => $alternativa): ?>
                    <label class="option">
                        <input type="radio" name="resposta" value="<?= $alternativaIndice ?>" required>
                        <span><?= htmlspecialchars($alternativa) ?></span>
                    </label>
                <?php endforeach; ?>
                <input type="hidden" name="questao" value="<?= $indice ?>">
                <div class="actions">
                    <button class="button" type="submit">Corrigir resposta</button>
                </div>
            </form>
        <?php endforeach; ?>
    </main>
</body>
</html>
