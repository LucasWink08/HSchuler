<?php
session_start();



?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bem-vindo - HSchuler</title>
    <link rel="stylesheet" href="<?= ASSET_URL ?>/css/estilo_homepage.css">
</head>
<body class="home-page">
<nav>
  <div class="left">
    <ul>
      <li><a href="<?= app_route('/') ?>">Home</a></li>
    </ul>
  </div>
  <div class="center">
    <ul>
      <li class="trilha-dropdown">
        <span class="trilha-trigger" tabindex="0" role="button">Trilha de aprendizado</span>
        <div class="trilha-menu">
          <a href="<?= app_route('/aluno/questoes') ?>&area=potenciacao">Potenciação</a>
          <a href="<?= app_route('/aluno/questoes') ?>&area=fracoes-algebricas">Frações Algébricas</a>
          <a href="<?= app_route('/aluno/questoes') ?>&area=produtos-notaveis">Produtos Notáveis</a>
          <a href="<?= app_route('/aluno/questoes') ?>&area=fatoracao">Fatoração</a>
          <a href="<?= app_route('/aluno/questoes') ?>&area=equacoes">Equações</a>
          <a href="<?= app_route('/aluno/questoes') ?>&area=inequacoes">Inequações</a>
        </div>
      </li>
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
  <div class="welcome" id="sobre">
    <h1>Bem-vindo!</h1>
    <p>Explore nosso plataforma de aprendizado com videoaulas, simulados e ranking. Comece sua jornada agora mesmo.</p>
    <div class="buttons">
        <?php
          if(!isset($_SESSION['usuario'])){
             echo "<a href='" . APP_URL . "/index.php?route=/cadastro'>Começar</a>" . "<a href='" . APP_URL . "/index.php?route=/login'>Entrar</a>";
          }
        ?>
    </div>
  </div>
</div>
</body>
</html>
