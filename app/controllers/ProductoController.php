<?php
require_once './models/Producto.php';
require_once './interfaces/IApiUsable.php';

class ProductoController extends Producto implements IApiUsable
{
    public function CargarUno($request, $response, $args)
    {
      $parametros = $request->getParsedBody();

      $nombreProducto = $parametros['nombreProducto'];
      $idSector = Producto::TraerIdSector($parametros['sectorDesc']); 

      // Creamos el producto
      $producto = new Producto();
      $producto->nombre_producto = $nombreProducto;
      $producto->id_sector = $idSector;
      $producto->crearProducto();

      $payload = json_encode(array("mensaje" => "Producto ". $nombreProducto ." agregado con exito"));

      $response->getBody()->write($payload);
      return $response
        ->withHeader('Content-Type', 'application/json');
    }

    public function TraerUno($request, $response, $args)
    {
      // Buscamos producto por nombre
      $nombre = $args['nombreProducto'];
      $producto = Producto::obtenerProducto($nombre);
      $payload = json_encode($producto);

      $response->getBody()->write($payload);
      return $response
        ->withHeader('Content-Type', 'application/json');
    }

    public function TraerTodos($request, $response, $args)
    {
      $lista = Producto::obtenerTodos();
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
