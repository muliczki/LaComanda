<?php
require_once './models/Producto.php';
require_once './models/Sector.php';
require_once './models/ConsultasPDO.php';
require_once './interfaces/IApiUsable.php';

use \App\Models\Producto as Producto;
use \App\Models\Sector as Sector;

class ProductoController implements IApiUsable
{
    public function CargarUno($request, $response, $args)
    {
      $parametros = $request->getParsedBody();

      $nombreProducto = $parametros['nombreProducto'];
      $precio = $parametros['precio'];
      $idSector = Sector::where('sector','=',$parametros['sectorDesc'])->get('id')[0]['id'];

      // Creamos el producto
      $producto = new Producto();
      $producto->nombre_producto = $nombreProducto;
      $producto->id_sector = $idSector;
      $producto->precio = $precio;
      $producto->save();

      $payload = json_encode(array("mensaje" => "Producto ". $nombreProducto ." agregado con exito"));

      $response->getBody()->write($payload);
      return $response
        ->withHeader('Content-Type', 'application/json');
    }

    public function TraerUno($request, $response, $args)
    {
      // Buscamos producto por nombre
      $nombre = $args['nombreProducto'];
      $producto = Producto::where('nombre_producto', $nombre)->first();
      $payload = json_encode($producto);

      $response->getBody()->write($payload);
      return $response
        ->withHeader('Content-Type', 'application/json');
    }

    public function TraerTodos($request, $response, $args)
    {
      $lista = Producto::all();
      $payload = json_encode(array("listaProductos" => $lista));

      $response->getBody()->write($payload);
      return $response
        ->withHeader('Content-Type', 'application/json');
    }
    
    public function ModificarUno($request, $response, $args)
    {
      // $parametros = $request->getParsedBody();

      // $productoCodigo = $parametros['codigoPedido'];
      // $minutosRestantes = $parametros['minutos'];


      // Pedido::modificarPedido($productoCodigo, $minutosRestantes);

      // $payload = json_encode(array("mensaje" => "Pedido ".$productoCodigo. " actualizado con exito"));

      // $response->getBody()->write($payload);
      // return $response
      //   ->withHeader('Content-Type', 'application/json');
    }

    public function BorrarUno($request, $response, $args)
    {
        // $parametros = $request->getParsedBody();

        // $usuarioId = $parametros['usuarioId'];
        // Usuario::borrarUsuario($usuarioId);

        // $payload = json_encode(array("mensaje" => "Usuario borrado con exito"));

        // $response->getBody()->write($payload);
        // return $response
        //   ->withHeader('Content-Type', 'application/json');
    }
}
