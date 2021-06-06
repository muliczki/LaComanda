<?php

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Slim\Psr7\Response;

require_once './models/ConsultasPDO.php';
require_once './models/Usuario.php';
// require_once './models/JwtTokens.php';

class Logger
{
    public static function LogOperacion($request, $handler)
    {
        $response = new Response();
        if($request->getMethod() == "GET")
        {
            $response = $handler->handle($request);
            // $response->getBody()->write("<p>NO necesita credenciales para los get</p>");
        }else{

            $parametros = $request->getParsedBody();
            $email = $parametros['email'];
            $clave = $parametros['clave'];
            
            //agregar validaciones
            $idUsuario = ConsultasPdo::TraerIdPersona($email);
            if($idUsuario!=0)
            {
                $idOcupacion = ConsultasPdo::TraerIdOcupacionPersona($idUsuario);
                $ocupacion = ConsultasPdo::TraerDescOcupacion($idOcupacion);

                $usuario = Usuario::obtenerUsuario($idUsuario);

                if($usuario->clave == $clave) //USER EXISTE
                {
                    $response = JwtTokens::CrearToken($response, $clave, $email, $ocupacion );
                    //var_dump($response);
                    $response = $handler->handle($request);

                }else{
                    echo "Error, clave incorrecta";
                }
            
            }else{
                echo "Error, email no registrado";
            }
    
            
        }

        return $response;
    }

    public static function Verificar($request, $handler)
    {
        $response = new Response();

        $response = JwtTokens::VerificarToken($request, $response);
        
        return $response;
    }
}