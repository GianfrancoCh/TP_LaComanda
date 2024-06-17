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
				$response = $handler->handle($request);
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
