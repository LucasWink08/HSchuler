<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Cadastro Professor</title>
  <link rel="stylesheet" href="<?= app_asset('css/estilo_homepage.css') ?>?v=33">
  <link rel="stylesheet" href="<?= app_asset('css/auth_theme.css') ?>?v=4">
</head>
<body class="home-page auth-page auth-register-page">
<?php require APP_ROOT . '/resources/views/layouts/navbar.php'; ?>

<div class="ring">
  <i style="--clr:#00002e;"></i>
  <i style="--clr:#ffffff;"></i>
  <i style="--clr:#00002e;"></i>

  <form class="login" method="POST" action="<?= app_route('/auth/register-professor-submit') ?>">
    <h2>Cadastro</h2>
    <p class="auth-description">Crie seu acesso para acompanhar suas turmas.</p>

    <?php if (isset($_GET['error'])): ?>
      <div class="error-message" role="alert"><?= htmlspecialchars($_GET['error'], ENT_QUOTES, 'UTF-8') ?></div>
    <?php endif; ?>

    <div class="inputBx">
      <label class="sr-only" for="siape">SIAPE</label>
      <input id="siape" type="text" name="siape" placeholder="SIAPE" required maxlength="20" autocomplete="username">
    </div>

    <div class="inputBx">
      <label class="sr-only" for="nome">Nome</label>
      <input id="nome" type="text" name="nome" placeholder="Nome" required maxlength="100" autocomplete="name">
    </div>

    <div class="inputBx">
      <label class="sr-only" for="email">E-mail</label>
      <input id="email" type="email" name="email" placeholder="E-mail" required maxlength="150" autocomplete="email">
    </div>

    <div class="inputBx">
      <label class="sr-only" for="senha">Senha</label>
      <input id="senha" type="password" name="senha" placeholder="Senha" required maxlength="255" autocomplete="new-password">
    </div>

    <div class="inputBx">
      <label class="sr-only" for="confirma_senha">Confirmação de senha</label>
      <input id="confirma_senha" type="password" name="confirma_senha" placeholder="Confirmação de senha" required maxlength="255" autocomplete="new-password">
    </div>

    <div class="inputBx">
      <input type="submit" value="Criar conta">
    </div>

    <div class="links login-actions">
      <a href="<?= app_route('/login/professor') ?>">Já possui conta? Entre</a>
    </div>
  </form>
</div>

<script src="<?= app_asset('js/javascript.js') ?>"></script>
</body>
</html>

