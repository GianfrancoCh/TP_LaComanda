<?php

include_once(__DIR__ . '/../db/AccesoDatos.php');
class PedidoProductos
{
    
    public $id_pedido;
	public $id_producto;
    public $id_mesa;
    public $tiempo;
    public $estado;

    public function crearPedidoProducto()
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("INSERT INTO pedidos_productos (id_pedido,id_producto,id_mesa,tiempo,estado)
         VALUES (:id_pedido, :id_producto, :id_mesa, :tiempo, :estado)");
        $consulta->bindValue(':id_pedido', $this->id_pedido, PDO::PARAM_INT);
        $consulta->bindValue(':id_producto', $this->id_producto, PDO::PARAM_INT);
        $consulta->bindValue(':id_mesa', $this->id_mesa, PDO::PARAM_INT);
        $consulta->bindValue(':tiempo', $this->tiempo, PDO::PARAM_INT);
        $consulta->bindValue(':estado', $this->estado, PDO::PARAM_STR);

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

}