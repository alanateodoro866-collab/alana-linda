<?php
require_once __DIR__ . '/../conexao.php';
if (!isset($_SESSION['usuario'])) { header('Location: login.php'); exit; }

$salas = [
  'ANFITEATRO',
  'ARMÁRIO FIXO',
  'ATÊLIE',
  'BIBLIOTECA',
  'LABORATÓRIO DE CIÊNCIAS',
  'LABORATÓRIO DE INFORMÁTICA',
  'LABORATÓRIO MÓVEL',
];
$busca = trim($_GET['q'] ?? '');
if ($busca !== '') {
  $salas = array_values(array_filter($salas, fn($s) => mb_stripos($s, $busca) !== false));
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="UTF-8">
  <title>Space - Salas</title>
  <link rel="stylesheet" href="../css/style.css">
</head>
<body class="salas-body">
  <header class="barra-salas">
    <form class="search" method="get">
      <input type="text" name="q" value="<?= htmlspecialchars($busca) ?>" placeholder="pesquisar...">
    </form>
    <a class="voltar" href="home.php">↩</a>
  </header>

  <main class="lista-salas">
    <?php foreach ($salas as $i => $sala): ?>
      <a class="btn-sala <?= $i%2 ? 'escura':'clara' ?>" href="agenda.php?sala=<?= urlencode($sala) ?>">
        <?= htmlspecialchars($sala) ?>
      </a>
    <?php endforeach; ?>
  </main>

  <div class="circulo amarelo"></div>
  <div class="circulo vermelho"></div>
</body>
</html>
