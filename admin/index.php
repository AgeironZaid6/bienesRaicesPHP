<?php

require '../includes/funciones.php';
$auth = estaAutenticado();

if (!$auth) {
    header('Location: /login');
}

//importar conexion
require '../includes/config/database.php';
$db = conectarBD();

//escribir query
$query = "SELECT id, titulo, precio, imagen FROM propiedades";

//consultar la bd
$resultado = mysqli_query($db, $query);

//muestra mensaje condicional
$resul = $_GET['resultado'] ?? null;

//procesar los datos del formulario para eliminar una propiedad
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id = $_POST['id'];
    $id = filter_var($id, FILTER_VALIDATE_INT);

    if ($id) {
        //elimina el archivo img de la propiedad
        $query2 = "SELECT imagen FROM propiedades WHERE id = $id";
        $resultado2 = mysqli_query($db, $query2);
        $propiedad = mysqli_fetch_assoc($resultado2);

        unlink('../imagenes' . $propiedad['imagen']);

        /*echo "<pre>";
        echo var_dump($propiedad);
        echo "</pre>";*/

        //eliminar la propiedad de la bd
        $query = "DELETE FROM propiedades WHERE id = $id";
        $resultado = mysqli_query($db, $query);

        if ($resultado) {
            header('Location: /admin?resultado=3');
        }
    }
}


//incluye un template
incluirTemplate('header');
?>

<main class="contenedor seccion">
    <h1>Administrador de Bienes Raices</h1>

    <?php if (intval($resul) === 1): ?>
        <p class="alerta exito">Anuncio Creado Correctamente!</p>
    <?php elseif (intval($resul) === 2): ?>
        <p class="alerta exito">Anuncio Actualizado Correctamente!</p>
    <?php elseif (intval($resul) === 3): ?>
        <p class="alerta exito">Anuncio Eliminado Correctamente!</p>
    <?php endif; ?>

    <a href="/admin/propiedades/crear.php" class="boton boton-verde">Nueva Propiedad</a>

    <table class="propiedades">
        <thead>
            <tr>
                <th>ID</th>
                <th>Titulo</th>
                <th>Precio</th>
                <th>Imagen</th>
                <th>Acciones</th>
            </tr>
        </thead>

        <tbody> <!-- Mostrar los resultados -->
            <?php while ($propiedad = mysqli_fetch_assoc($resultado)): ?>
                <tr>
                    <td><?php echo $propiedad['id']; ?></td>
                    <td><?php echo $propiedad['titulo']; ?></td>
                    <td><?php echo "$" . $propiedad['precio']; ?></td>
                    <td><img src="/imagenes/<?php echo $propiedad['imagen']; ?>" alt="<?php echo $propiedad['titulo']; ?>"
                            class="imagen-tabla"></td>
                    <td>
                        <form method="POST" class="w-100">
                            <input type="hidden" name="id" value="<?php echo $propiedad['id']; ?>">
                            <input type="submit" class="boton-rojo-block" value="Eliminar">
                        </form>
                        <a href="/admin/propiedades/actualizar.php?id=<?php echo $propiedad['id'] ?>"
                            class="boton-amarillo-block">Actualizar</a>
                    </td>
                </tr>
            <?php endwhile; ?>
            </tr>
        </tbody>
    </table>

</main>

<?php
//cerrar la conexion
mysqli_close($db);

incluirTemplate('footer');
?>