<?php

namespace Controllers;
use MVC\Router;
use Classes\Email;
use Model\Usuario;

class LoginController {
    public static function login(Router $router) {
        $alertas = [];
        $auth=new Usuario;

        if ($_SERVER['REQUEST_METHOD'] === 'POST'){
            $auth=new Usuario($_POST);
            
            $alertas = $auth->validarLogin();

            if (empty ($alertas)){
                // comprobar que exista el usuario - email
                $usuario = Usuario::where('email', $auth->email);
                
                if ($usuario){
                    // Verifico la clave y si está verificado
                    $resultado2=$usuario->comprobarPasswordAndVerificado($auth->password);
                    if ($resultado2){
                        //usuario autenticado
                        //iniciamos sesion
                        session_start();
                        $_SESSION['id'] = $usuario->id;
                        $_SESSION['nombre'] = $usuario->nombre . " " . $usuario->apellido;
                        $_SESSION['email'] = $usuario->email;
                        $_SESSION['login'] = true;

                        // Redireccionamiento

                        if($usuario->admin === "1"){

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
        $router->render('/auth/login',[
            'alertas'=> $alertas,
            'auth' => $auth
        
        ]);
    }

    public static function logout() {
        session_start();

        $_SESSION = [];

        header('Location: /');
    }

    public static function olvide(Router $router) {
        $alertas = [];

        if ($_SERVER['REQUEST_METHOD']==='POST'){
            $auth = new Usuario($_POST);
            $alertas = $auth->validarEmail();

            if (empty($alertas)){
                // consultar si el email existe como usuario
                $usuario = Usuario::where('email', $auth->email);

                if ($usuario && $usuario->confirmado ==="1"){
                    // usuario existe y está confirmado
                    // generamos token
                    $usuario->crearToken();
                    $usuario->guardar();

                    //enviar el email con el token
                    $email = new Email ($usuario->email, $usuario->nombre, $usuario->token, );
                    $email->enviarInstrucciones();

                    // alerta de éxito
                    Usuario::setAlerta('exito', 'Revisa tu email');



                }else{
                    // usuario no existe o no está confirmado
                    $alerta=[];
                    Usuario::setAlerta('error', 'El usuario no existe o no está confirmado');
                   
                }
            }else{

            }

        }
        $alertas=Usuario::getAlertas();
        $router->render('/auth/olvide-password', [
            'alertas'=>$alertas
        ]);
        
    }
    public static function recuperar(Router $router) {
        
        $alertas=[];
        $error=false;

        $token = s($_GET['token']);

        // buscar usuario por su token
        $usuario = Usuario::where('token', $token);

        if  (empty($usuario)){
            Usuario::setAlerta('error', 'Token no válido');
            $error=true;
        }

        if ($_SERVER['REQUEST_METHOD']==='POST'){
            // Leer el nuevo password y guardarlo en la BD

            $password= new Usuario($_POST);

            $alertas = $password->validarPassword();

            if (empty($alertas)){
                $usuario->password = null; // borro el viejo password del objeto usuario que vino de la bd

                $usuario->password = $password->password;   // reemplazo en el objeto usuario, la nueva password
                $usuario->hashPassword();  //hasheamos el password nuevo
                $usuario->token = null;   //null al token

                $resultado = $usuario->guardar();   // guardamos el objeto en la bd
                if ($resultado) {   // si hay resultado  redireccionamos al home
                    header('location: /');       

                }
            }

        }

        $alertas=Usuario::getAlertas();
        $router->render('auth/recuperar-password', [
            'alertas' => $alertas,
            'error' => $error
        ]);
    }
    public static function crear(Router $router) {
        $usuario = new Usuario($_POST);

        $alertas = [];
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $usuario->sincronizar($_POST);

            $alertas = $usuario->validarNuevaCuenta();

            // revisar que no haya alertas
            if (empty($alertas)){
                // revisar que el usuario no esté registrado

                $resultado = $usuario->existeUsuario();

                if ($resultado->num_rows){
                    // usuario ya registrado
                    $alertas = Usuario::getAlertas();
                }else{
                    // hashear el password
                   $usuario->hashPassword();

                   // Generar un token

                   $usuario->crearToken();

                   // enviar el email de confirmación
             
                   $email = new Email($usuario->email, $usuario->nombre, $usuario->token);

                   $email->enviarConfirmacion();
                   
                    // usuario no registrado, entonces hay que registrarlo.
                    //debuguear($usuario);
                   $resultado = $usuario->guardar();

                   if ($resultado) {
                        header('Location: /mensaje');
                   }
                   //debuguear($usuario);
                }
            }
            
        }
        
        $router->render('/auth/crear-cuenta',[
            'usuario' => $usuario,
            'alertas' => $alertas
        ]);


    }

    public static function mensaje(Router $router){
        $router->render('auth/mensaje', [
        
        ]);

    }

    public static function confirmar(Router $router){
        $alertas=[];

        $token = s($_GET['token']);

        $usuario = Usuario::where('token', $token);

        if (empty($usuario)){
            // mostrar mensaje de error. Token no es el mismo
            Usuario::setAlerta('error', 'Token no válido. Usuario no confirmado.');
        }else{

            // Usuario Confirmado
            // actualizar en BD
            $usuario->confirmado = "1";
            $usuario->token = null;
            $usuario->guardar();
            // mostrar mensaje de usuario confirmado
            Usuario::setAlerta('exito', 'Usuario confirmado. BIENVENIDO');

        }

        $alertas = Usuario::getAlertas();
        

        $router->render('auth/confirmar-cuenta', [
            'alertas' => $alertas
        ]);

    }


}