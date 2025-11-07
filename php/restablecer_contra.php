<?php
include 'conexion.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $usuario = $_POST['usuario'];
    $correo = $_POST['correo'];
    $contrasena = password_hash($_POST['contrasena'], PASSWORD_DEFAULT);

    $stmt = $conn->prepare("SELECT * FROM usuarios WHERE usuario = ? AND correo = ?");
    $stmt->bind_param("ss", $usuario, $correo);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 0) {
        echo "Usuario o correo incorrectos.";
        exit;
    }

    $update = $conn->prepare("UPDATE usuarios SET contrasena = ? WHERE usuario = ? AND correo = ?");
    $update->bind_param("sss", $contrasena, $usuario, $correo);

    if ($update->execute()) {
        echo "Contraseña actualizada correctamente. Ahora puedes iniciar sesión.";
    } else {
        echo "Error al actualizar la contraseña.";
    }
}
?>
