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

        $producto = Producto::obtenerProductoPorId($id_producto);
        $precio = $producto->precio;

        if(isset($parametros['id_pedido'])){

          $id_pedido = $parametros['id_pedido'];
          $pedido = Pedido::obtenerPedido($id_pedido);
          $nuevoPrecio = $pedido->precio + $producto->precio;
          Pedido::modificarPrecioPedido($id_pedido,$nuevoPrecio);
          self::AgregarPedidoProducto($id_pedido, $id_producto);
          $mensaje = "Producto añadido al pedido existente con ID: $id_pedido";
          
        }else{
          $pedido = new Pedido();
          $pedido->id_mesa = $id_mesa;
          $pedido->cliente = $cliente;
          // $pedido->foto = $foto;
          $pedido->fecha = $fecha;
          $pedido->precio = $precio;

          $id_pedido = $pedido->crearPedido();
          self::AgregarPedidoProducto($id_pedido, $id_producto);
          Mesa::modificarMesa($id_mesa, 'esperando');
          $mensaje = "Pedido creado con éxito con ID: " . $id_pedido;

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

        $header = $request->getHeaderLine('Authorization');
        $token = trim(explode("Bearer", $header)[1]);

        $parametros = $request->getParsedBody();
        $id_pedido = $parametros['id_pedido'];
        $id_producto = $parametros['id_producto'];
        $tiempoEstimado = $parametros['tiempo'];

        $datos = array('datos' => AutentificadorJWT::ObtenerData($token));
        $empleado = $datos['datos']->usuario;
        $estado = "preparacion";

        $pedido = Pedido::obtenerPedido($id_pedido);

        if($pedido->tiempoEstimado == null || $pedido->tiempoEstimado < $tiempoEstimado){
          Pedido::modificarTiempoEstimadoPedido($id_pedido, $tiempoEstimado);
        }

        PedidoProductos::asignarProductoEmpleado($id_producto,$id_pedido,$empleado);
        PedidoProductos::modificarTiempoEstimadoProducto($id_producto,$id_pedido,$tiempoEstimado);
        PedidoProductos::modificarEstadoProducto($id_producto,$id_pedido,$estado);

        

        $payload = json_encode(array("mensaje" => "Producto tomado por " . $empleado . ". Tiempo estimado ".$tiempoEstimado));

        $response->getBody()->write($payload);
        return $response->withHeader('Content-Type', 'application/json');
    }

    public function ListoProductoPedido($request, $response, $args)
    {

        $header = $request->getHeaderLine('Authorization');
        $token = trim(explode("Bearer", $header)[1]);

        $parametros = $request->getParsedBody();
        $id_pedido = $parametros['id_pedido'];
        $id_producto = $parametros['id_producto'];
        $tiempoFinal = $parametros['tiempo'];

        $datos = array('datos' => AutentificadorJWT::ObtenerData($token));
        $empleado = $datos['datos']->usuario;
        $estado = "listo";

        

        PedidoProductos::modificarEstadoProducto($id_producto,$id_pedido,$estado);
        PedidoProductos::modificarTiempoFinalProducto($id_producto,$id_pedido,$tiempoFinal);
        

        if (PedidoProductos::obtenerCantidadProductosPendientes($id_pedido) == 0) {

          $pedido = Pedido::obtenerPedido($id_pedido);
          $tiempoFinalMayorProductos = PedidoProductos::obtenerTiempoFinalMayorPedidoProductos($id_pedido);
          if($pedido->tiempoFinal == null || $pedido->tiempoFinal < $tiempoFinalMayorProductos){
            Pedido::modificarTiempoFinalPedido($id_pedido, $tiempoFinalMayorProductos);
          }

					Pedido::modificarPedido($id_pedido, 'listo');
					$payload = json_encode(array("msg" => "Pedido " . $id_pedido." listo!"));
          
					$pedido = Pedido::obtenerPedido($id_pedido);
					// Mesa::modificarMesa($pedido->id_mesa, 'comiendo');
				}else{

          $payload = json_encode(array("mensaje" => "Producto marcado como listo por " . $empleado));
        }
        
        $response->getBody()->write($payload);
        return $response->withHeader('Content-Type', 'application/json');
    }

    public function CobrarPedido($request, $response, $args)
    {

        $payload = json_encode(array("mensaje" => "Cobrar pedido"));
        
        $response->getBody()->write($payload);
        return $response->withHeader('Content-Type', 'application/json');
    }

}
