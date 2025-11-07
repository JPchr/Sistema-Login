<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once 'conn.php';
include('./autorizacion.php');

$usuario = trim($_POST['usuario']);
$contrasena = $_POST['contrasena'];
$recordar = isset($_POST['RecordarSesion']) ? $_POST['RecordarSesion'] : false;

$sql = 'SELECT *
        FROM usuarios
        WHERE usuario = :usuario
        LIMIT 1';

$query = db()->prepare($sql);
$query->bindValue(':usuario', $usuario);
$query->execute();
$fila = $query->fetch(PDO::FETCH_ASSOC);

if ($fila) {
    if (login($usuario, $contrasena, $recordar)) {
        echo "<script>
            alert('Inicio de sesión exitoso. ¡Bienvenido, " . htmlspecialchars($fila['nombre']) . "!');
            window.location = '../html/menu.html';
        </script>";
    } else {
        echo "<script>
            alert('Contraseña incorrecta.');
            window.location = '../php/index.php';
        </script>";
    }
} else {
    echo "<script>
        alert('Usuario no encontrado.');
        window.location = '../php/index.php';
    </script>";
}
?>
