<?php

function conectarBD(): mysqli
{
    $db = mysqli_connect('127.0.0.1', 'root', '', 'bienesraices_crud'); //para laptop victus 127.0.0.1:3307, localhost

    if (!$db) {
        echo "Error al Conectar a la Base de Datos!";
        exit();
    }
    return $db;
}