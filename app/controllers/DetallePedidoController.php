<?php
require_once './models/DetallePedido.php';
require_once './models/ConsultasPDO.php';
require_once './interfaces/IApiUsable.php';

use \App\Models\DetallePedido as DetallePedido;

class DetallePedidoController implements IApiUsable
{
  public function CargarUno($request, $response, $args)
  {
    $parametros = $request->getParsedBody();

    $idCodigoPedido = ConsultasPdo::TraerIdCodigoPedido($parametros['codigoPedido']);
    $idProducto = ConsultasPdo::TraerIdProducto($parametros['producto']);
    $idEstado = 1; // 1 = PENDIENTE EN BD
    $idResponsable = ConsultasPdo::TraerIdPersona($parametros['emailMozo']);

    // Creamos el Detallepedido
    $detPedido = new DetallePedido();
    $detPedido->id_codigo_pedido = $idCodigoPedido;
    $detPedido->id_producto = $idProducto;
    $detPedido->id_estado_pedido = $idEstado;
    $detPedido->id_responsable = $idResponsable;
    $detPedido->fecha_solicitud =  date ("Y-m-d H:i:s");
    $detPedido->fecha_actualizacion =  date ("Y-m-d H:i:s");
    $detPedido->save();

    $payload = json_encode(array("mensaje" => "Detalle Pedido cargado con exito"));

    $response->getBody()->write($payload);
    return $response
      ->withHeader('Content-Type', 'application/json');
  }

  public function TraerUno($request, $response, $args)
  {
    // Buscamos pedido por codigo
    $cod = $args['codigoPedido'];

    $pedido = DetallePedido::
      join('pedidos', 'pedidos.id', '=','detallepedidos.id_codigo_pedido')->
      join('productos', 'productos.id', '=','detallepedidos.id_producto')->
      join('estadospedidos', 'estadospedidos.id', '=','detallepedidos.id_estado_pedido')->
      join('personas', 'personas.id', '=','detallepedidos.id_responsable')->
      select('detallepedidos.id', 'productos.nombre_producto', 'pedidos.codigo_pedido', 'estadospedidos.descripcion', 'personas.email')->
      where('pedidos.codigo_pedido', '=', $cod)->
      get()
    ;
    $payload = json_encode($pedido);
    $response->getBody()->write($payload);
    return $response
      ->withHeader('Content-Type', 'application/json');
  }

  public function TraerTodos($request, $response, $args)
  {
    $lista = DetallePedido::all();
    $payload = json_encode(array("listaDetallePedidos" => $lista));
      
    $response->getBody()->write($payload);
    return $response
      ->withHeader('Content-Type', 'application/json');
  }
  
  public function ModificarUno($request, $response, $args)
  {
    // $parametros = $request->getParsedBody();

    // $idDetalle = $parametros['idDetalle'];
    // $empleadoId = ConsultasPdo::TraerIdPersona($parametros['emailEmpleado']);
    // $idEstadoPedido = ConsultasPdo::TraerIdEstadoPedido($parametros['estadoPedidoActualizar']);

    // if(!is_null($parametros['minutos']))
    // {
    //   $minutosRestantes = $parametros['minutos'];
    // }else{
    //   $minutosRestantes = 0;
    // }

    // DetallePedido::modificarDetallePedido($idDetalle, $minutosRestantes, $empleadoId, $idEstadoPedido);

    // $payload = json_encode(array("mensaje" => "Pedido ".$idDetalle. " actualizado con exito"));

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


  public function TraerPrimerPendiente($request, $response, $args)
  {
    $parametros = $request->getParsedBody();

    $empleadoId =  ConsultasPdo::TraerIdPersona($parametros['emailEmpleado']);

    $sectorId = ConsultasPdo::TraerIdSectorPersona($empleadoId);

    $idPendiente = ConsultasPdo::traerIdPrimerDetallePendiente($sectorId);

    $pedido = ConsultasPdo::traerDetallePendiente($idPendiente);

    $payload = json_encode(array("DetallePedido" => $pedido));
      
    $response->getBody()->write($payload);
    return $response
      ->withHeader('Content-Type', 'application/json');

  }

}
