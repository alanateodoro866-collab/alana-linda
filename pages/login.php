<?php
session_start();
include('../crud/conexao.php');

try {
  $qtd = $pdo->query("SELECT COUNT(*) as c FROM usuarios")->fetch()['c'];
  if ($qtd == 0) {
    $hash = password_hash('123456', PASSWORD_DEFAULT);
    $stmt = $pdo->prepare("INSERT INTO usuarios (nome, email, senha) VALUES (?,?,?)");
    $stmt->execute(['Admin', 'admin@marista.com', $hash]);
  }
} catch (Exception $e) { }

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
  <style>
    *{margin:0;padding:0;box-sizing:border-box;font-family:Arial,sans-serif;}
    body{display:flex;height:100vh;}
    .login-container{display:flex;width:100%;}

    .login-left{
      background:#0a2f57;
      width:50%;
      position:relative;
      display:flex;
      flex-direction:column;
      justify-content:flex-start;
      align-items:flex-start;
      padding:60px 0 0 40px; 
      overflow:hidden;
    }

    .space-logo{
      font-size:48px;
      font-weight:bold;
      display:flex;
      align-items:center;
      gap:2px;
      position:relative;
      z-index:1; /* fica acima das linhas */
    }
    .space-logo span.s { color: #ffffffff; }   
    .space-logo span.p { color: #FFD43B; }  
    .space-logo span.a { color: #FF6B6B; }   
    .space-logo span.c {
      color: #ffffffff;                       
      background: #2482adff;                   
      padding: 2px 8px;
      border-radius: 4px;
    }
    .space-logo span.e { color: #ffffffff; }   

    /* Estilo das curvas */
    .curvas {
      position:absolute;
      bottom:0;
      left:0;
      width:100%;
      height:100%;
      z-index:0;
    }
    .curvas svg {
      width:100%;
      height:100%;
    }

    .login-right{
      width:50%;
      display:flex;
      flex-direction:column;
      justify-content:center;
      align-items:center;
      background:#fff;
      padding:30px;
    }
    .marista-logo img{width:300px;}
    .login-form{
      display:flex;
      flex-direction:column;
      width:280px;
    }
    .login-form label{font-size:14px;}
    .login-form input{
      padding:12px;
      border:none;
      border-radius:25px;
      background:#d9e1ec;
      margin-bottom:15px;
      outline:none;
      font-size:14px;
    }
    .btn-primary{
      margin-top:10px;
      padding:12px;
      border:none;
      border-radius:25px;
      background:#005792;
      color:#fff;
      font-weight:bold;
      cursor:pointer;
    }
    .btn-primary:hover{background:#003d66;}
    .alert-erro{color:red;margin-bottom:10px;font-size:14px;}
  </style>
</head>
<body>
  <div class="login-container">
    <div class="login-left">
      <div class="space-logo">
        <span class="s">S</span>
        <span class="p">p</span>
        <span class="a">a</span>
        <span class="c">c</span>
        <span class="e">e</span>
      </div>

      <!-- SVG com curvas ajustadas -->
      <div class="curvas">
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 600 400" preserveAspectRatio="xMidYMax slice">
          <!-- Linha amarela mais alta -->
          <path d="M 0 320 Q 200 200 400 380 T 600 340" stroke="#FFD43B" stroke-width="8" fill="none"/>
          <!-- Linha branca cruzando pelo meio -->
          <path d="M 0 350 Q 150 250 300 350 Q 450 450 600 300" stroke="#ffffff" stroke-width="10" fill="none"/>
          <!-- Linha vermelha com looping -->
          <path d="M 0 380 Q 150 200 300 320 t 450 450 600 280" stroke="#FF6B6B" stroke-width="10" fill="none"/>
        </svg>
      </div>
    </div>

    <div class="login-right" style="margin-top: -70px;">
      <div class="marista-logo">
        <img src="../img/marista-logo" alt="Marista">
      </div>

      <?php if($erro): ?>
        <div class="alert-erro"><?= htmlspecialchars($erro) ?></div>
      <?php endif; ?>

      <form method="post" class="login-form">
        <label>E-mail</label>
        <input type="email" name="email" required>
        <label>Senha</label>
        <input type="password" name="senha" required>
        <button type="submit" class="btn-primary">Entrar</button>
      </form>
    </div>
  </div>
</body>
</html>

