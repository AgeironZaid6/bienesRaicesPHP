<?php
require './includes/funciones.php';
require './includes/config/database.php';

$db = conectarBD();

// Inicializar la variable de alerta
$alerta = "";

// Verificar si el formulario fue enviado
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = $_POST['nombre'];
    $email = $_POST['email'];
    $telefono = $_POST['telefono'];
    $mensaje = $_POST['mensaje'];
    $opciones = $_POST['opciones'];
    $presupuesto = $_POST['presupuesto'];
    $contacto = $_POST['contacto'];
    $fecha = $_POST['fecha'];
    $hora = $_POST['hora'];

    $query = "INSERT INTO contactos (nombre, email, telefono, mensaje, opciones, presupuesto, contacto, fecha, hora) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";

    $stmt = mysqli_prepare($db, $query);

    mysqli_stmt_bind_param($stmt, 'sssssssss', $nombre, $email, $telefono, $mensaje, $opciones, $presupuesto, $contacto, $fecha, $hora);

    $resultado = mysqli_stmt_execute($stmt);

    // Verificar el resultado
    if ($resultado) {
        $alerta = "<script>
            document.addEventListener('DOMContentLoaded', function() {
                Swal.fire({
                    icon: 'success',
                    title: 'Datos guardados correctamente!!',
                    text: 'En los proximos dias nuestro equipo se pondra en contacto contigo',
                    showConfirmButton: false,
                    timer: 2500
                });
            });
        </script>";
    } else {
        $alerta = "<script>
            document.addEventListener('DOMContentLoaded', function() {
                Swal.fire({
                    icon: 'error',
                    title: 'Error al insertar los datos',
                    text: '" . mysqli_error($db) . "',
                });
            });
        </script>";
    }

    mysqli_stmt_close($stmt);
    mysqli_close($db);
}

incluirTemplate('header');
echo $alerta;
?>

<main class="contenedor seccion">
    <h1>Contacto</h1>

    <picture>
        <source srcset="build/img/destacada3.webp" type="image/webp">
        <source srcset="build/img/destacada3.jpg" type="image/jpeg">
        <img loading="lazy" src="build/img/destacada3.jpg" alt="Imagen Contacto">
    </picture>

    <h2>Llene el formulario de Contacto</h2>

    <form class="formulario">
        <fieldset>
            <legend>Información Personal</legend>

            <label for="nombre">Nombre</label>
            <input type="text" placeholder="Tu Nombre" id="nombre">

            <label for="email">E-mail</label>
            <input type="email" placeholder="Tu Email" id="email">

            <label for="telefono">Teléfono</label>
            <input type="tel" placeholder="Tu Teléfono" id="telefono">

            <label for="mensaje">Mensaje:</label>
            <textarea id="mensaje"></textarea>
        </fieldset>

        <fieldset>
            <legend>Información sobre la propiedad</legend>

            <label for="opciones">Vende o Compra:</label>
            <select id="opciones">
                <option value="" disabled selected>-- Seleccione --</option>
                <option value="Compra">Compra</option>
                <option value="Vende">Vende</option>
            </select>

            <label for="presupuesto">Precio o Presupuesto</label>
            <input type="number" placeholder="Tu Precio o Presupuesto" id="presupuesto">

        </fieldset>

        <fieldset>
            <legend>Información sobre la propiedad</legend>

            <p>Como desea ser contactado</p>

            <div class="forma-contacto">
                <label for="contactar-telefono">Teléfono</label>
                <input name="contacto" type="radio" value="telefono" id="contactar-telefono">

                <label for="contactar-email">E-mail</label>
                <input name="contacto" type="radio" value="email" id="contactar-email">
            </div>

            <p>Si eligió teléfono, elija la fecha y la hora</p>

            <label for="fecha">Fecha:</label>
            <input type="date" id="fecha">

            <label for="hora">Hora:</label>
            <input type="time" id="hora" min="09:00" max="18:00">

        </fieldset>

        <input type="submit" value="Enviar" class="boton-verde">
    </form>
</main>

<?php
incluirTemplate('footer');
?>

<!-- SweetAlert2 CDN -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>