<?php
require_once './models/Pedido.php';
require_once './interfaces/IApiUsable.php';
require_once './models/Producto.php';

class PedidoController extends Pedido implements IApiUsable
{
    public function CargarUno($request, $response, $args)
    {
        $parametros = $request->getParsedBody();

        $id_mesa = $parametros['id_mesa'];
        $cliente = $parametros['cliente'];
        $tiempo = $parametros['tiempo'];
        // $foto = $parametros['foto'];
        $fecha = date("Y-m-d");
        $estado = $parametros['estado'];


        $pedido = new Pedido();
        $pedido->id_mesa = $id_mesa;
        $pedido->cliente = $cliente;
        $pedido->tiempo = $tiempo;
        // $pedido->foto = $foto;
        $pedido->fecha = $fecha;
        $pedido->estado = $estado;

        $test = $pedido->crearProducto();


        $payload = json_encode(array("mensaje" => "Pedido creado con exito con ID: ". $test));

        $response->getBody()->write($payload);
        return $response
          ->withHeader('Content-Type', 'application/json');
    }

    public function CargarPedido($request, $response, $args)
    {
        $parametros = $request->getParsedBody();
        
        $id_mesa = $parametros['id_mesa'];
        $cliente = $parametros['cliente'];
        $tiempo = $parametros['tiempo'];
        // $foto = $parametros['foto'];
        $fecha = date("Y-m-d");
        $estado = $parametros['estado'];

        if(isset($_POST['id_pedido'])){
          
        }


        $pedido = new Pedido();
        $pedido->id_mesa = $id_mesa;
        $pedido->cliente = $cliente;
        $pedido->tiempo = $tiempo;
        // $pedido->foto = $foto;
        $pedido->fecha = $fecha;
        $pedido->estado = $estado;

        $test = $pedido->crearProducto();


        $payload = json_encode(array("mensaje" => "Pedido creado con exito con ID: ". $test));

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
