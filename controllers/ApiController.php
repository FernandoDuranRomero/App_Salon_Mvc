<?php

namespace Controllers;

use Model\Cita;
use Model\CitaServicio;
use Model\Servicio;

class ApiController {

    public static function index() {

        $servicios = Servicio::all();

        echo json_encode($servicios);

    }

    public static function guardar() {

        $cita = new Cita($_POST);

        //Insertado en la base de datos, y devolviendo el id
        $resultado = $cita->guardar();

        //Obteniendo el id de la cita
        $id = $resultado['id'];

        //Separando por comas los ids de los servicios y mostrandolos en un arreglo
        //Estos ids de los servicios los recibimos por formdata de Js
        $idServicios = explode(",", $_POST['servicios']);

        //Iterando sobre todos los ids de los servicios y almacenandolos
        foreach($idServicios as $idServicio) {

            $args = [
                'cita_id' => $id,
                'servicio_id' => $idServicio
            ];

            //Nueva Instancia del modelo CitaServicio con los ids de cita y servicio que reciba 
            $cita_servicio = new CitaServicio($args);

            //Guardamos en la tabla citas_servicios
            $cita_servicio->guardar();

        }

        // //Array Asociativo(Equivalente a Un Objeto en JavaScript)
        // $respuesta = [
        //     'cita' => $cita
        // ];

        //Retornamos el resultado con todos los datos de la cita
        echo json_encode(['resultado' => $resultado]);

    }

    public static function eliminar() {
        
        if($_SERVER['REQUEST_METHOD'] === 'POST') {

            //Obteniendo id de la cita
            $id = $_POST['id'];

            //Deacuerdo a el id, esta funcion nos retorna el objeto
            //de la cita con todos sus atributos (fecha, hora, usuario, etc)
            $cita = Cita::find($id);

            $cita->eliminar();

            //Redireccionando a la misma pagina
            header('Location:' . $_SERVER['HTTP_REFERER']);

        }

    }

}