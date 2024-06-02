<?php
require_once './models/Pedido.php';
require_once './interfaces/IApiUsable.php';

class PedidoController extends Pedido implements IApiUsable
{
    public function CargarUno($request, $response, $args)
    {
        $parametros = $request->getParsedBody();

        $id_mesa = $parametros['id_mesa'];
        $id_usuario = $parametros['id_usuario'];
        $tiempoEstimado = $parametros['tiempoEstimado'];
        $tiempoFinal = $parametros['tiempoFinal'];
        // $foto = $parametros['foto'];
        $fecha = date("Y-m-d");
        $estado = $parametros['estado'];


        $pedido = new Pedido();
        $pedido->id_mesa = $id_mesa;
        $pedido->id_usuario = $id_usuario;
        $pedido->tiempoEstimado = $tiempoEstimado;
        $pedido->tiempoFinal = $tiempoFinal;
        $pedido->tiempoEstimado = $tiempoEstimado;
        // $pedido->foto = $foto;
        $pedido->fecha = $fecha;
        $pedido->estado = $estado;

        $pedido->crearProducto();

        $payload = json_encode(array("mensaje" => "Pedido creado con exito"));

        $response->getBody()->write($payload);
        return $response
          ->withHeader('Content-Type', 'application/json');
    }

    public function TraerUno($request, $response, $args)
    {
        $id = $args['id'];
        $producto = Pedido::obtenerPedido($id);
        $payload = json_encode($producto);

        $response->getBody()->write($payload);
        return $response
          ->withHeader('Content-Type', 'application/json');
    }

    public function TraerTodos($request, $response, $args)
    {
        $lista = Pedido::obtenerTodos();
        $payload = json_encode(array("listaPedidos" => $lista));

        $response->getBody()->write($payload);
        return $response
          ->withHeader('Content-Type', 'application/json');
    }
    
    public function ModificarUno($request, $response, $args)
    {
        $parametros = $request->getParsedBody();
        $id = $parametros['id'];
        $estado = $parametros['estado'];
        Pedido::modificarPedido($id, $estado);

        $payload = json_encode(array("mensaje" => "Pedido modificado con exito"));

        $response->getBody()->write($payload);
        return $response
          ->withHeader('Content-Type', 'application/json');
    }

}
