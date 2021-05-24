<?php
require_once './models/DetallePedido.php';
require_once './models/ConsultasPDO.php';
require_once './interfaces/IApiUsable.php';

class DetallePedidoController extends DetallePedido implements IApiUsable
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
    $detPedido->crearDetallePedido();

    $payload = json_encode(array("mensaje" => "Detalle Pedido cargado con exito"));

    $response->getBody()->write($payload);
    return $response
      ->withHeader('Content-Type', 'application/json');
  }

  public function TraerUno($request, $response, $args)
  {
    // Buscamos pedido por codigo
    $cod = $args['codigoPedido'];
    $pedido = DetallePedido::obtenerDetallePedido($cod);
    $payload = json_encode($pedido);
    $response->getBody()->write($payload);
    return $response
      ->withHeader('Content-Type', 'application/json');
  }

  public function TraerTodos($request, $response, $args)
  {
    $lista = DetallePedido::obtenerTodos();
    $payload = json_encode(array("listaDetallePedidos" => $lista));
      
    $response->getBody()->write($payload);
    return $response
      ->withHeader('Content-Type', 'application/json');
  }
  
  public function ModificarUno($request, $response, $args)
  {
    $parametros = $request->getParsedBody();

    $idDetalle = $parametros['idDetalle'];
    $empleadoId = ConsultasPdo::TraerIdPersona($parametros['emailEmpleado']);
    $idEstadoPedido = ConsultasPdo::TraerIdEstadoPedido($parametros['estadoPedidoActualizar']);

    if(!is_null($parametros['minutos']))
    {
      $minutosRestantes = $parametros['minutos'];
    }else{
      $minutosRestantes = 0;
    }

    DetallePedido::modificarDetallePedido($idDetalle, $minutosRestantes, $empleadoId, $idEstadoPedido);

    $payload = json_encode(array("mensaje" => "Pedido ".$idDetalle. " actualizado con exito"));

    $response->getBody()->write($payload);
    return $response
      ->withHeader('Content-Type', 'application/json');
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
