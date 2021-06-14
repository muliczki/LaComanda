<?php
require_once './models/Pedido.php';
require_once './models/Usuario.php';
require_once './models/Persona.php';
require_once './models/Mesa.php';
require_once './models/EstadosMesa.php';
require_once './models/CambiosEstadosMesa.php';
require_once './interfaces/IApiUsable.php';

use \App\Models\Pedido as Pedido;
use \App\Models\Usuario as Usuario;
use \App\Models\Persona as Persona;
use \App\Models\Mesa as Mesa;
use \App\Models\EstadosMesa as EstadosMesa;
use \App\Models\CambiosEstadosMesa as CambiosEstadosMesa;

//sacar la herencia del modelo!
class PedidoController implements IApiUsable
{
    public function CargarUno($request, $response, $args)
    {
      $parametros = $request->getParsedBody();

      $codigoPedido = self::crearCodigoPedido();
      
      $mesa = Mesa::where("codigo_mesa", '=', $parametros['peticion']['codigoMesa'])->first();
      $idEstadoMesa = 1; //CLIENTE ESPERANDO PEDIDO
      $cliente = Persona::where("email", "=", $parametros['peticion']['mailCliente'])->first();
      $empleado = Persona::where("email", "=", $parametros['dataToken']->email)->first();
      $userEmpleado = Usuario::where("id_persona", "=", $empleado['id'])->first();

      $uploadedFile = $request->getUploadedFiles()['foto'];
      if ($uploadedFile->getError() === UPLOAD_ERR_OK) {
        //ok foto
        $filename = $codigoPedido.'.jpg';

        $rutaFoto = "../app/files/photos/".$filename;
        $uploadedFile->moveTo($rutaFoto);
      }else{
        //no subio foto o error
        $rutaFoto = "";
      }

      // Creamos el pedido
      $pedido = new Pedido();
      $pedido->codigo_pedido = $codigoPedido;
      $pedido->foto = $rutaFoto;
      $pedido->id_mesa = $mesa['id'];
      $pedido->id_estado_mesa = $idEstadoMesa;
      $pedido->id_cliente = $cliente['id'];
      $pedido->fecha_alta = date("Y-m-d H:i:s");
      $pedido->save();


      // cargo cambio en tabla de estados mesa
      $cambioMesa = new CambiosEstadosMesa();
      $cambioMesa->id_mesa = $mesa['id'];
      $cambioMesa->id_codigo_pedido = $pedido->id;
      $cambioMesa->id_usuario = $userEmpleado['id'];
      $cambioMesa->id_estado_mesa = $idEstadoMesa;
      $cambioMesa->fecha_cambio = date("Y-m-d H:i:s");
      $cambioMesa->save();

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
      $mensaje = "Datos incorrectos";
      $parametros = $request->getParsedBody();

      $pedidoCodigo = $parametros['peticion']['codigoPedido'];
      $estadoMesa = $parametros['peticion']['descripEstadoMesa'];
      $email = $parametros['dataToken']->email;
      $perfil = $parametros['dataToken']->perfil;
      
      $mesa = EstadosMesa:: where("descripcion", '=',$estadoMesa)->first();
      //buscar mail y id user para pasarselo al cambio mesa
      $persona = Persona:: where("email", '=',$email)->first();
      $user = Usuario:: where("id_persona", '=',$persona['id'])->first();
      
      $pedido = Pedido::where("codigo_pedido", '=', $pedidoCodigo)->first();
      $cambio = FALSE;

      if(!is_null($pedido) && !is_null($mesa))
      {
        if($estadoMesa == 'CERRADA')
        {
          if($perfil == 'SOCIO'){
            $cambio = TRUE;
          }else{
            $mensaje = "Perfil NO autorizado para CERRAR mesa. Requiere SOCIO";
          }
        }else{
          if($perfil == 'MOZO'){
            $cambio = TRUE;
          }else{
            $mensaje = "Perfil NO autorizado. Requiere MOZO";
          }
        }

        if($cambio)
        {
          $pedido->id_estado_mesa = $mesa['id'];
          $pedido->save();
          $mensaje = "Pedido actualizado con exito";

          //cargo cambio en tabla de estados mesa
          $cambioMesa = new CambiosEstadosMesa();
          $cambioMesa->id_mesa = $pedido['id_mesa'];
          $cambioMesa->id_codigo_pedido = $pedido['id'];
          $cambioMesa->id_usuario = $user['id'];
          $cambioMesa->id_estado_mesa = $mesa['id'];
          $cambioMesa->fecha_cambio = date("Y-m-d H:i:s");
          $cambioMesa->save();

        }
      }
      // Pedido ".$productoCodigo. " actualizado con exito

      $payload = json_encode(array("mensaje" => $mensaje));

      $response->getBody()->write($payload);
      return $response
        ->withHeader('Content-Type', 'application/json');
    }

    public function BorrarUno($request, $response, $args)
    {
      $parametros = $request->getParsedBody();
      $id = $args['id'];
      // Buscamos el id
      $item = Pedido::find($id);

      $empleado = Persona::where("email", "=", $parametros['dataToken']->email)->first();
      $userEmpleado = Usuario::where("id_persona", "=", $empleado['id'])->first();

      
      // Borramos si existe
      if(!is_null($item)){
        //cargo cambio en tabla de estados mesa
        $cambioMesa = new CambiosEstadosMesa();
        $cambioMesa->id_mesa = $item['id_mesa'];
        $cambioMesa->id_codigo_pedido = $item['id'];
        $cambioMesa->id_usuario = $userEmpleado['id'];
        $cambioMesa->id_estado_mesa = 5; //CANCELADA
        $cambioMesa->fecha_cambio = date("Y-m-d H:i:s");
        $cambioMesa->save();
        
        $item->id_estado_mesa = 5;//CANCELADA
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

    //CREAR CODIGO PEDIDO
    public static function crearCodigoPedido()
    {
      $permitted_chars = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
      return substr(str_shuffle($permitted_chars), 0, 5);
    }
}
