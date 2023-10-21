<h1 class="nombre-pagina">Recuperar Password</h1>

<p class="descripcion-pagina">Coloca tu nueva password a continuación</p>
<?php 
    include_once __DIR__ . '/../templates/alertas.php';    // incluye las alertas 
?>
<?php if ($error) return ;   // si hay error entonces no muestro el formulario. simplemente retorna?>  
<form class="formulario" method="POST"> <!-- no le pongo action porque sino pierdo el token del get -->
    <div class="campo">
        <label for="password">Password</label>
        <input 
            type="password"
            id="password"
            name="password"
            placeholder="Tu Nuevo Password"
        />
    </div>

    <input type="submit" class="boton" value="Guardar Nuevo Password">

</form> 

<div class="acciones">
    <a href="/">¿Ya tienes cuenta? Iniciar sesión</a>
    <a href="/crear-cuenta">¿Aún no tienes cuenta?</a>

</div>