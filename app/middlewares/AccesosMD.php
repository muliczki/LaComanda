<?php

use App\Models\Pedido;
use App\Models\Persona;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Slim\Psr7\Response;

require_once './classes/Jwt.php';

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

    public function VerificarCliente(Request $request, RequestHandler $handler)
    {
        $response = new Response();
        $parametros = $request->getParsedBody();

        $emailCliente = $parametros['emailCliente'];
        $codPedido = $parametros['codPedido'];

        $cliente = Persona::where("email", "=", $emailCliente)->first();
        $pedido = Pedido::where("codigo_pedido", "=", $codPedido)->first();

        try {
            if( !is_null($cliente) || !is_null($pedido && $pedido['id_cliente'] == $cliente['id']))
            {
                $payload="";
                $response = $handler->handle($request);
            }else{
                $payload = json_encode(array('detalle' => 'Cliente o pedido inexistente!'));
            }
        } catch (Exception $e) {
            $payload = json_encode(array('error' => $e->getMessage()));
        }
        
        $response->getBody()->write($payload);
        return $response;
    }
}

?>