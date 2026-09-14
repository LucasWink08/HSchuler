<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login do Professor - HSchuler</title>
    <link rel="stylesheet" type="text/css" href="<?= app_asset('css/estilo_login.css') ?>">
</head>
<body>
<nav>
  <div class="left"><ul><li><a href="<?= app_route('/') ?>">Home</a></li></ul></div>
  <div class="center"><ul><li><a href="<?= app_route('/') ?>">Trilha de aprendizado</a></li><li><a href="<?= app_route('/videoaulas') ?>">Videoaulas</a></li><li><a href="<?= app_route('/ranking') ?>">Ranking</a></li></ul></div>
  <div class="right"><ul><li><a href="<?= app_route('/login') ?>">Login do aluno</a></li></ul></div>
</nav>

<div class="ring">
  <i style="--clr:#00002e;"></i>
  <i style="--clr:#ffffff;"></i>
  <i style="--clr:#00002e;"></i>
  <div class="login">
    <h2>Login do Professor</h2>
    <?php if (isset($_GET['auth_error'])): ?>
      <div class="error-message"><?= htmlspecialchars($_GET['auth_error'], ENT_QUOTES, 'UTF-8') ?></div>
    <?php endif; ?>
    <form action="<?= app_route('/auth/professor-login-submit') ?>" method="post">
      <div class="inputBx"><input type="text" name="siape" placeholder="SIAPE" required maxlength="50" autocomplete="username"></div>
      <div class="inputBx"><input type="password" name="senha" placeholder="Senha" required autocomplete="current-password"></div>
      <div class="inputBx"><input type="submit" value="Entrar"></div>
      <div class="links">
        <a href="<?= app_route('/login') ?>">Login de aluno</a>
        <a href="<?= app_route('/cadastro/professor') ?>">Cadastre-se</a>
      </div>
    </form>
  </div>
</div>
</body>
</html>
