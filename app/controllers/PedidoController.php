<?php
require_once './models/Pedido.php';
require_once './interfaces/IApiUsable.php';
require_once './models/Producto.php';
require_once './models/PedidoProductos.php';

class PedidoController extends Pedido implements IApiUsable
{
  

    public function CargarUno($request, $response, $args)
    {
        $parametros = $request->getParsedBody();
        $id_mesa = $parametros['id_mesa'];
        $id_producto = $parametros['id_producto'];
        $cliente = $parametros['cliente'];
        // $foto = $parametros['foto'];
        $fecha = date("Y-m-d");
        // $estado = $parametros['estado'];

        if(isset($parametros['id_pedido'])){

          $id_pedido = $parametros['id_pedido'];
          self::AgregarPedidoProducto($id_pedido, $id_producto);
          $mensaje = "Producto añadido al pedido existente con ID: $id_pedido";
          
        }else{
          $pedido = new Pedido();
          $pedido->id_mesa = $id_mesa;
          $pedido->cliente = $cliente;
          // $pedido->foto = $foto;
          $pedido->fecha = $fecha;


          $id_pedido = $pedido->crearPedido();
          self::AgregarPedidoProducto($id_pedido, $id_producto);
          Mesa::modificarMesa($id_mesa, 'esperando');
          $mensaje = "Pedido creado con éxito con ID: " . $id_pedido;
          //Modificar estado mesa

        }   

        $payload = json_encode(array("mensaje" => $mensaje));
        $response->getBody()->write($payload);
        return $response->withHeader('Content-Type', 'application/json');
    }

  private static function AgregarPedidoProducto($id_pedido, $id_producto)
	{
      $pedidoProd = new PedidoProductos();
      $pedidoProd->id_producto = $id_producto;
      $pedidoProd->id_pedido = $id_pedido;

      $pedidoProd->crearPedidoProducto();
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

    public function TraerSector($request, $response, $args)
    {
        $sector = $args['sector'];
        $listaPedidosSector = PedidoProductos::TraerPorSector($sector);
        $payload = json_encode(array("listaPedidosSector" => $listaPedidosSector));

        $response->getBody()->write($payload);
        return $response->withHeader('Content-Type', 'application/json');
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

    public function TomarProductoPedido($request, $response, $args)
    {
        $parametros = $request->getParsedBody();
        $id_pedido = $parametros['id_pedido'];
        $id_producto = $parametros['id_producto'];
        $tiempo = $parametros['tiempo'];

        PedidoProducto::PedidoProductoAsignar($idProducto, $idPedido);
        PedidoProductos::modificarTiempoProducto($id_producto,$id_pedido,$tiempo);

        $payload = json_encode(array("mensaje" => "Pedido modificado con exito"));

        $response->getBody()->write($payload);
        return $response->withHeader('Content-Type', 'application/json');
    }

}
