<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Escolha do Cadastro</title>
  <link rel="stylesheet" type="text/css" href="estilo_cadastro.css">
  <style>
    .escolha-container{
      position: relative;
      z-index: 20;
      width: 980px;
      max-width: 95vw;
      margin-top: 70px;
      display: flex;
      flex-direction: column;
      align-items: center;
      gap: 20px;
    }

    .escolha-container h2{
      color: #fff;
      font-size: 1.6em;
      font-weight: 300;
      text-align: center;
      text-shadow: 0 0 20px rgba(87, 24, 204, 0.5);
    }

    .cards{
      width: 100%;
      display: grid;
      grid-template-columns: repeat(2, minmax(0, 1fr));
      gap: 22px;
    }

    .card{
      border-radius: 16px;
      border: 2px solid #fff;
      padding: 22px 18px;
      color: #fff;
      background: rgba(0, 0, 0, 0.25);
      backdrop-filter: blur(6px);
      box-shadow: 0 0 25px rgba(87, 24, 204, 0.15);
      transition: transform 0.25s ease, box-shadow 0.25s ease, border-color 0.25s ease;
      text-align: center;
    }

    .card:hover{
      transform: translateY(-6px);
      border-color: #5718cc;
      box-shadow: 0 12px 30px rgba(87, 24, 204, 0.35);
    }

    .card .titulo{
      display: flex;
      align-items: center;
      justify-content: center;
      gap: 10px;
      font-size: 1.25em;
      font-weight: 400;
      margin-bottom: 14px;
    }

    .card ul{
      list-style: none;
      padding: 0;
      margin: 0 0 18px 0;
      display: flex;
      flex-direction: column;
      gap: 10px;
      font-weight: 300;
      opacity: 0.95;
    }

    .card ul li{
      padding: 0 8px;
    }

    .card .btn{
      display: inline-block;
      width: 100%;
      padding: 12px 18px;
      border-radius: 25px;
      background: linear-gradient(45deg, #00002e, #5718cc);
      color: #fff;
      text-decoration: none;
      font-weight: 300;
      transition: transform 0.2s ease, filter 0.2s ease;
    }

    .card .btn:hover{
      transform: scale(1.03);
      filter: brightness(1.1);
    }

    @media (max-width: 720px){
      .cards{ grid-template-columns: 1fr; }
      .escolha-container{ margin-top: 90px; gap: 16px; }
    }
  </style>
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
        <a class="btn" href="cadastro_aluno.php">Cadastrar</a>
      </div>

      <div class="card">
        <div class="titulo">Sou Professor</div>
        <ul>
          <li>Publicar aulas</li>
          <li>Criar simulados</li>
          <li>Gerenciar alunos</li>
        </ul>
        <a class="btn" href="cadastro_professor.php">Cadastrar</a>
      </div>
    </div>
  </div>
</body>
</html>

