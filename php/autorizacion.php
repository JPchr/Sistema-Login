<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once 'conn.php';
include('./recordar_sesion.php');

function login(string $Nusuario, string $contraseña, bool $recordar = false): bool
{
    if($usuario = encontrar_usuario_por_token($Nusuario)){
    }else{
        $sql = 'SELECT usuarios.id, usuario, contrasena
            FROM usuarios
            WHERE usuario = :usuario 
            LIMIT 1';

        $query = db()->prepare($sql);
        $query->bindValue(':usuario', $Nusuario);
        $query->execute();
        $usuario = $query->fetch(PDO::FETCH_ASSOC);
    }

    if ($usuario && esta_usuario_activo($usuario) && password_verify($contraseña, $usuario['contrasena'])) {

        iniciar_sesion_usuario($usuario);

        if ($recordar) {
            recordarme($usuario['id']);
        }

        return true;
    }

    return false;

}

/**
 * Iniciar sesion al usuario
 * @param array $usuario
 * @return bool
 */
function iniciar_sesion_usuario(array $usuario): bool
{

    if (session_regenerate_id()) {
        $_SESSION['usuario'] = $usuario['usuario'];
        $_SESSION['idUsuario'] = $usuario['id'];
        return true;
    }

    return false;
}

function recordarme(int $idUsuario, int $dia = 30)
{
    [$selector, $validador, $token] = generar_tokens();

    borrar_token_usuario($idUsuario);

    $segundos_expirados = time() + 60 * 60 * 24 * $dia;

    $validadorHash = password_hash($validador, PASSWORD_DEFAULT);
    $expiracion = date('Y-m-d H:i:s', $segundos_expirados);

    if (insertar_token_ususario($idUsuario, $selector, $validadorHash, $expiracion)) {
        setcookie('recordarme', $token, $segundos_expirados);
    }
}

function logout(): void
{
    if (esta_conectado_usuario()) {

        borrar_token_usuario($_SESSION['idUsuario']);

        unset($_SESSION['usuario'], $_SESSION['idUsuario']);

        if (isset($_COOKIE['recordarme'])) {
            unset($_COOKIE['recordarme']);
            setcookie('recordar_usuario', null, -1);
        }

        session_destroy();
        
        echo "<script>
        window.location = '../php/index.php';
        </script>";
    }else{
        echo "<script>
        window.location = '../php/index.php';
        </script>";
    }
    
}

function esta_conectado_usuario(): bool
{
    if (isset($_SESSION['usuario'])) {
        return true;
    }

    $token = filter_input(INPUT_COOKIE, 'recordarme', FILTER_SANITIZE_SPECIAL_CHARS);

    if ($token && validar_token($token)) {

        $user = encontrar_usuario_por_token($token);

        if ($user) {
            return iniciar_sesion_usuario($user);
        }
    }
    return false;
}

function esta_usuario_activo(array $usuario): bool
{

    $sql = 'SELECT activo
            FROM usuarios
            WHERE usuario = :usuario AND id = :id
            LIMIT 1';

    $query = db()->prepare($sql);
    $query->bindValue(':usuario', $usuario['usuario']);
    $query->bindValue(':id', $usuario['id']);
    $query->execute();

    $resultado = $query->fetch(PDO::FETCH_ASSOC);

    if ($resultado && isset($resultado['activo']) && $resultado['activo'] == 1) {
        return true;
    }

    return false;
}