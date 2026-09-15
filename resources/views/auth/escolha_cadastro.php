<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Escolha seu cadastro - HSchuler</title>
  <link rel="stylesheet" href="<?= app_asset('css/estilo_homepage.css') ?>?v=33">
  <link rel="stylesheet" href="<?= app_asset('css/escolha_cadastro.css') ?>?v=1">
</head>
<body class="home-page cadastro-choice-page">
  <?php require APP_ROOT . '/resources/views/layouts/navbar.php'; ?>

  <div class="escolha-container">
    <h2>Como você deseja se cadastrar?</h2>

    <div class="cards">
      <div class="card">
        <div class="titulo">Sou Aluno</div>
        <ul>
          <li>Fazer simulados</li>
          <li>Assistir vídeos</li>
          <li>Participar no ranking!</li>
        </ul>
        <a class="btn" href="<?= app_route('/cadastro/aluno') ?>">Cadastrar</a>
      </div>

      <div class="card">
        <div class="titulo">Sou Professor</div>
        <ul>
          <li>Publicar aulas</li>
          <li>Criar simulados</li>
          <li>Gerenciar alunos</li>
        </ul>
        <a class="btn" href="<?= app_route('/cadastro/professor') ?>">Cadastrar</a>
      </div>
    </div>
  </div>
</body>
</html>

