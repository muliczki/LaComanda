<?php
error_reporting(-1);
ini_set('display_errors', 1);
date_default_timezone_set('America/Argentina/Buenos_Aires');

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface;
use Slim\Factory\AppFactory;
use Slim\Routing\RouteCollectorProxy;
use Slim\Routing\RouteContext;
use Illuminate\Database\Capsule\Manager as Capsule;

#region Require
require __DIR__ . '/../vendor/autoload.php';
require_once './db/AccesoDatos.php';
require_once './middlewares/AutentificadorJWT.php';

require_once './controllers/PedidoController.php';
require_once './controllers/DetallePedidoController.php';
require_once './controllers/ProductoController.php';
require_once './controllers/UsuarioController.php';
require_once './controllers/PersonaController.php';
require_once './classes/Jwt.php';

// require_once './middlewares/MWparaCORS.php';
// require_once './middlewares/MWparaAutentificar.php';
#endregion

// Load ENV
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->safeLoad();

// Instantiate App
$app = AppFactory::create();
// // Set base path
// $app->setBasePath('/app');
$app->setBasePath('/LaComanda/app');

// Add error middleware
$app->addErrorMiddleware(true, true, true);
$app->addBodyParsingMiddleware();
$app->addRoutingMiddleware();
#endregion

#region Eloquent
$container=$app->getContainer();

$capsule = new Capsule;
$capsule->addConnection([
    'driver'    => 'mysql',
    'host'      => $_ENV['MYSQL_HOST'],
    'database'  => $_ENV['MYSQL_DB'],
    'username'  => $_ENV['MYSQL_USER'],
    'password'  => $_ENV['MYSQL_PASS'],
    'charset'   => 'utf8',
    'collation' => 'utf8_unicode_ci',
    'prefix'    => '',
]);

$capsule->setAsGlobal();
$capsule->bootEloquent();

#endregion

#region Routes
$app->group('/pedidos', function (RouteCollectorProxy $group) {
  $group->get('[/]', \PedidoController::class . ':TraerTodos');
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
  $group->get('/subirCsv/{nombreArchivo}', \ProductoController::class . ':CargarCsv');
  $group->get('/exportarCsv/{nombreArchivo}', \ProductoController::class . ':ExportarCsv');
  $group->get('/exportarPdf/{nombreArchivo}', \ProductoController::class . ':ExportarPdf');
});

$app->group('/personas', function (RouteCollectorProxy $group) {
  $group->get('[/]', \PersonaController::class . ':TraerTodos');
  $group->get('/{email}', \PersonaController::class . ':TraerUno');
  $group->post('[/]', \PersonaController::class . ':CargarUno');
});
#endregion

//->add(\MWparaCORS::class . ':HabilitarCORS8080');

// $app->add(Logger::class . ':Verificar');
// $app->add(Logger::class . ':LogOperacion');

$app->get('[/]', function (Request $request, Response $response) {    
    $response->getBody()->write("La Comanda - Uliczki Micaela");
    return $response;
});


$app->group('/jwt', function (RouteCollectorProxy $group) {
  $group->post('/crearToken', \Jwt::class . ':CrearToken');
  $group->get('/devolverPayLoad', \Jwt::class . ':DevolverPayload');
  $group->get('/devolverDatos', \Jwt::class . ':DevolverDatos');
  $group->get('/verificarToken', \Jwt::class . ':VerificarToken');

});






$app->run();
