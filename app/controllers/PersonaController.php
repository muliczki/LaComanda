<?php
require_once './models/Persona.php';
require_once './models/Sector.php';
require_once './models/Usuario.php';
require_once './models/ConsultasPDO.php';
require_once './models/Ocupacion.php';
require_once './models/CambiosEstadosEmpleados.php';
require_once './models/EstadosEmpleado.php';
require_once './interfaces/IApiUsable.php';

use \App\Models\Persona as Persona;
use \App\Models\Sector as Sector;
use \App\Models\Usuario as Usuario;
use \App\Models\ocupacion as Ocupacion;
use \App\Models\CambiosEstadosEmpleados as CambiosEstadosEmpleados;
use \App\Models\EstadosEmpleado as EstadosEmpleado;

//sacar la herencia del modelo!
class PersonaController implements IApiUsable
{
  public function CargarUno($request, $response, $args)
  {
    $parametros = $request->getParsedBody();

    $nombre = $parametros['peticion']['nombre'];
    $apellido = $parametros['peticion']['apellido'];
    $email = $parametros['peticion']['email'];
    $ocupacion = Ocupacion::where("ocupacion","=",$parametros['peticion']['descOcupacion'])->first();
    $empleado = Persona::where("email", "=", $parametros['dataToken']->email)->first();
    $userEmpleado = Usuario::where("id_persona", "=", $empleado['id'])->first();
    
    // Creamos la Persona
    $persona = new Persona();
    $persona->nombre = $nombre;
    $persona->apellido = $apellido;
    $persona->email = $email;
    $persona->id_ocupacion = $ocupacion['id'];
    $persona->fecha_registro = date("Y-m-d H:i:s");
    $idEstado =0;

    //OCPACION LO TIENEN TODOS 
    //SECTOR LO TIENEN TODOS MENOS CLIENTE, MOZO Y SOCIO
    //ESTADO EMPLEADO LO TIENEN TODOS MENOS CLIENTE Y SOCIO
    if($ocupacion['id']!= 6 && $ocupacion['id']!=1 )//NO ES SOCIO NI CLIENTE
    {
      $idEstado = 1; // 1 = EMPLEADO ACTIVO EN BD
      $persona->id_estado_empleado = $idEstado;

      if($ocupacion['id']!= 5) //NO ES MOZO, MOZO NO TIENE SECTOR
      {
        $sector = Sector::where('sector', '=',$parametros['peticion']['descSector'])->first();
        $persona->id_sector = $sector['id'];
      }

    }
    
    $persona->save();
    
    //SI NO ES SOCIO NI CLIENTE
    //CREO ESTADO DE EMPLEADOS EN TABLA
    if($idEstado==1)
    {
      $cambioEstadoEmpleado = new CambiosEstadosEmpleados();
      $cambioEstadoEmpleado->id_persona = $persona->id;
      $cambioEstadoEmpleado->id_estado_empleado = 1; //ACTIVO
      $cambioEstadoEmpleado->id_socio = $userEmpleado['id'];
      $cambioEstadoEmpleado->fecha_registro = date("Y-m-d H:i:s");
      $cambioEstadoEmpleado->save();

    } 

    $payload = json_encode(array("mensaje" => "Persona " .$email." cargado con exito"));

    $response->getBody()->write($payload);
    return $response
      ->withHeader('Content-Type', 'application/json');
  }

  public function TraerUno($request, $response, $args)
  {
    // Buscamos persona por email
    $email = $args['email'];

    // Buscamos por primary key
    // $usuario = Usuario::find($usr);

    // Buscamos por attr email
    $persona = Persona::where('email', $email)->first();

    $payload = json_encode($persona);
    $response->getBody()->write($payload);
    return $response
      ->withHeader('Content-Type', 'application/json');
  }

  public function TraerTodos($request, $response, $args)
  {
    $lista = Persona::all();
    $payload = json_encode(array("listaPersonas" => $lista));
     
    $response->getBody()->write($payload);
    return $response
      ->withHeader('Content-Type', 'application/json');
  }

  public function ModificarUno($request, $response, $args)
  {
    $parametros = $request->getParsedBody();
    $mensaje = "Error. Datos incorrectos";
    $email = $parametros['peticion']['emailEmpleado'];
    $socio = Persona::where("email", "=", $parametros['dataToken']->email)->first();
    $userSocio = Usuario::where("id_persona", "=", $socio['id'])->first();
    
    $persona = Persona::where("email", '=', $email)->first();
    if(!is_null($persona)){
      // Actalizamos la Persona
      $idEstado =FALSE;
      
      //OCPACION LO TIENEN TODOS 
      //SECTOR LO TIENEN TODOS MENOS CLIENTE, MOZO Y SOCIO
      //ESTADO EMPLEADO LO TIENEN TODOS MENOS CLIENTE Y SOCIO
      if($persona['id_ocupacion']!= 6 && $persona['id_ocupacion']!=1 )//NO ES SOCIO NI CLIENTE
      {
        $estado = EstadosEmpleado::where("descripcion","=",$parametros['peticion']['estadoEmpleado'])->first();

        $persona->id_estado_empleado = $estado['id'];
        $idEstado= TRUE;
      }
      
      $persona->save();
      $mensaje ="Persona actualizada";
      //SI NO ES SOCIO NI CLIENTE
      //CREO ESTADO DE EMPLEADOS EN TABLA
      if($idEstado)
      {
        $cambioEstadoEmpleado = new CambiosEstadosEmpleados();
        $cambioEstadoEmpleado->id_persona = $persona->id;
        $cambioEstadoEmpleado->id_estado_empleado = $estado['id']; 
        $cambioEstadoEmpleado->id_socio = $userSocio['id'];
        $cambioEstadoEmpleado->fecha_registro = date("Y-m-d H:i:s");
        $cambioEstadoEmpleado->save();
  
      } 
    }

    

    $payload = json_encode(array("mensaje" => $mensaje));

    $response->getBody()->write($payload);
    return $response
      ->withHeader('Content-Type', 'application/json');

  }

  public function BorrarUno($request, $response, $args)
  {
    $parametros = $request->getParsedBody();
    $empleado = Persona::where("email", "=", $parametros['dataToken']->email)->first();
    $userEmpleado = Usuario::where("id_persona", "=", $empleado['id'])->first();
    $id = $args['id'];
    // Buscamos el id
    $item = Persona::where("id", "=", $id)->first();
    
    // Borramos si existe
    if(!is_null($item)){

      //SI NO ES SOCIO NI CLIENTE
      //CREO ESTADO DE EMPLEADOS EN TABLA

      if($item->ocupacion!= 6 && $item->ocupacion!=1){
        $cambioEstadoEmpleado = new CambiosEstadosEmpleados();
        $cambioEstadoEmpleado->id_persona = $id;
        $cambioEstadoEmpleado->id_estado_empleado = 3; //INACTIVO
        $cambioEstadoEmpleado->id_socio = $userEmpleado['id'];
        $cambioEstadoEmpleado->fecha_registro = date("Y-m-d H:i:s");
        $cambioEstadoEmpleado->save();
      }
      $item->id_estado_empleado = 3; //INACTIVO
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


}
