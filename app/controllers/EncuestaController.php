<?php
require_once './models/Usuario.php';
require_once './models/Ocupacion.php';
require_once './models/Persona.php';
require_once './models/LoginUsers.php';
require_once './models/Encuesta.php';
require_once './interfaces/IApiUsable.php';

use \App\Models\Ocupacion as Ocupacion;
use \App\Models\Usuario as Usuario;
use \App\Models\Persona as Persona;
use \App\Models\LoginUsers as LoginUsers;
use \App\Models\Encuesta as Encuesta;
use App\Models\Pedido;

class EncuestaController implements IApiUsable
{
  public function CargarUno($request, $response, $args)
  {
    $parametros = $request->getParsedBody();

    $emailCliente = $parametros['email'];
    $codPedido = $parametros['codPedido'];
    $notaMesa = $parametros['notaMesa'];
    $notaResto = $parametros['notaResto'];
    $notaMozo = $parametros['notaMozo'];
    $notaCocinero = $parametros['notaCocinero'];
    $experiencia = $parametros["experiencia"];

    $pedido = Pedido::where("codigo_pedido", "=", $codPedido)->first();
    $persona = Persona::where("email", "=", $emailCliente)->first();

    if(!is_null($pedido) && !is_null($persona)){

      // Creamos la encuesta
      $enc = new Encuesta();
      $enc->id_cliente = $persona['id'];
      $enc->id_pedido = $pedido['id'];
      $enc->nota_mesa = $notaMesa;
      $enc->nota_resto = $notaResto;
      $enc->nota_mozo = $notaMozo;
      $enc->nota_cocinero = $notaCocinero;
      $enc->experiencia = $experiencia;
      $enc->fecha_creacion = date("Y-m-d H:i:s");
      $enc->save();

      $mensaje ="Encuesta creada con exito";
    }else{
      $mensaje = "El codigo no corresponde a un pedido registrada";
    }


    $payload = json_encode(array("mensaje" => $mensaje));

    $response->getBody()->write($payload);
    return $response
      ->withHeader('Content-Type', 'application/json');
  }

  public function TraerUno($request, $response, $args)
  {
    // Buscamos por codigo de pedido
    $cod = $args['codigoPedido'];

    $pedido = Pedido::where("codigo_pedido", "=", $cod)->first();
    $enc = Encuesta::where("id_pedido", "=", $pedido['id'])->first();
    
    if(!is_null($pedido)){
      $mensaje = $enc;
    }else{
      $mensaje = "No existe encuesta para ese pedido";
    }

    
    $payload = json_encode(array("mensaje" => $mensaje));

    $response->getBody()->write($payload);
    return $response
      ->withHeader('Content-Type', 'application/json');
  }

  public function TraerTodos($request, $response, $args)
  {
    $lista = Encuesta::all();
    $payload = json_encode(array("listaEncuestas" => $lista));

    $response->getBody()->write($payload);
    return $response
      ->withHeader('Content-Type', 'application/json');
  }
  
  public function ModificarUno($request, $response, $args)
  {
  
  }

  public function BorrarUno($request, $response, $args)
  {

  }




}
