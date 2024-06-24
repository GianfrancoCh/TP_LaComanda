<?php

include_once(__DIR__ . '/../db/AccesoDatos.php');
class PedidoProductos
{
    
    public $id_pedido;
	public $id_producto;
    public $responsable;
    public $tiempoFinal;

    public $tiempoEstimado;
    public $estado;

    public function crearPedidoProducto()
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("INSERT INTO pedidos_productos (id_pedido,id_producto,estado) VALUES (:id_pedido, :id_producto, 'pendiente')");
        $consulta->bindValue(':id_pedido', $this->id_pedido, PDO::PARAM_INT);
        $consulta->bindValue(':id_producto', $this->id_producto, PDO::PARAM_INT);

        $consulta->execute();

        return $objAccesoDatos->obtenerUltimoId();
    }
    public static function TraerTodosLosProductosPedidos()
    {
        $objetoAccesoDato = AccesoDatos::obtenerInstancia();
        $consulta = $objetoAccesoDato->prepararConsulta("select * from pedidos_productos ");
        $consulta->execute();
        return $consulta->fetchAll(PDO::FETCH_CLASS, "PedidoProductos");
    }

    public static function ObtenerProductosPorPedido($id_pedido)
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT * FROM pedidos_productos WHERE id_pedido = :id_pedido");
        $consulta->bindValue(':id_producto', $id_pedido, PDO::PARAM_INT);
        $consulta->execute();

        return $consulta->fetchObject('PedidoProductos');
    }

    
    public static function TraerPorSector($sector)
	{
        $objAccesoDatos = AccesoDatos::ObtenerInstancia();
		$consulta = $objAccesoDatos->PrepararConsulta("SELECT * FROM pedidos_productos JOIN productos ON pedidos_productos.id_producto = productos.id WHERE pedidos_productos.estado = 'pendiente' AND productos.tipo = :sector");
		$consulta->bindValue(':sector', $sector, PDO::PARAM_STR);
		$consulta->execute();
        
		// return $req->fetchAll(PDO::FETCH_CLASS, 'PedidoProductos');
        return $consulta->fetchAll(PDO::FETCH_OBJ);
	}
    
    public static function BuscarProductoEnPedido($id_producto, $id_pedido)
	{
        $objAccesoDatos = AccesoDatos::ObtenerInstancia();
		$consulta = $objAccesoDatos->PrepararConsulta("SELECT * from pedidos_productos WHERE id_producto=:id_producto AND id_pedido=:id_pedido");
		$consulta->bindValue(':id_producto', $id_producto, PDO::PARAM_INT);
		$consulta->bindValue(':id_pedido', $id_pedido, PDO::PARAM_STR);
		$consulta->execute();
        
		return $consulta->fetchAll(PDO::FETCH_CLASS, 'PedidoProductos');
	}
    public static function modificarEstadoProducto($id_producto,$id_pedido, $estado)
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("UPDATE pedidos_productos SET estado = :estado WHERE id_pedido = :id_pedido AND id_producto = :id_producto");
        $consulta->bindValue(':id_pedido', $id_pedido, PDO::PARAM_INT);
        $consulta->bindValue(':id_producto', $id_producto, PDO::PARAM_INT);
        $consulta->bindValue(':estado', $estado, PDO::PARAM_STR);
        $consulta->execute();
        return $objAccesoDatos->obtenerUltimoId();
    }


    public static function modificarTiempoEstimadoProducto($id_producto, $id_pedido, $tiempoEstimado)
	{
		$objAccesoDatos = AccesoDatos::ObtenerInstancia();
		$consulta = $objAccesoDatos->PrepararConsulta("UPDATE pedidos_productos SET tiempoEstimado=:tiempoEstimado WHERE id_producto=:id_producto AND id_pedido=:id_pedido");
		$consulta->bindValue(':id_producto', $id_producto, PDO::PARAM_INT);
		$consulta->bindValue(':id_pedido', $id_pedido, PDO::PARAM_INT);
		$consulta->bindValue(':tiempoEstimado', $tiempoEstimado, PDO::PARAM_INT);
		$consulta->execute();

		return $objAccesoDatos->obtenerUltimoId();
	}

    public static function modificarTiempoFinalProducto($id_producto, $id_pedido, $tiempoFinal)
	{
		$objAccesoDatos = AccesoDatos::ObtenerInstancia();
		$consulta = $objAccesoDatos->PrepararConsulta("UPDATE pedidos_productos SET tiempoFinal=:tiempoFinal WHERE id_producto=:id_producto AND id_pedido=:id_pedido");
		$consulta->bindValue(':id_producto', $id_producto, PDO::PARAM_INT);
		$consulta->bindValue(':id_pedido', $id_pedido, PDO::PARAM_INT);
		$consulta->bindValue(':tiempoFinal', $tiempoFinal, PDO::PARAM_INT);
		$consulta->execute();

		return $objAccesoDatos->obtenerUltimoId();
	}

    public static function asignarProductoEmpleado($id_producto, $id_pedido, $empleado)
	{
		$objAccesoDatos = AccesoDatos::ObtenerInstancia();
		$consulta = $objAccesoDatos->PrepararConsulta("UPDATE pedidos_productos SET responsable=:empleado WHERE id_producto=:id_producto AND id_pedido=:id_pedido");
		$consulta->bindValue(':id_producto', $id_producto, PDO::PARAM_INT);
		$consulta->bindValue(':id_pedido', $id_pedido, PDO::PARAM_INT);
		$consulta->bindValue(':empleado', $empleado, PDO::PARAM_STR);
		$consulta->execute();

		return $objAccesoDatos->obtenerUltimoId();
	}
    
    public static function obtenerCantidadProductosPendientes($id_pedido)
	{
		$objAccesoDatos = AccesoDatos::ObtenerInstancia();

		$consulta = $objAccesoDatos->PrepararConsulta("SELECT COUNT(*) FROM pedidos_productos WHERE id_pedido=:id_pedido AND estado IN ('pendiente', 'preparacion')");
		$consulta->bindValue(':id_pedido', $id_pedido, PDO::PARAM_INT);
		$consulta->execute();

		return $consulta->fetchColumn();
	}

    public static function obtenerTiempoFinalMayorPedidoProductos($id_pedido)
	{
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT MAX(tiempoFinal) AS maxTiempoFinal FROM pedidos_productos WHERE id_pedido = :id_pedido");
        $consulta->bindValue(':id_pedido', $id_pedido, PDO::PARAM_INT);
        $consulta->execute();

        $resultado = $consulta->fetch(PDO::FETCH_OBJ);

        return $resultado->maxTiempoFinal;
	}
  
}