<?php
session_start();

// Limpa todas as variáveis de sessão
$_SESSION = array();

// Se estiver usando cookies de sessão, remove também
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

// Destrói a sessão
session_destroy();

// Redireciona para página inicial (login)
header("Location: ../../indexPcp.php");
exit;
?>
