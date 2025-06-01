<?php
include "controller.php";  

$action = $_GET['action'] ?? "main";  

$controller = new Controller();

if (method_exists($controller, $action)) {
    $phtml = $controller->$action();  // Si hay accion, llama al metodo correspondiente
} else {
    $phtml = $controller->showMethodError();  // Manejo de errores
}

echo $phtml;  // Imprime el contenido generado por el controlador
?>