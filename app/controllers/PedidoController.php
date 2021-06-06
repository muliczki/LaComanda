<?php
require_once './models/Pedido.php';
require_once './models/ConsultasPDO.php';
require_once './interfaces/IApiUsable.php';

use \App\Models\Pedido as Pedido;

//sacar la herencia del modelo!
class PedidoController implements IApiUsable
{
    public function CargarUno($request, $response, $args)
    {
      $parametros = $request->getParsedBody();

      $codigoPedido = ConsultasPdo::crearCodigoPedido();
      $foto = $parametros['foto']; //ver si es null
      $idMesa = ConsultasPdo::TraerIdMesa($parametros['codigoMesa']); 
      $idEstadoMesa = 1; //CLIENTE ESPERANDO PEDIDO
      $idCliente = ConsultasPdo::TraerIdPersona($parametros['mailCliente']); 

      // Creamos el pedido
      $pedido = new Pedido();
      $pedido->codigo_pedido = $codigoPedido;
      $pedido->foto = $foto;
      $pedido->id_mesa = $idMesa;
      $pedido->id_estado_mesa = $idEstadoMesa;
      $pedido->id_cliente = $idCliente;
      $pedido->save();

      $payload = json_encode(array("mensaje" => "Pedido ". $codigoPedido ." creado con exito"));

      $response->getBody()->write($payload);
      return $response
        ->withHeader('Content-Type', 'application/json');
    }

    public function TraerUno($request, $response, $args)
    {
      // Buscamos pedido por codigo
      $cod = $args['codigoPedido'];

      // Buscamos por primary key
      // $usuario = Usuario::find($usr);

      // Buscamos por attr codigo
      $pedido = Pedido::where('codigo_pedido', $cod)->first();

      $payload = json_encode($pedido);
      $response->getBody()->write($payload);
      return $response
        ->withHeader('Content-Type', 'application/json');
    }

    public function TraerTodos($request, $response, $args)
    {
      $lista = Pedido::all();
      $payload = json_encode(array("listaPedidos" => $lista));
       
      $response->getBody()->write($payload);
      return $response
        ->withHeader('Content-Type', 'application/json');
    }
    
    public function ModificarUno($request, $response, $args)
    {
      // $parametros = $request->getParsedBody();

      // $productoCodigo = $parametros['codigoPedido'];
      // $idEstadoMesa = ConsultasPDO:: TraerIdEstadoMesa($parametros['descripEstadoMesa']);

      // Pedido::modificarPedidoEstadoMesa($productoCodigo, $idEstadoMesa);

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
