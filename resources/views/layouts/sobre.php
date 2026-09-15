<?php
$siteRoot = rtrim((string) preg_replace('#/public$#', '', APP_URL), '/');
$imagemPaginaUm = $siteRoot . '/imgs/pag1.png';
$imagemPaginaDois = $siteRoot . '/imgs/pag2.png';
?>
<!doctype html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>Sobre o HSchuler</title>
    <link rel="stylesheet" href="<?= app_asset('css/estilo_homepage.css') ?>?v=30">
    <style>
        body.about-page{display:block;min-height:100svh;overflow-x:hidden;background:#020307;color:#eef3ff;font-family:Arial,Helvetica,sans-serif}
        body.about-page::before{content:"";position:fixed;inset:0;z-index:0;pointer-events:none;background-image:linear-gradient(115deg,rgba(1,3,9,.96) 5%,rgba(2,5,13,.82) 48%,rgba(1,3,9,.93)),url("<?= htmlspecialchars($imagemPaginaUm, ENT_QUOTES, 'UTF-8') ?>"),url("<?= htmlspecialchars($imagemPaginaDois, ENT_QUOTES, 'UTF-8') ?>"),url("<?= htmlspecialchars($imagemPaginaDois, ENT_QUOTES, 'UTF-8') ?>"),url("<?= htmlspecialchars($imagemPaginaUm, ENT_QUOTES, 'UTF-8') ?>");background-repeat:no-repeat;background-position:center,left -180px top 135px,right -180px bottom -65px,right -300px top -65px,left -300px bottom -110px;background-size:cover,min(640px,52vw) auto,min(620px,50vw) auto,min(430px,35vw) auto,min(410px,34vw) auto;opacity:1}
        body.about-page::after{content:"";position:fixed;inset:0;z-index:0;pointer-events:none;background-image:radial-gradient(2px 2px at 15% 24%,#fff,transparent),radial-gradient(1px 1px at 72% 13%,#fff,transparent),radial-gradient(1.5px 1.5px at 86% 60%,#fff,transparent),radial-gradient(1px 1px at 28% 84%,#fff,transparent);background-size:380px 320px;opacity:.72}
        .about-nav{position:relative;z-index:2;display:flex;align-items:center;justify-content:space-between;gap:20px;width:min(1140px,calc(100% - 40px));margin:0 auto;padding:24px 0}.about-brand{display:flex;align-items:center;gap:10px;color:#fff;text-decoration:none;font-weight:700}.about-brand img{width:42px;height:42px;object-fit:contain}.about-nav-link{padding:10px 15px;border:1px solid rgba(151,179,255,.42);border-radius:9px;color:#eaf1ff;text-decoration:none;font-size:.85rem;background:rgba(5,11,28,.55);transition:.18s ease}.about-nav-link:hover{border-color:#8aa7ff;background:rgba(59,88,193,.22)}
        .about-main{position:relative;z-index:1;width:min(1000px,calc(100% - 40px));margin:42px auto 80px}.about-hero{text-align:center}.about-mark{margin:0 0 12px;color:#1fa9f5;font-size:.75rem;font-weight:700;letter-spacing:.14em;text-transform:uppercase}.about-hero h1{max-width:760px;margin:0 auto;color:#f8faff;font-size:clamp(2.15rem,5vw,4.3rem);line-height:1.05;letter-spacing:-.055em}.about-hero h1 span{color:#1fa9f5}.about-hero>p{max-width:730px;margin:24px auto 0;color:#c8d2e9;font-size:clamp(1rem,2vw,1.15rem);line-height:1.7}.about-grid{display:grid;grid-template-columns:1.15fr .85fr;gap:22px;align-items:stretch;margin-top:48px}.about-card{padding:clamp(23px,4vw,38px);border:1px solid rgba(101,181,255,.33);border-radius:20px;background:linear-gradient(145deg,rgba(8,22,42,.92),rgba(3,9,19,.84));box-shadow:inset 0 1px rgba(255,255,255,.08),0 24px 50px rgba(0,0,0,.3);backdrop-filter:blur(8px)}.about-card h2{margin:0 0 16px;color:#fff;font-size:1.35rem}.about-card p{margin:0;color:#c5cee3;font-size:1rem;line-height:1.72}.about-card p+p{margin-top:17px}.feature-list{display:grid;gap:12px;margin:0;padding:0;list-style:none}.feature-list li{display:flex;align-items:flex-start;gap:12px;color:#d8e0f0;line-height:1.45}.feature-icon{display:grid;flex:0 0 32px;width:32px;height:32px;place-items:center;border:1px solid rgba(104,192,255,.7);border-radius:9px;background:linear-gradient(135deg,#19aaf5,#176ed2);color:#fff;font-size:.82rem;font-weight:700}.about-cta{display:flex;justify-content:center;margin-top:30px}.about-cta a{padding:14px 22px;border-radius:10px;background:linear-gradient(100deg,#169ff0,#176ed2);box-shadow:0 10px 22px rgba(31,137,239,.35);color:#fff;text-decoration:none;font-weight:700}
        @media(max-width:720px){.about-nav,.about-main{width:min(100% - 28px,600px)}.about-main{margin-top:26px}.about-grid{grid-template-columns:1fr;margin-top:32px}.about-card{border-radius:15px}body.about-page::before{background-position:center,left -240px top 120px,right -250px bottom -30px,right -320px top 20px,left -320px bottom 20px;background-size:cover,620px auto,600px auto,380px auto,380px auto}.about-hero>p{font-size:.98rem}}
    </style>
</head>
<body class="about-page">
    <?php $navbarActive = 'sobre'; require APP_ROOT . '/resources/views/layouts/navbar.php'; ?>
    <main class="about-main">
        <header class="about-hero">
            <p class="about-mark">Conheça a plataforma</p>
            <h1>Sobre o <span>HSchuler</span></h1>
            <p>Aprender Matemática pode ser uma jornada mais clara, gradual e motivadora.</p>
        </header>
        <section class="about-grid" aria-label="O que é o HSchuler">
            <article class="about-card">
                <h2>Matemática no seu ritmo</h2>
                <p>O <strong>HSchuler</strong> é uma plataforma educacional de Matemática desenvolvida para auxiliar alunos que apresentam dificuldades na aprendizagem da disciplina.</p>
                <p>A plataforma oferece <strong>trilhas de aprendizado</strong> com conteúdos como <strong>potenciação, fatoração, equações, inequações e frações algébricas</strong>, permitindo que os estudantes avancem de forma gradual e interativa.</p>
            </article>
            <aside class="about-card">
                <h2>Como a plataforma apoia</h2>
                <ul class="feature-list">
                    <li><span class="feature-icon">01</span><span>Etapas organizadas para construir conhecimento passo a passo.</span></li>
                    <li><span class="feature-icon">02</span><span>Videoaulas cadastradas por professores para complementar os conteúdos.</span></li>
                    <li><span class="feature-icon">03</span><span>Níveis, XP e ranking para acompanhar o próprio progresso e manter a motivação.</span></li>
                </ul>
            </aside>
        </section>
        <section class="about-card" style="margin-top:22px">
            <h2>Aprender, praticar e evoluir</h2>
            <p>Além das trilhas e das videoaulas, o sistema de níveis, XP e ranking estimula os alunos a continuarem estudando e acompanharem seu próprio progresso. Cada atividade concluída é uma oportunidade de praticar, avançar e ganhar confiança em Matemática.</p>
        </section>
        <p class="about-cta"><a href="<?= app_route('/trilhas') ?>">Explorar trilhas de aprendizado →</a></p>
    </main>
</body>
</html>
