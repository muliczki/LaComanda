<?php
require_once './models/Usuario.php';
require_once './models/Ocupacion.php';
require_once './models/Persona.php';
require_once './models/LoginUsers.php';
require_once './interfaces/IApiUsable.php';

use \App\Models\Ocupacion as Ocupacion;
use \App\Models\Usuario as Usuario;
use \App\Models\Persona as Persona;
use \App\Models\LoginUsers as LoginUsers;

class UsuarioController implements IApiUsable
{
  public function CargarUno($request, $response, $args)
  {
    $parametros = $request->getParsedBody();

    $email = $parametros['email'];
    $clave = $parametros['clave'];

    $persona = Persona::where("email", "=", $email)->first();

    if(!is_null($persona)){

      // Creamos el usuario
      $usr = new Usuario();
      $usr->id_persona = $persona['id'];
      $usr->clave = $clave;
      $usr->save();

      $mensaje ="Usuario creado con exito";
    }else{
      $mensaje = "El email no corresponde a una persona registrada";
    }


    $payload = json_encode(array("mensaje" => $mensaje));

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


  // public static function Validar($email, $clave)
  // {
  //   $idPersona = Persona::where('email','=',$email)->get('id');
  //   var_dump($idPersona);
  //   //first

  //   if(!is_null($idPersona) || !empty($idPersona))
  //   {
  //     $idUser = Usuario::where('id_persona','=',$idPersona[0]['id'])->where('clave','=',$clave)->get('id');
        
  //     if(!is_null($idUser) || !empty($idUser)){
  //       $mensaje = TRUE;
  //     }
  //     else{
  //       $mensaje= "Error, clave incorrecta";
  //     }
      
  //   }else{
  //     $mensaje = "Error, email no registrado";
  //   }
    
  //   return $mensaje;
  //   // $payload = json_encode(array("mensaje" => $mensaje));

  //   // $response->getBody()->write($payload);
  //   // return $response
  //   //   ->withHeader('Content-Type', 'application/json');
  // }

  public function Login($request, $response, $args)
  {
    $mensaje = "Login error! Datos incorrectos. Reintentar!";
    $parametros = $request->getParsedBody();

    $email = $parametros['email'];
    $clave = $parametros['clave'];
    
    $persona = Persona::where('email','=',$email)->first();
    // var_dump($persona);
    
    if(!is_null($persona))
    {
      $user = Usuario::where('id_persona','=',$persona['id'])->where('clave','=',$clave)->first();
      if(!is_null($user))
      {
        $perfil = Ocupacion::where('id','=',$persona['id_ocupacion'])->first();
        $mensaje = 'OK';
        $token = Jwt::CrearToken($email, $perfil['ocupacion']);
      
        //guardo registro en tabla login
        $login = new LoginUsers();
        $login->id_usuario = $user['id'];
        $login->id_ocupacion = $persona['id_ocupacion'];
        $login->fecha_conexion = date ("Y-m-d H:i:s");
        $login->save();

        $payload = json_encode(array("mensaje" => $mensaje, "jwt" => $token));
      }else{
        $payload = json_encode(array("mensaje" => $mensaje));
      }
      
      
    }else{
      $payload = json_encode(array("mensaje" => $mensaje));
    }

    $response->getBody()->write($payload);
    return $response
      ->withHeader('Content-Type', 'application/json');
  }



}
