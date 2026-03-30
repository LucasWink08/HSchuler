<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login - HSchuler</title>
    <link rel="stylesheet" type="text/css" href="estilo_login.css">
</head>
<body>
<nav>
  <div class="left">
    <ul>
      <li><a href="homepage.php">Home</a></li>
    </ul>
  </div>
  <div class="center">
    <ul>
      <li><a href="#">Aprendizado</a></li>
      <li><a href="#">Videoaulas</a></li>
      <li><a href="#">Simulados</a></li>
      <li><a href="#">Ranking</a></li>
      <li><a href="#">Sobre</a></li>
    </ul>
  </div>
  <div class="right">
    <ul>
      <li><a href="#">Login</a></li>
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
    ?>
    <form action="processa_login.php" method="post">
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
            <a href="#">Esqueceu sua senha</a>
            <a href="cadastro.php" class="signup-trigger">Faça seu cadastro</a>
          </div>
          <div class="signup-dropdown">
            <p>Faça login para acessar o cadastro ou <a href="#">clique aqui</a> para criar uma conta.</p>
          </div>
    </form>
  </div>
</div>
<script src="javascript.js"></script>
</body>
</html>