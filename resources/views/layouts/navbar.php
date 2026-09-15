<?php
$navbarActive = $navbarActive ?? '';
$navbarRole = $_SESSION['role'] ?? null;
$navbarName = trim((string) ($_SESSION['usuario'] ?? ''));
$navbarLoggedIn = in_array($navbarRole, ['aluno', 'professor'], true) && $navbarName !== '';

if ($navbarLoggedIn) {
    $navbarProfileRoute = $navbarRole === 'professor' ? '/professor/dashboard' : '/aluno/dashboard';
    $navbarName = function_exists('mb_convert_case')
        ? mb_convert_case($navbarName, MB_CASE_TITLE, 'UTF-8')
        : ucwords($navbarName);
}

$navbarLinks = [
    'trilhas' => ['label' => 'Trilha de aprendizado', 'route' => '/trilhas'],
    'videoaulas' => ['label' => 'Videoaulas', 'route' => '/videoaulas'],
    'simulados' => ['label' => 'Simulados', 'route' => '/aluno/simulados'],
    'ranking' => ['label' => 'Ranking', 'route' => '/ranking'],
    'sobre' => ['label' => 'Sobre', 'route' => '/sobre'],
];
?>
<link rel="stylesheet" href="<?= app_asset('css/site_nav.css') ?>?v=1">
<nav class="site-nav" aria-label="Navegação principal">
    <a class="site-nav-brand" href="<?= app_route('/') ?>" aria-label="Página inicial">
        <img src="<?= app_asset('images/home/logo.png') ?>" alt="HSchuler">
    </a>

    <div class="site-nav-links">
        <?php foreach ($navbarLinks as $key => $link): ?>
            <a class="<?= $navbarActive === $key ? 'is-active' : '' ?>" href="<?= app_route($link['route']) ?>"><?= $link['label'] ?></a>
        <?php endforeach; ?>
    </div>

    <div class="site-nav-actions">
        <?php if ($navbarLoggedIn): ?>
            <a class="site-nav-profile" href="<?= app_route($navbarProfileRoute) ?>">
                <span class="site-nav-status" aria-hidden="true"></span>
                <span><?= htmlspecialchars($navbarName, ENT_QUOTES, 'UTF-8') ?></span>
            </a>
            <a class="site-nav-cta" href="<?= app_route('/auth/logout') ?>">Sair</a>
        <?php else: ?>
            <a class="site-nav-login" href="<?= app_route('/login') ?>">Login</a>
            <a class="site-nav-cta" href="<?= app_route('/cadastro') ?>">Começar agora</a>
        <?php endif; ?>
    </div>
</nav>
