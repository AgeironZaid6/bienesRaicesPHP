<?php

require '../../includes/funciones.php';
$auth = estaAutenticado();

if (!$auth) {
    header('Location: /login');
}

//valida la url con id valido
$id = $_GET['id'];
$id = filter_var($id, FILTER_VALIDATE_INT);

if (!$id) {
    header("Location: /admin");
}

require '../../includes/config/database.php';
$con = conectarBD();


$consulta2 = "SELECT * FROM propiedades WHERE id = $id";
$resultado2 = mysqli_query($con, $consulta2);
$propiedad = mysqli_fetch_assoc($resultado2);


//consulta para los vendedores
$consulta = "SELECT * FROM vendedores";
$resultado_S = mysqli_query($con, $consulta);

//arreglo de errores
$errores = [];

$titulo = $propiedad['titulo'];
$precio = $propiedad['precio'];
$descripcion = $propiedad['descripcion'];
$habitaciones = $propiedad['habitaciones'];
$wc = $propiedad['wc'];
$estacionamiento = $propiedad['estacionamiento'];
$vendedorId = $propiedad['vendedorId'];
$imagenPropiedad = $propiedad['imagen'];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $titulo = mysqli_real_escape_string($con, $_POST["titulo"]);
    $precio = mysqli_real_escape_string($con, $_POST["precio"]);
    $descripcion = mysqli_real_escape_string($con, $_POST["descripcion"]);
    $habitaciones = mysqli_real_escape_string($con, $_POST["habitaciones"]);
    $wc = mysqli_real_escape_string($con, $_POST["wc"]);
    $estacionamiento = mysqli_real_escape_string($con, $_POST["estacionamiento"]);
    $vendedorId = mysqli_real_escape_string($con, $_POST["vendedor"]);
    $creado = date("Y/m/d");
    //se asigna FILES  a una variable
    $imagen = $_FILES['imagen'];

    if (!$titulo) {
        $errores[] = "Debes añadir un título";
    }
    if (!$precio) {
        $errores[] = "Precio es obligatorio";
    }
    if (strlen($descripcion) < 50) {
        $errores[] = "La descripcion es obligatoria debe contener al menos 50 caracteres";
    }
    if (!$habitaciones) {
        $errores[] = "El numero de habitaciones es obligatorio";
    }
    if (!$wc) {
        $errores[] = "El numero de baños es obligatorio";
    }
    if (!$estacionamiento) {
        $errores[] = "El numero de lugares de estacionamiento es obligatorio";
    }
    if (!$vendedorId) {
        $errores[] = "Elige a un vendedor";
    }
    /*if (!$imagen['name'] || $imagen['error']) {
        $errores[] = 'La imagen es obligatoria';
    }*/
    //validar por tamaño de imagen (1mb maximo)
    $medida = 1000 * 1000;
    if ($imagen['size'] > $medida) {
        $errores[] = 'La imagen es muy pesada';
    }

    /*echo "<pre>";
    var_dump($errores);
    echo "</pre>";*/

    /*echo "<pre>";
    var_dump($imagen);
    echo "</pre>";*/

    // revisar que el arreglo de errores sea vacio
    if (empty($errores)) {
        //subida de archivos

        $carpetaImagenes = '../../imagenes/'; //crear carpeta

        if (!is_dir($carpetaImagenes)) {
            mkdir($carpetaImagenes);
        }

        $nombreImagen = '';

        if ($imagen['name']) {
            //eliminar la imagen previa
            unlink($carpetaImagenes . $propiedad['imagen']);

            //generar un nombre unico
            $nombreImagen = md5(uniqid(rand(), true)) . ".jpg";

            //subir la imagen
            move_uploaded_file($imagen['tmp_name'], $carpetaImagenes . $nombreImagen);
        } else {
            $nombreImagen = $propiedad['imagen'];
        }

        //actualizar datos en la base de datos
        $sql = "UPDATE propiedades SET titulo = '$titulo', precio = '$precio', imagen = '$nombreImagen', descripcion = '$descripcion', habitaciones = $habitaciones,
                wc = $wc, estacionamiento = $estacionamiento, vendedorId = $vendedorId WHERE id = $id";

        //echo $sql;

        $resultado = mysqli_query($con, $sql);

        if ($resultado) {
            //redireccionar al usuario para no saturar la base de datos
            //o que esten presionando el boton repetidas veces
            header('Location: /admin?resultado=2');
        }
    }
}
incluirTemplate('header');
?>

<main class="contenedor seccion">
    <h1>Actualizar Propiedad</h1>

    <a href="/admin" class="boton boton-verde">Volver</a>

    <?php foreach ($errores as $error) {
        echo "<p class='alerta error'>$error</p>";
    } ?>

    <form method="POST" class="formulario" enctype="multipart/form-data">
        <fieldset>
            <legend>Información General</legend>

            <label for="titulo">Título:</label>
            <input type="text" id="titulo" name="titulo" placeholder="Título Propiedad" value="<?php echo $titulo; ?>">

            <label for="precio">Precio:</label>
            <input type="number" id="precio" name="precio" placeholder="Precio Propiedad"
                value="<?php echo $precio; ?>">

            <label for="imagen">Imagen:</label>
            <input type="file" id="imagen" accept="image/jpeg, image/png" name="imagen">

            <img src="/imagenes/<?php echo $imagenPropiedad; ?>" alt="imagen de casa" class="imagen-small">

            <label for="descripcion">Descripción:</label>
            <textarea name="descripcion" id="descripcion"><?php echo $descripcion; ?></textarea>
        </fieldset>

        <fieldset>
            <legend>Información Propiedad</legend>

            <label for="habitaciones">Habitaciones:</label>
            <input type="number" id="habitaciones" name="habitaciones" placeholder="Ej: 3" min="1" max="9"
                value="<?php echo $habitaciones; ?>">

            <label for="wc">Baños:</label>
            <input type="number" id="wc" name="wc" placeholder="Ej: 3" min="1" max="3" value="<?php echo $wc; ?>">

            <label for="estacionamiento">Estacionamiento:</label>
            <input type="number" id="estacionamiento" name="estacionamiento" placeholder="Ej: 3" min="1" max="3"
                value="<?php echo $estacionamiento; ?>">
        </fieldset>

        <fieldset>
            <legend>Vendedor</legend>

            <select name="vendedor" id="vendedor">
                <option value="">-- Seleccione --</option>
                <?php while ($vendedor = mysqli_fetch_assoc($resultado_S)): ?>
                    <option <?php echo $vendedorId === $vendedor['id'] ? 'selected' : ''; ?>
                        value="<?php echo $vendedor['id']; ?>">
                        <?php echo $vendedor['nombre'] . " " . $vendedor['apellido']; ?>
                    </option>
                <?php endwhile; ?>
            </select>
        </fieldset>

        <input type="submit" value="Actualizar Propiedad" class="boton boton-verde">
    </form>
</main>

<?php
incluirTemplate('footer');
?>