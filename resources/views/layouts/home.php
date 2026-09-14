<?php
$alunoId = filter_var($_SESSION['user_id'] ?? null, FILTER_VALIDATE_INT);
$alunoId = $alunoId !== false && $alunoId !== null && $alunoId > 0 ? (int) $alunoId : null;
$usuario = trim((string) ($_SESSION['usuario'] ?? ''));
$usuarioExibicao = $usuario;

if ($usuarioExibicao !== '') {
    $usuarioExibicao = function_exists('mb_substr') && function_exists('mb_strtoupper')
        ? mb_strtoupper(mb_substr($usuarioExibicao, 0, 1, 'UTF-8'), 'UTF-8') . mb_substr($usuarioExibicao, 1, null, 'UTF-8')
        : ucfirst($usuarioExibicao);
}

$role = $_SESSION['role'] ?? null;
$estaLogado = $alunoId !== null && $usuario !== '' && in_array($role, ['aluno', 'professor'], true);
$alunoLogado = $estaLogado && $role === 'aluno';
$resumo = [
    'xp' => null,
    'streak_atual' => null,
    'etapas_concluidas' => null,
    'total_etapas' => null,
];
$progressoAreas = [];

if ($alunoLogado) {
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
    <link rel="stylesheet" href="<?= app_asset('css/estilo_homepage.css') ?>?v=28">
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
                        <a href="<?= app_route('/aluno/trilha') ?>&amp;area=<?= urlencode($area['id']) ?>" data-auth-required><?= htmlspecialchars($area['titulo'], ENT_QUOTES, 'UTF-8') ?></a>
                    <?php endforeach; ?>
                </div>
            </div>
            <a href="<?= app_route('/videoaulas') ?>">Videoaulas</a>
            <a href="<?= app_route('/aluno/simulados') ?>" data-auth-required>Simulados</a>
            <a href="<?= app_route('/ranking') ?>">Ranking</a>
            <a href="#sobre">Sobre</a>
        </div>
        <div class="home-nav-actions">
            <?php if ($estaLogado): ?>
                <span class="account-link welcome-account">Bem-vindo, <strong><?= htmlspecialchars($usuarioExibicao, ENT_QUOTES, 'UTF-8') ?></strong></span>
                <button class="nav-cta logout-trigger" type="button" aria-haspopup="dialog" aria-controls="logout-modal">Sair</button>
            <?php else: ?>
                <a class="account-link" href="<?= app_route('/login') ?>"><span aria-hidden="true">&#9787;</span>Login</a>
                <a class="nav-cta" href="<?= app_route('/cadastro') ?>">Começar agora</a>
            <?php endif; ?>
        </div>
    </nav>

    <?php if ($estaLogado): ?>
        <div class="logout-modal" id="logout-modal" role="presentation" hidden>
            <section class="logout-dialog" role="dialog" aria-modal="true" aria-labelledby="logout-title" aria-describedby="logout-description">
                <p class="logout-dialog-mark" aria-hidden="true">↗</p>
                <h2 id="logout-title">Sair da conta?</h2>
                <p id="logout-description">Você precisará informar seus dados para entrar novamente.</p>
                <div class="logout-dialog-actions">
                    <button class="logout-cancel" type="button">Cancelar</button>
                    <a class="logout-confirm" href="<?= app_route('/auth/logout') ?>">Sair</a>
                </div>
            </section>
        </div>
    <?php endif; ?>

    <?php if (!$alunoLogado): ?>
        <div class="auth-modal" id="auth-modal" role="presentation" hidden>
            <section class="auth-dialog" role="dialog" aria-modal="true" aria-labelledby="auth-title" aria-describedby="auth-description">
                <button class="auth-modal-close" type="button" aria-label="Fechar">&times;</button>
                <p class="auth-dialog-mark" aria-hidden="true">✦</p>
                <h2 id="auth-title">Entre para continuar</h2>
                <p id="auth-description">Crie sua conta ou faça login para acessar a trilha e seus exercícios.</p>
                <div class="auth-dialog-actions">
                    <a class="auth-login" href="<?= app_route('/login') ?>">Entrar</a>
                    <a class="auth-register" href="<?= app_route('/cadastro') ?>">Cadastre-se</a>
                </div>
            </section>
        </div>
    <?php endif; ?>

    <main class="home-main">
        <section class="home-hero" aria-labelledby="hero-title">
            <div class="hero-orbit" aria-hidden="true"></div>
            <img class="hero-logo" src="<?= app_asset('images/home/logo.png') ?>" alt="HSchuler">
            <h1 id="hero-title">
                <?php if ($estaLogado): ?>
                    Seja bem-vindo, <span><?= htmlspecialchars($usuarioExibicao, ENT_QUOTES, 'UTF-8') ?></span>
                <?php else: ?>
                    Domine a <span>matemática.</span>
                <?php endif; ?>
            </h1>
            <p>Aprenda, pratique e evolua com uma plataforma criada para transformar seus estudos em uma experiência de jogo.</p>
            <div class="hero-actions">
                <a class="hero-button hero-button-primary" href="<?= $alunoLogado ? app_route('/aluno/trilha') : app_route('/cadastro') ?>"><?= $alunoLogado ? 'Continuar jornada' : 'Começar agora' ?> <span aria-hidden="true">&rarr;</span></a>
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
                    <a class="learning-card" href="<?= app_route('/aluno/trilha') ?>&amp;area=<?= urlencode($area['id']) ?>" data-auth-required aria-label="Abrir trilha de <?= htmlspecialchars($area['titulo'], ENT_QUOTES, 'UTF-8') ?>">
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
            <p class="learning-description">Aqui você encontra estas trilhas de conteúdos.</p>
            <?php if ($estaLogado && $totalEtapas !== null && $totalEtapas > 0): ?>
                <p class="learning-summary"><?= $etapasConcluidas ?> de <?= $totalEtapas ?> etapas concluídas na sua jornada.</p>
            <?php endif; ?>
        </section>

        <section class="about-section" id="sobre" aria-labelledby="about-title">
            <div class="about-intro">
                <p class="about-eyebrow">Conheça a plataforma</p>
                <h2 id="about-title">Aprender matemática pode ser <span>mais leve.</span></h2>
                <p>O HSchuler é uma plataforma de aprendizagem que une conteúdo, prática e acompanhamento para tornar sua evolução em matemática mais clara e motivadora.</p>
            </div>
            <div class="about-highlights">
                <article class="about-highlight">
                    <span class="about-icon" aria-hidden="true">01</span>
                    <h3>Aprenda no seu ritmo</h3>
                    <p>Explore os conteúdos passo a passo e avance quando se sentir preparado.</p>
                </article>
                <article class="about-highlight">
                    <span class="about-icon" aria-hidden="true">02</span>
                    <h3>Pratique de verdade</h3>
                    <p>Resolva exercícios e fortaleça o que aprendeu em cada etapa da trilha.</p>
                </article>
                <article class="about-highlight">
                    <span class="about-icon" aria-hidden="true">03</span>
                    <h3>Acompanhe sua evolução</h3>
                    <p>Veja seu progresso, conquiste experiência e mantenha a motivação para continuar.</p>
                </article>
            </div>
        </section>
    </main>
    <script>
        const dropdown = document.querySelector('.trilha-dropdown');
        const trigger = dropdown?.querySelector('.trilha-trigger');
        const logoutTrigger = document.querySelector('.logout-trigger');
        const logoutModal = document.querySelector('#logout-modal');
        const logoutCancel = logoutModal?.querySelector('.logout-cancel');
        const authModal = document.querySelector('#auth-modal');
        const authModalClose = authModal?.querySelector('.auth-modal-close');
        const authRequiredLinks = document.querySelectorAll('[data-auth-required]');
        let lastAuthTrigger = null;

        const closeAuthModal = () => {
            if (!authModal) {
                return;
            }

            authModal.hidden = true;
            lastAuthTrigger?.focus();
        };

        const openAuthModal = (triggerElement = null) => {
            if (!authModal) {
                return;
            }

            lastAuthTrigger = triggerElement;
            authModal.hidden = false;
            authModalClose?.focus();
        };

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

                if (authModal && !authModal.hidden) {
                    closeAuthModal();
                } else if (logoutModal && !logoutModal.hidden) {
                    logoutModal.hidden = true;
                    logoutTrigger?.focus();
                } else {
                    trigger?.focus();
                }
            }
        });

        logoutTrigger?.addEventListener('click', () => {
            logoutModal.hidden = false;
            logoutCancel?.focus();
        });

        logoutCancel?.addEventListener('click', () => {
            logoutModal.hidden = true;
            logoutTrigger?.focus();
        });

        logoutModal?.addEventListener('click', (event) => {
            if (event.target === logoutModal) {
                logoutModal.hidden = true;
                logoutTrigger?.focus();
            }
        });

        authRequiredLinks.forEach((link) => {
            link.addEventListener('click', (event) => {
                if (authModal) {
                    event.preventDefault();
                    openAuthModal(link);
                }
            });
        });

        authModalClose?.addEventListener('click', closeAuthModal);

        authModal?.addEventListener('click', (event) => {
            if (event.target === authModal) {
                closeAuthModal();
            }
        });

        if (new URLSearchParams(window.location.search).get('access') === 'login-required') {
            openAuthModal();
            window.history.replaceState({}, '', window.location.pathname + '?route=%2F');
        }

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
