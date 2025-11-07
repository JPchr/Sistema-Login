<?php
include('conexion.php');

$nombre = $_POST['nombre'];
$usuario = $_POST['usuario'];
$correo = $_POST['correo'];
$contrasena = $_POST['contrasena'];
$confirmar = $_POST['confirmar'];

if ($contrasena != $confirmar) {
    echo "<script>
        alert('Las contraseñas no coinciden.');
        window.location.href = 'Registro.html';
    </script>";
    exit();
}

$hash = password_hash($contrasena, PASSWORD_DEFAULT);

// Verificar si el usuario o correo ya existen
$sql_check = "SELECT * FROM usuarios WHERE usuario = '$usuario' OR correo = '$correo'";
$resultado = $conexion->query($sql_check);

if ($resultado && $resultado->num_rows > 0) {
    echo "<script>
        alert('El usuario o correo ya existen. Intenta con otros.');
        window.location.href = '../html/Registro.html';
    </script>";
    exit();
} else {
    // Insertar nuevo usuario
    $sql_insert = "INSERT INTO usuarios (nombre, usuario, correo, contrasena, activo, rol)
                   VALUES ('$nombre', '$usuario', '$correo', '$hash', 1, 3)";

    if ($conexion->query($sql_insert) === TRUE) {
        echo "<script>
            alert('Usuario registrado correctamente.');
            window.location.href = '../php/index.php';
        </script>";
    } else {
        echo "<script>
            alert('Error al registrar el usuario: " . $conexion->error . "');
            window.location.href = '../html/Registro.html';
        </script>";
    }
}

$conexion->close();
?>
