<?php
declare(strict_types=1);
 
//BLOQUE 1
// Muestra todos los errores de PHP en pantalla durante el desarrollo.
ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);
 
// Array asociativo multidimensional con el nombre y precio de cada taller.
$catalogoTalleres = [
    'videojuegos' => ['nombre' => 'Torneo de Videojuegos', 'precio' => 10],
    'cosplay'     => ['nombre' => 'Taller de Cosplay',     'precio' => 15],
    'rol'         => ['nombre' => 'Zona de Rol en Vivo',   'precio' => 5],
];
 
// Array indexado con los tipos de entrada permitidos.
$tiposEntrada = ['General', 'VIP', 'Infantil'];
 
// Variables globales de resultado que se usarán en el HTML.
$exito = false;
$mensajeError = '';
$resguardo = [];
 
// Variables para mantener los datos del formulario tras enviarlo.
$nombreForm = '';
$edadForm = '';
$tipoForm = 'General';
$talleresForm = [];
?>