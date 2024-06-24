<?php

include_once(__DIR__ . '/../db/AccesoDatos.php');
include_once(__DIR__ . '/../utils/Archivos.php');

class Mesa
{
	public $id;
    public $estado; /* esperando, comiendo, pagando, cerrada */
    public function crearMesa()
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("INSERT INTO mesas (estado) VALUES (:estado)");
        $consulta->bindValue(':estado', $this->estado, PDO::PARAM_STR);
        $consulta->execute();

        return $objAccesoDatos->obtenerUltimoId();
    }
   
    public static function obtenerTodos()
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT * FROM mesas");
        $consulta->execute();

        return $consulta->fetchAll(PDO::FETCH_CLASS, 'Mesa');
    }
    public static function obtenerTodosId()
	{
		$objAccesoDatos = AccesoDatos::ObtenerInstancia();
		$consulta = $objAccesoDatos->PrepararConsulta("SELECT id FROM mesas");
		$consulta->execute();
		return $consulta->fetchAll(PDO::FETCH_COLUMN);
	}

    public static function obtenerMesa($id)
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT * FROM mesas WHERE id = :id");
        $consulta->bindValue(':id', $id, PDO::PARAM_INT);
        $consulta->execute();

        return $consulta->fetchObject('Mesa');
    }

    public static function modificarMesa($id, $estado)
    {
        $objAccesoDato = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDato->prepararConsulta("UPDATE mesas SET estado = :estado WHERE id = :id");
        $consulta->bindValue(':id', $id, PDO::PARAM_INT);
        $consulta->bindValue(':estado', $estado, PDO::PARAM_STR);
        $consulta->execute();
        return $consulta->fetchAll(PDO::FETCH_CLASS, 'Mesa');
    }

    public static function CsvAMesa($rutaArchivo)
	{
		$archivo = fopen($rutaArchivo, "r");
		$arrayArchivo = array();
		$datos = array();

		if ($archivo) {
			while (!feof($archivo)) {
				$arrayArchivo = fgetcsv($archivo);
				if (!empty($arrayArchivo)) {
					$mesa = new Mesa();
					$mesa->estado = $arrayArchivo[0];
					array_push($datos, $mesa);
				}
			}
			fclose($archivo);
		}

		return $datos;
	}

    public static function subirMesaCsv()
	{
		$archivo = Archivo::GuardarArchivo("db/", "mesas", 'csv', '.csv');
		if ($archivo != "N/A") {
			$arrayMesas = self::CsvAMesa($archivo);
			foreach ($arrayMesas as $mesa) {
				$mesa->crearMesa();
			}
			return true;
		}

		return false;
	}


    public static function descargaDbCsv($rutaArchivo)
	{
		$mesas = self::obtenerTodos();
        $arrayCsv = [];

        if (empty($mesas)) {
            return false;  

        }

        $archivo = fopen($rutaArchivo, 'w');

        fputcsv($archivo, ['id', 'estado']);

        foreach ($mesas as $mesa) {

            fputcsv($archivo, [(int)$mesa->id, $mesa->estado]);
        }

        fclose($archivo);
		
		return true;
	}


}