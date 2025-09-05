
<?php
require_once __DIR__ . '/../crud/conexao.php';
if (!isset($_SESSION['usuario'])) { header('Location: login.php'); exit; }
$u = $_SESSION['usuario'];
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="UTF-8">
  <title>Space - Home</title>
  <link rel="stylesheet" href="../css/style.css">
</head>
<body class="home-body">
  <header class="topbar">
    <div class="topbar-left">
      <img src="../img/marista-logo-branco.png" alt="Marista" class="logo-top" onerror="this.style.opacity=0;">
    </div>
    <div class="topbar-right">
      <a class="badge" href="login.php" onclick="event.preventDefault();document.getElementById('logout').submit();">Login</a>
    </div>
    <form id="logout" method="post" action="logout.php"></form>
  </header>

  <main class="home-hero">
    <section class="home-card">
      <p class="ola">OLÁ!</p>
      <h1 class="educadores">Educadores</h1>
      <p class="texto">
        Aqui, na Escola Marista, você tem acesso a diversas ferramentas que vão facilitar sua jornada.
        Agende os espaços do técnico de forma rápida e prática. Selecione o que precisa e vá direto ao que importa! :)
      </p>
      <div class="home-actions">
        <a href="salas.php" class="btn-danger">↩ sair</a>
      </div>
    </section>

    <div class="home-foto">
      <img src="../img/alunos.jpg" alt="Alunos" onerror="this.style.opacity=0;">
      <div class="decoracoes"></div>
    </div>

    <div class="space-logo canto">Sp<span>a</span>ce</div>
  </main>
</body>
</html>
