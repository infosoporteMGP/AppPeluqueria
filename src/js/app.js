let paso =1;  // el paso que tendrà por defecto al cargar la pàgina.
let pasoInicial=1;
let pasoFinal=3;
// recordar que en JS los objetos si se declaran como const se pueden modificar igual.
const cita={
    id:'',
    nombre:'',
    fecha:'',
    hora:'',
    servicios:[]
};

document.addEventListener('DOMContentLoaded', function(){
      
    iniciarApp();
});

function iniciarApp(){
    mostrarSeccion();  // Muestra y oculta las secciones
    tabs();  // Cambia la sección cuando se presionen los tabs
    botonesPaginador();  // Agrega o quita los botones del paginador
    paginaSiguiente();
    paginaAnterior();
    consultarAPI(); // consulta la API en el backend de PHP
    idCliente();// añade el id del cliente al objeto de cita
    nombreCliente(); // añade el nombre del cliente al objeto de cita
    seleccionarFecha(); // añade la fecha de la cita en el objeto
    seleccionarHora();  // añade la hora de la cita en el objeto

    mostrarResumen();  //muestra el resumen de la cita
}

function mostrarSeccion(){
    // ocultar la seccion que tenga la clase de mostrar
    const seccionAnterior = document.querySelector('.mostrar');
        if (seccionAnterior){
            seccionAnterior.classList.remove('mostrar');
        }

    // Seleccionar la sección con el paso para mostrar la seccion donde se dio click
    const pasoSelector = `#paso-${paso}`;
    const seccion = document.querySelector(pasoSelector);  // selecciona el selector id=paso-x del index.php
    seccion.classList.add('mostrar'); // agrega class="mostrar"
    
    // quita la clase del tab actual al tab anterior
    const tabAnterior = document.querySelector('.actual');
    if (tabAnterior){
        tabAnterior.classList.remove('actual');
    }

    // resalta el tab actual
    const tab = document.querySelector(`[data-paso="${paso}"]`);  // son corchetes porque es un selector de atributo
    tab.classList.add('actual');
}


function tabs(){
    const botones = document.querySelectorAll('.tabs button');

    // como el nodeList es un array, debemos iterarlo.

    botones.forEach(boton=>{
        
        boton.addEventListener('click', function (e){
            //console.log(parseInt(e.target.dataset.paso));  // éste paso es el parámetro de usuario data-paso=x y se debe convertir a entero

            paso= parseInt(e.target.dataset.paso);
           
            mostrarSeccion();
            botonesPaginador();


            
        });
    })
}

function botonesPaginador(){
    const paginaSiguiente=document.querySelector('#siguiente'); 
    const paginaAnterior=document.querySelector('#anterior');

    if (paso===1){
        paginaAnterior.classList.add('ocultar');
        paginaSiguiente.classList.remove('ocultar');
    }else if (paso===3){
        paginaAnterior.classList.remove('ocultar');
        paginaSiguiente.classList.add('ocultar');
        mostrarResumen();   // si es paso 3 entonces debo recargar el resumen
    }else{
        paginaAnterior.classList.remove('ocultar'); 
        paginaSiguiente.classList.remove('ocultar');
    }

    mostrarSeccion();

}

function paginaAnterior(){
    const paginaAnterior=document.querySelector('#anterior');
    paginaAnterior.addEventListener('click', function(){
        if (paso <= pasoInicial) return;    
        paso--     
        botonesPaginador();
    })

}

function paginaSiguiente(){
    const paginaSiguiente=document.querySelector('#siguiente');
    paginaSiguiente.addEventListener('click', function(){
        if (paso >= pasoFinal) return;    
        paso++
        botonesPaginador();
    })

}

// las funciones asincronas permiten que arranque la funcion y que pueda seguir la ejecucion de otras funciones también al mismo tiempo
// el await funciona dentrpde una funcion async y sirve para detener la ejecución hasta que termine esa instrucción
async function consultarAPI(){

    try{
        //const url = '/api/servicios';
        const url = `${location.origin}/api/servicios`;

        const resultado = await fetch(url);
        const servicios = await resultado.json();
        mostrarServicios(servicios);

    } catch (error){
        console.log(error);
    }
}

function mostrarServicios(servicios){
    // Servicios va a ser un array debo iterarlo
    servicios.forEach(servicio=>{
        const {id, nombre, precio} = servicio;   // pongo cada valor del array en una variable
        
        // mostraremos la info con scripting. Es más laborioso, pero es más seguro.

        const nombreServicio = document.createElement('P');  // creo un elemento párrafo
        nombreServicio.classList.add('nombre-servicio');    // creo una clase en el párrafo
        nombreServicio.textContent=nombre;                  // pongo el contenido del párrafo

        const precioServicio = document.createElement('P');  // creo un elemento párrafo
        precioServicio.classList.add('precio-servicio');    // creo una clase en el párrafo
        precioServicio.textContent=`$${precio}`;                  // pongo el contenido del párrafo


        const servicioDiv = document.createElement('DIV');  // creo un contenedor
        servicioDiv.classList.add('servicio');              // creo una clase al contenedor
        servicioDiv.dataset.idServicio = id;                   // crea un atributo personalizado que tendrá el valor del id del servicio

        // linea que controla el evento click en el botón de servicios pero debo hacerlo con un callback
        servicioDiv.onclick = function() {
            seleccionarServicio(servicio);
        }

        servicioDiv.appendChild(nombreServicio);            // le agrego los párrafos dentro del contenedor
        servicioDiv.appendChild(precioServicio);

        // selecciono el id="servicios" del views/cita/index.php que es un DIV y le meto el div con los párrafos dentro.

        document.querySelector('#servicios').appendChild(servicioDiv);     
    });


}

function seleccionarServicio(servicio){
    const {id} = servicio;     // extraigo el id dentro de servicio con destructuring(o como se diga....)
    const {servicios} = cita;    // extraigo el array servicios dentro del objeto cita
    
    // identificar el elemento al que se da click
    const divServicio = document.querySelector(`[data-id-servicio="${id}"]`);

    // comprobar si un servicio ya fue agregado
    // some es un array method que itera y devuelve true o false. 
    // agregado.id es el iterado de lo que ya está, id es el que estoy queriendo incorporar. Si existe, entonces tengo que deshabilitar.
    if( servicios.some(agregado => agregado.id === servicio.id)) {         // id sería servicio.id, pero ya lo extraje antes    
        // debo deseleccionarlo
        // filter es otro array method que permite sacar un elemento del array cuando se da la condicion
        cita.servicios = servicios.filter( agregado => agregado.id !== id);
     // marcamos los servicios como no seleccionados

    
        divServicio.classList.remove('seleccionado');

    
    }else{
        // debo seleccionarlo
        cita.servicios = [...servicios, servicio]; // va a copiar lo que ya tiene el array srvicios y agrega otro servicio y lo mete en el objeto cita
        // marcamos los servicios como no 
        divServicio.classList.add('seleccionado');

    }


    
}

function idCliente(){
    const inputIdCliente = document.querySelector('#id').value;
    cita.id = inputIdCliente;
  
}

function nombreCliente(){
    const inputNombre = document.querySelector('#nombre').value;
    cita.nombre = inputNombre;
  
}

function seleccionarFecha(){
    const inputFecha = document.querySelector('#fecha');
    inputFecha.addEventListener('input', function(e) {
        const dia = new Date(e.target.value).getUTCDay();   // dia tomará el valor del día de la semana. Domingo es 0 y sabado es 6
        if ([6,0].includes(dia)){              // si el día ingresado está incluído en el array [6, 0]
            e.target.value = '';       // borro la entrada en el input porque no es válida
            cita.fecha='';
            mostrarAlerta('Fines de Semanas no permitidos', 'error','.formulario');
        }else{
            cita.fecha=e.target.value ;     // si es válida entonce cargo la fecha en la cita

        }
        

    });
}

function seleccionarHora(){
    const inputHora = document.querySelector('#hora');
    inputHora.addEventListener('input', function (e) {
        const horaCita= e.target.value;
         // split separa elementos en un array separados por : . Entonces queda la hora y los minutos por separado. entonces del array toma el elemento 0 para la hora
        const hora = horaCita.split(":")[0];  
        
        if (hora < 10  || hora >18){
            // horas no válidas
            e.target.value='';  // pongo en vacío el campo hora
            cita.hora='';
            mostrarAlerta('Hora no válida', 'error','.formulario',true);
            
        }else{
            // horas válidas
            cita.hora = e.target.value;     // asigno el valor de hora válida al objeto cita
           
        }
        
    });
}

// funcion para mostrar una alerta pero desde javaScript
function mostrarAlerta(mensaje, tipo, elemento, desaparece = true){
    // previene que se genere más de una alerta
    const alertaPrevia = document.querySelector('.alerta');    // selecciono la clase alerta. 
    if (alertaPrevia) { // si existe alerta, entonces no ejecute la funcion, (porque ya esá la alerta en la pantalla)
        alertaPrevia.remove();          // remueve la alerta
    }
    // scripting para crear la alerta
    const alerta = document.createElement('DIV');
    alerta.textContent = mensaje;
    alerta.classList.add ( 'alerta');
    alerta.classList.add (tipo);

    // selecciono el formulario para mostrar el mensaje
    const referencia = document.querySelector(elemento);
    referencia.appendChild(alerta);  // ésto va a agregar el div como hijo dentro del formulario donde está la clase formulario

    // eliminar la alerta
    if (desaparece){
        setTimeout (function (){
            alerta.remove();        // removerá el div de la alerta luego de 3 segundos de aparecer
        }, 3000);      
    }                
}

function mostrarResumen(){
    const resumen = document.querySelector('.contenedor-resumen');
    // limpiar el contenido del resumen
    while(resumen.firstChild){
        resumen.removeChild(resumen.firstChild);
    }

    // objet.values lo que hace es iterar en el objeto y .includes busca por , en éste caso , por vacío ''
    // cita.servicios.length me permite ver si en el objeto tenemos servicios cargados
    if ( Object.values(cita).includes('') || cita.servicios.length === 0){
        
        mostrarAlerta('Faltan Cargar Datos o Servicios', 'error','.contenedor-resumen',false);
        return;
    }


    // cuando todo está bien
    // formatear el div de resumen
    const {nombre, fecha, hora, servicios} = cita;             //descompongo cada dato de cita
    
    // heading para servicios en resumen
    const headingServicios = document.createElement('H3');
    headingServicios.textContent = 'Resumen de Servicios';
    resumen.appendChild(headingServicios);


    // iterando y mostrando los servicios
    servicios.forEach(servicio =>{
        const {id, precio, nombre} = servicio;
        const contenedorServicio = document.createElement('DIV');
        contenedorServicio.classList.add('contenedor-servicio');

        const textoServicio = document.createElement('P');
        textoServicio.textContent = nombre;

        const precioServicio = document.createElement('P');
        precioServicio.innerHTML = `<span>Precio:</span> $${precio}`;

        contenedorServicio.appendChild(textoServicio);
        contenedorServicio.appendChild(precioServicio);

        resumen.appendChild(contenedorServicio);
    })
    // heading para cita en resumen

    // Explicacion... con TextConten modifico el texto, pero con innerhtml puedo modificar texto incluyendo etiquetas html

    const headingCita = document.createElement('H3');
    headingCita.textContent = 'Resumen de Cita';
    resumen.appendChild(headingCita);

    // formatear la fecha en español  ("muy importante")

    const fechaObj = new Date(fecha);  // es string de fecha lo transforma a objeto de fecha
    const mes = fechaObj.getMonth();    // extraigo el mes
    const dia = fechaObj.getDate() + 2; // extraigo el día y le sumo 2 días porque cada vez que se aplica Date() resta 1 día y se     aplica 2 veces una antes y la otra unas líneas más abajo...
    const year = fechaObj.getFullYear(); // extraigo el año

    const fechaUTC = new Date(Date.UTC(year, mes, dia));

    const opciones = {weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' };
    // const fechaFormateada = fechaUTC.toLocaleDateString('es-AR');    // ésto ya me lo presenta como 29/8/2023

    const fechaFormateada = fechaUTC.toLocaleDateString('es-AR', opciones);  // esto lo muestra como   martes, 29 de agosto de 2023
    

    const nombreCliente = document.createElement('P');
    nombreCliente.innerHTML = `<span>Nombre:</span> ${nombre}`;

    const fechaCita = document.createElement('P');
    fechaCita.innerHTML = `<span>Fecha:</span> ${fechaFormateada}`;

    const horaCita = document.createElement('P');
    horaCita.innerHTML = `<span>Hora:</span> ${hora} Horas`;

    // Botón para crear la cita
    const botonReservar = document.createElement('BUTTON');
    botonReservar.classList.add('boton');
    botonReservar.textContent  = 'Reservar Cita';

     // la función reservarCita no debe llevar paréntesis porque se manda a llamar la función y debe esperar a que se dé click para que se ejecute la misma. si lleva parámetros y debe llevar paréntesis, entonces hacerlo con callback .

     // es decir :      botonReservar.onclick = function(){
     //                     reservarcita(parametro1, parametro2,......)   ;
    //                  }
    // DEFINICION
    //Los callbacks aseguran que una función no se va a ejecutar antes de que se complete una tarea, sino que se ejecutará justo después de que la tarea se haya completado. Nos ayuda a desarrollar código JavaScript asíncrono y nos mantiene a salvo de problemas y errores.
                    
    botonReservar.onclick = reservarCita;  

    resumen.appendChild(nombreCliente);
    resumen.appendChild(fechaCita);
    resumen.appendChild(horaCita);

    resumen.appendChild(botonReservar);


}

async function reservarCita()  {
    // comienza aqui cosas que tienen que ver con FETCH API  ( la evolución de AJAX )
    const {nombre, fecha, hora, servicios, id} = cita;     // desgloso el objeto cita
    // recordar que .foreach sólo itera, y .map también itera y las coincidencias las va colocando en la variable idServicio
    const idServicios = servicios.map(servicio => servicio.id);
    //console.log(idServicios);
    //console.log(servicios);
    //return;
    const datos = new FormData();   // OBJETO PARA ENVIAR DATOS AL SERVIDOR. INCLUSO ARCHIVOS. es como el submit de un formulario
    //datos.append ( 'nombre', nombre);
    datos.append ( 'fecha', fecha);
    datos.append ( 'hora', hora);
    datos.append ( 'usuarioId', id);
    datos.append ('servicios', idServicios)

    //console.log([...datos]);
    //return
    
    // para ver un formdata no alcanza con console.log(datos);  aparecerá vacío. 
    // si se puede ver si se toma una copia y se lo adiciona.... console.log([...datos]);

    // para visualizar el json generado usar thunder client con server mapeado en 127.0.0.1:3000

    //  Petición hacia la api

    try {
        const  url = `${location.origin}/api/citas`;

        const respuesta = await fetch(url, {          //esto envìa el post con el contenido del formData llamado datos
            method: 'POST',
            body: datos
        });
    
        const resultado = await respuesta.json();
        
        
    
        if (resultado.resultado){    // si el resultado es true es porque se grabó correctamente el registro en la bd
            Swal.fire({      // ventana de sweetAlert
                icon: 'success',    //success, error, information,warning, etc
                title: 'Cita Creada',
                text: 'Tu cita fue creada correctamente !!!'
                
                
                //footer: '<a href="">Why do I have this issue?</a>'     //para poner un footer con en este caso un link
              }).then( () =>{
                setTimeout(() =>{
                window.location.reload();         //para que luego que tire la ventana modal, al cerrarla, recarga la página
                },3000);
              })
    
        }
            
    } catch (error) {
        Swal.fire({      // ventana de sweetAlert
            icon: 'warning',    //success, error, information,warning, etc
            title: 'Error',
            text: 'Error al cargar la cita'
          
        })
        console.log(error);

        
    }

} 
