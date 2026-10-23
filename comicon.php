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

//BLOQUE 1 (try/catch/finally)
    // Bloque que agrupa todo el procesamiento para controlar los errores.
    try {
        //BLOQUE 3 (saneamiento, validación y cálculos)
        // Recupera los datos con el operador ?? para evitar avisos si faltan.
        $nombreBruto = $_POST['nombre']   ?? '';
        $edadBruta   = $_POST['edad']     ?? '';
        $tipo        = $_POST['tipo']     ?? '';
        $talleresSel = $_POST['talleres'] ?? [];
 
        // Sanea el nombre para evitar inyección de código (XSS).
        $nombre = htmlspecialchars(trim((string) $nombreBruto), ENT_QUOTES, 'UTF-8');
 
        // Guarda los valores para repintar el formulario.
        $nombreForm   = $nombre;
        $edadForm     = htmlspecialchars((string) $edadBruta, ENT_QUOTES, 'UTF-8');
        $tipoForm     = htmlspecialchars((string) $tipo, ENT_QUOTES, 'UTF-8');
        $talleresForm = is_array($talleresSel) ? $talleresSel : [];
 
        // Condición que lanza una excepción si el nombre está vacío.
        if ($nombre === '') {
            throw new Exception('El nombre completo es obligatorio.');
        }
 
        // Valida que la edad sea un entero positivo.
        $edad = filter_var($edadBruta, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
        if ($edad === false) {
            throw new Exception('La edad debe ser un número entero positivo.');
        }
 
        // Condición que comprueba que el tipo de entrada sea uno de los permitidos.
        if (!in_array($tipo, $tiposEntrada, true)) {
            throw new Exception('Tipo de entrada no válido.');
        }
 
        // Condición que comprueba que los talleres lleguen como array.
        if (!is_array($talleresSel)) {
            throw new Exception('Formato de talleres no válido.');
        }
 
        // Condición que impide la entrada VIP a menores de 18 años.
        if ($edad < 18 && $tipo === 'VIP') {
            throw new Exception('La entrada VIP está restringida a mayores de edad');
        }
 
        // Calcula el precio total y obtiene los datos del resguardo.
        $resguardo = procesarInscripcion($nombre, $tipo, $talleresSel);
        $exito = true;
 
    //BLOQUE 1 (catch)
    // Captura la excepción y guarda su mensaje para mostrarlo al usuario.
    } catch (Exception $e) {
        $mensajeError = $e->getMessage();
 
    //BLOQUE 1 (finally)
    // Se ejecuta siempre: registra en el log del servidor la fecha y el resultado.
    } finally {
        $resultado = $exito ? 'EXITOSO' : 'FALLIDO';
        error_log('[' . date('Y-m-d H:i:s') . '] Inscripción Comic-Con procesada. Resultado: ' . $resultado);
    }
}
?>