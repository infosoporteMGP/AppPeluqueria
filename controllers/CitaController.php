<?php 

namespace Controllers;

use MVC\Router;

class CitaController {
    public static function index(Router $router){
        //session_start();

        isAuth();  // comprueba si el usuario está autenticado

        
        //if (empty($_SESSION) or is_null($_SESSION) or !isset($_SESSION)){
        //    header('Location: /');           
        //}  

        $router->render('cita/index', [
            'nombre'=>$_SESSION['nombre'],
            'id'=>$_SESSION['id']

        ]);

    }

}