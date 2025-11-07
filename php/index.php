<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once 'conn.php';
include('./autorizacion.php');

if (esta_conectado_usuario()) {
        echo "<script>
        window.location = '../html/menu.html';
        </script>";
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Control Escolar - Iniciar Sesión</title>
  <link rel="stylesheet" href="../css/Inicio.css">
</head>
<body>
  <header>
    Control Escolar
  </header>

  <main>
    <div class="login-container">
      <h2>Inicio de sesión</h2>
      <form action="../php/procesar_login.php" method="POST">
        <label for="usuario">Usuario:</label>
        <input type="text" id="usuario" name="usuario" required>

        <label for="contrasena">Contraseña:</label>
        <input type="password" id="contrasena" name="contrasena" required>

        <div>
            <label for="remember_me">
                <input type="checkbox" name="RecordarSesion" id="RecordarSesion" value="checked"/>
                Recordar Sesion
            </label>
        </div>

        <div class="links">
          <a href="./Registro.html">¿No tienes cuenta? Regístrate ahora!</a><br>
          <a href="./Restablecer-contra.html">¿Olvidaste tu contraseña? Recupérala aquí!!</a>
        </div>

        <button type="submit" class="btn-iniciar">Iniciar</button>
      </form>
    </div>
  </main>

  <footer>
    Control escolar @2025
  </footer>
</body>
</html>