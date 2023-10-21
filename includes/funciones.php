<?php

function debuguear($variable) : string {
    echo "<pre>";
    var_dump($variable);
    echo "</pre>";
    exit;
}

function mostrar($variable) {
    echo "<pre>";
    var_dump($variable);
    echo "</pre>";
    
   
}

// Escapa / Sanitizar el HTML
function s($html) : string {
    $s = htmlspecialchars($html);
    return $s;
}

// función para determinar el último elemento dentro de un array
function esUltimo(string $actual, string $proximo) : bool {
    if($actual !== $proximo) {
        return true;
    }
    return false;
}

// Funciòn que revisa que el usuario estè autenticado

function isAuth() : void{        // retornar un void es no retornar nada
    if(!isset($_SESSION['login'])){   // si no está como true el 'login'
        header('Location: /'); 
    }
}

function isAdmin() : void {
    if (!isset($_SESSION['admin'])){
        header('Location: /');
    }
}

