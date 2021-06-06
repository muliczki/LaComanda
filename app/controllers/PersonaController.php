<?php
require_once './models/Persona.php';
require_once './models/Sector.php';
require_once './models/ConsultasPDO.php';
require_once './interfaces/IApiUsable.php';

use \App\Models\Persona as Persona;
use \App\Models\Sector as Sector;

//sacar la herencia del modelo!
class PersonaController implements IApiUsable
{
  public function CargarUno($request, $response, $args)
  {
    $parametros = $request->getParsedBody();

    $nombre = $parametros['nombre'];
    $apellido = $parametros['apellido'];
    $email = $parametros['email'];
    $idOcupacion = ConsultasPdo::TraerIdOcupacion($parametros['descOcupacion']);
    
    // Creamos la Persona
    $persona = new Persona();
    $persona->nombre = $nombre;
    $persona->apellido = $apellido;
    $persona->email = $email;
    $persona->id_ocupacion = $idOcupacion;

    if($idOcupacion!= 6 && $idOcupacion!=1 )//NO ES SOCIO NI CLIENTE
    {
        $idEstado = 1; // 1 = ACTIVO EN BD
        $persona->id_estado_empleado = $idEstado;

        if($idOcupacion!= 5) //NO ES MOZO, MOZO NO TIENE SECTOR
        {
          //ver
          // $id = Sector::where('sector', $parametros['descSector'])->first();

          // var_dump($idSector);
            // $idSector = $t->id;
          
          $idSector = ConsultasPdo::TraerIdSector($parametros['descSector']);
          $persona->id_sector = $idSector;
        }
    }


    $persona->save();

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

  }

  public function BorrarUno($request, $response, $args)
  {
  }


}
