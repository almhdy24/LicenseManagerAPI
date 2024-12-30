<?php
require_once "../vendor/autoload.php";
require_once "../middleware.php";  // Include the middleware
require_once "../cors.php";        // Include the CORS file

use App\Database\Database;
use App\Controllers\LicenseController;
use App\Controllers\UsersController;
use FastRoute\RouteCollector;
use function FastRoute\simpleDispatcher;

header("Content-Type: application/json");

// Enable CORS for the API
enableCors();

// Authenticate all incoming requests
authenticateRequest();

// Set up the database connection
$database = new Database(__DIR__ . '/../licenses.db');
$conn = $database->getConnection();
$licenseController = new LicenseController($conn);
$usersController = new UsersController($conn);

// Create a dispatcher
$dispatcher = simpleDispatcher(function (RouteCollector $r) use ($licenseController, $usersController) {

    // License routes
    $r->addRoute('POST', '/generate', function() use ($licenseController) {
        $input = json_decode(file_get_contents('php://input'), true);
        if (isset($input['email']) && isset($input['application_id'])) {
            echo $licenseController->generate($input['email'], $input['application_id']);
        } else {
            echo json_encode(["message" => "Email and Application ID are required."]);
        }
    });

    $r->addRoute('GET', '/validate', function() use ($licenseController) {
        if (isset($_GET['code']) && isset($_GET['application_id'])) {
            echo $licenseController->validate($_GET['code'], $_GET['application_id']);
        } else {
            echo json_encode(["message" => "License code and Application ID are required."]);
        }
    });

    // User routes
    $r->addRoute('GET', '/users', function() use ($usersController) {
        echo $usersController->index();
    });

    $r->addRoute('GET', '/users/{id:\d+}', function($id) use ($usersController) {
        echo $usersController->show($id);
    });

    $r->addRoute('POST', '/users', function() use ($usersController) {
        $input = json_decode(file_get_contents('php://input'), true);
        echo $usersController->store($input);
    });

    $r->addRoute('PUT', '/users/{id:\d+}', function($id) use ($usersController) {
        $input = json_decode(file_get_contents('php://input'), true);
        echo $usersController->update($id, $input);
    });

    $r->addRoute('DELETE', '/users/{id:\d+}', function($id) use ($usersController) {
        echo $usersController->destroy($id);
    });
});

// Fetch method and URI from server variables
$httpMethod = $_SERVER['REQUEST_METHOD'];
$uri = $_SERVER['REQUEST_URI'];

// Strip query string (?foo=bar) and decode URI
if (false !== $pos = strpos($uri, '?')) {
    $uri = substr($uri, 0, $pos);
}
$uri = rawurldecode($uri);

$routeInfo = $dispatcher->dispatch($httpMethod, $uri);
switch ($routeInfo[0]) {
    case FastRoute\Dispatcher::NOT_FOUND:
        echo json_encode(["message" => "Not Found"]);
        break;
    case FastRoute\Dispatcher::METHOD_NOT_ALLOWED:
        $allowedMethods = $routeInfo[1];
        echo json_encode(["message" => "Method Not Allowed", "allowed_methods" => $allowedMethods]);
        break;
    case FastRoute\Dispatcher::FOUND:
        $handler = $routeInfo[1];
        $vars = $routeInfo[2];
        call_user_func_array($handler, $vars);
        break;
}

// Close connection
$conn = null;
?>