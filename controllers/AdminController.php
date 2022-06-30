<?php

namespace Controllers;

use Model\AdminCita;
use MVC\Router;

class AdminController {

    public static function index(Router $router) {

        //Funcion para comprobar si el usuario es admin
        isAdmin();

        //Si no existe ninguna fecha por la url mostramos la del servidor
        $fecha = $_GET['fecha'] ?? date('Y-m-d');

        //dividimos la los valores de la fecha en un arreglo
        $fechas = explode('-', $fecha);

        //Validamos la fecha
        if(!checkdate($fechas[1], $fechas[2], $fechas[0])) {
            header('Location: /404');
        }

        //Consultar la DB
        $consulta = " SELECT citas.id, citas.hora, CONCAT( usuarios.nombre, ' ', usuarios.apellido ) as 
        cliente, " . " usuarios.email, usuarios.telefono, servicios.nombre as servicio, servicios.precio " . 
        " FROM citas " . " LEFT OUTER JOIN usuarios " . " ON citas.usuario_id=usuarios.id " . 
        " LEFT OUTER JOIN citas_servicios " . " ON citas_servicios.cita_id=citas.id " . 
        " LEFT OUTER JOIN servicios " . " ON servicios.id=citas_servicios.servicio_id " . 
        " WHERE fecha = '${fecha}' ";

        $citas = AdminCita::SQL($consulta);

        $router->render('admin/index', [
            'nombre' => $_SESSION['nombre'],
            'citas' => $citas,
            'fecha' => $fecha
        ]);

    }

}