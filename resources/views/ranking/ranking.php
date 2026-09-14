<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Ranking</title>
  <link rel="stylesheet" type="text/css" href="<?= app_asset('css/estilo_homepage.css') ?>?v=31">
  <style>
    body.ranking-page {
      position: relative;
      display: block;
      min-height: 100svh;
      overflow-x: hidden;
      overflow-y: auto;
      background: #02050a url("<?= app_asset('images/home/background.png') ?>") center top / cover fixed;
      color: #edf3ff;
      font-family: Arial, Helvetica, sans-serif;
    }

    body.ranking-page::before {
      content: "";
      position: fixed;
      inset: 0;
      z-index: 0;
      background:
        radial-gradient(circle at 50% 28%, rgba(28, 77, 202, .18), transparent 22%),
        linear-gradient(180deg, rgba(0, 2, 6, .15), rgba(0, 2, 7, .74) 72%, #010205 100%);
      pointer-events: none;
    }

    body.ranking-page::after {
      content: "";
      position: fixed;
      inset: 0;
      z-index: 0;
      pointer-events: none;
      background-image:
        radial-gradient(2px 2px at 20px 30px, rgba(255,255,255,.9), transparent),
        radial-gradient(2px 2px at 80px 65px, rgba(255,255,255,.8), transparent),
        radial-gradient(1.5px 1.5px at 150px 40px, rgba(255,255,255,.9), transparent),
        radial-gradient(1.5px 1.5px at 210px 100px, rgba(255,255,255,.8), transparent),
        radial-gradient(2px 2px at 270px 40px, rgba(255,255,255,.9), transparent),
        radial-gradient(2px 2px at 340px 190px, rgba(255,255,255,.75), transparent),
        radial-gradient(1px 1px at 420px 120px, rgba(255,255,255,.9), transparent),
        radial-gradient(2px 2px at 500px 80px, rgba(255,255,255,.7), transparent),
        radial-gradient(1.5px 1.5px at 700px 90px, rgba(255,255,255,.8), transparent),
        radial-gradient(1.5px 1.5px at 880px 160px, rgba(255,255,255,.8), transparent);
      background-repeat: repeat;
      background-size: 980px 360px;
      opacity: .8;
      animation: ranking-stars 30s linear infinite;
    }

    @keyframes ranking-stars {
      from { transform: translateY(0); }
      to { transform: translateY(-120px); }
    }

    .ranking-page .home-nav {
      position: relative;
      z-index: 2;
      display: grid;
      grid-template-columns: 120px minmax(0, 1fr) 120px;
      align-items: center;
      width: 100%;
      min-height: 64px;
      padding: 10px 28px 0;
      background: transparent;
    }

    .ranking-page .home-nav-links,
    .ranking-page .home-nav-actions {
      display: flex;
      align-items: center;
      justify-content: center;
      gap: 28px;
    }

    .ranking-page .home-nav-links a,
    .ranking-page .home-nav-actions a,
    .ranking-page .home-return {
      color: rgba(233, 239, 255, .85);
      text-decoration: none;
      font-size: .8rem;
      font-weight: 500;
      line-height: 1.2;
      letter-spacing: .01em;
      transition: color .18s ease, transform .18s ease;
    }

    .ranking-page .home-nav-links a:hover,
    .ranking-page .home-nav-links a:focus-visible,
    .ranking-page .home-return:hover,
    .ranking-page .home-return:focus-visible,
    .ranking-page .home-nav-actions a:hover,
    .ranking-page .home-nav-actions a:focus-visible {
      color: #fff;
      outline: none;
    }

    .ranking-page .home-nav-links a.is-active {
      color: #fff;
      text-shadow: 0 0 12px rgba(121, 151, 255, .8);
    }

    .ranking-page .home-return {
      justify-self: start;
      text-align: left;
      margin-left: 8px;
    }

    .ranking-page .home-nav-actions {
      justify-content: flex-end;
    }

    .ranking-page .home-nav-actions a {
      padding: 10px 20px;
      border: 1px solid rgba(103, 134, 255, .7);
      border-radius: 10px;
      background: linear-gradient(135deg, #3d3ac7, #1f3b9f);
      box-shadow: inset 0 1px rgba(255,255,255,.25), 0 0 20px rgba(93, 110, 255, .22);
      color: #eff5ff;
    }

    .ranking-wrap {
      position: relative;
      z-index: 1;
      width: min(920px, calc(100% - 40px));
      margin: 48px auto 70px;
      color: #fff;
    }

    .ranking-title {
      display: flex;
      align-items: center;
      justify-content: center;
      gap: 18px;
      margin-bottom: 18px;
    }

    .trofeu-slot {
      display: inline-flex;
      align-items: center;
      justify-content: center;
      width: 110px;
      height: 110px;
      border: none;
      border-radius: 0;
      background: transparent;
      box-shadow: none;
      font-size: 1.5rem;
      overflow: visible;
    }

    .trofeu-slot svg { display: none; }

    .trofeu-slot img {
      display: block;
      width: 100%;
      height: 100%;
      object-fit: contain;
      transform: scale(1.08);
    }

    .ranking-title h1 {
      margin: 0;
      font-size: clamp(2.1rem, 2.8vw, 3.1rem);
      font-weight: 700;
      letter-spacing: -.045em;
      color: #f3f7ff;
      text-shadow: 0 0 18px rgba(124, 154, 255, .2);
    }

    .ranking-sub {
      margin: 0 auto 28px;
      max-width: 800px;
      text-align: center;
      color: rgba(222, 232, 255, .78);
      font-size: 1rem;
      line-height: 1.6;
    }

    .ranking-board {
      display: grid;
      grid-template-columns: 1fr;
      gap: 18px;
    }

    .rank-card {
      position: relative;
      display: flex;
      align-items: center;
      justify-content: space-between;
      gap: 18px;
      min-height: 88px;
      padding: 16px 18px;
      border: 1px solid rgba(109, 142, 255, .52);
      border-radius: 20px;
      background: linear-gradient(160deg, rgba(9, 17, 34, .92), rgba(3, 8, 22, .82));
      box-shadow: inset 0 1px rgba(182, 206, 255, .08), 0 18px 34px rgba(8, 18, 42, .28);
      transition: transform .18s ease, border-color .18s ease, box-shadow .18s ease;
    }

    .rank-card:hover {
      transform: translateY(-2px);
      border-color: rgba(143, 177, 255, .75);
      box-shadow: 0 18px 36px rgba(36, 63, 170, .24), inset 0 1px rgba(182, 206, 255, .12);
    }

    .rank-left {
      display: flex;
      align-items: center;
      gap: 16px;
      min-width: 0;
      flex: 1;
    }

    .rank-pos {
      display: inline-flex;
      align-items: center;
      justify-content: center;
      width: 38px;
      height: 38px;
      border: 1px solid rgba(132, 171, 255, .5);
      border-radius: 12px;
      background: linear-gradient(135deg, rgba(70, 87, 255, .22), rgba(90, 138, 255, .08));
      color: #eff5ff;
      font-weight: 700;
      font-size: .9rem;
      box-shadow: inset 0 1px rgba(255,255,255,.08);
    }

    .rank-name {
      display: flex;
      flex-direction: column;
      gap: 4px;
      min-width: 0;
    }

    .rank-name strong {
      color: #f4f8ff;
      font-size: 1.05rem;
      font-weight: 700;
      letter-spacing: -.02em;
      white-space: nowrap;
      overflow: hidden;
      text-overflow: ellipsis;
    }

    .rank-name span {
      color: rgba(205, 220, 255, .76);
      font-size: .78rem;
      line-height: 1.4;
    }

    .rank-score {
      display: flex;
      align-items: center;
      justify-content: flex-end;
      flex-shrink: 0;
    }

    .score-pill {
      display: inline-flex;
      align-items: center;
      justify-content: center;
      min-width: 112px;
      padding: 9px 14px;
      border: 1px solid rgba(128, 169, 255, .58);
      border-radius: 999px;
      background: rgba(79, 107, 255, .12);
      color: #edf4ff;
      font-size: .74rem;
      font-weight: 700;
      letter-spacing: .02em;
      white-space: nowrap;
      box-shadow: inset 0 1px rgba(255,255,255,.18);
    }

    @media (max-width: 760px) {
      .ranking-page .home-nav {
        grid-template-columns: 1fr auto;
        gap: 12px;
        padding: 12px 18px 0;
      }

      .ranking-page .home-nav-links {
        grid-column: 1 / -1;
        justify-content: flex-start;
        gap: 18px;
        overflow-x: auto;
      }

      .ranking-page .home-nav-links a,
      .ranking-page .home-return,
      .ranking-page .home-nav-actions a {
        font-size: .72rem;
      }

      .rank-card {
        align-items: flex-start;
        flex-direction: column;
      }

      .rank-score {
        width: 100%;
        justify-content: flex-start;
      }
    }
  </style>
</head>
<body class="ranking-page">
  <nav class="home-nav" aria-label="Navegação principal">
    <a class="home-return" href="<?= app_route('/') ?>">Home</a>
    <div class="home-nav-links">
      <a href="<?= app_route('/') ?>">Trilha de aprendizado</a>
      <a href="<?= app_route('/videoaulas') ?>">Videoaulas</a>
      <a href="<?= app_route('/aluno/simulados') ?>">Simulados</a>
      <a href="<?= app_route('/ranking') ?>" class="is-active">Ranking</a>
      <a href="<?= app_route('/sobre') ?>">Sobre</a>
    </div>
    <div class="home-nav-actions">
      <a href="<?= app_route('/login') ?>">Login</a>
    </div>
  </nav>

  <div class="ranking-wrap">
    <div class="ranking-title">
      <div class="trofeu-slot" aria-hidden="true">
        <svg viewBox="0 0 500 500" xmlns="http://www.w3.org/2000/svg" role="img" aria-label="Troféu Hschuler">
          <defs>
            <linearGradient id="gold1" x1="0" x2="1">
              <stop offset="0%" stop-color="#fff2b2"/>
              <stop offset="18%" stop-color="#f6d34a"/>
              <stop offset="42%" stop-color="#e7ac1a"/>
              <stop offset="60%" stop-color="#ffd650"/>
              <stop offset="100%" stop-color="#d57b00"/>
            </linearGradient>
            <linearGradient id="gold2" x1="0" x2="0" y1="0" y2="1">
              <stop offset="0%" stop-color="#fbe67d"/>
              <stop offset="100%" stop-color="#d68200"/>
            </linearGradient>
            <linearGradient id="metal" x1="0" x2="1">
              <stop offset="0%" stop-color="#a7aeb8"/>
              <stop offset="40%" stop-color="#4d5765"/>
              <stop offset="100%" stop-color="#b8c1c9"/>
            </linearGradient>
          </defs>

          <g transform="translate(0 10)">
            <path d="M90 165c0-58 46-104 104-104h120c58 0 104 46 104 104v24c0 67-35 124-88 160l-35 25c-16 11-37 11-53 0l-35-25c-53-36-88-93-88-160v-24z" fill="url(#gold1)" stroke="#d98b00" stroke-width="10"/>
            <path d="M109 170c0-44 36-80 80-80h120c44 0 80 36 80 80v14c0 50-26 94-67 122l-30 21-30-21c-41-28-67-72-67-122v-14z" fill="url(#gold2)" opacity="0.95"/>
            <path d="M156 112c31-48 86-66 139-52 60 16 100 58 111 115-19-12-45-20-73-23-53-5-104 10-177-40z" fill="none" stroke="#fbe67d" stroke-width="10" stroke-linecap="round" opacity=".75"/>
            <path d="M83 194c-15 8-30 17-42 30-35 37-42 92-24 138 18 44 61 72 111 74l31-77c-26-16-44-46-44-78v-87h-32z" fill="url(#gold1)" stroke="#d98b00" stroke-width="10"/>
            <path d="M417 194c15 8 30 17 42 30 35 37 42 92 24 138-18 44-61 72-111 74l-31-77c26-16 44-46 44-78v-87h32z" fill="url(#gold1)" stroke="#d98b00" stroke-width="10"/>
            <path d="M140 140h220v18c0 71-57 128-128 128s-128-57-128-128v-18h36z" fill="url(#gold2)"/>

            <g transform="translate(0 16)">
              <path d="M140 182c0-62 52-113 116-113s116 51 116 113v11c0 51-21 100-59 131l-35 30c-17 15-44 15-61 0l-35-30c-38-31-59-80-59-131v-11z" fill="url(#metal)" stroke="#2f3842" stroke-width="10"/>
              <path d="M145 184c0-59 47-106 105-106 58 0 105 47 105 106v10c0 41-17 80-47 106l-28 24-27-24c-30-26-47-65-47-106v-10z" fill="#c9ced4" opacity="0.85"/>
              <path d="M198 152h104c26 0 46 20 46 46v9c0 77-62 139-139 139s-139-62-139-139v-9c0-26 20-46 46-46h82z" fill="#4b5564" opacity="0.65"/>

              <path d="M205 178v54h90v-54c0-27-22-49-49-49h-8c-27 0-49 22-49 49z" fill="#1b2029" opacity="0.8"/>
              <path d="M162 187c10-32 34-54 62-66 24-10 53-8 77 3 18 8 34 22 45 39-8 5-18 8-28 10-10 1-18 0-27-1-16-2-31-2-46 0-23 4-46 16-69 35-8 7-18 16-14-20z" fill="#363d49" opacity=".72"/>

              <g fill="#dfe8f8" stroke="#1a1d22" stroke-width="5">
                <ellipse cx="197" cy="242" rx="39" ry="29"/>
                <ellipse cx="304" cy="242" rx="39" ry="29"/>
                <circle cx="212" cy="235" r="14" fill="#0f1721"/>
                <circle cx="289" cy="235" r="14" fill="#0f1721"/>
                <path d="M257 237c10 7 16 10 27 10 5 0 10-1 15-3" fill="none" stroke="#0f1721" stroke-width="6" stroke-linecap="round"/>
                <path d="M233 290c13 10 23 16 33 16 11 0 22-6 35-16" fill="none" stroke="#1a1d22" stroke-width="8" stroke-linecap="round"/>
              </g>

              <path d="M254 290l-15 15 15 16 15-16-15-15z" fill="#1d232c"/>
            </g>

            <g fill="none" stroke="#f4d24d" stroke-width="8" stroke-linecap="round" opacity=".85">
              <path d="M75 98c30-10 45-4 62 15"/>
              <path d="M422 98c-30-10-45-4-62 15"/>
            </g>

            <g fill="#f6d54d" stroke="#d98b00" stroke-width="5">
              <circle cx="250" cy="42" r="10"/>
              <path d="M250 5v52"/>
              <path d="M218 18l64 48"/>
              <path d="M282 18l-64 48"/>
            </g>
          </g>
        </svg>
        <img src="<?= rtrim(preg_replace('#/public$#', '', APP_URL), '/') ?>/imgs/trofy.png" alt="">
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

