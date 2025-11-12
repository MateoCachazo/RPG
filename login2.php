<?php
session_start();

$archivo = 'USUARIOS.json';

// Validar datos
if (empty($_POST["username"]) || empty($_POST["password"])) {
    header("Location: login.php?error=faltan_datos");
    exit;
}

$username = trim($_POST["username"]);
$password = trim($_POST["password"]);

// Leer usuarios
if (file_exists($archivo)) {
    $usuarios = json_decode(file_get_contents($archivo), true);
} else {
    $usuarios = [];
}

if (!is_array($usuarios)) {
    $usuarios = [];
}

$loginCorrecto = false;

// Buscar usuario en la lista
foreach ($usuarios as $user) {
    // Compara usuario y contraseña exactamente
    if (isset($user['username']) && isset($user['password'])) {
        if (trim($user['username']) === $username && trim($user['password']) === $password) {
            $loginCorrecto = true;
            $_SESSION["username"] = $user['username'];
            break;
        }
    }
}

if ($loginCorrecto) {
    $_SESSION['username'] = $user['username'];
     echo "<script>
localStorage.setItem('username', " . json_encode($user['username']) . ")
window.location.href = 'perfil.php';
</script>";
    exit;
} else {
    // Si no coincide usuario o contraseña
    header("Location: login.php?error=credenciales_invalidas");
    exit;
}
?>
