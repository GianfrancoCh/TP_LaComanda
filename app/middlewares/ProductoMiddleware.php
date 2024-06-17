<?php

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Slim\Psr7\Response;

class ProductoIdMiddleware
{	public function __invoke(Request $request, RequestHandler $handler): Response
	{
		$response = new Response();
		$params = $request->getParsedBody();
		$ids = Producto::obtenerTodosId();
		if (isset($params['id_producto'])) {
			if (in_array($params['id_producto'], $ids)) {
				$response = $handler->handle($request);
			} else {
				$payload = json_encode(array("mensaje" => "No existe Producto con es ID"));
                $response->getBody()->write($payload);
			}
		} else {
			$payload = json_encode(array("mensaje" => "Falta ID Producto"));
            $response->getBody()->write($payload);
		}


		return $response->withHeader('Content-Type', 'application/json');
	}

}
