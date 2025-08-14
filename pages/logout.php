<?php
require_once __DIR__ . '/../conexao.php';
session_destroy();
header('Location: login.php');
exit;
