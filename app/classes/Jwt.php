<?php

use \App\Models\Ocupacion as Ocupacion;
use \App\Models\Persona as Persona;
require_once './middlewares/AutentificadorJWT.php';

class Jwt 
{
  public static function CrearToken($email, $perfil)
  {
    $datos = array('email' => $email, 'perfil' => $perfil);
    $token = AutentificadorJWT::CrearToken($datos);
    return $token;
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
  

  public static function VerificarToken ($request, $response)
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