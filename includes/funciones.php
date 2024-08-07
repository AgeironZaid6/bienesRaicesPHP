<?php

require 'app.php';

function incluirTemplate(string $nombre, bool $inicio = false)
{
    include TEMPLATES_URL . "/$nombre.php";
}

function estaAutenticado(): bool
{
    session_start();

    $auth = $_SESSION['login'];
    if ($auth) {
        return true;
    }
    return false;
}

function nivelUsuario(): int
{
    $nivel = $_SESSION['nivel'];
    if ($nivel < 2) {
        return 1;
    }
    return 2;
}