<?php

namespace Model;

class Usuario extends ActiveRecord {

    //Base de datos
    protected static $tabla = 'usuarios';
    protected static $columnasDB = ['id', 'nombre', 'apellido', 'email', 'password', 'telefono', 'admin',
    'confirmado', 'token'];

    public $id;
    public $nombre;
    public $apellido;
    public $email;
    public $password;
    public $telefono;
    public $admin;
    public $confirmado;
    public $token;

    public function __construct($args = [])
    {
        $this->id = $args['id'] ?? null;
        $this->nombre = $args['nombre'] ?? '';
        $this->apellido = $args['apellido'] ?? '';
        $this->email = $args['email'] ?? '';
        $this->password = $args['password'] ?? '';
        $this->telefono = $args['telefono'] ?? '';
        $this->admin = $args['admin'] ?? '0';
        $this->confirmado = $args['confirmado'] ?? '0';
        $this->token = $args['token'] ?? '';
    }

    //Validacion
    public function validarNuevaCuenta() {

        if(!$this->nombre) {
            self::$alertas['error'][] = 'El nombre es obligatorio';
        }

        if(!$this->apellido) {
            self::$alertas['error'][] = 'El apellido es obligatorio';
        }

        if(!$this->email) {
            self::$alertas['error'][] = 'El email es obligatorio';
        }

        if(!$this->password) {
            self::$alertas['error'][] = 'El password es obligatorio';
        }

        if(strlen($this->password) < 6) {
            self::$alertas['error'][] = 'El password debe contener almenos 6 caracteres';
        }

        if(!$this->telefono) {
            self::$alertas['error'][] = 'El telefono es obligatorio';
        }

        if(!preg_match('/[0-9]{10}/', $this->telefono)){ 
            self::$alertas['error'][] = "Formato de telefono no valido";
        }

        return self::$alertas;

    }

    public function validarLogin() {

        if(!$this->email) {
            self::$alertas['error'][] = "El email es obligatorio";
        }

        if(!$this->password) {
            self::$alertas['error'][] = "El password es obligatorio";
        }

        return self::$alertas;

    }

    public function validarEmail() {

        if(!$this->email) {
            self::$alertas['error'][] = "El email es obligatorio";
        }

        return self::$alertas;
    }

    public function validarPassword() {

        if(!$this->password) {

            if(!$this->password) {
                self::$alertas['error'][] = "El password es obligatorio";
            }

            if(strlen($this->password) < 6) {

                self::$alertas['error'][] = "El password debe tener almenos 6 caracteres"; 

            }

        }

        return self::$alertas;

    }

    public function existeUsuario() {

        $query = "SELECT * FROM " . self::$tabla . " WHERE email = '" . $this->email . "' LIMIT 1";
        
        $resultado = self::$db->query($query);

        if($resultado->num_rows) {
            self::$alertas['error'][] = "El usuario ya esta registrado";
        }

        return $resultado;

    }

    public function hashPassword() {

        $this->password = password_hash($this->password, PASSWORD_BCRYPT);

    }

    public function crearToken() {

        //uniqid genera un token unico cada vez que se usa
        $this->token = uniqid();

    }

    public function comprobarPasswordAndVerificado($password) {

        $resultado = password_verify($password, $this->password);

        if(!$resultado || !$this->confirmado) {
           
            self::$alertas['error'][] = 'Password Incorrecto o Tu cuenta no ha sido confirmada';

        }else {
            return true;
        }

    }

}