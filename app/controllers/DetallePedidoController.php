<?php
require_once './models/DetallePedido.php';
require_once './models/Pedido.php';
require_once './models/Producto.php';
require_once './models/Persona.php';
require_once './models/Usuario.php';
require_once './models/EstadosPedido.php';
require_once './models/Ocupacion.php';
require_once './models/ConsultasPDO.php';
require_once './models/CambiosEstadosPedidos.php';
require_once './interfaces/IApiUsable.php';

use \App\Models\DetallePedido as DetallePedido;
use \App\Models\Pedido as Pedido;
use \App\Models\Producto as Producto;
use \App\Models\Persona as Persona;
use \App\Models\Usuario as Usuario;
use \App\Models\EstadosPedido as EstadosPedido;
use \App\Models\ocupacion as Ocupacion;
use \App\Models\CambiosEstadosPedidos as CambiosEstadosPedidos;

class DetallePedidoController implements IApiUsable
{
  public function CargarUno($request, $response, $args)
  {
    $parametros = $request->getParsedBody();

    $codigoPedido = $parametros['peticion']['codigoPedido'];
    $producto = $parametros['peticion']['producto'];

    $pedidoGral = Pedido::where("codigo_pedido", "=", $codigoPedido)->first();
    $producto = Producto::where("nombre_producto", "=", $producto)->first();
    $empleado = Persona::where("email", "=", $parametros['dataToken']->email)->first();
    $userEmpleado = Usuario::where("id_persona", "=", $empleado['id'])->first();
    $idEstadoPedido = 1; // 1 = PENDIENTE EN BD

    // Creamos el Detallepedido
    $detPedido = new DetallePedido();
    $detPedido->id_codigo_pedido = $pedidoGral['id'];
    $detPedido->id_producto = $producto['id'];
    $detPedido->id_estado_pedido = $idEstadoPedido;
    $detPedido->id_responsable = $empleado['id'];
    $detPedido->fecha_solicitud =  date ("Y-m-d H:i:s");
    $detPedido->fecha_actualizacion =  date ("Y-m-d H:i:s");
    $detPedido->save();

    // cargo cambio en tabla de estados pedidos
    $cambioPedido = new CambiosEstadosPedidos();
    $cambioPedido->id_codigo_pedido = $pedidoGral['id'];
    $cambioPedido->id_detalle_pedido = $detPedido->id;
    $cambioPedido->id_usuario = $userEmpleado['id'];
    $cambioPedido->id_estado_pedido = $idEstadoPedido;
    $cambioPedido->fecha_registro = date("Y-m-d H:i:s");
    $cambioPedido->save();

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
    $parametros = $request->getParsedBody();

    $idDetalle = $parametros['peticion']['idDetalle'];
    $minutos = $parametros['peticion']['minutos'];
    $estadoPedidoAct = $parametros['peticion']['estadoPedidoActualizar'];

    $empleado = Persona::where("email", "=", $parametros['dataToken']->email)->first();
    $userEmpleado = Usuario::where("id_persona", "=", $empleado['id'])->first();
    $fechaActual = new DateTime();
    $fechaFin = $fechaActual->modify('+'.$minutos.' minute');
    $idEstadoPedido = EstadosPedido::where("descripcion", "=", $estadoPedidoAct)->first();

    $detPedido = DetallePedido::where('id', "=", $idDetalle)->first();
    $detPedido->id_estado_pedido = $idEstadoPedido['id'];
    $detPedido->fecha_actualizacion = date("Y-m-d H:i:s");
    $detPedido->fecha_estimada = $fechaFin;
    $detPedido->id_responsable = $empleado['id'];
    $detPedido->save();

    // cargo cambio en tabla de estados pedidos
    $cambioPedido = new CambiosEstadosPedidos();
    $cambioPedido->id_codigo_pedido = $detPedido['id_codigo_pedido'];
    $cambioPedido->id_detalle_pedido = $detPedido->id;
    $cambioPedido->id_usuario = $userEmpleado['id'];
    $cambioPedido->id_estado_pedido = $idEstadoPedido['id'];
    $cambioPedido->fecha_registro = date("Y-m-d H:i:s");
    $cambioPedido->save();

    $payload = json_encode(array("mensaje" => "Pedido ".$idDetalle. " actualizado con exito"));

    $response->getBody()->write($payload);
    return $response
      ->withHeader('Content-Type', 'application/json');
  }

  public function BorrarUno($request, $response, $args)
  {
    $parametros = $request->getParsedBody();
    $id = $args['id'];
    // Buscamos el detalle
    $item = DetallePedido::find($id);
    
    // Borramos si existe
    if(!is_null($item)){
      $empleado = Persona::where("email", "=", $parametros['dataToken']->email)->first();
      $userEmpleado = Usuario::where("id_persona", "=", $empleado['id'])->first();
      // cargo cambio en tabla de estados pedidos
      $cambioPedido = new CambiosEstadosPedidos();
      $cambioPedido->id_codigo_pedido = $item['id_codigo_pedido'];
      $cambioPedido->id_detalle_pedido = $item->id;
      $cambioPedido->id_usuario = $userEmpleado['id'];
      $cambioPedido->id_estado_pedido = 5; //CANCELADO
      $cambioPedido->fecha_registro = date("Y-m-d H:i:s");
      $cambioPedido->save();

      $item->id_responsable = $empleado["id"];
      $item->id_estado_pedido = 5; //CANCELADO
      $item->save();
      $item->delete();
      $mensaje = "Item borrado con exito";
    }else{
      $mensaje = "El ID no existe";
    }

    $payload = json_encode(array("mensaje" => $mensaje));

    $response->getBody()->write($payload);
    return $response
      ->withHeader('Content-Type', 'application/json');
  }


  public function TraerPrimerPendiente($request, $response, $args)
  {
    $parametros = $request->getParsedBody();

    $email = $parametros['dataToken']->email;
    $perfil = $parametros['dataToken']->perfil;

    $empleado = Persona::where("email", "=", $email)->first();
    $ocupacion = Ocupacion::where("ocupacion", "=", $perfil)->first();

    if($perfil != "MOZO")
    {
      //si no es mozo, busco pedidos PENDIENTES ID=1
      $detPedido = DetallePedido::
      join('productos', 'productos.id', '=','detallepedidos.id_producto')->
      join('sectores', 'sectores.id', '=','productos.id_sector')->
      select('detallepedidos.id', 'productos.nombre_producto', 'sectores.sector')->
      where('productos.id_sector', '=', $empleado['id_sector'])->where("detallepedidos.id_estado_pedido", "=", 1)->first();

    }else{
      //si soy mozo, busco pedidos LISTO PARA SERVIR ID=3
      $detPedido = DetallePedido::
      join('productos', 'productos.id', '=','detallepedidos.id_producto')->
      join('sectores', 'sectores.id', '=','productos.id_sector')->
      select('detallepedidos.id', 'productos.nombre_producto', 'sectores.sector')->
      where("detallepedidos.id_estado_pedido", "=", 3)->first();
    }

    if(is_null($detPedido))
    {
      $detPedido = "No hay pedidos pendientes!";
    }

    $payload = json_encode(array("Ocupacion" => $ocupacion["ocupacion"],"DetallePedido" => $detPedido));
      
    $response->getBody()->write($payload);
    return $response
      ->withHeader('Content-Type', 'application/json');

  }

  

}
