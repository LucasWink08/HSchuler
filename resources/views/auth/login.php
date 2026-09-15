<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login - HSchuler</title>
    <link rel="stylesheet" href="<?= app_asset('css/estilo_homepage.css') ?>?v=33">
    <link rel="stylesheet" href="<?= app_asset('css/auth_theme.css') ?>?v=4">
</head>
<body class="home-page auth-page">
<?php require APP_ROOT . '/resources/views/layouts/navbar.php'; ?>
<div class="ring">
    <i style="--clr:#00002e;"></i>
    <i style="--clr:#ffffff;"></i>
    <i style="--clr:#00002e;"></i>
  <div class="login">
    <h2>Login</h2>
    <p class="auth-description">Acesse sua jornada de aprendizagem.</p>
    <?php
    if (isset($_GET['auth_error'])) {
        echo '<div class="error-message" role="alert">' . htmlspecialchars($_GET['auth_error'], ENT_QUOTES, 'UTF-8') . '</div>';
    }
    if (isset($_GET['success'])) {
        echo '<div class="success-message" role="status">' . htmlspecialchars($_GET['success'], ENT_QUOTES, 'UTF-8') . '</div>';
    }
    ?>
    <form action="<?= app_route('/auth/login-submit') ?>" method="post">
          <div class="inputBx">
            <label class="sr-only" for="usuario">Usu&aacute;rio</label>
            <input id="usuario" type="text" placeholder="Usu&aacute;rio" name="usuario" required autocomplete="username">
          </div>
          <div class="inputBx">
            <label class="sr-only" for="senha">Senha</label>
            <input id="senha" type="password" placeholder="Senha" name="senha" required autocomplete="current-password">
          </div>
          <div class="inputBx">
            <input type="submit" value="Entrar">
          </div>
          <div class="links login-actions">
            <a class="login-action login-action-secondary" href="<?= app_route('/login/professor') ?>">Login de professor</a>
            <a class="login-action login-action-primary" href="<?= app_route('/cadastro') ?>">Criar conta</a>
          </div>
    </form>
  </div>
</div>
<script src="<?= app_asset('js/javascript.js') ?>"></script>
</body>
</html>
