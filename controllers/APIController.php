<?php  

namespace Controllers;

use COM;
use Model\Cita;
use Model\CitaServicio;
use Model\Servicio;

class APIController {
    public static function index() {
        $servicios = Servicio::all();
        echo json_encode($servicios);
    }

    public static function guardar(){
        //$respuesta = [                   // arreglo asociativo - un array asociativo en php es equivalente a un objeto en javascript
        //    'datos' => $_POST
        //];

        //almacena la cita y devuerve el id de la cita y lo dejamos en resultado
        $cita = new Cita($_POST);

        $resultado = $cita->guardar();
        
        $id = $resultado['id'];

        //$respuesta = [
        //    'cita' => $cita
        //];

        // almacena los servicios con el id de la cita

        $idServicios = explode(",", $_POST['servicios']);    // mete en $idServicios un array con elementos de string, porque anter era un solo string separado por comas. eso hace explode

        foreach ($idServicios as $idServicio) {
            $args = [
                'citaId' => $id,
                'servicioId' => $idServicio
            ];
            $citaServicio = new CitaServicio($args);
            $citaServicio ->guardar();
        }

        //retornamos la respuesta
        $respuesta = [
            'resultado' => $resultado
        ];

        echo json_encode($respuesta);       // arreglo asociativo codificado en json
    }

    public static function eliminar(){
        if ($_SERVER["REQUEST_METHOD"] === 'POST'){
            $id=$_POST['id'];

            $cita = Cita::find($id);            // busca la cita

            $cita->eliminar();      // Elimina la cita

            header('Location:' . $_SERVER['HTTP_REFERER']);   // redirecciona luego de eliminar a la URL anterior


        }
    }

   
}

 