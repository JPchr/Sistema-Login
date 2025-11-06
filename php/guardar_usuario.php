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
        window.location.href = 'Registro.html';
    </script>";
    exit();
} else {
    // Insertar nuevo usuario
    $sql_insert = "INSERT INTO usuarios (nombre, usuario, correo, contrasena)
                   VALUES ('$nombre', '$usuario', '$correo', '$hash')";

    if ($conexion->query($sql_insert) === TRUE) {
        echo "<script>
            alert('Usuario registrado correctamente.');
            window.location.href = 'Inicio.html';
        </script>";
    } else {
        echo "<script>
            alert('Error al registrar el usuario: " . $conexion->error . "');
            window.location.href = 'Registro.html';
        </script>";
    }
}

$conexion->close();
?>
