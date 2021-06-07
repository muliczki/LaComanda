<?php

use \App\Models\Ocupacion as Ocupacion;
use \App\Models\Persona as Persona;

class Jwt 
{
  public function CrearToken($request, $response, $args)
  {
    $parametros = $request->getParsedBody();

    $email = $parametros['email'];
    $clave = $parametros['clave'];

    $login = UsuarioController::Validar($email, $clave);

    if($login == TRUE){
      $idOcupacion = Persona::where('email','=',$email)->get('id_ocupacion')[0]['id_ocupacion'];
      $ocupacion = Ocupacion::where('id','=',$idOcupacion)->get('ocupacion')[0]['ocupacion'];
      
      $datos = array('email' => $email, 'ocupacion' => $ocupacion);
      $token = AutentificadorJWT::CrearToken($datos);
      $payload = json_encode(array('jwt' => $token));

    }else{
      $payload = json_encode(array('mensaje' => $login));
    }

    $response->getBody()->write($payload);
    return $response
      ->withHeader('Content-Type', 'application/json');

  }

  public function DevolverPayload ($request, $response, $args)
  {
    $header = $request->getHeaderLine('Authorization');
    $token = trim(explode("Bearer", $header)[1]);

    try {
    $payload = json_encode(array('payload' => AutentificadorJWT::ObtenerPayLoad($token)));
    } catch (Exception $e) {
    $payload = json_encode(array('error' => $e->getMessage()));
    }

    $response->getBody()->write($payload);
    return $response
    ->withHeader('Content-Type', 'application/json');
  }

  public function DevolverDatos ($request, $response, $args)
  {
    $header = $request->getHeaderLine('Authorization');
    $token = trim(explode("Bearer", $header)[1]);

    try {
      $payload = json_encode(array('datos' => AutentificadorJWT::ObtenerData($token)));
    } catch (Exception $e) {
      $payload = json_encode(array('error' => $e->getMessage()));
    }

    $response->getBody()->write($payload);
    return $response
      ->withHeader('Content-Type', 'application/json');
  }
  

  public function VerificarToken ($request, $response, $args)
  {
    $header = $request->getHeaderLine('Authorization');
    $token = trim(explode("Bearer", $header)[1]);
    $esValido = false;

    try {
    AutentificadorJWT::verificarToken($token);
    $esValido = true;
    } catch (Exception $e) {
    $payload = json_encode(array('error' => $e->getMessage()));
    }

    if ($esValido) {
    $payload = json_encode(array('valid' => $esValido));
    }

    $response->getBody()->write($payload);
    return $response
        ->withHeader('Content-Type', 'application/json');
  }


}



?>