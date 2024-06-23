<?php

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Slim\Psr7\Response;

class MesaIdMiddleware
{	public function __invoke(Request $request, RequestHandler $handler): Response
	{
		$response = new Response();
		$params = $request->getParsedBody();
		$ids = Mesa::obtenerTodosId();
		if (isset($params['id_mesa'])) {
			if (in_array($params['id_mesa'], $ids)) {
				$mesa = Mesa::obtenerMesa($params['id_mesa']);
				if($mesa->estado == 'vacia' || $mesa->estado == 'esperando' || $mesa->estado == 'comiendo'){
					$response = $handler->handle($request);

				}else{
					$payload = json_encode(array("mensaje" => "La mesa esta cerrada o pagando"));
                	$response->getBody()->write($payload);
				}
				
			} else {
				$payload = json_encode(array("mensaje" => "No existe Mesa con es ID"));
                $response->getBody()->write($payload);
			}
		} else {
			$payload = json_encode(array("mensaje" => "Falta ID Mesa"));
            $response->getBody()->write($payload);
		}
		return $response->withHeader('Content-Type', 'application/json');
	}
}

class MesaParametrosMiddleware
{	public function __invoke(Request $request, RequestHandler $handler): Response
	{
		$response = new Response();
		$params = $request->getParsedBody();
		if (isset($params['estado'])) {

			$estado = $params['estado'];
			if ($estado === 'vacio' || $estado === 'esperando' || $estado === 'estado' || $estado === 'pagando' || $estado === 'cerrada') {
				$response = $handler->handle($request);
			} else {
				$payload = json_encode(array("mensaje" => "Estado incorrecto"));
                $response->getBody()->write($payload);
			}
		} else {
			$payload = json_encode(array("mensaje" => "Falta el estado de la mesa"));
            $response->getBody()->write($payload);
		}
		return $response->withHeader('Content-Type', 'application/json');
	}

}
