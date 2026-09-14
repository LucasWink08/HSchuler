<?php
$alunoId = filter_var($_SESSION['user_id'] ?? null, FILTER_VALIDATE_INT);
$alunoId = $alunoId !== false && $alunoId !== null && $alunoId > 0 ? (int) $alunoId : null;
$usuario = trim((string) ($_SESSION['usuario'] ?? ''));
$estaLogado = $alunoId !== null && $usuario !== '';
$resumo = [
    'xp' => null,
    'streak_atual' => null,
    'etapas_concluidas' => null,
    'total_etapas' => null,
];
$progressoAreas = [];

if ($estaLogado) {
    $trilhaService = new TrilhaService();
    $resumo = array_merge($resumo, $trilhaService->getResumo($alunoId));
    $progressoAreas = $trilhaService->getProgressoPorArea($alunoId);
}

$xp = $resumo['xp'];
$streak = $resumo['streak_atual'];
$etapasConcluidas = $resumo['etapas_concluidas'];
$totalEtapas = $resumo['total_etapas'];
$areas = [
    ['id' => 'potenciacao', 'titulo' => 'Potenciação', 'icone' => 'a<sup>2</sup>', 'nivel' => 'Iniciante', 'cor' => 'blue'],
    ['id' => 'fracoes-algebricas', 'titulo' => 'Frações algébricas', 'icone' => '<span>x</span><small>y</small>', 'nivel' => 'Intermediário', 'cor' => 'orange'],
    ['id' => 'produtos-notaveis', 'titulo' => 'Produtos notáveis', 'icone' => '(a + b)<sup>2</sup>', 'nivel' => 'Intermediário', 'cor' => 'orange'],
    ['id' => 'fatoracao', 'titulo' => 'Fatoração', 'icone' => '(x - a)(x + a)', 'nivel' => 'Avançado', 'cor' => 'pink'],
    ['id' => 'equacoes', 'titulo' => 'Equações', 'icone' => 'x<sup>2</sup> - 4 = 0', 'nivel' => 'Avançado', 'cor' => 'pink'],
    ['id' => 'inequacoes', 'titulo' => 'Inequações', 'icone' => 'x &gt; 0', 'nivel' => 'Especialista', 'cor' => 'purple'],
];
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>HSchuler — Domine a matemática</title>
    <link rel="stylesheet" href="<?= app_asset('css/estilo_homepage.css') ?>?v=22">
    <script src="https://unpkg.com/scrollreveal"></script>
</head>
<body class="home-page">
    <nav class="home-nav" aria-label="Navegação principal">
        <a class="home-logo" href="<?= app_route('/') ?>" aria-label="Página inicial">
            <img src="<?= app_asset('images/home/logo.png') ?>" alt="HSchuler">
        </a>
        <div class="home-nav-links">
            <div class="trilha-dropdown">
                <button class="trilha-trigger is-active" type="button" aria-expanded="false" aria-controls="trilha-menu">Trilha de aprendizado <span aria-hidden="true">⌄</span></button>
                <div class="trilha-menu" id="trilha-menu">
                    <?php foreach ($areas as $area): ?>
                        <a href="<?= app_route('/aluno/trilha') ?>&amp;area=<?= urlencode($area['id']) ?>"><?= htmlspecialchars($area['titulo'], ENT_QUOTES, 'UTF-8') ?></a>
                    <?php endforeach; ?>
                </div>
            </div>
            <a href="<?= app_route('/videoaulas') ?>">Videoaulas</a>
            <a href="<?= app_route('/aluno/simulados') ?>">Simulados</a>
            <a href="<?= app_route('/ranking') ?>">Ranking</a>
            <a href="#sobre">Sobre</a>
        </div>
        <div class="home-nav-actions">
            <?php if ($estaLogado): ?>
                <span class="account-link welcome-account">Bem-vindo, <strong><?= htmlspecialchars($usuario, ENT_QUOTES, 'UTF-8') ?></strong></span>
                <a class="nav-cta" href="<?= app_route('/auth/logout') ?>">Sair</a>
            <?php else: ?>
                <a class="account-link" href="<?= app_route('/login') ?>"><span aria-hidden="true">&#9787;</span>Login</a>
                <a class="nav-cta" href="<?= app_route('/cadastro') ?>">Começar agora</a>
            <?php endif; ?>
        </div>
    </nav>

    <main class="home-main">
        <section class="home-hero" id="sobre" aria-labelledby="hero-title">
            <div class="hero-orbit" aria-hidden="true"></div>
            <img class="hero-logo" src="<?= app_asset('images/home/logo.png') ?>" alt="HSchuler">
            <h1 id="hero-title">Domine a <span>matemática.</span></h1>
            <p>Aprenda, pratique e evolua com uma plataforma criada para transformar seus estudos em uma experiência de jogo.</p>
            <div class="hero-actions">
                <a class="hero-button hero-button-primary" href="<?= $estaLogado ? app_route('/aluno/trilha') : app_route('/cadastro') ?>"><?= $estaLogado ? 'Continuar jornada' : 'Começar agora' ?> <span aria-hidden="true">&rarr;</span></a>
                <a class="hero-button hero-button-secondary" href="#trilha"><span aria-hidden="true">&#9675;</span> Explorar trilha</a>
            </div>
        </section>

        <aside class="home-profile-card" aria-label="Resumo do seu progresso">
            <div class="profile-level">
                <div class="level-badge" aria-hidden="true">◆</div>
                <div>
                    <span>Nível</span>
                    <strong><?= $estaLogado ? 'Em evolução' : 'Comece sua jornada' ?></strong>
                </div>
            </div>
            <div class="xp-row"><span><?= $estaLogado ? 'XP registrado: ' . ($xp ?? 0) : 'Entre para registrar XP' ?></span></div>
            <div class="profile-stats">
                <div><span class="stat-symbol fire" aria-hidden="true">&#128293;</span><p>Sequência<strong><?= $estaLogado && $streak !== null ? $streak . ' dias' : '&mdash;' ?></strong></p></div>
                <a href="<?= app_route('/ranking') ?>"><span class="stat-symbol trophy" aria-hidden="true">&#127942;</span><p>Ranking<strong>Ver ranking</strong></p></a>
            </div>
        </aside>

        <section class="learning-section" id="trilha" aria-labelledby="learning-title">
            <header class="learning-header">
                <div>
                    <p class="section-mark" aria-hidden="true">//</p>
                    <h2 id="learning-title">Trilha de aprendizado</h2>
                    <p>Domine os principais conteúdos e avance no seu ritmo.</p>
                </div>
            </header>
            <div class="learning-cards">
                <?php foreach ($areas as $area): ?>
                    <?php $progresso = $progressoAreas[$area['id']] ?? ['concluidas' => 0, 'total' => 0, 'percentual' => 0]; ?>
                    <a class="learning-card" href="<?= app_route('/aluno/trilha') ?>&amp;area=<?= urlencode($area['id']) ?>" aria-label="Abrir trilha de <?= htmlspecialchars($area['titulo'], ENT_QUOTES, 'UTF-8') ?>">
                        <span class="card-formula <?= $area['cor'] ?>"><?= $area['icone'] ?></span>
                        <strong><?= htmlspecialchars($area['titulo'], ENT_QUOTES, 'UTF-8') ?></strong>
                        <span class="card-level"><i class="<?= $area['cor'] ?>"></i><?= $area['nivel'] ?></span>
                        <div class="card-footer">
                            <div class="card-progress"><span><i style="--progress:<?= $progresso['percentual'] ?>%"></i></span><small><?= $progresso['percentual'] ?>%</small></div>
                            <span class="card-open" aria-hidden="true">&rarr;</span>
                        </div>
                    </a>
                <?php endforeach; ?>
            </div>
            <?php if ($estaLogado && $totalEtapas !== null && $totalEtapas > 0): ?>
                <p class="learning-summary"><?= $etapasConcluidas ?> de <?= $totalEtapas ?> etapas concluídas na sua jornada.</p>
            <?php endif; ?>
        </section>
    </main>
    <script>
        const dropdown = document.querySelector('.trilha-dropdown');
        const trigger = dropdown?.querySelector('.trilha-trigger');

        trigger?.addEventListener('click', () => {
            const aberto = dropdown.classList.toggle('is-open');
            trigger.setAttribute('aria-expanded', String(aberto));
        });

        document.addEventListener('click', (event) => {
            if (dropdown && !dropdown.contains(event.target)) {
                dropdown.classList.remove('is-open');
                trigger?.setAttribute('aria-expanded', 'false');
            }
        });

        document.addEventListener('keydown', (event) => {
            if (event.key === 'Escape') {
                dropdown?.classList.remove('is-open');
                trigger?.setAttribute('aria-expanded', 'false');
                trigger?.focus();
            }
        });

        if (window.ScrollReveal) {
            ScrollReveal().reveal('.learning-card', {
                origin: 'bottom',
                distance: '50px',
                duration: 1000,
                interval: 200,
                reset: false,
                viewFactor: 0.2
            });
        }
    </script>
</body>
</html>
