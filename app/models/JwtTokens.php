<?php

require_once '../vendor/autoload.php';

use Firebase\JWT\JWT;

class JwtTokens{


    public static function CrearToken($response, $clave, $email, $ocupacion)
    {
        $ahora = time();

        $datos = array (
            'email' => $email,
            'clave' => $clave,
            'ocupacion' => $ocupacion
        );

        //PARAMETROS DEL PAYLOAD
        $payload = array(
            'iat'=> $ahora,
            'exp' => $ahora + (50),
            'data' => $datos,
            'app' => "La Comanda - Uliczki Micaela"
        );

        //CREO TOKEN - CODIFICO A JWT (PAYLOAD, CLAVE, ALGORITMO DE CODIFICACION)
        $token = JWT::encode($payload, "TodoRojo", "HS256");

        $newResponse = $response->withStatus(200,"Exito! Token generado.");

        //GENERO JSON A PARTIR DEL ARRAY DEL TOKEN
        $newResponse->getBody()->write(json_encode($token));

        //INDICO CONTENIDO QUE SE RETORNARA EN EL HEADER
        return $newResponse
            ->withHeader('Content-Type', 'application/json');

    }

    public static function VerificarToken($request, $response)
    {
        $datos = $request->getParsedBody();
        $token = $datos['token'];
        var_dump($token);

        $retorno = new stdClass();
        $status = 200;

        try{
            // DECODIFICAR TOKEN RECIBIDO

            JWT::decode(
                $token, 
                "TodoRojo",
                ['HS256']
            );
            $retorno->mensaje="TOKEN OK";
        }catch(Exception $e){
            
            $retorno->mensaje="TOKEN NO VALIDO --->". $e->getMessage();
            $status = 500;
        }

        $newResponse = $response->withStatus($status);
        $newResponse->getBody()->write(json_encode($retorno));

        //INDICO CONTENIDO QUE SE RETORNARA EN EL HEADER
        return $newResponse
            ->withHeader('Content-Type', 'application/json');
    }
}

?>