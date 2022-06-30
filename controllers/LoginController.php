<?php

namespace Controllers;

use MVC\Router;
use Model\Usuario;
use Clases\Email;

class LoginController {
    public static function login (Router $router) {
        $alertas = [];

        if($_SERVER['REQUEST_METHOD'] === 'POST') {

            $auth = new Usuario($_POST);

            //Validacion 
            $alertas = $auth->validarLogin();

            //Si el arreglo de alertas esta vacio
            if(empty($alertas)) {

                //Comprobar que el usuario exista
                $usuario = Usuario::where('email', $auth->email);

                if($usuario) {

                    //Verificar password
                    $password_correcto = $usuario->comprobarPasswordAndVerificado($auth->password);

                    //Si el password es correcto y esta confirmado
                    if($password_correcto) {

                        //Autenticar el usuario
                        session_start();

                        //Datos del usuario que inicio sesion
                        $_SESSION['id'] = $usuario->id;
                        $_SESSION['nombre'] = $usuario->nombre . " " . $usuario->apellido;
                        $_SESSION['email'] = $usuario->email;

                        //Variable de sesion creada solo aqui para comprobar si el usuario
                        //esta autenticado
                        $_SESSION['login'] = true;

                        if($usuario->admin === '1') {

                            $_SESSION['admin'] = $usuario->admin ?? null;

                            header('Location: /admin');

                        }else{

                            header('Location: /cita');

                        }

                    }

                }else{
                    Usuario::setAlerta('error', 'Usuario no encontrado');
                }

            }

        }

        $alertas = Usuario::getAlertas();
        
        $router->render('auth/login', [
            'alertas' => $alertas
        ]);

    }

    public static function logout () {
        
        session_start();

        //Cerrando sesion
        $_SESSION = [];

        //Redireccionando
        header('Location: /');

    }

    public static function olvide (Router $router) {

        $alertas = [];

        if($_SERVER['REQUEST_METHOD'] === 'POST') {

            $auth = new Usuario($_POST);

            $alertas = $auth->validarEmail();

            if(empty($alertas)) {

                $usuario = Usuario::where('email', $auth->email);
                
                if($usuario && $usuario->confirmado === '1') {

                    //Generar un Token
                    $usuario->crearToken();

                    //Actualizar al usuario(agregarle el token)
                    $usuario->guardar();

                    //Enviar Email
                    $email = new Email($usuario->email, $usuario->nombre, $usuario->token);

                    $email->enviarInstrucciones();

                    Usuario::setAlerta('exito', 'Revisa tu Email');

                }else{

                    Usuario::setAlerta('error', 'El usuario no existe o no esta confirmado');

                }

            }

        }

        $alertas = Usuario::getAlertas();
        
        $router->render('auth/olvide-password', [
            'alertas' => $alertas
        ]);

    }

    public static function recuperar (Router $router) {

        $alertas = [];

        $error = false;

        //Sanitizando token
        $token = s($_GET['token']);

        //Buscar Usuario por su token
        $usuario = Usuario::where('token', $token);

        //Si no existe ningun usuario con ese token
        if(empty($usuario)) {

            Usuario::setAlerta('error', 'Token no Valido');

            $error = true;

        }

        if($_SERVER['REQUEST_METHOD'] === 'POST') {

            //Leer el nuevo password
            $password = new Usuario($_POST);

            $alertas = $password->validarPassword();

            if(empty($alertas)) {

                //Eliminando el password de la DB
                $usuario->password = null;

                //Agregamos el nuevo password de la instancia de password y lo agregamos
                //a la DB en el objeto $usuario->password
                $usuario->password = $password->password;

                //Hasheamos el password
                $usuario->hashPassword();
                  
                //Eliminando el token
                $usuario->token = '';

                //Una vez realizadas las especificaciones, lo guardamos(actualizamos en este caso)
                $resultado = $usuario->guardar();

                if($resultado) {

                    //Redireccionamos a login para iniciar sesion
                    header('Location: /');

                }

            }
        }

        $alertas = Usuario::getAlertas();
        
        $router->render('auth/recuperar-password', [
            'alertas' => $alertas,
            'error' => $error
        ]);

    }

    public static function crear (Router $router) {

        //alertas vacias
        $alertas = [];

        $usuario = new Usuario;

        if($_SERVER['REQUEST_METHOD'] === 'POST') {

            //Sincronizar el objeto vacio con los datos nuevos(datos de POST)
            //Esto para que los campos correctos no se vacien al recargar
            $usuario->sincronizar($_POST);

            //obtenemos los errores retornados por el metodo de validacion
            $alertas = $usuario->validarNuevaCuenta();

            //Si no hay errores(alertas esta vacio)
            if(empty($alertas)) {

                //Verificar que el usuario no este registrado
                $resultado = $usuario->existeUsuario();

                if($resultado->num_rows) {
                    $alertas = Usuario::getAlertas();
                }else{
                    //Hashear el password
                    $usuario->hashPassword();

                    //Generar un token unico
                    $usuario->crearToken();

                    //Enviar un email
                    $email = new Email($usuario->email, $usuario->nombre, $usuario->token);

                    $email->enviarConfirmacion();

                    //Crear el usuario
                    $resultado = $usuario->guardar();

                    //Redireccionando a mensaje 
                    if($resultado) {
                        header('Location: /mensaje');
                    }

                }

            }

        }
        
        $router->render('auth/crear-cuenta', [
            'usuario' => $usuario,
            'alertas' => $alertas
        ]);

    }

    public static function mensaje(Router $router) {

        $router->render('auth/mensaje', [

        ]);

    }

    public static function confirmar(Router $router) {
        $alertas = [];

        $token = s($_GET['token']);

        //Buscando un registro por el token de la URL(Como es metodo estatico, no se requiere instanciar)
        $usuario = Usuario::where('token', $token);

        //Si no encontro ningun token valido que coincida
        if(empty($usuario)) {

            //Mostrar mensaje de error
            Usuario::setAlerta('error', 'Token no valido');

        }else{

            //Modificar el campo de 'confirmado en la base de datos por un 1'
            $usuario->confirmado = "1";

            //Eliminando el token de confirmacion
            $usuario->token = '';

            $usuario->guardar();

            Usuario::setAlerta('exito', 'Cuenta Comprobada Correctamente');
        }

        //Obtener alertas
        $alertas = Usuario::getAlertas();

        $router->render('auth/confirmar-cuenta', [
            'alertas' => $alertas
        ]);

    }
}