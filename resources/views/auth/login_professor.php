<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login do Professor - HSchuler</title>
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
    <h2>Login do Professor</h2>
    <p class="auth-description">Entre para gerenciar suas aulas e atividades.</p>
    <?php if (isset($_GET['auth_error'])): ?>
      <div class="error-message" role="alert"><?= htmlspecialchars($_GET['auth_error'], ENT_QUOTES, 'UTF-8') ?></div>
    <?php endif; ?>
    <form action="<?= app_route('/auth/professor-login-submit') ?>" method="post">
      <div class="inputBx"><label class="sr-only" for="siape">SIAPE</label><input id="siape" type="text" name="siape" placeholder="SIAPE" required maxlength="50" autocomplete="username"></div>
      <div class="inputBx"><label class="sr-only" for="senha">Senha</label><input id="senha" type="password" name="senha" placeholder="Senha" required autocomplete="current-password"></div>
      <div class="inputBx"><input type="submit" value="Entrar"></div>
      <div class="links login-actions">
        <a href="<?= app_route('/login') ?>">Login de aluno</a>
        <a href="<?= app_route('/cadastro/professor') ?>">Cadastre-se</a>
      </div>
    </form>
  </div>
</div>
</body>
</html>
