<?php
$service = new TrilhaService();
$modulos = $service->getModulos();
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Trilha de Aprendizado</title>
    <link rel="stylesheet" href="<?= ASSET_URL ?>/css/estilo_homepage.css">
    <style>
        body { font-family: Arial, sans-serif; color: #fff; background: #00002e; }
        .container { max-width: 1100px; margin: 50px auto; padding: 20px; }
        .modulo { border: 1px solid rgba(255,255,255,0.3); padding: 20px; border-radius: 14px; margin-bottom: 20px; background: rgba(255,255,255,0.04); }
        .modulo h2 { margin-bottom: 18px; }
        .etapas { display: grid; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); gap: 12px; }
        .etapa { border-radius: 10px; padding: 14px; border: 1px solid rgba(255,255,255,0.2); background: rgba(0,0,0,0.2); }
        .etapa.disponivel { border-color: #5ad0ff; }
        .etapa.concluida { border-color: #63ff9a; }
        .etapa.em_andamento { border-color: #ffd166; }
        .etapa.bloqueada { opacity: 0.5; }
        .badge { display: inline-block; padding: 6px 10px; border-radius: 999px; font-size: 12px; margin-bottom: 8px; background: rgba(255,255,255,0.08); }
    </style>
</head>
<body>
    <div class="container">
        <h1>Trilha de Aprendizado — Álgebra</h1>
        <?php foreach ($modulos as $modulo): ?>
            <div class="modulo">
                <h2><?= htmlspecialchars($modulo['titulo']) ?></h2>
                <div class="etapas">
                    <?php foreach ($modulo['etapas'] as $etapa): ?>
                        <div class="etapa <?= htmlspecialchars($etapa['estado']) ?>">
                            <span class="badge"><?= htmlspecialchars(str_replace('_', ' ', $etapa['estado'])) ?></span>
                            <div><?= htmlspecialchars($etapa['nome']) ?></div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</body>
</html>
