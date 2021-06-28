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
// require_once './db/AccesoDatos.php';
require_once './middlewares/AutentificadorJWT.php';
require_once './middlewares/AccesosMD.php';

require_once './controllers/PedidoController.php';
require_once './controllers/DetallePedidoController.php';
require_once './controllers/ProductoController.php';
require_once './controllers/UsuarioController.php';
require_once './controllers/PersonaController.php';
require_once './controllers/EncuestaController.php';
require_once './controllers/EstadisticaController.php';
require_once './classes/Jwt.php';
require_once './classes/Archivo.php';

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

$app->post('/login[/]', \UsuarioController::class . ':Login'); //para cualquier persona
$app->post('/usuarios[/]', \UsuarioController::class . ':CargarUno'); //para cualquier persona

$app->post('/detallePedido/{codigoPedido}', \DetallePedidoController::class . ':TraerUno')->add(\AccesosMD::class . ':VerificarCliente');

$app->group('/encuestas', function (RouteCollectorProxy $group) {
  $group->post('[/]', \EncuestaController::class . ':CargarUno');//para cualquier persona
  
  // todas lo puede ver socio
  $group->get('[/]', \EncuestaController::class . ':TraerTodos')->add(\AccesosMD::class . ':VerificarPerfilSocio')->add(\AccesosMD::class . ':VerificarUsuario');
  // una lo puede ver socio
  $group->get('/{codigoPedido}', \EncuestaController::class . ':TraerUno')->add(\AccesosMD::class . ':VerificarPerfilSocio')->add(\AccesosMD::class . ':VerificarUsuario');
});



$app->group('/pedidos', function (RouteCollectorProxy $group) {
  // todos los pedidos solo lo pueden ver los socios
  $group->get('[/]', \PedidoController::class . ':TraerTodos')->add(\AccesosMD::class . ':VerificarPerfilSocio');
  // cargar pedido solo lo pueden hacer socios o mozos
  $group->post('[/]', \PedidoController::class . ':CargarUno')->add(\AccesosMD::class . ':VerificarPerfilSocioMozo');
  // modificar estado mesa solo mozos o socios (socios solo cerrar)
  $group->put('[/]', \PedidoController::class . ':ModificarUno')->add(\AccesosMD::class . ':VerificarPerfilSocioMozo');
  // borrar pedido solo mozos o socios 
  $group->delete('/{id}', \PedidoController::class . ':BorrarUno')->add(\AccesosMD::class . ':VerificarPerfilSocioMozo');

  // todo el detalle de TODOS los pedidos solo lo pueden ver los socios
  $group->get('/detalle[/]', \DetallePedidoController::class . ':TraerTodos')->add(\AccesosMD::class . ':VerificarPerfilSocio');
  // todo el detalle de un pedido lo pueden ver los socios y mozos
  $group->get('/detalle/{codigoPedido}', \DetallePedidoController::class . ':TraerUno')->add(\AccesosMD::class . ':VerificarPerfilSocioMozo');

  //cargar detalle/producto al pedido solo lo pueden hacer socios o mozos
  $group->post('/detalle[/]', \DetallePedidoController::class . ':CargarUno')->add(\AccesosMD::class . ':VerificarPerfilSocioMozo');
  
  // un pedido lo pueden ver socio o mozo 
  $group->get('/{codigoPedido}', \PedidoController::class . ':TraerUno')->add(\AccesosMD::class . ':VerificarPerfilSocioMozo');

})->add(\AccesosMD::class . ':VerificarUsuario');


$app->group('/siguiente', function (RouteCollectorProxy $group) {
  // acceso empleados no socios >> devuelve PRIMER PENDIENTE del sector del token. Y si es mozo el PRIMER LISTO PARA ENTREGAR
  $group->post('[/]', \DetallePedidoController::class . ':TraerPrimerPendiente')->add(\AccesosMD::class . ':VerificarPerfilNoSocio');
  // actualizar detalle/prodcuto solo empleados que no sean socios
  $group->put('[/]', \DetallePedidoController::class . ':ModificarUno')->add(\AccesosMD::class . ':VerificarPerfilNoSocio');
  //borrar detalle de pedido solo socio y mozo
  $group->delete('/{id}', \DetallePedidoController::class . ':BorrarUno')->add(\AccesosMD::class . ':VerificarPerfilSocioMozo');

})->add(\AccesosMD::class . ':VerificarUsuario');


$app->group('/productos', function (RouteCollectorProxy $group) {
  //ver TODOS producto SOLO socio
  $group->get('[/]', \ProductoController::class . ':TraerTodos')->add(\AccesosMD::class . ':VerificarPerfilSocio');
  //agregar producto SOLO socio
  $group->post('[/]', \ProductoController::class . ':CargarUno')->add(\AccesosMD::class . ':VerificarPerfilSocio');
  //put solo socio, modificar PRECIO
  $group->put('[/]', \ProductoController::class . ':ModificarUno')->add(\AccesosMD::class . ':VerificarPerfilSocio');
  //solo socio, borrar prodcto
  $group->delete('/{id}', \ProductoController::class . ':BorrarUno')->add(\AccesosMD::class . ':VerificarPerfilSocio');
  
  //exportar datos desde CSV de prodctos SOLO SOCIO
  $group->get('/subirCsv/{nombreArchivo}', \Archivo::class . ':CargarDatosCsv')->add(\AccesosMD::class . ':VerificarPerfilSocio');
  //exportar CSV  de prodctos SOLO SOCIO
  $group->get('/exportarCsv/{nombreArchivo}', \Archivo::class . ':CrearCsv')->add(\AccesosMD::class . ':VerificarPerfilSocio');
  //exportar PDF  de prodctos SOLO SOCIO
  $group->get('/exportarPdf[/]', \Archivo::class . ':CrearPdf')->add(\AccesosMD::class . ':VerificarPerfilSocio');

  //ver 1 producto socio y mozo
  $group->get('/{nombreProducto}', \ProductoController::class . ':TraerUno')->add(\AccesosMD::class . ':VerificarPerfilSocioMozo');
})->add(\AccesosMD::class . ':VerificarUsuario');

//personas SOLO SOCIOS
$app->group('/personas', function (RouteCollectorProxy $group) {
  $group->get('[/]', \PersonaController::class . ':TraerTodos');
  $group->get('/{email}', \PersonaController::class . ':TraerUno');
  $group->post('[/]', \PersonaController::class . ':CargarUno');
  //modif solo estado
  $group->put('[/]', \PersonaController::class . ':ModificarUno');
  $group->delete('/{id}', \PersonaController::class . ':BorrarUno');
})->add(\AccesosMD::class . ':VerificarPerfilSocio')->add(\AccesosMD::class . ':VerificarUsuario');


//estadisticas SOLO SOCIOS
$app->group('/estadisticas', function (RouteCollectorProxy $group) {
  
  $group->group('/empleados',function (RouteCollectorProxy $group){
    $group->get('/logins[/]', \EstadisticaController::class . ':ListarLogins'); 
    $group->get('/opSector[/]', \EstadisticaController::class . ':ListarOperacionesPorSector'); 
    $group->get('/opSectorEmpleado[/]', \EstadisticaController::class . ':ListarOperacionesPorSectorEmpleado'); 
    $group->get('/cantidadOpEmpleado[/]', \EstadisticaController::class . ':ListarCantidadOperacionesEmpleados'); 
  });
  $group->group('/pedidos',function (RouteCollectorProxy $group){
    $group->get('/masVendido[/]', \EstadisticaController::class . ':ProductoMasVendido'); 
    $group->get('/menosVendido[/]', \EstadisticaController::class . ':ProductoMenosVendido'); 
    $group->get('/cancelados[/]', \EstadisticaController::class . ':ProductosCancelados'); 
    $group->get('/factura/{pedido}', \EstadisticaController::class . ':ListarFacturaPedido'); 
  });

  $group->group('/mesas',function (RouteCollectorProxy $group){
    $group->get('/masUsada[/]', \EstadisticaController::class . ':MesaMasUsada'); 
    $group->get('/menosUsada[/]', \EstadisticaController::class . ':MesaMenosUsada'); 
    $group->get('/masFacturacion[/]', \EstadisticaController::class . ':MasFacturacion'); 
    $group->get('/menosFacturacion[/]', \EstadisticaController::class . ':MenosFacturacion'); 
    $group->get('/mejorFactura[/]', \EstadisticaController::class . ':MejorFactura'); 
    $group->get('/peorFactura[/]', \EstadisticaController::class . ':PeorFactura'); 
    $group->get('/mejoresComentarios[/]', \EstadisticaController::class . ':MesaMejoresComentarios'); 
    $group->get('/peoresComentarios[/]', \EstadisticaController::class . ':MesaPeoresComentarios'); 
  });

})->add(\AccesosMD::class . ':VerificarPerfilSocio')->add(\AccesosMD::class . ':VerificarUsuario');
#endregion



$app->group('/jwt', function (RouteCollectorProxy $group) {
  $group->post('/crearToken', \Jwt::class . ':CrearToken');
  $group->get('/devolverPayLoad', \Jwt::class . ':DevolverPayload');
  $group->get('/devolverDatos', \Jwt::class . ':DevolverDatos');
  $group->get('/verificarToken', \Jwt::class . ':VerificarToken');

});

$app->get('[/]', function (Request $request, Response $response) {    
  $response->getBody()->write("La Comanda - Uliczki Micaela");
  return $response;
});



$app->run();
