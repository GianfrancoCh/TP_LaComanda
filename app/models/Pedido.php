<?php

include_once(__DIR__ . '/../db/AccesoDatos.php');
class Pedido
{
    
	public $id;
    public $id_mesa;
    public $cliente;
    public $tiempoEstimado;
    public $tiempoFinal;
    public $precio;
    public $foto;
    public $fecha;
    public $estado; 

    public function crearPedido()
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("INSERT INTO pedidos (id, id_mesa,cliente,precio,fecha,estado)
         VALUES (:id, :id_mesa, :cliente,:precio, :fecha, 'pendiente')");
        $consulta->bindValue(':id', $this->id, PDO::PARAM_STR);
        $consulta->bindValue(':id_mesa', $this->id_mesa, PDO::PARAM_INT);
        $consulta->bindValue(':cliente', $this->cliente, PDO::PARAM_STR);
        $consulta->bindValue(':precio', $this->precio, PDO::PARAM_INT);
        $consulta->bindValue(':fecha', $this->fecha, PDO::PARAM_STR);

        $consulta->execute();

        return $objAccesoDatos->obtenerUltimoId();
    }
    public static function TraerTodosLosProductos()
    {
        $objetoAccesoDato = AccesoDatos::obtenerInstancia();
        $consulta = $objetoAccesoDato->prepararConsulta("select * from pedidos");
        $consulta->execute();
        return $consulta->fetchAll(PDO::FETCH_CLASS, "pedido");
    }

    public static function obtenerTodos()
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT * FROM pedidos");
        $consulta->execute();

        return $consulta->fetchAll(PDO::FETCH_CLASS, 'pedido');
    }

    public static function obtenerTodosId()
	{
		$objAccesoDatos = AccesoDatos::ObtenerInstancia();
		$consulta = $objAccesoDatos->PrepararConsulta("SELECT id FROM pedidos");
		$consulta->execute();
		return $consulta->fetchAll(PDO::FETCH_COLUMN);
	}
    public static function obtenerPedido($id)
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT * FROM pedidos WHERE id = :id");
        $consulta->bindValue(':id', $id, PDO::PARAM_INT);
        $consulta->execute();

        return $consulta->fetchObject('pedido');
    }

    public static function modificarPedido($id, $estado)
    {
        $objAccesoDato = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDato->prepararConsulta("UPDATE pedidos SET estado = :estado WHERE id = :id");
        $consulta->bindValue(':id', $id, PDO::PARAM_INT);
        $consulta->bindValue(':estado', $estado, PDO::PARAM_STR);
        $consulta->execute();
        return $consulta->fetchAll(PDO::FETCH_CLASS, 'pedido');
    }

    public static function modificarPrecioPedido($id, $precio)
    {
        $objAccesoDato = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDato->prepararConsulta("UPDATE pedidos SET precio = :precio WHERE id = :id");
        $consulta->bindValue(':id', $id, PDO::PARAM_INT);
        $consulta->bindValue(':precio', $precio, PDO::PARAM_STR);
        $consulta->execute();
        return $consulta->fetchAll(PDO::FETCH_CLASS, 'pedido');
    }

    public static function modificarTiempoEstimadoPedido($id, $tiempo)
    {
        $objAccesoDato = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDato->prepararConsulta("UPDATE pedidos SET tiempoEstimado = :tiempo WHERE id = :id");
        $consulta->bindValue(':id', $id, PDO::PARAM_INT);
        $consulta->bindValue(':tiempo', $tiempo, PDO::PARAM_STR);
        $consulta->execute();
        return $consulta->fetchAll(PDO::FETCH_CLASS, 'pedido');
    }

    public static function modificarTiempoFinalPedido($id, $tiempo)
    {
        $objAccesoDato = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDato->prepararConsulta("UPDATE pedidos SET tiempoFinal = :tiempo WHERE id = :id");
        $consulta->bindValue(':id', $id, PDO::PARAM_INT);
        $consulta->bindValue(':tiempo', $tiempo, PDO::PARAM_STR);
        $consulta->execute();
        return $consulta->fetchAll(PDO::FETCH_CLASS, 'pedido');
    }

    public static function ValidarTipoProductoRolEmpleado($tipo, $rol)
	{

        if($tipo == 'cocina' && !strcasecmp($rol, "cocinero")){

            return true;
        }else if($tipo == 'candy' && !strcasecmp($rol, "cocinero")){

            return true;
        }else if($tipo == 'tragos' && !strcasecmp($rol, "bartender")){

            return true;

        }else if($tipo == 'cerveza' && !strcasecmp($rol, "cervecero")){

            return true;
        }

        return false;
    		
	}

    

}