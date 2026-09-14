<?php
$area = $area ?? 'potenciacao';

$conteudos = [
    'potenciacao' => ['titulo' => 'Potencia&ccedil;&atilde;o', 'etapas' => ['Introdu&ccedil;&atilde;o', 'Base e expoente', 'Pot&ecirc;ncias de base 10', 'Exerc&iacute;cios de potencia&ccedil;&atilde;o', 'Revis&atilde;o', 'Produto de pot&ecirc;ncias', 'Quociente de pot&ecirc;ncias', 'Expoentes negativos', 'Desafio final']],
    'fracoes-algebricas' => ['titulo' => 'Fra&ccedil;&otilde;es alg&eacute;bricas', 'etapas' => ['Termos alg&eacute;bricos', 'Dom&iacute;nio', 'Fator comum', 'Exerc&iacute;cios com fra&ccedil;&otilde;es', 'Revis&atilde;o', 'Multiplica&ccedil;&atilde;o', 'Soma e subtra&ccedil;&atilde;o', 'Fra&ccedil;&otilde;es complexas', 'Desafio final']],
    'produtos-notaveis' => ['titulo' => 'Produtos not&aacute;veis', 'etapas' => ['Padr&otilde;es alg&eacute;bricos', 'Quadrado da soma', 'Quadrado da diferen&ccedil;a', 'Exerc&iacute;cios de produtos not&aacute;veis', 'Revis&atilde;o', 'Soma pela diferen&ccedil;a', 'Aplica&ccedil;&otilde;es', 'F&oacute;rmulas', 'Desafio final']],
    'fatoracao' => ['titulo' => 'Fatora&ccedil;&atilde;o', 'etapas' => ['Fator comum', 'Agrupamento', 'Diferen&ccedil;a de quadrados', 'Exerc&iacute;cios de fatora&ccedil;&atilde;o', 'Revis&atilde;o', 'Trin&ocirc;mios', 'Soma de cubos', 'Pr&aacute;tica', 'Desafio final']],
    'equacoes' => ['titulo' => 'Equa&ccedil;&otilde;es', 'etapas' => ['Princ&iacute;pio da igualdade', 'Termos semelhantes', 'Isolando a inc&oacute;gnita', 'Exerc&iacute;cios de equa&ccedil;&otilde;es', 'Revis&atilde;o', 'Par&ecirc;nteses', 'Problemas', '2&ordm; grau', 'Desafio final']],
    'inequacoes' => ['titulo' => 'Inequa&ccedil;&otilde;es', 'etapas' => ['S&iacute;mbolos de compara&ccedil;&atilde;o', 'Conjunto solu&ccedil;&atilde;o', 'Reta num&eacute;rica', 'Exerc&iacute;cios de inequa&ccedil;&otilde;es', 'Revis&atilde;o', 'Intervalos', 'Sistemas', 'Pr&aacute;tica', 'Desafio final']],
];

$conteudo = $conteudos[$area] ?? $conteudos['potenciacao'];
$urlExercicios = app_route('/aluno/questoes') . '&area=' . urlencode($area);
$layoutNos = [
    ['x' => 43, 'y' => 6, 'icone' => '&#128214;'], ['x' => 27, 'y' => 17, 'icone' => '&#9733;'],
    ['x' => 17, 'y' => 28, 'icone' => '&#9679;'], ['x' => 34, 'y' => 40, 'icone' => 'x<sup>2</sup>'],
    ['x' => 55, 'y' => 51, 'icone' => '&#128214;'], ['x' => 43, 'y' => 62, 'icone' => '&#9679;'],
    ['x' => 57, 'y' => 73, 'icone' => '&#9733;'], ['x' => 70, 'y' => 84, 'icone' => '&#8801;'],
    ['x' => 59, 'y' => 95, 'icone' => '&#9679;'],
];

$alunoId = filter_var($_SESSION['user_id'] ?? null, FILTER_VALIDATE_INT);
$alunoId = $alunoId !== false && $alunoId !== null && $alunoId > 0 ? (int) $alunoId : null;
$trilhaService = new TrilhaService();
$resumo = $trilhaService->getResumo($alunoId, $area);
$etapasBanco = $trilhaService->getEtapas($area);
$estadosEtapas = $alunoId === null ? [] : $trilhaService->getEstadosEtapas($alunoId, $area);
$nos = [];
foreach ($layoutNos as $indice => $no) {
    $etapaBanco = $etapasBanco[$indice] ?? null;
    $nomeBanco = $etapaBanco['nome'] ?? '';
    $nomeEtapa = $etapaBanco !== null
        ? $trilhaService->getNomeEtapaDaArea($area, (int) $etapaBanco['ordem_num'])
        : html_entity_decode($conteudo['etapas'][$indice], ENT_QUOTES, 'UTF-8');
    $no['nome'] = $nomeEtapa;
    $no['etapa_id'] = $etapaBanco['id'] ?? null;
    $no['estado'] = $estadosEtapas[$trilhaService->normalizarIdentificador($nomeBanco)] ?? 'locked';
    $nos[] = $no;
}

$etapasConcluidas = $resumo['etapas_concluidas'];
$totalEtapas = $resumo['total_etapas'];
$progresso = $totalEtapas !== null && $totalEtapas > 0 && $etapasConcluidas !== null
    ? (int) round(($etapasConcluidas / $totalEtapas) * 100)
    : null;
$streakAtual = $resumo['streak_atual'];
$maiorStreak = $resumo['maior_streak'];
$diasAtividade = $resumo['dias_atividade'];
$percentualAcertos = $resumo['percentual_acertos'];
$formatarDado = static function ($valor): string { return $valor === null ? '&mdash;' : (string) $valor; };
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Trilha de <?= html_entity_decode($conteudo['titulo'], ENT_QUOTES, 'UTF-8') ?> - HSchuler</title>
    <link rel="stylesheet" href="<?= app_asset('css/estilo_homepage.css') ?>">
    <style>
        body.path-page{display:block;min-height:100svh;overflow-x:hidden;overflow-y:auto;padding:28px 20px 48px;color:#fff;background:#030305}.path-header{display:flex;align-items:center;justify-content:space-between;gap:16px;width:min(520px,100%);margin:0 auto 18px}.path-header h1{margin:0;font-size:clamp(1.45rem,4vw,2rem);font-weight:500;text-align:center}.path-back{padding:8px 10px;border:1px solid rgba(255,255,255,.2);border-radius:7px;color:rgba(255,255,255,.8);text-decoration:none;font-size:.75rem;white-space:nowrap}.path-back:hover{border-color:#a53af0;background:rgba(165,58,240,.12);color:#fff}
        .path-layout{display:grid;grid-template-columns:minmax(180px,220px) minmax(340px,380px) minmax(270px,310px);align-items:start;justify-content:center;gap:28px;width:min(1120px,100%);margin:0 auto}.path-track{min-width:0}.learning-path{position:relative;isolation:isolate;width:100%;height:1280px;margin:0 auto}.path-constellation{position:absolute;inset:0;z-index:0;width:100%;height:100%;overflow:visible;pointer-events:none}.constellation-glow{fill:none;stroke:#176eff;stroke-width:3;opacity:.45;filter:blur(3px)}.constellation-line{fill:none;stroke:#67b7ff;stroke-width:1.25;stroke-linecap:round;stroke-linejoin:round;filter:drop-shadow(0 0 3px #2377ff)}.constellation-star{fill:#e8f7ff;filter:drop-shadow(0 0 4px #3a9cff)}.path-node{position:absolute;z-index:1;left:calc(var(--x) * 1%);top:calc(var(--y) * 1%);display:grid;width:72px;height:72px;padding:0;place-items:center;transform:translate(-50%,-50%);border:0;border-radius:16px;background:transparent;color:#fff;cursor:pointer;font:inherit}.path-node:disabled{cursor:not-allowed}.node-circle{display:grid;width:62px;height:62px;place-items:center;border:3px solid #f4f4f4;border-radius:14px;background:linear-gradient(145deg,#eee,#c6c6c6 48%,#909090);box-shadow:inset 0 2px rgba(255,255,255,.72),inset 0 -7px rgba(63,63,63,.26),0 7px #6f6f6f,0 0 0 4px rgba(255,255,255,.1);color:#656565;font-size:1.35rem;font-weight:600;text-shadow:0 1px rgba(255,255,255,.48);transition:transform .18s,filter .18s}.path-node.complete .node-circle{border-color:#c8ffd1;background:linear-gradient(145deg,#68e584,#35bd59 48%,#167a35);box-shadow:inset 0 2px rgba(255,255,255,.6),inset 0 -7px rgba(0,72,26,.3),0 7px #0d6330,0 0 0 4px rgba(61,211,96,.15);color:#fff;text-shadow:0 1px rgba(0,70,25,.45)}.path-node.current .node-circle{width:66px;height:66px;border-color:#d4f4ff;background:linear-gradient(145deg,#32c7ff,#1599ed 46%,#0756af);box-shadow:inset 0 2px rgba(255,255,255,.56),inset 0 -7px rgba(0,48,113,.36),0 8px #063d78,0 0 0 5px rgba(24,153,235,.25);color:#fff;text-shadow:0 1px rgba(0,35,90,.55)}.path-node sup{font-size:.65em;line-height:0}.path-node:not(:disabled):hover .node-circle,.path-node:focus-visible .node-circle{transform:translateY(-6px) scale(1.05);filter:brightness(1.07)}.path-node:focus-visible{outline:2px solid #b9eaff;outline-offset:5px}.node-tooltip{position:absolute;top:calc(100% + 8px);left:50%;width:max-content;max-width:150px;padding:7px 9px;transform:translateX(-50%) translateY(-4px);border:1px solid rgba(255,255,255,.22);border-radius:6px;background:rgba(9,7,16,.94);box-shadow:0 8px 20px rgba(0,0,0,.34);font-size:.68rem;line-height:1.3;opacity:0;pointer-events:none;transition:opacity .16s,transform .16s;z-index:3;text-align:center}.path-node:hover .node-tooltip,.path-node:focus-visible .node-tooltip{opacity:1;transform:translateX(-50%)}.path-note{width:100%;margin:-6px auto 0;color:rgba(255,255,255,.55);font-size:.73rem;line-height:1.5;text-align:center}
        .path-streak,.path-sidebar{display:grid;gap:12px}.path-streak{position:sticky;top:24px;max-height:calc(100svh - 48px);overflow-y:auto}.path-card{padding:17px;border:1px solid rgba(255,255,255,.14);border-radius:12px;background:rgba(8,8,12,.9);box-shadow:0 12px 28px rgba(0,0,0,.22)}.card-eyebrow{margin:0 0 8px;color:#b65cff;font-size:.68rem;font-weight:700;letter-spacing:.08em;text-transform:uppercase}.path-card h2{margin:0;color:#fff;font-size:1rem;font-weight:700}.path-card-copy{margin:0;color:rgba(255,255,255,.64);font-size:.76rem;line-height:1.45}.streak-count{display:flex;align-items:center;gap:8px;margin:8px 0;color:#fff}.streak-count strong{font-size:2rem;line-height:1}.streak-count span{color:#ffae2e;font-size:1.7rem}.streak-count small{color:rgba(255,255,255,.67);font-size:.78rem}.streak-stats{display:grid;gap:8px;margin:16px 0 0;padding-top:13px;border-top:1px solid rgba(255,255,255,.12)}.streak-stats div,.progress-stat{display:flex;align-items:center;justify-content:space-between;gap:10px;color:rgba(255,255,255,.63);font-size:.73rem}.streak-stats strong,.progress-stat strong{color:#fff;font-size:.82rem}.streak-days{display:flex;flex-wrap:wrap;gap:6px;margin-top:14px}.streak-days time{display:grid;min-width:34px;height:26px;padding:0 5px;place-items:center;border:1px solid rgba(255,174,46,.48);border-radius:6px;color:#ffd085;background:rgba(255,174,46,.1);font-size:.63rem}.progress-label{display:flex;justify-content:space-between;gap:10px;margin:14px 0 8px;color:rgba(255,255,255,.68);font-size:.72rem}.progress-label strong{color:#fff;font-weight:600}.progress-bar{height:7px;overflow:hidden;border-radius:999px;background:#282832}.progress-bar span{display:block;width:var(--progress);height:100%;border-radius:inherit;background:linear-gradient(90deg,#781fe1,#c457ff)}.legend-list{display:grid;gap:10px;margin:14px 0 0;padding:0;list-style:none}.legend-list li{display:flex;align-items:center;gap:8px;color:rgba(255,255,255,.7);font-size:.74rem}.legend-dot{width:11px;height:11px;flex:0 0 11px;border-radius:50%;background:#b9b9b9}.legend-dot.complete{background:#43d666}.legend-dot.current{background:#1d9eea}.legend-dot.locked{background:#8d8d8d}.legend-dot.checkpoint{background:#d8ac2c}.player-overview{display:grid;grid-template-columns:90px 1fr;gap:14px;align-items:center;margin-top:14px}.progress-ring{position:relative;display:grid;width:90px;height:90px;place-items:center;border-radius:50%;background:conic-gradient(#9a37ef var(--progress),#282832 0)}.progress-ring::before{content:"";position:absolute;inset:7px;border-radius:50%;background:#09090d}.progress-ring strong,.progress-ring small{position:relative;display:block;text-align:center}.progress-ring strong{font-size:1.35rem}.progress-ring small{color:rgba(255,255,255,.62);font-size:.58rem}.overview-stats{display:grid;gap:8px}
        @media(max-width:1040px){.path-layout{grid-template-columns:minmax(340px,380px) minmax(270px,310px);width:min(750px,100%)}.path-streak{grid-column:1;grid-row:2;position:static;max-height:none}.path-track{grid-column:1;grid-row:1}.path-sidebar{grid-column:2;grid-row:1 / span 2}}@media(max-width:720px){body.path-page{padding:20px 12px 36px}.path-header{margin-bottom:14px}.path-header h1{font-size:1.32rem}.path-back{padding:7px 8px}.path-layout{grid-template-columns:1fr;width:min(380px,100%);gap:18px}.path-track,.path-streak,.path-sidebar{grid-column:auto;grid-row:auto}.path-track{order:1}.path-streak{order:2}.path-sidebar{order:3}.learning-path{height:1180px;width:min(340px,100%)}.path-node{width:66px;height:66px}.node-circle{width:56px;height:56px}.path-node.current .node-circle{width:60px;height:60px}}
    </style>
</head>
<body class="path-page">
    <header class="path-header">
        <a class="path-back" href="<?= app_route('/') ?>">&larr; Voltar</a>
        <h1>Trilha de <?= $conteudo['titulo'] ?></h1>
        <span aria-hidden="true" style="width:57px"></span>
    </header>

    <section class="path-layout" aria-label="Progresso de aprendizagem">
        <aside class="path-streak" aria-label="SequÃªncia diÃ¡ria">
            <section class="path-card">
                <p class="card-eyebrow">SequÃªncia diÃ¡ria</p>
                <h2>Seu ritmo</h2>
                <?php if ($streakAtual === null): ?>
                    <p class="path-card-copy" style="margin-top:10px">Entre para consultar a sequÃªncia registrada nas suas atividades.</p>
                <?php else: ?>
                    <div class="streak-count"><span aria-hidden="true">&#128293;</span><strong><?= $streakAtual ?></strong><small>dias consecutivos</small></div>
                    <div class="streak-stats"><div><span>Maior sequÃªncia</span><strong><?= $formatarDado($maiorStreak) ?> dias</strong></div><div><span>Dias registrados</span><strong><?= count($diasAtividade) ?></strong></div></div>
                    <?php if ($diasAtividade === []): ?>
                        <p class="path-card-copy" style="margin-top:14px">Nenhuma atividade foi registrada ainda.</p>
                    <?php else: ?>
                        <div class="streak-days" aria-label="Dias com atividades registradas">
                            <?php foreach ($diasAtividade as $dia): ?><time datetime="<?= htmlspecialchars($dia, ENT_QUOTES, 'UTF-8') ?>" title="Atividade em <?= htmlspecialchars($dia, ENT_QUOTES, 'UTF-8') ?>"><?= htmlspecialchars(substr($dia, 8, 2) . '/' . substr($dia, 5, 2), ENT_QUOTES, 'UTF-8') ?></time><?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                <?php endif; ?>
            </section>
        </aside>

        <div class="path-track">
            <main class="learning-path" aria-label="Trilha de <?= strip_tags(html_entity_decode($conteudo['titulo'], ENT_QUOTES, 'UTF-8')) ?>">
                <svg class="path-constellation" viewBox="0 0 100 100" preserveAspectRatio="none" aria-hidden="true">
                    <polyline class="constellation-glow" points="43,6 27,17 17,28 34,40 55,51 43,62 57,73 70,84 59,95" />
                    <polyline class="constellation-line" points="43,6 27,17 17,28 34,40 55,51 43,62 57,73 70,84 59,95" />
                    <g class="constellation-star">
                        <circle cx="43" cy="6" r="1.25" /><circle cx="27" cy="17" r="1.25" />
                        <circle cx="17" cy="28" r="1.25" /><circle cx="34" cy="40" r="1.25" />
                        <circle cx="55" cy="51" r="1.25" /><circle cx="43" cy="62" r="1.25" />
                        <circle cx="57" cy="73" r="1.25" /><circle cx="70" cy="84" r="1.25" />
                        <circle cx="59" cy="95" r="1.25" />
                    </g>
                </svg>
                <?php foreach ($nos as $indice => $no): ?>
                    <?php $podeAbrir = $no['etapa_id'] !== null && $no['estado'] !== 'locked'; $urlEtapa = app_route('/aluno/etapa') . '&area=' . urlencode($area) . '&etapa_id=' . (int) ($no['etapa_id'] ?? 0) . '&etapa=' . urlencode($no['nome']); ?>
<button class="path-node <?= $no['estado'] ?>" type="button" style="--x:<?= $no['x'] ?>;--y:<?= $no['y'] ?>" aria-label="<?= htmlspecialchars($no['nome'], ENT_QUOTES, 'UTF-8') ?>"<?= $podeAbrir ? ' data-url="' . htmlspecialchars($urlEtapa, ENT_QUOTES, 'UTF-8') . '"' : ' disabled aria-disabled="true"' ?>><span class="node-circle" aria-hidden="true"><?= $no['icone'] ?></span><span class="node-tooltip"><?= htmlspecialchars($no['nome'], ENT_QUOTES, 'UTF-8') ?></span></button>
                <?php endforeach; ?>
            </main>
            <p class="path-note">O estado das etapas Ã© carregado a partir do progresso registrado.</p>
        </div>

        <aside class="path-sidebar" aria-label="InformaÃ§Ãµes de progresso">
            <section class="path-card">
                <p class="card-eyebrow">Unidade atual</p>
                <h2><?= html_entity_decode($conteudo['titulo'], ENT_QUOTES, 'UTF-8') ?></h2>
                <?php if ($alunoId === null): ?>
                    <p class="path-card-copy" style="margin-top:14px">Entre para carregar o progresso salvo da sua conta.</p>
                <?php elseif ($totalEtapas === 0): ?>
                    <p class="path-card-copy" style="margin-top:14px">Nenhuma etapa foi cadastrada no banco ainda.</p>
                <?php else: ?>
                    <div class="progress-label"><span><?= $formatarDado($etapasConcluidas) ?> de <?= $formatarDado($totalEtapas) ?> etapas concluÃ­das</span><strong><?= $formatarDado($progresso) ?><?= $progresso === null ? '' : '%' ?></strong></div>
                    <div class="progress-bar" aria-label="Progresso registrado"><span style="--progress:<?= $progresso ?? 0 ?>%"></span></div>
                <?php endif; ?>
            </section>

            <section class="path-card">
                <h2>Legenda</h2>
                <ul class="legend-list"><li><i class="legend-dot complete" aria-hidden="true"></i>ConcluÃ­do</li><li><i class="legend-dot current" aria-hidden="true"></i>Atual</li><li><i class="legend-dot" aria-hidden="true"></i>DisponÃ­vel</li><li><i class="legend-dot locked" aria-hidden="true"></i>Bloqueado</li><li><i class="legend-dot checkpoint" aria-hidden="true"></i>Desafio / checkpoint</li></ul>
            </section>

            <section class="path-card">
                <h2>Seu progresso geral</h2>
                <?php if ($alunoId === null): ?>
                    <p class="path-card-copy" style="margin-top:14px">Os indicadores sÃ£o exibidos apÃ³s o acesso Ã  conta.</p>
                <?php else: ?>
                    <div class="player-overview" style="--progress:<?= $percentualAcertos ?? 0 ?>%"><div class="progress-ring"><div><strong><?= $percentualAcertos === null ? '&mdash;' : $percentualAcertos . '%' ?></strong><small>acertos</small></div></div><div class="overview-stats"><div class="progress-stat"><span>XP registrado</span><strong><?= $formatarDado($resumo['xp']) ?></strong></div><div class="progress-stat"><span>Etapas concluÃ­das</span><strong><?= $formatarDado($etapasConcluidas) ?></strong></div><div class="progress-stat"><span>Simulados</span><strong><?= $formatarDado($resumo['simulados_realizados']) ?></strong></div></div></div>
                <?php endif; ?>
            </section>
        </aside>
    </section>
    <script>document.querySelectorAll('.path-node[data-url]').forEach((button) => { button.addEventListener('click', () => window.location.assign(button.dataset.url)); });</script>
</body>
</html>

