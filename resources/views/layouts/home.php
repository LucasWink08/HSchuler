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
$professorLogado = $estaLogado && $role === 'professor';
$siteRoot = rtrim((string) preg_replace('#/public$#', '', APP_URL), '/');
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
$nivel = $resumo['nivel'] ?? 1;
$streak = $resumo['streak_atual'];
$etapasConcluidas = $resumo['etapas_concluidas'];
$totalEtapas = $resumo['total_etapas'];
$progressaoNivel = [
    'nivel' => 1,
    'inicio' => 0,
    'fim' => 50,
    'percentual' => 0,
    'xp_restante' => 50,
];
if ($alunoLogado) {
    $progressaoNivel = $trilhaService->getProgressaoNivel((int) ($xp ?? 0));
    $nivel = $progressaoNivel['nivel'];
}
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
    <link rel="stylesheet" href="<?= app_asset('css/estilo_homepage.css') ?>?v=37">
    <script src="https://unpkg.com/scrollreveal"></script>
</head>
<body class="home-page">
    <?php $navbarActive = 'trilhas'; require APP_ROOT . '/resources/views/layouts/navbar.php'; ?>

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

    <?php if (!$estaLogado): ?>
        <div class="auth-modal" id="auth-modal" role="presentation" hidden>
            <section class="auth-dialog" role="dialog" aria-labelledby="auth-title" aria-describedby="auth-description">
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

    <div class="home-floating-art" aria-hidden="true">
        <img class="home-floating-mask" src="<?= $siteRoot ?>/imgs/mask.png" alt="">
        <?php if (!$estaLogado): ?>
            <img class="guest-floating-page guest-floating-page-one" src="<?= $siteRoot ?>/imgs/pag1.png" alt="">
            <img class="guest-floating-page guest-floating-page-two" src="<?= $siteRoot ?>/imgs/pag2.png" alt="">
            <img class="guest-floating-page guest-floating-page-three" src="<?= $siteRoot ?>/imgs/pag3.png" alt="">
            <img class="guest-floating-page guest-floating-page-four" src="<?= $siteRoot ?>/imgs/pag4.png" alt="">
            <img class="guest-floating-page guest-floating-page-five" src="<?= $siteRoot ?>/imgs/pag5.png" alt="">
            <img class="guest-floating-page guest-floating-page-six" src="<?= $siteRoot ?>/imgs/pag6.png" alt="">
        <?php endif; ?>
    </div>

    <main class="home-main">
        <?php if ($professorLogado): ?>
            <section class="home-hero professor-hero" aria-labelledby="hero-title">
                <div class="hero-orbit" aria-hidden="true"></div>
                <img class="hero-logo" src="<?= app_asset('images/home/logo.png') ?>" alt="HSchuler">
                <p class="professor-hero-eyebrow">Área do professor</p>
                <h1 id="hero-title">Seja bem-vindo, <span>professor <?= htmlspecialchars($usuarioExibicao, ENT_QUOTES, 'UTF-8') ?></span></h1>
                <p>Organize conteúdos para seus alunos e acompanhe a evolução da turma pela plataforma.</p>
                <div class="hero-actions">
                    <a class="hero-button hero-button-primary" href="<?= app_route('/professor/videos') ?>">Cadastrar videoaulas <span aria-hidden="true">&rarr;</span></a>
                    <a class="hero-button hero-button-secondary" href="<?= app_route('/ranking') ?>"><span aria-hidden="true">&#9733;</span> Visualizar ranking</a>
                </div>
            </section>
        <?php else: ?>
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
        <?php endif; ?>

        <?php if ($alunoLogado): ?>
            <aside class="home-profile-card" aria-label="Resumo do seu progresso">
                <div class="profile-level">
                    <div class="level-badge" aria-hidden="true">◆</div>
                    <div>
                        <span>Nível</span>
                        <strong>Nível <?= (int) $nivel ?></strong>
                    </div>
                </div>
                <div class="xp-row">
                    <span>XP registrado: <?= $xp ?? 0 ?></span>
                    <?php if ($alunoLogado): ?>
                        <div class="level-progress" aria-label="<?= $progressaoNivel['percentual'] ?>% do nível <?= $nivel ?>">
                            <div class="level-progress-label"><span><?= $progressaoNivel['inicio'] ?> XP</span><strong><?= $xp ?? 0 ?> / <?= $progressaoNivel['fim'] ?> XP</strong></div>
                            <span class="level-progress-track"><i style="--level-progress:<?= $progressaoNivel['percentual'] ?>%"></i></span>
                            <small><?= $nivel < 5 ? $progressaoNivel['xp_restante'] . ' XP para o nível ' . ($nivel + 1) : 'Nível máximo alcançado nesta trilha' ?></small>
                        </div>
                    <?php endif; ?>
                </div>
                <div class="profile-stats">
                    <div><span class="stat-symbol fire" aria-hidden="true">&#128293;</span><p>Sequência<strong><?= $streak !== null ? $streak . ' dias' : '&mdash;' ?></strong></p></div>
                    <a href="<?= app_route('/ranking') ?>"><span class="stat-symbol trophy" aria-hidden="true">&#127942;</span><p>Ranking<strong>Ver ranking</strong></p></a>
                </div>
            </aside>
        <?php endif; ?>

        <?php if ($professorLogado): ?>
            <section class="professor-home-actions" aria-label="Ações do professor">
                <a class="professor-home-card" href="<?= app_route('/professor/videos') ?>">
                    <span class="professor-action-mark" aria-hidden="true">01</span>
                    <strong>Cadastrar videoaulas</strong>
                    <p>Prepare aulas e disponibilize os conteúdos para os alunos acessarem.</p>
                    <span class="professor-action-link">Gerenciar videoaulas <b aria-hidden="true">&rarr;</b></span>
                </a>
                <a class="professor-home-card" href="<?= app_route('/ranking') ?>">
                    <span class="professor-action-mark" aria-hidden="true">02</span>
                    <strong>Visualizar ranking</strong>
                    <p>Confira o desempenho dos alunos e acompanhe quem mais evoluiu.</p>
                    <span class="professor-action-link">Abrir ranking <b aria-hidden="true">&rarr;</b></span>
                </a>
            </section>
        <?php else: ?>
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
        <?php endif; ?>

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
        const authRequiredLinks = document.querySelectorAll('[data-auth-required]');
        let lastAuthTrigger = null;
        let authModalTimeout = null;

        const closeAuthModal = (restoreFocus = false) => {
            if (!authModal) {
                return;
            }

            window.clearTimeout(authModalTimeout);
            authModal.hidden = true;

            if (restoreFocus) {
                lastAuthTrigger?.focus();
            }
        };

        const openAuthModal = (triggerElement = null) => {
            if (!authModal) {
                return;
            }

            lastAuthTrigger = triggerElement;
            window.clearTimeout(authModalTimeout);
            authModal.hidden = false;
            authModalTimeout = window.setTimeout(closeAuthModal, 3000);
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
                    closeAuthModal(true);
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
