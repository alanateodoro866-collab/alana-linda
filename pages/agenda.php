<?php
require_once __DIR__ . '/../conexao.php';
if (!isset($_SESSION['usuario'])) { header('Location: login.php'); exit; }

$sala = $_GET['sala'] ?? 'LABORATÓRIO DE INFORMÁTICA';

// calcula segunda-feira da semana escolhida (?semana=YYYY-MM-DD)
$base = isset($_GET['semana']) ? new DateTime($_GET['semana']) : new DateTime();
$w = (int)$base->format('N'); // 1..7
if ($w !== 1) { $base->modify('-'.($w-1).' day'); } // vai para segunda

// faixa de horários (iguais à imagem)
$slots = [
  ['07:00','07:50'],
  ['07:55','08:40'],
  ['08:40','09:30'],
  ['09:45','10:35'],
  ['10:35','11:25'],
  ['12:15','13:05'],
  ['13:15','14:05'],
  ['14:05','14:55'],
  ['15:05','15:45'],
  ['15:45','16:15'],
  ['16:15','17:05'],
  ['17:05','17:55'],
];

// Reservar (POST)
$msg = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['dia'], $_POST['inicio'], $_POST['fim'])) {
  $dia = $_POST['dia'];
  $inicio = $_POST['inicio'];
  $fim = $_POST['fim'];

  // verifica conflito
  $stmt = $pdo->prepare("SELECT id FROM reservas WHERE sala=? AND data=? AND NOT (hora_fim <= ? OR hora_inicio >= ?) AND status='reservado'");
  $stmt->execute([$sala, $dia, $inicio, $fim]);
  if ($stmt->fetch()) {
    $msg = 'Este horário já está reservado.';
  } else {
    $stmt = $pdo->prepare("INSERT INTO reservas (sala, data, hora_inicio, hora_fim, usuario_id, status) VALUES (?,?,?,?,?,'reservado')");
    $stmt->execute([$sala, $dia, $inicio, $fim, $_SESSION['usuario']['id']]);
    $msg = 'Reserva criada com sucesso.';
  }
  header("Location: agenda.php?sala=".urlencode($sala)."&semana=".$base->format('Y-m-d')."&msg=".urlencode($msg));
  exit;
}

// Busca reservas da semana
$inicioSemana = clone $base;
$fimSemana = (clone $base)->modify('+4 day'); // segunda a sexta
$stmt = $pdo->prepare("SELECT * FROM reservas WHERE sala=? AND data BETWEEN ? AND ? AND status='reservado'");
$stmt->execute([$sala, $inicioSemana->format('Y-m-d'), $fimSemana->format('Y-m-d')]);
$reservas = $stmt->fetchAll();

// facilita lookup
$ocupado = [];
foreach ($reservas as $r) {
  $ocupado[$r['data']][] = $r;
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="UTF-8">
  <title>Space - Agenda</title>
  <link rel="stylesheet" href="../css/style.css">
</head>
<body class="agenda-body">
  <header class="agenda-top">
    <div class="space-logo">Sp<span>a</span>ce</div>
    <div class="agenda-nav">
      <a class="voltar" href="salas.php">↩</a>
    </div>
  </header>

  <div class="agenda-header">
    <div class="icone-cal">📅</div>
    <?php for ($d=0;$d<5;$d++): 
      $dia = (clone $base)->modify("+$d day"); ?>
      <div class="dia">
        <div class="dia-num"><?= $dia->format('d') ?></div>
        <div class="dia-nome"><?= ucfirst(strftime('%A', $dia->getTimestamp())) ?></div>
      </div>
    <?php endfor; ?>
  </div>

  <?php if(isset($_GET['msg'])): ?>
    <div class="alert-info"><?= htmlspecialchars($_GET['msg']) ?></div>
  <?php endif; ?>

  <div class="agenda-titulo-sala"><?= htmlspecialchars($sala) ?></div>

  <form method="post" class="grade">
    <input type="hidden" name="sala" value="<?= htmlspecialchars($sala) ?>">
    <table class="tabela-agenda">
      <thead>
        <tr>
          <th></th>
          <?php for ($d=0;$d<5;$d++): $dia = (clone $base)->modify("+$d day"); ?>
            <th><?= $dia->format('d') ?></th>
          <?php endfor; ?>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($slots as $slot): 
          [$ini,$fim] = $slot; ?>
          <tr>
            <td class="hora"><?= $ini ?> - <?= $fim ?></td>
            <?php for ($d=0;$d<5;$d++):
              $dia = (clone $base)->modify("+$d day")->format('Y-m-d');
              $isReservado = false;
              if (!empty($ocupado[$dia])) {
                foreach ($ocupado[$dia] as $r) {
                  if (!($r['hora_fim'] <= $ini || $r['hora_inicio'] >= $fim)) { $isReservado = true; break; }
                }
              }
            ?>
              <td class="<?= $isReservado ? 'reservado' : 'livre' ?>">
                <?php if ($isReservado): ?>
                  <span>Reservado</span>
                <?php else: ?>
                  <button name="dia" value="<?= $dia ?>" class="slot" onclick="
                    this.form.inicio.value='<?= $ini ?>';
                    this.form.fim.value='<?= $fim ?>';
                  " type="submit" title="Reservar este horário"></button>
                <?php endif; ?>
              </td>
            <?php endfor; ?>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
    <input type="hidden" name="inicio">
    <input type="hidden" name="fim">
  </form>

  <div class="agenda-rodape">
    <a class="semana-btn" href="?sala=<?= urlencode($sala) ?>&semana=<?= (clone $base)->modify('-7 day')->format('Y-m-d') ?>">◀ Semana anterior</a>
    <a class="semana-btn" href="?sala=<?= urlencode($sala) ?>&semana=<?= (clone $base)->modify('+7 day')->format('Y-m-d') ?>">Próxima semana ▶</a>
  </div>
</body>
</html>
