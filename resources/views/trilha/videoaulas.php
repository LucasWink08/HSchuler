<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Videoaulas - Álgebra</title>
  <link rel="stylesheet" type="text/css" href="<?= app_asset('css/estilo_homepage.css') ?>">
  <style>
    .video-wrap{
      position: relative;
      width: 1100px;
      max-width: 95vw;
      margin: 120px auto 60px;
      z-index: 20;
      color: #fff;
    }

    .video-title{
      text-align: center;
      margin-bottom: 22px;
    }

    .video-title h1{
      font-size: 2.1em;
      font-weight: 300;
      text-shadow: 0 0 20px rgba(87, 24, 204, 0.5);
    }

    .video-title p{
      margin-top: 10px;
      opacity: 0.9;
      font-size: 1.08em;
      line-height: 1.6;
    }

    .video-grid{
      display: grid;
      grid-template-columns: repeat(3, minmax(0, 1fr));
      gap: 18px;
      margin-top: 18px;
    }

    .video-card{
      cursor: pointer;
      border-radius: 12px;
      border: 2px solid rgba(255,255,255,0.85);
      background: rgba(0,0,0,0.25);
      backdrop-filter: blur(6px);
      padding: 18px 16px;
      box-shadow: 0 0 25px rgba(87, 24, 204, 0.15);
      transition: transform .22s ease, box-shadow .22s ease, border-color .22s ease;
      min-height: 160px;
      display: flex;
      flex-direction: column;
      gap: 10px;
      overflow: hidden;
      position: relative;
    }

    .video-card::after{
      content: '';
      position: absolute;
      inset: -40px;
      background: radial-gradient(circle at 30% 20%, rgba(87,24,204,0.35), transparent 55%);
      opacity: 0;
      transition: opacity .22s ease;
      pointer-events: none;
    }

    .video-card:hover{
      transform: translateY(-6px);
      border-color: #5718cc;
      box-shadow: 0 12px 30px rgba(87, 24, 204, 0.35);
    }

    .video-card:hover::after{ opacity: 1; }

    .video-top{
      display: flex;
      align-items: center;
      justify-content: space-between;
      gap: 12px;
      position: relative;
      z-index: 1;
    }

    .video-badge{
      display: flex;
      align-items: center;
      justify-content: center;
      width: 46px;
      height: 46px;
      border-radius: 14px;
      border: 1px solid rgba(255,255,255,0.7);
      background: linear-gradient(45deg, rgba(0,0,46,0.55), rgba(87,24,204,0.25));
      font-size: 1.25em;
    }

    .video-level{
      font-size: .95em;
      opacity: 0.95;
      color: rgba(255,255,255,0.9);
      white-space: nowrap;
    }

    .video-name{
      font-size: 1.28em;
      font-weight: 400;
      position: relative;
      z-index: 1;
    }

    .video-desc{
      opacity: 0.9;
      font-weight: 300;
      line-height: 1.5;
      font-size: 0.98em;
      position: relative;
      z-index: 1;
      margin-top: -4px;
    }

    .video-actions{
      margin-top: auto;
      position: relative;
      z-index: 1;
      display: flex;
      align-items: center;
      justify-content: space-between;
      gap: 10px;
    }

    .video-pill{
      border-radius: 999px;
      padding: 8px 12px;
      border: 1px solid rgba(255,255,255,0.65);
      background: rgba(255,255,255,0.06);
      font-size: .92em;
      opacity: 0.95;
      font-weight: 300;
      white-space: nowrap;
    }

    .video-link{
      color: #fff;
      text-decoration: none;
      border-radius: 6px;
      padding: 10px 14px;
      border: 1px solid rgba(255,255,255,0.75);
      background: rgba(0,0,0,0.18);
      box-shadow: 0 2px 0 #1b0754, 0 5px 10px rgba(0,0,0,.3);
      font-weight: 300;
      transition: transform .18s ease, border-color .18s ease;
      white-space: nowrap;
    }

    .video-card:hover .video-link{
      border-color: #5718cc;
      transform: translateY(-1px);
      box-shadow: 0 3px 0 #1b0754, 0 7px 12px rgba(87,24,204,.25);
    }

    .video-link:active {
      transform: translateY(2px);
      box-shadow: 0 1px 0 #1b0754, 0 3px 6px rgba(0,0,0,.25);
    }

    @media (max-width: 980px){
      .video-grid{ grid-template-columns: repeat(2, minmax(0, 1fr)); }
    }

    @media (max-width: 640px){
      .video-grid{ grid-template-columns: 1fr; }
      .video-wrap{ margin-top: 110px; }
      .video-title h1{ font-size: 1.8em; }
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

  <div class="video-wrap">
    <div class="video-title">
      <h1>Videoaulas de Álgebra</h1>
      <p>Escolha uma área para acessar a lista de aulas disponíveis.</p>
    </div>

    <div class="video-grid">
      <div class="video-card" onclick="location.href='<?= app_route('/videoaulas') ?>&area=potenciacao'">
        <div class="video-top">
          <div class="video-badge">x²</div>
          <div class="video-level">Nível: Básico</div>
        </div>
        <div class="video-name">Potenciação</div>
        <div class="video-desc">Expoentes, regras de potências e simplificação.</div>
        <div class="video-actions">
          <span class="video-pill">🟦 0/5 aulas</span>
          <a class="video-link" href="<?= app_route('/videoaulas') ?>&area=potenciacao" onclick="event.stopPropagation();">Assistir</a>
        </div>
      </div>

      <div class="video-card" onclick="location.href='<?= app_route('/videoaulas') ?>&area=fracoes-algebricas'">
        <div class="video-top">
          <div class="video-badge">(a/b)</div>
          <div class="video-level">Nível: Intermediário</div>
        </div>
        <div class="video-name">Frações Algébricas</div>
        <div class="video-desc">MMC, simplificação e operações com frações.</div>
        <div class="video-actions">
          <span class="video-pill">🟦 0/6 aulas</span>
          <a class="video-link" href="<?= app_route('/videoaulas') ?>&area=fracoes-algebricas" onclick="event.stopPropagation();">Assistir</a>
        </div>
      </div>

      <div class="video-card" onclick="location.href='<?= app_route('/videoaulas') ?>&area=produtos-notaveis'">
        <div class="video-top">
          <div class="video-badge">(a±b)</div>
          <div class="video-level">Nível: Intermediário</div>
        </div>
        <div class="video-name">Produtos Notáveis</div>
        <div class="video-desc">Fórmulas e fatoração para agilizar exercícios.</div>
        <div class="video-actions">
          <span class="video-pill">🟦 0/4 aulas</span>
          <a class="video-link" href="<?= app_route('/videoaulas') ?>&area=produtos-notaveis" onclick="event.stopPropagation();">Assistir</a>
        </div>
      </div>

      <div class="video-card" onclick="location.href='<?= app_route('/videoaulas') ?>&area=fatoracao'">
        <div class="video-top">
          <div class="video-badge">(x)</div>
          <div class="video-level">Nível: Avançado</div>
        </div>
        <div class="video-name">Fatoração</div>
        <div class="video-desc">Trinômios, agrupamento e fator comum.</div>
        <div class="video-actions">
          <span class="video-pill">🟦 0/4 aulas</span>
          <a class="video-link" href="<?= app_route('/videoaulas') ?>&area=fatoracao" onclick="event.stopPropagation();">Assistir</a>
        </div>
      </div>

      <div class="video-card" onclick="location.href='<?= app_route('/videoaulas') ?>&area=equacoes'">
        <div class="video-top">
          <div class="video-badge">=</div>
          <div class="video-level">Nível: Básico</div>
        </div>
        <div class="video-name">Equações</div>
        <div class="video-desc">Noções e exercícios resolvidos passo a passo.</div>
        <div class="video-actions">
          <span class="video-pill">🟦 0/5 aulas</span>
          <a class="video-link" href="<?= app_route('/videoaulas') ?>&area=equacoes" onclick="event.stopPropagation();">Assistir</a>
        </div>
      </div>

      <div class="video-card" onclick="location.href='<?= app_route('/videoaulas') ?>&area=inequacoes'">
        <div class="video-top">
          <div class="video-badge">≠</div>
          <div class="video-level">Nível: Intermediário</div>
        </div>
        <div class="video-name">Inequações</div>
        <div class="video-desc">Resolução e interpretação no conjunto de números.</div>
        <div class="video-actions">
          <span class="video-pill">🟦 0/3 aulas</span>
          <a class="video-link" href="<?= app_route('/videoaulas') ?>&area=inequacoes" onclick="event.stopPropagation();">Assistir</a>
        </div>
      </div>
    </div>
  </div>
</body>
</html>

