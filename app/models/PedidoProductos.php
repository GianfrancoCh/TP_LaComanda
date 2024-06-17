<?php

include_once(__DIR__ . '/../db/AccesoDatos.php');
class PedidoProductos
{
    
    public $id_pedido;
	public $id_producto;
    public $responsable;
    public $tiempo;
    public $estado;

    // public $id;
    // public $nombre;

    // public $tipo;
    // public $precio;

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

    public static function modificarEstadoProducto($id_pedido, $id_producto, $estado)
    {
        $objAccesoDato = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDato->prepararConsulta("UPDATE pedidos_productos SET estado = :estado WHERE id_pedido = :id_pedido AND id_producto = :id_producto");
        $consulta->bindValue(':id_pedido', $id_pedido, PDO::PARAM_INT);
        $consulta->bindValue(':id_producto', $id_producto, PDO::PARAM_INT);
        $consulta->bindValue(':estado', $estado, PDO::PARAM_STR);
        $consulta->execute();
        return $consulta->fetchAll(PDO::FETCH_CLASS, 'PedidoProductos');
    }

    public static function TraerPorSector($sector)
	{
		$objAccesoDatos = AccesoDatos::ObtenerInstancia();
		$req = $objAccesoDatos->PrepararConsulta("SELECT * FROM pedidos_productos JOIN productos ON pedidos_productos.id_producto = productos.id WHERE pedidos_productos.estado = 'pendiente' AND productos.tipo = :sector");
		$req->bindValue(':sector', $sector, PDO::PARAM_STR);
		$req->execute();

		// return $req->fetchAll(PDO::FETCH_CLASS, 'PedidoProductos');
        return $req->fetchAll(PDO::FETCH_OBJ);
	}
    
  
}