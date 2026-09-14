<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login - HSchuler</title>
    <link rel="stylesheet" type="text/css" href="<?= ASSET_URL ?>/css/estilo_login.css">
</head>
<body>
<nav>
  <div class="left">
    <ul>
      <li><a href="<?= app_route('/') ?>">Home</a></li>
    </ul>
  </div>
  <div class="center">
    <ul>
      <li><a href="<?= app_route('/') ?>">Trilha de aprendizado</a></li>
      <li><a href="<?= app_route('/videoaulas') ?>">Videoaulas</a></li>
      <li><a href="<?= app_route('/aluno/simulados') ?>">Simulados</a></li>
      <li><a href="<?= app_route('/ranking') ?>">Ranking</a></li>
      <li><a href="<?= app_route('/') ?>#sobre">Sobre</a></li>
    </ul>
  </div>
  <div class="right">
    <ul>
      <li><a href="<?= app_route('/login') ?>">Login</a></li>
    </ul>
  </div>
</nav>
<div class="ring">
    <i style="--clr:#00002e;"></i>
    <i style="--clr:#ffffff;"></i>
    <i style="--clr:#00002e;"></i>
  <div class="login">
    <h2>Login</h2>
    <?php
    if (isset($_GET['auth_error'])) {
        echo '<div class="error-message">' . htmlspecialchars($_GET['auth_error']) . '</div>';
    }
    if (isset($_GET['success'])) {
        echo '<div class="success-message">' . htmlspecialchars($_GET['success']) . '</div>';
    }
    ?>
    <form action="<?= APP_URL ?>/index.php?route=/auth/login-submit" method="post">
          <div class="inputBx">
            <input type="text" placeholder="Usuario" name="usuario" required>
          </div>
          <div class="inputBx">
            <input type="password" placeholder="Senha" name="senha" required>
          </div>
          <div class="inputBx">
            <input type="submit" value="Entrar">
          </div>
          <div class="links">
            <a href="<?= app_route('/login') ?>">Esqueceu sua senha</a>
            <a href="<?= APP_URL ?>/index.php?route=/cadastro" class="signup-trigger">Faça seu cadastro</a>
          </div>
          <div class="signup-dropdown">
            <p>Faça login para acessar o cadastro ou <a href="<?= app_route('/cadastro') ?>">clique aqui</a> para criar uma conta.</p>
          </div>
    </form>
  </div>
</div>
<script src="<?= ASSET_URL ?>/js/javascript.js"></script>
</body>
</html>