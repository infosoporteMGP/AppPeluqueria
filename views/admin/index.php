<h1 class="nombre-pagina">Panel de Administración</h1>


<?php  
    include_once __DIR__ . '/../templates/barra.php'
?>
<h2>Buscar Citas</h2>
<div class="busqueda">
    <form class="formulario">
        <div class="campo">
            <label for="fecha">Fecha</label>
            <input type="date"
                    id="fecha"
                    name="fecha"
                    value="<?php echo $fecha; ?>"
            />
        </div>
    </form>

</div>

<?php
    if (count($citas)===0){
        echo "<h2>No hay Citas en ésta fecha</h2>";
    }

?>

<div id="citas-admin">
    <ul class="citas">
        <?php
        $idCita=0;
        
            foreach($citas as $key => $cita) {  // key me va a traer el elemento dentro del array 
               
                if ($idCita !== $cita->id){
                    $total=0;
                
        ?>
                    <li>
                        <p>ID: <span><?php  echo $cita->id;?></span></p>
                        <p>Hora: <span><?php  echo $cita->hora;?></span></p>
                        <p>Cliente: <span><?php  echo $cita->cliente;?></span></p>
                        <p>Email: <span><?php  echo $cita->email;?></span></p>
                        <p>Teléfono: <span><?php  echo $cita->telefono;?></span></p>

                        <h3>Servicios</h3>

                    
        <?php       
                        $idCita = $cita->id;
                } // fin del IF
                $total += $cita->precio;
        ?>
                        <p class="servicio"><?php echo $cita->servicio . "  $" . $cita->precio; ?></p>
                    <!-- </li>    lo saco la /li para que html lo acomode sólo porque lo muestra desplazado-->
        <?php
                // ésto es para detectar el último servicio de la cita.
                $actual = $cita->id;    // id de la cita actual
                $proximo = $citas[$key + 1] ->id ?? 0;  // id de la próxima cita a la actual
                if (esUltimo($actual, $proximo)){
        ?>
                <p class="total">Total: <span>$ <?php echo $total  ;?></span></p>
                <form action="/api/eliminar" method="POST">
                    <input type="hidden" name="id" value="<?php echo $cita->id ;?>">
                    <input type="submit" class="boton-eliminar" value="Eliminar">
                </form>
        <?php

                }
            }   // fin del foreach
        ?>
                
        
        
    </ul>
    

</div>

<?php
    $script="<script src='build/js/buscador.js'></script>"

?>