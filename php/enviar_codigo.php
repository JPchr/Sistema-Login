<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require '../vendor/autoload.php'; // Asegúrate de tener PHPMailer instalado (composer require phpmailer/phpmailer)

include 'conexion.php'; // Tu conexión a la BD

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $correo = $_POST['correo'];

    // Verifica que el correo exista en la base de datos
    $stmt = $conn->prepare("SELECT usuario FROM usuarios WHERE correo = ?");
    $stmt->bind_param("s", $correo);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows == 0) {
        echo "El correo no está registrado.";
        exit;
    }

    // Generar código de verificación
    $codigo = rand(100000, 999999);

    // Guardar el código en la BD (tabla temporal o en la de usuarios)
    $update = $conn->prepare("UPDATE usuarios SET codigo_verificacion = ?, codigo_expira = DATE_ADD(NOW(), INTERVAL 10 MINUTE) WHERE correo = ?");
    $update->bind_param("is", $codigo, $correo);
    $update->execute();

    // Enviar correo
    $mail = new PHPMailer(true);
    try {
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com'; // o tu servidor SMTP
        $mail->SMTPAuth = true;
        $mail->Username = 'tu_correo@gmail.com';
        $mail->Password = 'tu_contraseña_de_aplicacion'; // no la normal, usa una contraseña de app
        $mail->SMTPSecure = 'tls';
        $mail->Port = 587;

        $mail->setFrom('tu_correo@gmail.com', 'Control Escolar');
        $mail->addAddress($correo);
        $mail->isHTML(true);
        $mail->Subject = 'Código de verificación';
        $mail->Body = "Tu código de verificación es: <b>$codigo</b> (expira en 10 minutos)";

        $mail->send();
        echo "Código enviado a tu correo.";
    } catch (Exception $e) {
        echo "Error al enviar el correo: {$mail->ErrorInfo}";
    }
}
?>
