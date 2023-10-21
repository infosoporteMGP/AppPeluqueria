<?php 
namespace Controllers;

use Model\AdminCita;
use MVC\Router;

class AdminController {
    public static function index(Router $router ) {
        //session_start();
        
        isAdmin();
        
        $fecha = $_GET['fecha'] ?? date('Y-m-d');    // si no viene el get entonces tomar la del servidor
        // validamos que la fecha es válida  separando la fecha completa en sus miembros componentes
        $fechas = explode('-', $fecha);
        if (!checkdate($fechas[1], $fechas[2], $fechas[0])){
            // si no cumple redirecciono a error
            header('Location: /404');
        }
        
        
        //$fecha='2023-09-04';

        // consultar la bd

        $consulta = "SELECT citas.id, citas.hora, CONCAT( usuarios.nombre, ' ', usuarios.apellido) as cliente, ";
        $consulta .= " usuarios.email, usuarios.telefono, servicios.nombre as servicio, servicios.precio  ";
        $consulta .= " FROM citas  ";
        $consulta .= " LEFT OUTER JOIN usuarios ";
        $consulta .= " ON citas.usuarioId=usuarios.id  ";
        $consulta .= " LEFT OUTER JOIN citasservicios ";
        $consulta .= " ON citasservicios.citaId=citas.id ";
        $consulta .= " LEFT OUTER JOIN servicios ";
        $consulta .= " ON servicios.id=citasservicios.servicioId ";
        $consulta .= " WHERE citas.fecha =  '$fecha' ";  
        
        $citas = AdminCita::SQL($consulta);
 
        $router->render('admin/index', [
            'nombre' => $_SESSION['nombre'],
            'citas' => $citas,
            'fecha' => $fecha
        ]);
    }
}