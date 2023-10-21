<h1 clase="nombre-pagina">Actualizar Servicios</h1>
<p class="descripcion-pagina">Modifica los valores del servicio en el formulario</p>

<?php

    include_once __DIR__ . '/../templates/barra.php';
    include_once __DIR__ . '/../templates/alertas.php';

?>

<form clsss="formulario"  method="POST">
    <?php 
    include_once __DIR__ . "/formulario.php";
    ?>
    <input class="boton" type="submit" value="Atualizar">
</form>