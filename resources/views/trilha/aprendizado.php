<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Aprendizado - Álgebra</title>
  <link rel="stylesheet" type="text/css" href="<?= app_asset('css/estilo_homepage.css') ?>">
  <style>
    /* Layout tipo Duolingo: cards clicáveis */
    .aprendizado-wrap{
      position: relative;
      width: 1100px;
      max-width: 95vw;
      margin: 120px auto 60px;
      z-index: 20;
      color: #fff;
    }

    .aprendizado-title{
      text-align: center;
      margin-bottom: 22px;
    }

    .aprendizado-title h1{
      font-size: 2.2em;
      font-weight: 300;
      text-shadow: 0 0 20px rgba(87, 24, 204, 0.5);
    }

    .trilha-dropdown{
      position: relative;
    }

    .trilha-trigger{
      display: block;
      padding: 10px 14px;
      color: #fff;
      text-decoration: none;
      letter-spacing: .04em;
      white-space: nowrap;
    }

    .trilha-menu{
      position: absolute;
      top: calc(100% + 12px);
      left: 50%;
      display: grid;
      grid-template-columns: repeat(2, minmax(170px, 1fr));
      gap: 6px;
      width: 390px;
      padding: 10px;
      border: 1px solid rgba(255,255,255,.18);
      border-radius: 8px;
      background: rgba(10, 6, 28, .97);
      box-shadow: 0 16px 34px rgba(0,0,0,.45), 0 0 24px rgba(87,24,204,.2);
      opacity: 0;
      visibility: hidden;
      pointer-events: none;
      transform: translate(-50%, -8px);
      transition: opacity .18s ease, transform .18s ease, visibility .18s ease;
    }

    .trilha-dropdown:hover .trilha-menu,
    .trilha-dropdown:focus-within .trilha-menu{
      opacity: 1;
      visibility: visible;
      pointer-events: auto;
      transform: translate(-50%, 0);
    }

    .trilha-menu a{
      display: flex;
      align-items: center;
      gap: 10px;
      min-height: 48px;
      padding: 10px 12px;
      border: 1px solid rgba(255,255,255,.12);
      border-radius: 6px;
      color: rgba(255,255,255,.88);
      text-decoration: none;
      transition: color .18s ease, border-color .18s ease, background .18s ease;
    }

    .trilha-menu a strong{
      display: block;
      font-size: .92em;
      font-weight: 400;
    }

    .trilha-menu a small{
      display: block;
      margin-top: 2px;
      color: rgba(255,255,255,.55);
      font-size: .72em;
    }

    .trilha-menu a:hover,
    .trilha-menu a:focus-visible{
      border-color: #5718cc;
      background: rgba(87,24,204,.18);
      color: #fff;
      outline: none;
    }

    .aprendizado-title p{
      margin-top: 10px;
      opacity: 0.9;
      font-size: 1.1em;
      line-height: 1.6;
    }

    .cards-grid{
      display: grid;
      grid-template-columns: repeat(3, minmax(0, 1fr));
      gap: 18px;
      margin-top: 18px;
    }

    .quest-card{
      cursor: pointer;
      border-radius: 8px;
      border: 2px solid rgba(255,255,255,0.85);
      background: rgba(0,0,0,0.25);
      backdrop-filter: blur(6px);
      padding: 18px 16px;
      text-align: left;
      box-shadow: 0 0 25px rgba(87, 24, 204, 0.15);
      transition: transform .22s ease, border-color .22s ease, box-shadow .22s ease;
      display: flex;
      flex-direction: column;
      gap: 10px;
      min-height: 150px;
      position: relative;
      overflow: hidden;
    }

    .quest-card::after{
      content: '';
      position: absolute;
      inset: -40px;
      background: radial-gradient(circle at 30% 20%, rgba(87,24,204,0.35), transparent 55%);
      opacity: 0;
      transition: opacity .22s ease;
      pointer-events: none;
    }

    .quest-card:hover{
      transform: translateY(-6px);
      border-color: #5718cc;
      box-shadow: 0 12px 30px rgba(87, 24, 204, 0.35);
    }

    .quest-card:hover::after{
      opacity: 1;
    }

    .quest-top{
      display: flex;
      align-items: center;
      justify-content: space-between;
      gap: 12px;
      position: relative;
      z-index: 1;
    }

    .quest-icon{
      width: 46px;
      height: 46px;
      border-radius: 6px;
      display: flex;
      align-items: center;
      justify-content: center;
      border: 1px solid rgba(255,255,255,0.7);
      background: linear-gradient(45deg, rgba(0,0,46,0.55), rgba(87,24,204,0.25));
      font-size: 1.3em;
    }

    .quest-level{
      font-size: .95em;
      opacity: 0.95;
      color: rgba(255,255,255,0.9);
      white-space: nowrap;
    }

    .quest-title{
      font-size: 1.35em;
      font-weight: 400;
      position: relative;
      z-index: 1;
    }

    .quest-desc{
      position: relative;
      z-index: 1;
      opacity: 0.9;
      line-height: 1.5;
      font-weight: 300;
      font-size: 0.98em;
      margin-top: -2px;
    }

    .quest-footer{
      margin-top: auto;
      position: relative;
      z-index: 1;
      display: flex;
      align-items: center;
      justify-content: space-between;
      gap: 10px;
    }

    .quest-btn{
      color: #fff;
      text-decoration: none;
      border-radius: 6px;
      box-shadow: 0 2px 0 #1b0754, 0 5px 10px rgba(0,0,0,.3);
      padding: 10px 14px;
      border: 1px solid rgba(255,255,255,0.75);
      background: rgba(0,0,0,0.18);
      transition: transform .18s ease, border-color .18s ease;
      font-weight: 300;
    }

    .quest-card:hover .quest-btn{
      border-color: #5718cc;
      transform: translateY(-1px);
      box-shadow: 0 3px 0 #1b0754, 0 7px 12px rgba(87,24,204,.25);
    }

    .quest-btn:active {
      transform: translateY(2px);
      box-shadow: 0 1px 0 #1b0754, 0 3px 6px rgba(0,0,0,.25);
    }

    .progress-pill{
      border-radius: 999px;
      padding: 8px 12px;
      border: 1px solid rgba(255,255,255,0.65);
      background: rgba(255,255,255,0.06);
      font-size: .92em;
      opacity: 0.95;
      font-weight: 300;
      white-space: nowrap;
    }

    @media (max-width: 980px){
      .cards-grid{ grid-template-columns: repeat(2, minmax(0, 1fr)); }
    }

    @media (max-width: 640px){
      .trilha-menu{
        position: absolute;
        top: calc(100% + 8px);
        left: 0;
        right: auto;
        grid-template-columns: 1fr;
        width: min(320px, calc(100vw - 32px));
        transform: translateY(-8px);
      }

      .trilha-dropdown:hover .trilha-menu,
      .trilha-dropdown:focus-within .trilha-menu{
        transform: translateY(0);
      }

      .cards-grid{ grid-template-columns: 1fr; }
      .aprendizado-title h1{ font-size: 1.8em; }
      .aprendizado-wrap{ margin-top: 110px; }
    }
  </style>
</head>
<body>
  <?php $navbarActive = 'trilhas'; require APP_ROOT . '/resources/views/layouts/navbar.php'; ?>
  <nav hidden>
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
            <a href="#potenciacao"><span>x²</span><span><strong>Potenciação</strong><small>Básico</small></span></a>
            <a href="#fracoes-algebricas"><span>(a/b)</span><span><strong>Frações Algébricas</strong><small>Intermediário</small></span></a>
            <a href="#produtos-notaveis"><span>(a±b)</span><span><strong>Produtos Notáveis</strong><small>Intermediário</small></span></a>
            <a href="#fatoracao"><span>(x)</span><span><strong>Fatoração</strong><small>Avançado</small></span></a>
            <a href="#equacoes"><span>=</span><span><strong>Equações</strong><small>Básico</small></span></a>
            <a href="#inequacoes"><span>≠</span><span><strong>Inequações</strong><small>Intermediário</small></span></a>
          </div>
        </li>
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

  <div class="aprendizado-wrap">
    <div class="aprendizado-title">
      <h1>TRILHA DE APRENDIZADO</h1>
      <p>Selecione uma área de conhecimento para acessar as questões disponíveis.</p>
    </div>

    <div class="cards-grid">
      <!-- URLs placeholders: ajuste para a rota real das suas questões -->
      <div class="quest-card" id="potenciacao" onclick="location.href='<?= app_route('/aluno/questoes') ?>&area=potenciacao'">
        <div class="quest-top">
          <div class="quest-icon">x²</div>
          <div class="quest-level">Nível: Básico</div>
        </div>
        <div class="quest-title">Potenciação</div>
        <div class="quest-desc">Regras de potências, expoentes e simplificação.</div>
        <div class="quest-footer">
          <span class="progress-pill">🟦 0/10 concluídas</span>
          <a class="quest-btn" href='<?= app_route('/aluno/questoes') ?>&area=potenciacao' onclick="event.stopPropagation();">Ver questões</a>
        </div>
      </div>

      <div class="quest-card" id="fracoes-algebricas" onclick="location.href='<?= app_route('/aluno/questoes') ?>&area=fracoes-algebricas'">
        <div class="quest-top">
          <div class="quest-icon">(a/b)</div>
          <div class="quest-level">Nível: Intermediário</div>
        </div>
        <div class="quest-title">Frações Algébricas</div>
        <div class="quest-desc">Simplificação, MMC e operações com frações.</div>
        <div class="quest-footer">
          <span class="progress-pill">🟦 0/12 concluídas</span>
          <a class="quest-btn" href='<?= app_route('/aluno/questoes') ?>&area=fracoes-algebricas' onclick="event.stopPropagation();">Ver questões</a>
        </div>
      </div>

      <div class="quest-card" id="produtos-notaveis" onclick="location.href='<?= app_route('/aluno/questoes') ?>&area=produtos-notaveis'">
        <div class="quest-top">
          <div class="quest-icon">(a±b)</div>
          <div class="quest-level">Nível: Intermediário</div>
        </div>
        <div class="quest-title">Produtos Notáveis</div>
        <div class="quest-desc">Fórmulas e fatoração para agilizar exercícios.</div>
        <div class="quest-footer">
          <span class="progress-pill">🟦 0/9 concluídas</span>
          <a class="quest-btn" href='<?= app_route('/aluno/questoes') ?>&area=produtos-notaveis' onclick="event.stopPropagation();">Ver questões</a>
        </div>
      </div>

      <div class="quest-card" id="fatoracao" onclick="location.href='<?= app_route('/aluno/questoes') ?>&area=fatoracao'">
        <div class="quest-top">
          <div class="quest-icon">(x)</div>
          <div class="quest-level">Nível: Avançado</div>
        </div>
        <div class="quest-title">Fatoração</div>
        <div class="quest-desc">Trinômios, agrupamento e fator comum.</div>
        <div class="quest-footer">
          <span class="progress-pill">🟦 0/8 concluídas</span>
          <a class="quest-btn" href='<?= app_route('/aluno/questoes') ?>&area=fatoracao' onclick="event.stopPropagation();">Ver questões</a>
        </div>
      </div>

      <div class="quest-card" id="equacoes" onclick="location.href='<?= app_route('/aluno/questoes') ?>&area=equacoes'">
        <div class="quest-top">
          <div class="quest-icon">=</div>
          <div class="quest-level">Nível: Básico</div>
        </div>
        <div class="quest-title">Equações</div>
        <div class="quest-desc">1º e 2º grau (introdução e exercícios).</div>
        <div class="quest-footer">
          <span class="progress-pill">🟦 0/10 concluídas</span>
          <a class="quest-btn" href='<?= app_route('/aluno/questoes') ?>&area=equacoes' onclick="event.stopPropagation();">Ver questões</a>
        </div>
      </div>

      <div class="quest-card" id="inequacoes" onclick="location.href='<?= app_route('/aluno/questoes') ?>&area=inequacoes'">
        <div class="quest-top">
          <div class="quest-icon">≠</div>
          <div class="quest-level">Nível: Intermediário</div>
        </div>
        <div class="quest-title">Inequações</div>
        <div class="quest-desc">Resolução e interpretação de intervalos.</div>
        <div class="quest-footer">
          <span class="progress-pill">🟦 0/7 concluídas</span>
          <a class="quest-btn" href='<?= app_route('/aluno/questoes') ?>&area=inequacoes' onclick="event.stopPropagation();">Ver questões</a>
        </div>
      </div>

    </div>
  </div>

  <script>
    // Esboço: sem backend ainda. Quando tiver a rota/arquivo das questões, troque os links acima.
  </script>
</body>
</html>

