<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Cadastro</title>
    <link rel="stylesheet" type="text/css" href="estilo_cadastro.css">
</head>
<body>
<nav>
  <div class="left">
    <ul>
      <li><a href="login.php">Home</a></li>
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
      <li><a href="login.php">Login</a></li>
    </ul>
  </div>
</nav>
<div class="ring">
    <i style="--clr:#00002e;"></i>
    <i style="--clr:#ffffff;"></i>
    <i style="--clr:#00002e;"></i>
  <form class="login" method="POST" action="processa_cadastro.php">
        <h2>Cadastro</h2>
        <div class="inputBx">
          <input type="text" name="usuario" placeholder="Usuario" required>
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
          <input type="submit" value="Cadastrar">
        </div>
        <div class="links">
          <a href="login.php">Já possui conta? Faça login</a>
        </div>
  </form>
</div>
<script src="javascript.js"></script>
</body>
</html>
