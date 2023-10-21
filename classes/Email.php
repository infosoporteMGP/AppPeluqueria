<?php
namespace Classes;

use PHPMailer\PHPMailer\PHPMailer;

class Email {
    public $email;
    public $nombre;
    public $token;

    public function  __construct($email, $nombre, $token){

        $this->email = $email;
        $this->nombre = $nombre;
        $this->token = $token;
       
    }

    public function enviarConfirmacion(){
        // crear el objeto de mail

        $mail = new PHPMailer();
        $mail->isSMTP();
        $mail->Host = $_ENV['EMAI_HOST'];
        $mail->SMTPAuth = true;
        $mail->Port = $_ENV['EMAI_PORT'];
        $mail->Username = $_ENV['EMAI_USER'];
        $mail->Password = $_ENV['EMAI_PASS'];
        $mail->SMTPSecure = 'tls';

        $mail->setFrom('cuentas@appsalon.com');
        $mail->addAddress('cuentas@appsalon.com', 'AppSalon.com');
        $mail->Subject = 'Confirma tu cuenta';

        // Set HTML
        $mail->isHTML(TRUE);
        $mail->CharSet= "UTF-8";

        $contenido ="<html>";
        $contenido .="<p><strong>Hola " . $this->nombre . "</strong> Has creado tu cuenta en AppSalon, sólo debes confirmar la cuenta presionando el siguiente enlace</p>";
        $contenido .="<p>Presiona aquí: <a href='" . $_ENV['APP_URL'] . "/confirmar-cuenta?token=" . $this->token . "'> CONFIRMAR CUENTA </a></p>";
        $contenido .= "<p>Si tu no solicitaste ésta cuenta, puedes ignorar éste mensaje.</p>";
        $contenido .="</html>";

        $mail->Body = $contenido;

        // Enviar el email
        //mostrar($mail->ErrorInfo);
        //debuguear($mail);
        $mail->send();

    }

    public function enviarInstrucciones(){
        // crear el objeto de mail

        $mail = new PHPMailer();
        $mail->isSMTP();
        $mail->Host = $_ENV['EMAI_HOST'];
        $mail->SMTPAuth = true;
        $mail->Port = $_ENV['EMAI_PORT'];
        $mail->Username = $_ENV['EMAI_USER'];
        $mail->Password = $_ENV['EMAI_PASS'];
        $mail->SMTPSecure = 'tls';

        $mail->setFrom('cuentas@appsalon.com');
        $mail->addAddress('cuentas@appsalon.com', 'AppSalon.com');
        $mail->Subject = 'Restablecer tu password';

        // Set HTML
        $mail->isHTML(TRUE);
        $mail->CharSet= "UTF-8";

        $contenido ="<html>";
        $contenido .="<p><strong>Hola " . $this->nombre . "</strong>Has solicitado restablecer tu password, Sigue el siguiente para hacerlo</p>";
        $contenido .="<p>Presiona aquí: <a href='" . $_ENV['APP_URL'] . "/recuperar?token=" . $this->token . "'> restablecer password </a></p>";
        $contenido .= "<p>Si tu no solicitaste ésta cuenta, puedes ignorar éste mensaje.</p>";
        $contenido .="</html>";

        $mail->Body = $contenido;

        // Enviar el email
        //mostrar($mail->ErrorInfo);
        //debuguear($mail);
        $mail->send();
    }


}
