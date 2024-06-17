<?php

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Slim\Psr7\Response;

class UsuarioRolMiddleware
{
   
   
    public function __invoke(Request $request, RequestHandler $handler): Response
    {   
        $response = new Response();
        $parametros = $request->getParsedBody();

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

class UsuarioMozoMiddleware
{
   
    public function __invoke(Request $request, RequestHandler $handler): Response
	{
		$response = new Response();
        $header = $request->getHeaderLine('Authorization');
        $token = trim(explode("Bearer", $header)[1]);

  
        try {

            // $payload = json_decode(json_encode(array('datos' => AutentificadorJWT::ObtenerData($token))), true);
            $datos = array('datos' => AutentificadorJWT::ObtenerData($token));
            $rol = $datos['datos']->rol;
                    
            if (!strcasecmp($rol, "mozo")) {
                $response = $handler->handle($request);
            } else {
                $response->getBody()->write(json_encode(array("msg" => "Solo los mozos pueden realizar esta accion!".$rol)));
            }

        } catch (Exception $e) {

            $payload = json_encode(array('error' => $e->getMessage()));
            $response->getBody()->write($payload);
            
        }
	
        
        return $response->withHeader('Content-Type', 'application/json');
	}
}

class UsuarioLoginMiddleware
{
    public function __invoke(Request $request, RequestHandler $handler): Response
    {
        $response = new Response();
        $parametros = $request->getParsedBody();

        if (isset($parametros['id']) && isset($parametros['usuario']) && isset($parametros['clave'])) {
            
            $usuario = Usuario::obtenerUsuarioId($parametros['id']);
            
            if(!empty($usuario)){

                if (!strcasecmp($parametros['usuario'], $usuario->usuario) && password_verify($parametros['clave'], $usuario->clave)){

                    if($usuario->estado == 'activo'){
                        
                        $nombre = $usuario->usuario;
                        $rol = $usuario->rol;    
                        $token = AutentificadorJWT::CrearToken(array('usuario' => $nombre, 'rol' => $rol));
                        $payload = json_encode(array("mensaje" => "Logueado con exito como " . $usuario->usuario, "token"=> $token));
                        
    
                    }else{
    
                        $payload = json_encode(array("mensaje" => "Usuario inactivo"));
                    }
                }else{

                    $payload = json_encode(array("mensaje" => "Usuario o clave incorrecta"));


                }
                
                
            }else{
                $payload = json_encode(array("mensaje" => "Usuario con ID no existente"));
            }
            $response->getBody()->write($payload);

        } else {
            $response = new Response();
            $payload = json_encode(array("mensaje" => "Faltan campos para loguearse"));
            $response->getBody()->write($payload);
            return $response->withHeader('Content-Type', 'application/json');
        }

        return $response->withHeader('Content-Type', 'application/json');
    }
}

