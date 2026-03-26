<?php
session_start();



?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bem-vindo - HSchuler</title>
    <link rel="stylesheet" href="estilo_homepage.css">
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
      <li><a href="login.php">Login</a></li>
      <li><a href="cadastro.php">Cadastro</a></li>
    </ul>
  </div>
</nav>
<div class="ring">
    <i style="--clr:#00002e;"></i>
    <i style="--clr:#ffffff;"></i>
    <i style="--clr:#00002e;"></i>
  <div class="welcome">
    <h1>Bem-vindo!</h1>
    <p>Explore nosso plataforma de aprendizado com videoaulas, simulados e ranking. Comece sua jornada agora mesmo.</p>
    <div class="buttons">
        <?php
          if(!isset($_SESSION['usuario'])){
             echo "<a href='cadastro.php'>Começar</a>". "<a href='login.php'>Entrar</a>";
          }
        ?>
    </div>
  </div>
</div>
</body>
</html>
