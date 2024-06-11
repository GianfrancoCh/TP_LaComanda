<?php

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Slim\Psr7\Response;

class UsuarioRolMiddlware
{
   
   
    public function __invoke(Request $request, RequestHandler $handler): Response
    {   
        $response = new Response();
        $parametros = $request->getQueryParams();

        if(isset($parametros['rol'])){
            
            $rol = $parametros['rol'];

            if ($rol === 'socio' || $rol === 'mozo' || $rol === 'cocinero' || $rol === 'cervecero' || $rol === 'bartender') {
                $response = $handler->handle($request);
            } else {
                
                $payload = json_encode(array("mensaje" => "Rol incorrecto"));
                $response->getBody()->write($payload);
            }
        }else{
            $payload = json_encode(array("mensaje" => "Rol no especificado"));
            $response->getBody()->write($payload);
            return $response->withHeader('Content-Type', 'application/json');
        }

        return $response->withHeader('Content-Type', 'application/json');
    }
}