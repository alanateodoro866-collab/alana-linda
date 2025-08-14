<?php
// require_once __DIR__ . './crud/conexao.php';
include('../crud/conexao.php');
// Seed automático do 1º usuário (apenas se tabela estiver vazia)
try {
  $qtd = $pdo->query("SELECT COUNT(*) as c FROM usuarios")->fetch()['c'];
  if ($qtd == 0) {
    $hash = password_hash('123456', PASSWORD_DEFAULT);
    $stmt = $pdo->prepare("INSERT INTO usuarios (nome, email, senha) VALUES (?,?,?)");
    $stmt->execute(['Admin', 'admin@marista.com', $hash]);
  }
} catch (Exception $e) {
  // silencioso
}

$erro = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $email = trim($_POST['email'] ?? '');
  $senha = trim($_POST['senha'] ?? '');

  $stmt = $pdo->prepare("SELECT * FROM usuarios WHERE email = ?");
  $stmt->execute([$email]);
  $user = $stmt->fetch();

  if ($user && password_verify($senha, $user['senha'])) {
    $_SESSION['usuario'] = ['id' => $user['id'], 'nome' => $user['nome'], 'email' => $user['email']];
    header('Location: home.php');
    exit;
  } else {
    $erro = 'E-mail ou senha inválidos.';
  }
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="UTF-8">
  <title>Space - Login</title>
  <link rel="stylesheet" href="../css/style.css">
</head>
<body class="login-body">
  <div class="login-container">
    <div class="login-left">
      <div class="space-logo">Sp<span>a</span>ce</div>
      <div class="curvas"></div>
    </div>
    <div class="login-right">
      <div class="marista-logo">
        <img src="../img/marista-logo.png" alt="Marista" onerror="this.style.opacity=0;">
      </div>

      <?php if($erro): ?>
        <div class="alert-erro"><?= htmlspecialchars($erro) ?></div>
      <?php endif; ?>

      <form method="post" class="login-form">
        <label>E-mail</label>
        <input type="email" name="email" placeholder="Seu e-mail" required>
        <label>Senha</label>
        <input type="password" name="senha" placeholder="Sua senha" required>
        <button type="submit" class="btn-primary">Entrar</button>
      </form>
    </div>
  </div>
</body>
</html>
