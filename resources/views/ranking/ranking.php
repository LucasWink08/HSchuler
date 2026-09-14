<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Ranking</title>
  <link rel="stylesheet" type="text/css" href="<?= app_asset('css/estilo_homepage.css') ?>">
  <style>
    .ranking-wrap{
      position: relative;
      width: 1100px;
      max-width: 95vw;
      margin: 120px auto 60px;
      z-index: 20;
      color: #fff;
    }

    .ranking-title{
      display: flex;
      align-items: center;
      justify-content: center;
      gap: 18px;
      text-align: center;
      margin-bottom: 22px;
    }

    .ranking-title h1{
      font-size: 2.2em;
      font-weight: 300;
      text-shadow: 0 0 20px rgba(87, 24, 204, 0.5);
    }

    .trofeu-slot{
      width: 56px;
      height: 56px;
      border-radius: 16px;
      border: 2px solid rgba(255,255,255,0.85);
      background: rgba(0,0,0,0.25);
      display: flex;
      align-items: center;
      justify-content: center;
      box-shadow: 0 0 25px rgba(87, 24, 204, 0.12);
    }

    .trofeu-slot img{
      max-width: 90%;
      max-height: 90%;
      display: none;
    }

    .ranking-sub{
      text-align: center;
      opacity: 0.9;
      margin-bottom: 18px;
      font-size: 1.05em;
      font-weight: 300;
      line-height: 1.6;
    }

    .ranking-board{
      display: grid;
      grid-template-columns: 1fr;
      gap: 12px;
    }

    /* Cards estilo duolingo */
    .rank-card{
      cursor: default;
      border-radius: 18px;
      border: 2px solid rgba(255,255,255,0.85);
      background: rgba(0,0,0,0.25);
      backdrop-filter: blur(6px);
      padding: 14px 16px;
      box-shadow: 0 0 25px rgba(87, 24, 204, 0.15);
      display: flex;
      align-items: center;
      justify-content: space-between;
      gap: 14px;
      transition: border-color .22s ease, transform .22s ease, box-shadow .22s ease;
    }

    .rank-card:hover{
      transform: translateY(-3px);
      border-color: #5718cc;
      box-shadow: 0 12px 30px rgba(87, 24, 204, 0.28);
    }

    .rank-left{
      display: flex;
      align-items: center;
      gap: 14px;
      min-width: 0;
    }

    .rank-pos{
      width: 44px;
      height: 44px;
      border-radius: 14px;
      border: 1px solid rgba(255,255,255,0.7);
      background: linear-gradient(45deg, rgba(0,0,46,0.55), rgba(87,24,204,0.25));
      display: flex;
      align-items: center;
      justify-content: center;
      font-weight: 400;
      flex: 0 0 auto;
    }

    .rank-name{
      display: flex;
      flex-direction: column;
      gap: 4px;
      min-width: 0;
    }

    .rank-name strong{
      font-size: 1.1em;
      font-weight: 400;
      white-space: nowrap;
      overflow: hidden;
      text-overflow: ellipsis;
    }

    .rank-name span{
      opacity: 0.9;
      font-weight: 300;
      font-size: 0.95em;
    }

    .rank-score{
      display: flex;
      align-items: center;
      gap: 10px;
      justify-content: flex-end;
      flex: 0 0 auto;
    }

    .score-pill{
      border-radius: 999px;
      padding: 9px 12px;
      border: 1px solid rgba(255,255,255,0.65);
      background: rgba(255,255,255,0.06);
      font-size: 0.95em;
      font-weight: 300;
      white-space: nowrap;
    }

    @media (max-width: 640px){
      .ranking-wrap{ margin-top: 110px; }
      .ranking-title{ gap: 12px; }
      .ranking-title h1{ font-size: 1.9em; }
      .rank-card{ padding: 12px 12px; }
    }
  </style>
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

  <div class="ranking-wrap">
    <div class="ranking-title">
      <div class="trofeu-slot">
        <!-- Coloque aqui a imagem do troféu -->
        <!-- Exemplo: <img src="img/trofeu.png" alt="Trofeu"> -->
        <span style="font-size: 1.3em; opacity: 0.95;">🏆</span>
      </div>
      <h1>Ranking</h1>
    </div>

    <div class="ranking-sub">Leaderboard (esboço). Substitua os dados fixos por resultados do banco.</div>

    <div class="ranking-board">
      <div class="rank-card">
        <div class="rank-left">
          <div class="rank-pos">1</div>
          <div class="rank-name">
            <strong>Aluno Exemplo</strong>
            <span>Concluiu 42 atividades</span>
          </div>
        </div>
        <div class="rank-score">
          <div class="score-pill">🏅 1200 pts</div>
        </div>
      </div>

      <div class="rank-card">
        <div class="rank-left">
          <div class="rank-pos">2</div>
          <div class="rank-name">
            <strong>Maria Souza</strong>
            <span>Concluiu 38 atividades</span>
          </div>
        </div>
        <div class="rank-score">
          <div class="score-pill">🥈 1050 pts</div>
        </div>
      </div>

      <div class="rank-card">
        <div class="rank-left">
          <div class="rank-pos">3</div>
          <div class="rank-name">
            <strong>João Pedro</strong>
            <span>Concluiu 33 atividades</span>
          </div>
        </div>
        <div class="rank-score">
          <div class="score-pill">🥉 980 pts</div>
        </div>
      </div>

      <div class="rank-card">
        <div class="rank-left">
          <div class="rank-pos">4</div>
          <div class="rank-name">
            <strong>Ana Clara</strong>
            <span>Concluiu 29 atividades</span>
          </div>
        </div>
        <div class="rank-score">
          <div class="score-pill">900 pts</div>
        </div>
      </div>

      <div class="rank-card">
        <div class="rank-left">
          <div class="rank-pos">5</div>
          <div class="rank-name">
            <strong>Lucas Lima</strong>
            <span>Concluiu 27 atividades</span>
          </div>
        </div>
        <div class="rank-score">
          <div class="score-pill">860 pts</div>
        </div>
      </div>
    </div>
  </div>
</body>
</html>

