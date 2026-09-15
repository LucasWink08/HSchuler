<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastrar videoaula | HSchuler</title>
    <link rel="stylesheet" href="<?= app_asset('css/estilo_homepage.css') ?>?v=37">
    <link rel="stylesheet" href="<?= app_asset('css/professor_videos.css') ?>?v=1">
</head>
<body class="teacher-videos-page">
    <?php $navbarActive = 'publicar-videoaula'; require APP_ROOT . '/resources/views/layouts/navbar.php'; ?>

    <main class="teacher-video-shell teacher-form-shell">
        <a class="teacher-form-back" href="<?= app_route('/professor/videos') ?>">← Voltar para conteúdos</a>

        <form class="teacher-video-form" method="post" action="<?= app_route('/professor/video/salvar') ?>">
            <header class="teacher-video-form-header">
                <p>Nova videoaula</p>
                <h1>Publicar conteúdo</h1>
                <span>Informe os dados da aula para disponibilizá-la aos alunos.</span>
            </header>

            <?php if (isset($_GET['error'])): ?>
                <p class="teacher-video-notice is-error" role="alert"><?= htmlspecialchars((string) $_GET['error'], ENT_QUOTES, 'UTF-8') ?></p>
            <?php endif; ?>

            <p class="teacher-form-area"><span aria-hidden="true">◆</span> Conteúdo: <b><?= htmlspecialchars($area['titulo'], ENT_QUOTES, 'UTF-8') ?></b></p>
            <input type="hidden" name="area" value="<?= htmlspecialchars($area['slug'], ENT_QUOTES, 'UTF-8') ?>">
            <input type="hidden" name="token" value="<?= htmlspecialchars((string) ($_SESSION['video_form_token'] ?? ''), ENT_QUOTES, 'UTF-8') ?>">

            <div class="teacher-field">
                <label for="titulo">Título da videoaula</label>
                <input id="titulo" name="titulo" type="text" required maxlength="150" placeholder="Ex.: Propriedades da potenciação" autocomplete="off">
            </div>

            <div class="teacher-field">
                <label for="descricao">Descrição</label>
                <textarea id="descricao" name="descricao" maxlength="2000" placeholder="Explique brevemente o que o aluno aprenderá nesta aula."></textarea>
                <small>Opcional. Máximo de 2.000 caracteres.</small>
            </div>

            <div class="teacher-field">
                <label for="url_video">URL do vídeo</label>
                <input id="url_video" name="url_video" type="url" required maxlength="255" placeholder="https://www.youtube.com/watch?v=..." inputmode="url">
                <small>Use um link público com http ou https.</small>
            </div>

            <div class="teacher-form-actions">
                <button class="teacher-3d-button" type="submit">Publicar videoaula</button>
            </div>
        </form>
    </main>
</body>
</html>
