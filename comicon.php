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

//BLOQUE 3
// Función que devuelve el precio base de la entrada según su tipo.
function calcularPrecioBase(string $tipo): int
{
    // Switch que asigna el precio según el tipo de entrada.
    switch ($tipo) {
        case 'General':
            return 30;
        case 'VIP':
            return 60;
        case 'Infantil':
            return 10;
        default:
            throw new InvalidArgumentException('Tipo de entrada no válido.');
    }
}
 
// Función que calcula el total (entrada + talleres) y devuelve los datos del resguardo.
function procesarInscripcion(string $nombre, string $tipo, array $talleresSeleccionados): array
{
    // Accede al catálogo de talleres definido fuera de la función.
    global $catalogoTalleres;
 
    $precioBase = calcularPrecioBase($tipo);
    $total = $precioBase;
    $talleresElegidos = [];
 
    // Bucle que recorre los talleres marcados y suma su precio al total.
    foreach ($talleresSeleccionados as $clave) {
        // Condición que comprueba que el taller existe en el catálogo.
        if (!isset($catalogoTalleres[$clave])) {
            throw new InvalidArgumentException('Taller no válido seleccionado.');
        }
        $taller = $catalogoTalleres[$clave];
        $talleresElegidos[] = $taller;
        $total += $taller['precio'];
    }
 
    return [
        'nombre'     => $nombre,
        'tipo'       => $tipo,
        'precioBase' => $precioBase,
        'talleres'   => $talleresElegidos,
        'total'      => $total,
    ];
}
 
$procesado = false;
 
// Condición que detecta si el formulario se ha enviado por POST.
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $procesado = true;
?>