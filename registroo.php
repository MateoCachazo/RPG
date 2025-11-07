<?php
session_start();

$archivo = 'USUARIOS.json';

// Recibir datos del formulario
$username = $_POST['username'] ?? '';
$email = $_POST['email'] ?? '';
$password = $_POST['password'] ?? '';
$confirm = $_POST['confirm-password'] ?? '';

// Validar contraseñas
if ($password !== $confirm) {
    header("Location: registro.php?error=contraseña_no_coincide");
    exit;
}

// Leer archivo JSON existente
$usuarios = [];
if (file_exists($archivo)) {
    $contenido = file_get_contents($archivo);
    $usuarios = json_decode($contenido, true);
    if (!is_array($usuarios)) {
        $usuarios = [];
    }
}

// Verificar si el email ya existe
foreach ($usuarios as $user) {
    if ($user['email'] === $email) {
        header("Location: registro.php?error=usuario_existente");
        exit;
    }
}

// Agregar nuevo usuario
$usuarios[] = [
    'username' => $username,
    'email' => $email,
    'password' => $password 
];

// Guardar en el archivo
file_put_contents($archivo, json_encode($usuarios, JSON_PRETTY_PRINT));

echo "<script>
localStorage.setItem('username', " . json_encode($username) . ");
window.location.href = 'login.php?registro=exitoso';
</script>";

header("Location: login.php?registro=exitoso");
exit;
?>
