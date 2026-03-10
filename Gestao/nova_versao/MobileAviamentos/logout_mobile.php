<?php
session_start();

// Destrói os dados da sessão
session_unset();
session_destroy();

// Redireciona de volta para a tela de login
header("Location: login_mobile.php");
exit;
?>