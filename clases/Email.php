<?php

namespace Clases;

use PHPMailer\PHPMailer\PHPMailer;

class Email {

    public $email;
    public $nombre;
    public $token;

    public function __construct($email, $nombre, $token)
    {
        
        $this->email = $email;
        $this->nombre = $nombre;
        $this->token = $token;

    }

    public function enviarConfirmacion() {

        //Crear el objeto de emali
        $mail = new PHPMailer();

        //Credenciales de Mailtrap
        $mail->isSMTP();
        $mail->Host = 'smtp.mailtrap.io';
        $mail->SMTPAuth = true;
        $mail->Port = 2525;
        $mail->Username = 'd96c5eaa59759a';
        $mail->Password = '940777bd89b5ee';

        //Quien lo envia
        $mail->setFrom('cuentas@appsalon.com');

        //Quien recibe
        $mail->addAddress('cuentas@appsalon.com', 'AppSalon.com');
        $mail->Subject = 'Confirma tu Cuenta';

        //Set HTML
        $mail->isHTML(TRUE);
        $mail->CharSet = 'UTF-8';

        $contenido = "<html>";

        $contenido .= "<p> <strong>Hola " . $this->nombre . " </strong> Has creado tu cuenta en AppSalon 
        Solo debes confirmarla presionando el siguiente enlace</p>";
        $contenido .= "<p> Presiona aqui: <a href='https://stark-coast-24034.herokuapp.com/confirmar-cuenta?token=" . 
        $this->token . "'>Confirmar Cuenta</a> </p>";
        $contenido .= "<p>Si tu no solicitaste esta cuenta, puedes ignorar el mensaje</p>";

        $contenido .= "</html>";

        //Agregandolo a la instancia
        $mail->Body = $contenido;

        //Enviar email
        $mail->send();
    }

    public function enviarInstrucciones() {

        //Crear el objeto de emali
        $mail = new PHPMailer();

        //Credenciales de Mailtrap
        $mail->isSMTP();
        $mail->Host = 'smtp.mailtrap.io';
        $mail->SMTPAuth = true;
        $mail->Port = 2525;
        $mail->Username = 'd96c5eaa59759a';
        $mail->Password = '940777bd89b5ee';

        //Quien lo envia
        $mail->setFrom('cuentas@appsalon.com');

        //Quien recibe
        $mail->addAddress('cuentas@appsalon.com', 'AppSalon.com');
        $mail->Subject = 'Reestablece tu Password';

        //Set HTML
        $mail->isHTML(TRUE);
        $mail->CharSet = 'UTF-8';

        $contenido = "<html>";

        $contenido .= "<p> <strong>Hola " . $this->nombre . " </strong> Has solicitado reestablecer tu 
        password, sigue el siguiente enlace para hacerlo</p>";
        $contenido .= "<p> Presiona aqui: <a href='http://localhost:3000/recuperar?token=" . 
        $this->token . "'>Reestablecer Password</a> </p>";
        $contenido .= "<p>Si tu no solicitaste esta cuenta, puedes ignorar el mensaje</p>";

        $contenido .= "</html>";

        //Agregandolo a la instancia
        $mail->Body = $contenido;

        //Enviar email
        $mail->send();

    }
}
