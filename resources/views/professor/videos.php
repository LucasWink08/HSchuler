<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Publicar videoaulas | HSchuler</title>
    <link rel="stylesheet" href="<?= app_asset('css/estilo_homepage.css') ?>?v=37">
    <link rel="stylesheet" href="<?= app_asset('css/professor_videos.css') ?>?v=1">
</head>
<body class="teacher-videos-page">
    <?php $navbarActive = 'publicar-videoaula'; require APP_ROOT . '/resources/views/layouts/navbar.php'; ?>

    <main class="teacher-video-shell">
        <header class="teacher-video-header">
            <p>Publicação de conteúdo</p>
            <h1>Escolha um <span>conteúdo</span></h1>
            <span>Cadastre videoaulas para cada tema da plataforma. Assim que publicadas, elas ficam disponíveis para os alunos na área de Videoaulas.</span>
        </header>

        <?php if ($success !== ''): ?>
            <p class="teacher-video-notice" role="status"><?= htmlspecialchars($success, ENT_QUOTES, 'UTF-8') ?></p>
        <?php endif; ?>
        <?php if ($error !== ''): ?>
            <p class="teacher-video-notice is-error" role="alert"><?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?></p>
        <?php endif; ?>

        <section class="teacher-video-grid" aria-label="Conteúdos disponíveis para publicação">
            <?php foreach ($areas as $area): ?>
                <article class="teacher-content-card">
                    <div class="teacher-content-top">
                        <span class="teacher-content-icon" aria-hidden="true"><?= htmlspecialchars($area['icone'], ENT_QUOTES, 'UTF-8') ?></span>
                        <span class="teacher-content-level"><?= htmlspecialchars($area['nivel'], ENT_QUOTES, 'UTF-8') ?></span>
                    </div>
                    <h2><?= htmlspecialchars($area['titulo'], ENT_QUOTES, 'UTF-8') ?></h2>
                    <p><?= htmlspecialchars($area['descricao'], ENT_QUOTES, 'UTF-8') ?></p>
                    <div class="teacher-card-footer">
                        <span class="teacher-video-count"><?= (int) $area['video_count'] ?> <?= (int) $area['video_count'] === 1 ? 'videoaula publicada' : 'videoaulas publicadas' ?></span>
                        <?php if ($area['modulo_id'] !== null): ?>
                            <a class="teacher-3d-button" href="<?= app_route('/professor/video/cadastro') ?>&amp;area=<?= urlencode($area['slug']) ?>">Cadastrar videoaula</a>
                        <?php else: ?>
                            <span class="teacher-3d-button is-disabled">Indisponível</span>
                        <?php endif; ?>
                    </div>
                </article>
            <?php endforeach; ?>
        </section>
    </main>
</body>
</html>
