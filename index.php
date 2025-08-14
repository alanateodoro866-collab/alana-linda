<?php
session_start();
if (isset($_SESSION['usuario'])) {
  header('Location: pages/home.php');
  exit;
} else {
  header('Location: pages/login.php');
  exit;
}
