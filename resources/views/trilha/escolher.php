<?php
$siteRoot = rtrim((string) preg_replace('#/public$#', '', APP_URL), '/');
$folhaUm = $siteRoot . '/imgs/pag1.png';
$folhaDois = $siteRoot . '/imgs/pag2.png';
$alunoId = filter_var($_SESSION['user_id'] ?? null, FILTER_VALIDATE_INT);
$alunoId = $alunoId !== false && $alunoId !== null && $alunoId > 0 && ($_SESSION['role'] ?? null) === 'aluno'
    ? (int) $alunoId
    : null;
$trilhaService = new TrilhaService();
$trilhas = [
    ['id' => 'potenciacao', 'titulo' => 'Potenciação', 'icone' => 'x<sup>2</sup>', 'descricao' => 'Domine bases, expoentes e propriedades das potências.', 'cor' => 'cyan'],
    ['id' => 'fracoes-algebricas', 'titulo' => 'Frações algébricas', 'icone' => '<span>x</span>/<small>y</small>', 'descricao' => 'Simplifique, multiplique e some expressões algébricas.', 'cor' => 'blue'],
    ['id' => 'produtos-notaveis', 'titulo' => 'Produtos notáveis', 'icone' => '(a+b)<sup>2</sup>', 'descricao' => 'Reconheça padrões e desenvolva expressões com segurança.', 'cor' => 'sky'],
    ['id' => 'fatoracao', 'titulo' => 'Fatoração', 'icone' => '(x-a)(x+a)', 'descricao' => 'Encontre fatores comuns e decomponha expressões.', 'cor' => 'cyan'],
    ['id' => 'equacoes', 'titulo' => 'Equações', 'icone' => 'x = ?', 'descricao' => 'Resolva problemas de 1º e 2º grau passo a passo.', 'cor' => 'blue'],
    ['id' => 'inequacoes', 'titulo' => 'Inequações', 'icone' => 'x &gt; 0', 'descricao' => 'Compare valores, intervalos e conjuntos solução.', 'cor' => 'sky'],
];

foreach ($trilhas as &$trilha) {
    $resumo = $alunoId === null ? null : $trilhaService->getResumo($alunoId, $trilha['id']);
    $trilha['concluidas'] = $resumo['etapas_concluidas'] ?? 0;
    $trilha['total'] = $resumo['total_etapas'] ?? 9;
    $trilha['progresso'] = $trilha['total'] > 0 ? (int) round(($trilha['concluidas'] / $trilha['total']) * 100) : 0;
}
unset($trilha);
?>
<!doctype html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>Escolha uma trilha | HSchuler</title>
    <link rel="stylesheet" href="<?= app_asset('css/estilo_homepage.css') ?>?v=30">
    <style>
        body.trail-choice-page{display:block;min-height:100svh;overflow-x:hidden;background:#02050a;color:#edf4ff;font-family:Arial,Helvetica,sans-serif}body.trail-choice-page::before{content:"";position:fixed;inset:0;z-index:0;pointer-events:none;background-image:radial-gradient(circle at 50% -10%,rgba(20,145,255,.22),transparent 36%),radial-gradient(circle at 8% 70%,rgba(39,100,220,.14),transparent 25%),url("<?= htmlspecialchars($folhaUm, ENT_QUOTES, 'UTF-8') ?>"),url("<?= htmlspecialchars($folhaDois, ENT_QUOTES, 'UTF-8') ?>"),url("<?= htmlspecialchars($folhaUm, ENT_QUOTES, 'UTF-8') ?>"),linear-gradient(180deg,#020711,#010207 76%);background-repeat:no-repeat;background-position:center,center,left -290px top 150px,right -300px bottom -100px,right -310px top 110px,center;background-size:cover,cover,min(480px,40vw) auto,min(510px,42vw) auto,min(340px,28vw) auto,cover;animation:choice-leaves-float 19s ease-in-out infinite alternate}body.trail-choice-page::after{content:"";position:fixed;inset:0;z-index:0;pointer-events:none;background-image:radial-gradient(1.5px 1.5px at 80px 80px,#d8f0ff,transparent),radial-gradient(1px 1px at 300px 150px,#fff,transparent),radial-gradient(1px 1px at 660px 90px,#fff,transparent),radial-gradient(1.5px 1.5px at 910px 230px,#d8f0ff,transparent);background-size:960px 360px;opacity:.7}@keyframes choice-leaves-float{from{transform:translate(-8px,-10px) rotate(-.5deg)}to{transform:translate(12px,14px) rotate(.7deg)}}.choice-nav,.choice-main{position:relative;z-index:1;width:min(1150px,calc(100% - 40px));margin:0 auto}.choice-nav{display:flex;align-items:center;justify-content:space-between;gap:18px;padding:24px 0}.choice-brand{display:flex;align-items:center;gap:10px;color:#fff;text-decoration:none;font-weight:700}.choice-brand img{width:42px;height:42px;object-fit:contain}.choice-back{padding:10px 15px;border:1px solid rgba(117,184,255,.55);border-radius:9px;background:rgba(6,22,45,.64);color:#edf6ff;text-decoration:none;font-size:.85rem}.choice-back:hover{background:rgba(23,103,190,.27);border-color:#9ad5ff}.choice-main{margin-top:24px;margin-bottom:72px}.choice-head{text-align:center}.choice-head p{margin:0 0 11px;color:#31b7ff;font-size:.76rem;font-weight:700;letter-spacing:.13em;text-transform:uppercase}.choice-head h1{margin:0;color:#f7fbff;font-size:clamp(2rem,4vw,3.6rem);letter-spacing:-.055em}.choice-head h1 span{color:#25aefa}.choice-head>span{display:block;max-width:610px;margin:18px auto 0;color:#b9cbe5;line-height:1.6}.trails-grid{display:grid;grid-template-columns:repeat(3,minmax(0,1fr));gap:20px;margin-top:42px}.trail-card{position:relative;display:flex;min-height:278px;flex-direction:column;overflow:hidden;padding:25px;border:1px solid rgba(116,181,255,.34);border-radius:18px;background:linear-gradient(145deg,rgba(11,26,48,.94),rgba(3,10,21,.88));box-shadow:inset 0 1px rgba(255,255,255,.08),0 20px 34px rgba(0,0,0,.24);color:#edf5ff;text-decoration:none;transition:transform .18s ease,border-color .18s ease,box-shadow .18s ease}.trail-card::after{content:"";position:absolute;width:150px;height:150px;right:-65px;top:-65px;border-radius:50%;background:radial-gradient(circle,rgba(33,165,255,.26),transparent 68%);pointer-events:none}.trail-card:hover{transform:translateY(-5px);border-color:#78caff;box-shadow:0 22px 40px rgba(16,106,212,.25),inset 0 1px rgba(255,255,255,.11)}.trail-icon{position:relative;z-index:1;display:grid;width:68px;height:68px;place-items:center;border:1px solid rgba(157,222,255,.7);border-radius:17px;background:linear-gradient(135deg,#159eec,#1858c2);box-shadow:inset 0 2px rgba(255,255,255,.37),0 8px 18px rgba(6,110,230,.26);font-size:1.5rem;font-weight:700}.trail-icon small{font-size:.7em}.trail-card h2{position:relative;z-index:1;margin:22px 0 9px;font-size:1.27rem}.trail-card p{position:relative;z-index:1;margin:0;color:#bdcbe0;font-size:.9rem;line-height:1.5}.trail-footer{position:relative;z-index:1;margin-top:auto;padding-top:20px}.trail-progress-label{display:flex;justify-content:space-between;gap:8px;margin-bottom:8px;color:#c8d7ed;font-size:.74rem}.trail-progress{height:7px;overflow:hidden;border-radius:99px;background:#16233a}.trail-progress i{display:block;width:var(--progress);height:100%;border-radius:inherit;background:linear-gradient(90deg,#20b7ff,#1b74e5);box-shadow:0 0 11px rgba(31,167,255,.6)}.trail-open{display:flex;align-items:center;justify-content:space-between;margin-top:18px;color:#5ec8ff;font-size:.83rem;font-weight:700}.trail-open b{display:grid;width:26px;height:26px;place-items:center;border:1px solid rgba(99,193,255,.55);border-radius:50%;font-size:1rem}.choice-note{margin:24px auto 0;color:#93aacb;text-align:center;font-size:.84rem}@media(max-width:850px){.trails-grid{grid-template-columns:repeat(2,minmax(0,1fr))}}@media(max-width:560px){.choice-nav,.choice-main{width:min(100% - 28px,500px)}.choice-nav{padding-top:16px}.trails-grid{grid-template-columns:1fr;margin-top:30px}.trail-card{min-height:245px}.choice-back{padding:9px 11px;font-size:.78rem}}
    </style>
</head>
<body class="trail-choice-page">
    <nav class="choice-nav" aria-label="Navegação de trilhas">
        <a class="choice-brand" href="<?= app_route('/') ?>"><img src="<?= app_asset('images/home/logo.png') ?>" alt="HSchuler"><span>HSchuler</span></a>
        <a class="choice-back" href="<?= app_route('/sobre') ?>">← Voltar</a>
    </nav>
    <main class="choice-main">
        <header class="choice-head"><p>Trilhas de aprendizado</p><h1>Escolha sua <span>jornada</span></h1><span>Selecione um conteúdo para estudar no seu ritmo. Cada trilha possui etapas interativas, questões e progresso salvo na sua conta.</span></header>
        <section class="trails-grid" aria-label="Trilhas disponíveis">
            <?php foreach ($trilhas as $trilha): ?>
                <a class="trail-card <?= $trilha['cor'] ?>" href="<?= app_route('/aluno/trilha') ?>&area=<?= urlencode($trilha['id']) ?>" aria-label="Abrir trilha de <?= htmlspecialchars($trilha['titulo'], ENT_QUOTES, 'UTF-8') ?>">
                    <span class="trail-icon"><?= $trilha['icone'] ?></span><h2><?= htmlspecialchars($trilha['titulo'], ENT_QUOTES, 'UTF-8') ?></h2><p><?= htmlspecialchars($trilha['descricao'], ENT_QUOTES, 'UTF-8') ?></p>
                    <div class="trail-footer"><div class="trail-progress-label"><span><?= $alunoId === null ? '9 etapas disponíveis' : $trilha['concluidas'] . ' de ' . $trilha['total'] . ' etapas concluídas' ?></span><strong><?= $trilha['progresso'] ?>%</strong></div><div class="trail-progress"><i style="--progress:<?= $trilha['progresso'] ?>%"></i></div><span class="trail-open">Abrir trilha <b aria-hidden="true">→</b></span></div>
                </a>
            <?php endforeach; ?>
        </section>
        <?php if ($alunoId === null): ?><p class="choice-note">Entre na sua conta para salvar o avanço, receber XP e acompanhar sua sequência diária.</p><?php endif; ?>
    </main>
</body>
</html>
