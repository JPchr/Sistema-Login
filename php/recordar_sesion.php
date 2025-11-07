<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once 'conn.php';

function generar_tokens(): array
{
    $selector = bin2hex(random_bytes(16));
    $validador = bin2hex(random_bytes(32));

    return [$selector, $validador, $selector . ':' . $validador];
}

function parsear_token(string $token): ?array
{
    $parts = explode(':', $token);

    if ($parts && count($parts) == 2) {
        return [$parts[0], $parts[1]];
    }
    return null;
}

function insertar_token_ususario(int $id_usuario, string $selector, string $validador_hash, string $expiracion): bool
{
    $sql = 'INSERT INTO tokensusuarios(idUsuario, selector, validadorHash, expiracion)
            VALUES(:id_usuario, :selector, :validador_hash, :expiracion)';

    $query = db()->prepare($sql);
    $query->bindValue(':id_usuario', $id_usuario);
    $query->bindValue(':selector', $selector);
    $query->bindValue(':validador_hash', $validador_hash);
    $query->bindValue(':expiracion', $expiracion);

    return $query->execute();
}

function encontrar_token_usuario_por_selector(string $selector)
{

    $sql = 'SELECT idToken, selector, validadorHash, idUsuario, expiracion
                FROM tokensusuarios
                WHERE selector = :selector AND
                    expiracion >= now()
                LIMIT 1';

    $query = db()->prepare($sql);
    $query->bindValue(':selector', $selector);

    $query->execute();

    return $query->fetch(PDO::FETCH_ASSOC);
}

function borrar_token_usuario(int $id_usuario): bool
{
    $sql = 'DELETE FROM tokensusuarios WHERE idUsuario = :id_usuario';
    $query = db()->prepare($sql);
    $query->bindValue(':id_usuario', $id_usuario);

    return $query->execute();
}

function encontrar_usuario_por_token(string $token)
{
    $tokens = parsear_token($token);

    if (!$tokens) {
        return null;
    }

    $sql = 'SELECT usuarios.id, usuario
            FROM usuarios
            INNER JOIN tokensusuarios ON idUsuario = usuarios.id
            WHERE selector = :selector AND
                expiracion > now()
            LIMIT 1';

    $query = db()->prepare($sql);
    $query->bindValue(':selector', $tokens[0]);
    $query->execute();

    return $query->fetch(PDO::FETCH_ASSOC);
}

function validar_token(string $token): bool { 
    
    [$selector, $validador] = parsear_token($token);
    $tokens = encontrar_token_usuario_por_selector($selector);
    if (!$tokens) {
        return false;
    }
    return password_verify($validador, $tokens['validadorHash']);
}
?>