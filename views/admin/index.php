<h1 class="nombre-pagina">Panel de Administracion</h1>

<?php include_once __DIR__ . '/../templates/barra.php' ?>

<h2>Buscar Citas</h2>

<div class="busqueda">

    <form action="" class="formulario">

        <div class="campo">

            <label for="fecha">Fecha</label>
    
            <input type="date" id="fecha" name="fecha" value="<?php echo $fecha; ?>">

        </div>

    </form>

</div>

    <?php

        if(count($citas) === 0) {
            echo "<h2>No Hay Citas en Esta Fecha</h2>";
        }

    ?>

<div id="citas-admin">

    <ul class="citas">

        <!--Definicio de variable-->
        <?php $idCita = 0; ?>

        <!--Iterando sobre las citas-->
        <?php foreach($citas as $key => $cita): ?>

            <!--Comprobando que la variable $idCita no sea igual al id de la cita iterada-->
            <?php if($idCita !== $cita->id): ?>

                <?php $total = 0; ?>

                <li>

                    <p>ID: <span><?php echo $cita->id; ?></span></p>
                    <p>HORA: <span><?php echo $cita->hora; ?></span></p>
                    <p>Cliente: <span><?php echo $cita->cliente; ?></span></p>
                    <p>Email: <span><?php echo $cita->email; ?></span></p>
                    <p>Telefono: <span><?php echo $cita->telefono; ?></span></p>

                    <h3>Servicios</h3>

                    <!--Igualando la variable $idCita con el id de la cita para que no se vuelva a mostrar
                    la cita con el id que estemos evaluando-->
                    <?php $idCita = $cita->id; ?>   
                
            <?php endif; ?>

                    <!--Empezamos a sumar el total + el precio de los servicios del id de cita evaluado-->
                    <?php $total += $cita->precio; ?>

                    <!--Colocando la logica para mostrar los servicios fuera del if, porque los servicios
                    si los necesitamos que se mustren todos-->
                    <p class="servicio"><?php echo $cita->servicio ." " . $cita->precio; ?></p>

                <!-- </li>  eliminando el cierre del <li> para que se cierre automaticamente y todo
                    se muestre correctamente-->

                    <?php  
                    
                        //Id Actual (el que se esta evaluando)
                        $actual = $cita->id;

                        //Este es del arreglo de todas las citas, estamos tomando el id de la cita que
                        //se encuentre 1 indice arriba de el id de la cita evaluada
                        $proximo = $citas[$key + 1]->id ?? 0;

                    ?>

                    <!--Si el id actual es diferente a el proximo, quiere decir que ya terminamos
                    de recorrer todos los servicios asociados al id evaluado, por lo tanto ya podemos mostrar
                    el total del precio de los servicios asociados a el id de cita evaluado-->
                    <?php if(esUltimo($actual, $proximo)): ?>

                        <p class="total">Total: <span>$ <?php echo $total; ?></span> </p>

                        <!--Formulario para eliminar la cita-->
                        <form action="/api/eliminar" method="POST">

                            <input type="hidden" name="id" value="<?php echo $cita->id; ?>">

                            <input type="submit" class="boton-eliminar" value="Eliminar">

                        </form>

                    <?php endif; ?>

        <?php endforeach; ?>    

    </ul>

</div>

<?php  

    $script = "<script src='build/js/buscador.js'></script>"

?>
