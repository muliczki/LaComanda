<?php

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Slim\Psr7\Response;

require_once './classes/JWT.php';

class AccesosMD
{
    public function VerificarUsuario(Request $request, RequestHandler $handler)
    {
        $response = new Response();
        $header = $request->getHeaderLine('Authorization');
        $token ="";

        if($header != ""){
            $token = trim(explode("Bearer", $header)[1]);
        }
        $esValido = false;

        try {
        AutentificadorJWT::verificarToken($token);
        $esValido = true;
        $payload="";
        $data = AutentificadorJWT::ObtenerData($token);
        $request = $request->withParsedBody(array("peticion" => $request->getParsedBody(), "dataToken" => $data));
        } catch (Exception $e) {
        $payload = json_encode(array('error' => $e->getMessage()));
        }

        if ($esValido) {
            $response = $handler->handle($request);
            // $payload = json_encode(array('valid' => $esValido));
        }

        $response->getBody()->write($payload);
        return $response;
    }


    public function VerificarPerfilSocio(Request $request, RequestHandler $handler)
    {
        $response = new Response();
        
        $parametros = $request->getParsedBody();
        
        try {
        if($parametros['dataToken']->perfil=='SOCIO')
        {
            $payload="";
            $response = $handler->handle($request);
        }else{
            $payload = json_encode(array('detalle' => 'Perfil no autorizado!'));
        }
        } catch (Exception $e) {
            $payload = json_encode(array('error' => $e->getMessage()));
        }
        
        $response->getBody()->write($payload);
        return $response;
    }

    public function VerificarPerfilMozo(Request $request, RequestHandler $handler)
    {
        $response = new Response();
        
        $parametros = $request->getParsedBody();
        
        try {
        if($parametros['dataToken']->perfil=='MOZO')
        {
            $payload="";
            $response = $handler->handle($request);
        }else{
            $payload = json_encode(array('detalle' => 'Perfil no autorizado!'));
        }
        } catch (Exception $e) {
            $payload = json_encode(array('error' => $e->getMessage()));
        }
        
        $response->getBody()->write($payload);
        return $response;
    }

    public function VerificarPerfilSocioMozo(Request $request, RequestHandler $handler)
    {
        $response = new Response();
        
        $parametros = $request->getParsedBody();
        
        try {
        if($parametros['dataToken']->perfil=='MOZO'|| $parametros['dataToken']->perfil=='SOCIO' )
        {
            $payload="";
            $response = $handler->handle($request);
        }else{
            $payload = json_encode(array('detalle' => 'Perfil no autorizado!'));
        }
        } catch (Exception $e) {
            $payload = json_encode(array('error' => $e->getMessage()));
        }
        
        $response->getBody()->write($payload);
        return $response;
    }

    public function VerificarPerfilNoSocio(Request $request, RequestHandler $handler)
    {
        $response = new Response();
        
        $parametros = $request->getParsedBody();
        
        try {
        if($parametros['dataToken']->perfil!='SOCIO')
        {
            $payload="";
            $response = $handler->handle($request);
        }else{
            $payload = json_encode(array('detalle' => 'Perfil no autorizado!'));
        }
        } catch (Exception $e) {
            $payload = json_encode(array('error' => $e->getMessage()));
        }
        
        $response->getBody()->write($payload);
        return $response;
    }
}

?>