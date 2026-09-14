<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Cadastro</title>
    <link rel="stylesheet" type="text/css" href="<?= app_asset('css/estilo_cadastro.css') ?>">
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
  <form class="login" method="POST" action="<?= APP_URL ?>/index.php?route=/auth/register-aluno-submit">
        <h2>Cadastro</h2>
        <div class="inputBx">
          <input type="text" name="nome" placeholder="Usuario" required>
        </div>
        <div class="inputBx">
          <input type="email" name="email" placeholder="Email" required>
        </div>
        <div class="inputBx">
          <input type="date" name="data_nascimento" required>
        </div>
        <div class="inputBx">
          <input type="password" name="senha" placeholder="Senha" required>
        </div>
        <div class="inputBx">
          <input type="password" name="confirma_senha" placeholder="Confirmação de senha" required>
        </div>
        <div class="inputBx">
          <input type="submit" value="Criar conta">
        </div>
        <div class="links">
          <a href="<?= APP_URL ?>/index.php?route=/login">Já possui conta? Faça login</a>
        </div>
  </form>
</div>
<script src="<?= ASSET_URL ?>/js/javascript.js"></script>
</body>
</html>
