<?php
session_start();
include('conexion.php');

$usuario = trim($_POST['usuario']);
$contrasena = $_POST['contrasena'];

$sql = "SELECT * FROM usuarios WHERE usuario = '$usuario'";
$resultado = $conexion->query($sql);

if ($resultado->num_rows > 0) {
    $fila = $resultado->fetch_assoc();

   
    if (password_verify($contrasena, $fila['contrasena'])) {
      
        $_SESSION['usuario'] = $fila['usuario'];
        $_SESSION['nombre'] = $fila['nombre'];

        echo "<script>
            alert('Inicio de sesión exitoso. ¡Bienvenido, " . $fila['nombre'] . "!');
            window.location = 'menu.html';
        </script>";
    } else {
        echo "<script>
            alert('Contraseña incorrecta.');
            window.location = 'Inicio.html';
        </script>";
    }
} else {
    echo "<script>
        alert('Usuario no encontrado.');
        window.location = 'Inicio.html';
    </script>";
}

$conexion->close();
?>
