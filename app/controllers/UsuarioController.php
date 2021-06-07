<?php
require_once './models/Usuario.php';
require_once './models/Ocupacion.php';
require_once './models/Persona.php';
require_once './interfaces/IApiUsable.php';

use \App\Models\Usuario as Usuario;
use \App\Models\Persona as Persona;

class UsuarioController extends Usuario implements IApiUsable
{
    public function CargarUno($request, $response, $args)
    {
        $parametros = $request->getParsedBody();

        $usuario = $parametros['usuario'];
        $clave = $parametros['clave'];

        // Creamos el usuario
        $usr = new Usuario();
        $usr->usuario = $usuario;
        $usr->clave = $clave;
        $usr->save();

        $payload = json_encode(array("mensaje" => "Usuario creado con exito"));

        $response->getBody()->write($payload);
        return $response
          ->withHeader('Content-Type', 'application/json');
    }

    public function TraerUno($request, $response, $args)
    {
        // Buscamos usuario por nombre
        $usr = $args['usuario'];
        $usuario = Usuario::obtenerUsuario($usr);
        $payload = json_encode($usuario);

        $response->getBody()->write($payload);
        return $response
          ->withHeader('Content-Type', 'application/json');
    }

    public function TraerTodos($request, $response, $args)
    {
        $lista = Usuario::obtenerTodos();
        $payload = json_encode(array("listaUsuario" => $lista));

        $response->getBody()->write($payload);
        return $response
          ->withHeader('Content-Type', 'application/json');
    }
    
    public function ModificarUno($request, $response, $args)
    {
        $parametros = $request->getParsedBody();

        $nombre = $parametros['nombre'];
        Usuario::modificarUsuario($nombre);

        $payload = json_encode(array("mensaje" => "Usuario modificado con exito"));

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


    public static function Validar($email, $clave)
    {
      $idPersona = Persona::where('email','=',$email)->get('id');
      var_dump($idPersona);

      if(!is_null($idPersona) || !empty($idPersona))
      {
        $idUser = Usuario::where('id_persona','=',$idPersona[0]['id'])->where('clave','=',$clave)->get('id');
          
        if(!is_null($idUser) || !empty($idUser)){
          $mensaje = TRUE;
        }
        else{
          $mensaje= "Error, clave incorrecta";
        }
        
      }else{
        $mensaje = "Error, email no registrado";
      }
      
      return $mensaje;
      // $payload = json_encode(array("mensaje" => $mensaje));

      // $response->getBody()->write($payload);
      // return $response
      //   ->withHeader('Content-Type', 'application/json');
    }



}
