<?php
error_reporting(-1);
ini_set('display_errors', 1);

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface;
use Slim\Factory\AppFactory;
use Slim\Routing\RouteCollectorProxy;
use Slim\Routing\RouteContext;

require __DIR__ . '/../vendor/autoload.php';
require_once './db/AccesoDatos.php';
require_once './middlewares/Logger.php';

require_once './controllers/PedidoController.php';
require_once './controllers/DetallePedidoController.php';
require_once './controllers/ProductoController.php';
require_once './controllers/UsuarioController.php';
require_once './controllers/PersonaController.php';

require_once './models/AutentificadorJWT.php';
require_once './middlewares/MWparaCORS.php';
require_once './middlewares/MWparaAutentificar.php';

// Load ENV
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->safeLoad();

// Instantiate App
$app = AppFactory::create();
// // Set base path
// $app->setBasePath('/app');
$app->setBasePath('/LaComanda/app');
$app->addBodyParsingMiddleware();
$app->addRoutingMiddleware();


// Add error middleware
$app->addErrorMiddleware(true, true, true);

$app->add(\MWparaAutentificar::class . ':VerificarUsuario');

// Routes
$app->group('/pedidos', function (RouteCollectorProxy $group) {
  $group->get('[/]', \PedidoController::class . ':TraerTodos')->add(\MWparaCORS::class . ':HabilitarCORSTodos');
  $group->post('[/]', \PedidoController::class . ':CargarUno');
  $group->put('[/]', \PedidoController::class . ':ModificarUno');

  $group->get('/detalle[/]', \DetallePedidoController::class . ':TraerTodos');
  $group->get('/detalle/{codigoPedido}', \DetallePedidoController::class . ':TraerUno');
  $group->post('/detalle[/]', \DetallePedidoController::class . ':CargarUno');
  
  $group->get('/{codigoPedido}', \PedidoController::class . ':TraerUno');

});


$app->group('/siguiente', function (RouteCollectorProxy $group) {
  //ok los dos
  $group->post('[/]', \DetallePedidoController::class . ':TraerPrimerPendiente'); 
  $group->put('[/]', \DetallePedidoController::class . ':ModificarUno');
});

$app->group('/productos', function (RouteCollectorProxy $group) {
  $group->get('[/]', \ProductoController::class . ':TraerTodos');
  $group->get('/{nombreProducto}', \ProductoController::class . ':TraerUno');
  $group->post('[/]', \ProductoController::class . ':CargarUno');
  $group->put('[/]', \ProductoController::class . ':ModificarUno');
});

$app->group('/personas', function (RouteCollectorProxy $group) {
  $group->get('[/]', \PersonaController::class . ':TraerTodos');
  $group->get('/{email}', \PersonaController::class . ':TraerUno');
  $group->post('[/]', \PersonaController::class . ':CargarUno');
});


//->add(\MWparaCORS::class . ':HabilitarCORS8080');

// $app->add(Logger::class . ':Verificar');
// $app->add(Logger::class . ':LogOperacion');

// $app->get('[/]', function (Request $request, Response $response) {    
//     $response->getBody()->write("Slim Framework 4 PHP");
//     return $response;

// });

$app->run();
