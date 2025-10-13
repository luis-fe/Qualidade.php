<?php
// Iniciar a sessão
session_start();

// Remover todas as variáveis de sessão
$_SESSION = array();

// Finalizar a sessão
session_destroy();

header("Location: index.php");
?>