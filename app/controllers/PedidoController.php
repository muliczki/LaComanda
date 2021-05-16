<?php
require_once './models/Pedido.php';
require_once './interfaces/IApiUsable.php';

class PedidoController extends Pedido implements IApiUsable
{
    public function CargarUno($request, $response, $args)
    {
      $parametros = $request->getParsedBody();

      $codigoPedido = Pedido::crearCodigoPedido();
      $nombreCliente = $parametros['nombreCliente'];
      $producto = $parametros['producto'];
      $idEstado = 1; // 1 = PENDIENTE EN BD
      $idSector = Pedido::TraerIdSector($parametros['sectorDesc']); 
      $precio = $parametros['precio'];
      $idMesa = Pedido::TraerIdMesa($parametros['codigoMesa']); 

      // Creamos el pedido
      $pedido = new Pedido();
      $pedido->codigo_pedido = $codigoPedido;
      $pedido->producto = $producto;
      $pedido->nombre_cliente = $nombreCliente;
      $pedido->id_estado_pedido = $idEstado;
      $pedido->id_sector = $idSector;
      $pedido->precio = $precio;
      $pedido->id_mesa = $idMesa;
      $pedido->crearPedido();

      $payload = json_encode(array("mensaje" => "Pedido ". $codigoPedido ." creado con exito"));

      $response->getBody()->write($payload);
      return $response
        ->withHeader('Content-Type', 'application/json');
    }

    public function TraerUno($request, $response, $args)
    {
      // Buscamos pedido por codigo
      $cod = $args['codigoPedido'];
      $pedido = Pedido::obtenerPedido($cod);
      $payload = json_encode($pedido);

      $response->getBody()->write($payload);
      return $response
        ->withHeader('Content-Type', 'application/json');
    }

    public function TraerTodos($request, $response, $args)
    {
      $lista = Pedido::obtenerTodos();
      $payload = json_encode(array("listaPedidos" => $lista));

      $response->getBody()->write($payload);
      return $response
        ->withHeader('Content-Type', 'application/json');
    }
    
    public function ModificarUno($request, $response, $args)
    {
      $parametros = $request->getParsedBody();

      $productoCodigo = $parametros['codigoPedido'];
      $minutosRestantes = $parametros['minutos'];


      Pedido::modificarPedido($productoCodigo, $minutosRestantes);

      $payload = json_encode(array("mensaje" => "Pedido ".$productoCodigo. " actualizado con exito"));

      $response->getBody()->write($payload);
      return $response
        ->withHeader('Content-Type', 'application/json');
    }

    public function BorrarUno($request, $response, $args)
    {
        $parametros = $request->getParsedBody();

        $usuarioId = $parametros['usuarioId'];
        Usuario::borrarUsuario($usuarioId);

        $payload = json_encode(array("mensaje" => "Usuario borrado con exito"));

        $response->getBody()->write($payload);
        return $response
          ->withHeader('Content-Type', 'application/json');
    }
}
